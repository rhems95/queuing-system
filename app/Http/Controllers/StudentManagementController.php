<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Student;
use App\Services\StudentQueueGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentManagementController extends Controller
{
    private const MAX_CSV_ROWS = 2000;

    public function __construct(private StudentQueueGuard $students)
    {
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->input('q', ''));

        $query = Student::query()->orderBy('student_id');

        if ($q !== '') {
            $query->where(function ($inner) use ($q) {
                $inner->where('student_id', 'like', '%'.$q.'%')
                    ->orWhere('name', 'like', '%'.$q.'%')
                    ->orWhere('card_uid', 'like', '%'.$q.'%')
                    ->orWhere('mobile', 'like', '%'.$q.'%');
            });
        }

        $records = $query->paginate(20)->withQueryString();

        return view('admin.students.index', compact('records', 'q'));
    }

    public function create(): View
    {
        return view('admin.students.form');
    }

    public function store(Request $request): RedirectResponse
    {
        $fields = $this->studentFields($request);
        if ($fields instanceof RedirectResponse) {
            return $fields;
        }

        $now = now();
        Student::query()->create([
            'student_id' => $fields['student_id'],
            'name' => $fields['name'],
            'card_uid' => $fields['card_uid'],
            'mobile' => $fields['mobile'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return redirect()
            ->route('admin.students.index')
            ->with('status', 'Student '.$fields['student_id'].' added.');
    }

    public function edit(Student $student): View
    {
        return view('admin.students.form', compact('student'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $fields = $this->studentFields($request, $student);
        if ($fields instanceof RedirectResponse) {
            return $fields;
        }

        $previousId = $student->student_id;

        DB::transaction(function () use ($student, $fields, $previousId) {
            if ($previousId !== $fields['student_id']) {
                Queue::query()
                    ->where('student_id', $previousId)
                    ->update(['student_id' => $fields['student_id']]);
            }

            $student->fill([
                'student_id' => $fields['student_id'],
                'name' => $fields['name'],
                'card_uid' => $fields['card_uid'],
                'mobile' => $fields['mobile'],
                'updated_at' => now(),
            ]);
            $student->save();
        });

        return redirect()
            ->route('admin.students.index')
            ->with('status', 'Student '.$fields['student_id'].' updated.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $open = $this->students->openTicketToday($student->student_id);
        if ($open) {
            return back()->withErrors([
                'delete' => 'Cannot delete '.$student->student_id.' while ticket '.$open->queue_number.' is still open.',
            ]);
        }

        $label = $student->student_id;
        $student->delete();

        return redirect()
            ->route('admin.students.index')
            ->with('status', 'Student '.$label.' deleted.');
    }

    public function sample(): StreamedResponse
    {
        $filename = 'pecit-students-sample.csv';

        $sample = database_path('samples/pecit-students.csv');

        return response()->streamDownload(function () use ($sample) {
            $out = fopen('php://output', 'w');
            if (is_file($sample) && is_readable($sample)) {
                fwrite($out, file_get_contents($sample));
            } else {
                fwrite($out, "student_id,name,card_uid,mobile\n");
                fwrite($out, "2024-0006,Sample Student,,\n");
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'max:2048'],
        ]);

        $file = $request->file('csv');
        $ext = strtolower((string) $file->getClientOriginalExtension());
        if (! in_array($ext, ['csv', 'txt'], true)) {
            return back()->withErrors(['csv' => 'Upload a .csv file.']);
        }

        $path = $file->getRealPath() ?: $file->getPathname();
        if ($path === false || ! is_readable($path)) {
            return back()->withErrors(['csv' => 'Could not read the uploaded file.']);
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return back()->withErrors(['csv' => 'Could not open the uploaded file.']);
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);

            return back()->withErrors(['csv' => 'The CSV file is empty.']);
        }

        $firstLine = $this->stripBom($firstLine);
        $delimiter = substr_count($firstLine, ';') > substr_count($firstLine, ',') ? ';' : ',';
        rewind($handle);

        $added = 0;
        $skipped = 0;
        $invalid = 0;
        $errors = [];
        $rowNum = 0;
        $seenInFile = [];
        $now = now();
        $columnMap = [
            'student_id' => 0,
            'name' => 1,
            'card_uid' => 2,
            'mobile' => 3,
        ];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                $rowNum++;
                if ($row === [null] || $row === false) {
                    continue;
                }
                if ($rowNum === 1 && isset($row[0])) {
                    $row[0] = $this->stripBom((string) $row[0]);
                }
                if ($rowNum === 1 && $this->isHeaderRow($row)) {
                    $columnMap = $this->csvColumnMap($row);
                    continue;
                }

                if ($rowNum > self::MAX_CSV_ROWS + 1) {
                    $errors[] = 'Stopped at '.self::MAX_CSV_ROWS.' data rows (file is larger).';
                    break;
                }

                $cells = array_map(fn ($v) => trim((string) $v), $row);
                if ($this->rowIsEmpty($cells)) {
                    continue;
                }

                $rawId = $cells[$columnMap['student_id']] ?? '';
                $rawName = $cells[$columnMap['name']] ?? '';
                $studentId = $this->students->normalize($rawId);
                $name = trim(preg_replace('/\s+/', ' ', $rawName) ?? '');
                $cardUid = StudentQueueGuard::normalizeUid($cells[$columnMap['card_uid']] ?? null);
                $mobile = $this->normalizeMobile($cells[$columnMap['mobile']] ?? null);

                if ($studentId === '' || ! preg_match('/^[A-Z0-9][A-Z0-9\-]*$/', $studentId) || strlen($studentId) > 50) {
                    $invalid++;
                    $this->pushError($errors, 'Row '.$rowNum.': invalid student ID.');
                    continue;
                }

                if ($name === '' || strlen($name) > 100) {
                    $invalid++;
                    $this->pushError($errors, 'Row '.$rowNum.': missing or invalid name for '.$studentId.'.');
                    continue;
                }

                if (isset($seenInFile[$studentId])) {
                    $skipped++;
                    continue;
                }
                $seenInFile[$studentId] = true;

                if (Student::query()->where('student_id', $studentId)->exists()) {
                    $skipped++;
                    continue;
                }

                if ($cardUid !== '' && Student::query()->where('card_uid', $cardUid)->exists()) {
                    $invalid++;
                    $this->pushError($errors, 'Row '.$rowNum.': card serial already assigned ('.$cardUid.').');
                    continue;
                }

                Student::query()->create([
                    'student_id' => $studentId,
                    'name' => $name,
                    'card_uid' => $cardUid !== '' ? $cardUid : null,
                    'mobile' => $mobile !== '' ? $mobile : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $added++;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);

            return back()->withErrors(['csv' => 'Import failed. Check the file and try again.']);
        }

        fclose($handle);

        $summary = $added.' added';
        if ($skipped > 0) {
            $summary .= ', '.$skipped.' skipped (already listed or duplicate in file)';
        }
        if ($invalid > 0) {
            $summary .= ', '.$invalid.' invalid';
        }

        return redirect()
            ->route('admin.students.index')
            ->with('status', 'CSV import: '.$summary.'.')
            ->with('import_errors', $errors);
    }

    /**
     * @return array{student_id: string, name: string, card_uid: ?string, mobile: ?string}|RedirectResponse
     */
    private function studentFields(Request $request, ?Student $existing = null): array|RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:100'],
            'card_uid' => ['nullable', 'string', 'max:32'],
            'mobile' => ['nullable', 'string', 'max:20'],
        ]);

        $studentId = $this->students->normalize($data['student_id']);
        $name = trim(preg_replace('/\s+/', ' ', $data['name']) ?? '');
        $cardUid = StudentQueueGuard::normalizeUid($data['card_uid'] ?? null);
        $mobile = $this->normalizeMobile($data['mobile'] ?? null);

        if ($studentId === '' || ! preg_match('/^[A-Z0-9][A-Z0-9\-]*$/', $studentId)) {
            return back()
                ->withErrors(['student_id' => 'Enter a valid student ID (letters, numbers, and hyphen).'])
                ->withInput();
        }

        if ($name === '') {
            return back()
                ->withErrors(['name' => 'Enter the student name.'])
                ->withInput();
        }

        $idTaken = Student::query()->where('student_id', $studentId);
        $cardTaken = Student::query()->where('card_uid', $cardUid);
        if ($existing) {
            $idTaken->where('id', '!=', $existing->id);
            $cardTaken->where('id', '!=', $existing->id);
        }

        if ($idTaken->exists()) {
            return back()
                ->withErrors(['student_id' => 'This student ID is already on the list.'])
                ->withInput();
        }

        if ($cardUid !== '' && $cardTaken->exists()) {
            return back()
                ->withErrors(['card_uid' => 'This card serial is already assigned to another student.'])
                ->withInput();
        }

        return [
            'student_id' => $studentId,
            'name' => $name,
            'card_uid' => $cardUid !== '' ? $cardUid : null,
            'mobile' => $mobile !== '' ? $mobile : null,
        ];
    }

    private function normalizeMobile(?string $raw): string
    {
        $mobile = trim((string) $raw);
        $mobile = preg_replace('/[^\d+]/', '', $mobile) ?? '';
        if ($mobile === '+' || $mobile === '') {
            return '';
        }

        return substr($mobile, 0, 20);
    }

    private function stripBom(string $text): string
    {
        if (str_starts_with($text, "\xEF\xBB\xBF")) {
            return substr($text, 3);
        }

        return $text;
    }

    /**
     * @param  list<string|null>  $row
     * @return array{student_id: int, name: int, card_uid: int, mobile: int}
     */
    private function csvColumnMap(array $row): array
    {
        $map = [
            'student_id' => 0,
            'name' => 1,
            'card_uid' => 2,
            'mobile' => 3,
        ];
        $aliases = [
            'student_id' => ['studentid', 'id', 'studentnumber'],
            'name' => ['name', 'studentname', 'fullname'],
            'card_uid' => ['carduid', 'uid', 'card', 'cardserial', 'serial'],
            'mobile' => ['mobile', 'contactnumber', 'contact', 'phone', 'cellphone'],
        ];

        foreach ($row as $index => $cell) {
            $key = strtolower(preg_replace('/[^a-z0-9]/', '', $this->stripBom((string) $cell)) ?? '');
            foreach ($aliases as $field => $names) {
                if (in_array($key, $names, true)) {
                    $map[$field] = (int) $index;
                }
            }
        }

        return $map;
    }

    /**
     * @param  list<string|null>  $row
     */
    private function isHeaderRow(array $row): bool
    {
        $first = strtolower(trim((string) ($row[0] ?? '')));
        $first = $this->stripBom($first);

        return str_contains($first, 'student') || $first === 'id' || $first === 'student_id' || $first === 'uid';
    }

    /**
     * @param  list<string>  $cells
     */
    private function rowIsEmpty(array $cells): bool
    {
        foreach ($cells as $cell) {
            if ($cell !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  list<string>  $errors
     */
    private function pushError(array &$errors, string $message): void
    {
        if (count($errors) < 15) {
            $errors[] = $message;
        }
    }
}
