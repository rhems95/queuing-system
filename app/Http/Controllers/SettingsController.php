<?php

namespace App\Http\Controllers;

use App\Models\PaperSmsContact;
use App\Models\Setting;
use App\Services\GsmNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings', [
            'walkinPin' => Setting::getValue('walkin_pin', '1981'),
            'paperRows' => $this->paperFormRows(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'walkin_pin' => ['required', 'regex:/^\d{4,6}$/'],
        ], [
            'walkin_pin.regex' => 'PIN must be 4 to 6 digits.',
        ]);

        Setting::setValue('walkin_pin', $data['walkin_pin']);

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Kiosk walk-in PIN updated.');
    }

    public function updatePaperSms(Request $request, GsmNotifier $sms)
    {
        if (! Schema::hasTable('paper_sms_contacts')) {
            return redirect()
                ->route('admin.settings.edit')
                ->withErrors(['Run the paper SMS migration before saving numbers.'], 'paper');
        }

        $rows = $request->input('contacts', []);
        if (! is_array($rows)) {
            $rows = [];
        }

        $kept = [];
        $errors = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $name = trim((string) ($row['name'] ?? ''));
            $mobileRaw = trim((string) ($row['mobile'] ?? ''));
            $remove = (string) ($row['remove'] ?? '') === '1';
            if ($remove || ($name === '' && $mobileRaw === '')) {
                continue;
            }
            if ($name === '' || $mobileRaw === '') {
                $errors[] = 'Each paper-alert row needs a name and a mobile number.';

                continue;
            }
            if (mb_strlen($name) > 80) {
                $errors[] = 'Name must be 80 characters or less.';

                continue;
            }
            $mobile = $sms->normalizeMobile($mobileRaw);
            if ($mobile === null) {
                $errors[] = 'Use a Philippine mobile like 09XXXXXXXXX.';

                continue;
            }
            $kept[] = [
                'name' => $name,
                'mobile' => $mobile,
                'enabled' => (string) ($row['enabled'] ?? '0') === '1',
            ];
        }

        if (count($kept) > 8) {
            $errors[] = 'Save up to 8 numbers.';
        }

        $seen = [];
        foreach ($kept as $row) {
            if (isset($seen[$row['mobile']])) {
                $errors[] = 'Each mobile number can be listed once.';
                break;
            }
            $seen[$row['mobile']] = true;
        }

        if ($errors !== []) {
            return redirect()
                ->route('admin.settings.edit')
                ->withErrors($errors, 'paper')
                ->withInput();
        }

        DB::transaction(function () use ($kept) {
            PaperSmsContact::query()->delete();
            foreach ($kept as $row) {
                PaperSmsContact::query()->create($row);
            }
        });

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Printer paper SMS list updated.');
    }

    /**
     * @return array<int, array{name: string, mobile: string, enabled: string}>
     */
    private function paperFormRows(): array
    {
        $old = old('contacts');
        if (is_array($old)) {
            $rows = [];
            foreach ($old as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $name = trim((string) ($row['name'] ?? ''));
                $mobile = trim((string) ($row['mobile'] ?? ''));
                if ($name === '' && $mobile === '') {
                    continue;
                }
                $rows[] = [
                    'name' => $name,
                    'mobile' => $mobile,
                    'enabled' => (string) ($row['enabled'] ?? '0') === '1' ? '1' : '0',
                ];
            }

            return $rows;
        }

        if (! Schema::hasTable('paper_sms_contacts')) {
            return [];
        }

        return PaperSmsContact::query()
            ->orderBy('id')
            ->get()
            ->map(fn (PaperSmsContact $contact) => [
                'name' => (string) $contact->name,
                'mobile' => PaperSmsContact::asLocalMobile((string) $contact->mobile),
                'enabled' => $contact->enabled ? '1' : '0',
            ])
            ->all();
    }
}
