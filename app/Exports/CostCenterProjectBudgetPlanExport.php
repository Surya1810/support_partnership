<?php

namespace App\Exports;

use App\Models\CostCenter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CostCenterProjectBudgetPlanExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    protected $projectId;
    protected $year;

    public function __construct($projectId, $year)
    {
        $this->projectId = $projectId;
        $this->year = $year;
    }

    public function collection()
    {
        $costCenters = CostCenter::where('type', 'project')
            ->where('project_id', $this->projectId)
            ->where('year', $this->year)
            ->get();

        return $costCenters->map(function ($item, $index) {
            $detail = $item->detail ?? '';

            $step1 = explode('<br/>', $detail);
            $step2 = implode("\n", $step1);
            $step3 = explode('<hr style="margin:0"/>', $step2);
            $finalDetail = strip_tags(implode("\n\n", $step3));

            return [
                'No' => $index + 1,
                'Tahun' => $item->year,
                'Bulan' => $item->month_name,
                'Judul' => $item->name,
                'Kode Transaksi' => $item->code_ref,
                'Debet' => $item->amount_debit == 0 ? '' : $item->amount_debit,
                'Limit' => $item->amount_credit == 0 ? '' : $item->amount_credit,
                'Keterangan' => $finalDetail,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tahun',
            'Bulan',
            'Judul',
            'Kode Transaksi',
            'Debet (Rp)',
            'Limit (Rp)',
            'Keterangan',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('H')->getAlignment()->setWrapText(true);
            },
        ];
    }
}
