<?php

namespace App\Exports;

use App\Models\UserJob;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MyTaskExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
    }

    public function collection()
    {
        $jobs = UserJob::with(['assigner', 'assignee', 'assignee.department'])
            ->where('assignee_id', Auth::user()->id)
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereBetween('start_date', [$this->startDate, $this->endDate]);
            })
            ->orderBy('created_at', 'desc');

        $no = 1;

        return $jobs->get()->map(function ($job) use (&$no) {
            return [
                'No'              => $no++,
                'Pemberi'         => $job->assigner->name ?? '-',
                'Penerima'        => $job->assignee->name ?? '-',
                'Divisi'          => $job->assignee->department->name ?? '-',
                'Detail Pekerjaan' => $job->job_detail ?? '-',
                'Adendum/Catatan' => $job->feedback ?? '-',
                'Revisi' => $job->notes ?? '-',
                'Tanggal Mulai'   => $job->start_date,
                'Tanggal Akhir'   => $job->end_date,
                'Tanggal Selesai'   => $job->completed_at ?? '-',
                'Status'          => $job->status,
                'Sisa Waktu/Hari' => $this->calculateTimeRemaining($job),
                'Point'           => $this->calculateEfficiency($job),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Pemberi',
            'Penerima',
            'Divisi',
            'Detail Pekerjaan',
            'Adendum/Catatan',
            'Revisi',
            'Tanggal Mulai',
            'Tanggal Akhir',
            'Tanggal Selesai',
            'Status',
            'Sisa Waktu/Hari',
            'Point(%)',
        ];
    }

    /**
     * Event untuk freeze pane dan styling
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Freeze top row
                $event->sheet->freezePane('A2');

                // Optional: Bold heading
                $event->sheet->getStyle('A1:J1')->getFont()->setBold(true);
            },
        ];
    }

    private function calculateTimeRemaining($job)
    {
        if (!$job->end_date || !$job->start_date) return '-';

        $endDate = Carbon::parse($job->end_date);

        if ($job->completed_at) {
            $completedAt = Carbon::parse($job->completed_at);
            if ($completedAt->equalTo($endDate)) return "0";
            elseif ($completedAt->lessThan($endDate)) return "+" . $endDate->diffInDays($completedAt) * -1;
            else return $completedAt->diffInDays($endDate);
        } else {
            $today = Carbon::today();
            if ($today->gt($endDate)) return $today->diffInDays($endDate);
            elseif ($today->eq($endDate)) return "0";
            else return $endDate->diffInDays($today) * -1;
        }
    }

    private function calculateEfficiency($job)
    {
        if (!$job->start_date || !$job->end_date) return '-';

        $start = Carbon::parse($job->start_date);
        $end = Carbon::parse($job->end_date);

        if ($start->equalTo($end)) return '0%';

        $totalDuration = $start->diffInSeconds($end);

        if (!$job->completed_at) {
            $today = Carbon::today();
            $actualDuration = $start->diffInSeconds($today);
            $diff = $today->gt($end)
                ? (($actualDuration - $totalDuration) / $totalDuration) * 100 * -1
                : (($totalDuration - $actualDuration) / $totalDuration) * 100;
            return ($diff > 0 ? '+' : '') . round($diff) . '%';
        }

        $completed = Carbon::parse($job->completed_at);
        $actualDuration = $start->diffInSeconds($completed);
        $diffPercentage = (($totalDuration - $actualDuration) / $totalDuration) * 100;

        return $actualDuration < $totalDuration
            ? "+" . round($diffPercentage) . "%"
            : "-" . round(abs($diffPercentage)) . "%";
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestCol = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$highestCol}{$highestRow}")
            ->getAlignment()
            ->setWrapText(true);
    }
}
