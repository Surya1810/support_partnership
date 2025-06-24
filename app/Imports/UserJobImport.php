<?php

namespace App\Imports;

use App\Models\User;
use App\Models\UserJob;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Exception;

class UserJobImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $rows->skip(1)->each(function ($column, $index) {
            $username = ucfirst(strtolower(trim($column[1])));
            $detail = trim($column[2]);
            $start = $this->parseDate($column[3]);
            $end = $this->parseDate($column[4]);

            if (!$username || !$start || !$end) {
                return;
            }

            $user = User::where('username', $username)->first();

            if (!$user) {
                throw new Exception("User dengan username '{$username}' tidak ditemukan di baris " . ($index + 2));
            }

            UserJob::create([
                'assigner_id' => Auth::user()->id,
                'assignee_id' => $user->id,
                'department_id' => $user->department_id,
                'job_detail' => $detail,
                'start_date' => $start,
                'end_date' => $end,
                'notes' => 'Pengerjaan ke-1',
            ]);
        });
    }

    private function parseDate($value)
    {
        try {
            // Jika value berupa Excel serial number (numeric)
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            // Format-format umum Excel (dari user manual entry)
            $formats = [
                'd/m/Y',
                'd-m-Y',
                'Y-m-d',
                'm/d/Y',
                'm-d-Y',
                'd M Y',
            ];

            foreach ($formats as $format) {
                $dt = Carbon::createFromFormat($format, $value);
                if ($dt !== false) {
                    return $dt->format('Y-m-d');
                }
            }

            // Jika tidak cocok, coba parsing bebas
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
