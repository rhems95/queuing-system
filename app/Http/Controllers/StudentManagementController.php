<?php

namespace App\Http\Controllers;

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
                    ->orWhere('name', 'like', '%'.$q.'%');
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
        $data = $request->validate([
            'student_id' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:100'],
        ]);

        $studentId = $this->students->normalize($data['student_id']);
        $name = trim(preg_replace('/\s+/', ' ', $data['name']) ?? '');

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

        if (Student::query()->where('student_id', $studentId)->exists()) {
            return back()
                ->withErrors(['student_id' => 'This student ID is already on the list.'])
                ->withInput();
        }

        $now = now();
        Student::query()->create([
            'student_id' => $studentId,
            'name' => $name,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return redirect()
            ->route('admin.students.index')
            ->with('status', 'Student '.$studentId.' added.');
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

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "student_id,name\n");
            fwrite($out, "2024-0006,Sample Student\n");
            fwrite($out, "2024-0007,Another Student\n");
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

                $rawId = $cells[0] ?? '';
                $rawName = $cells[1] ?? '';
                $studentId = $this->students->normalize($rawId);
                $name = trim(preg_replace('/\s+/', ' ', $rawName) ?? '');

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

                Student::query()->create([
                    'student_id' => $studentId,
                    'name' => $name,
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

    private function stripBom(string $text): string
    {
        if (str_starts_with($text, "\xEF\xBB\xBF")) {
            return substr($text, 3);
        }

        return $text;
    }

    /**
     * @param  list<string|null>  $row
     */
    private function isHeaderRow(array $row): bool
    {
        $first = strtolower(trim((string) ($row[0] ?? '')));
        $first = $this->stripBom($first);

        return str_contains($first, 'student') || $first === 'id' || $first === 'student_id';
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
