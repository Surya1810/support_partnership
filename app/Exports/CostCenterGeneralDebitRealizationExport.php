<?php

namespace App\Exports;

use App\Models\CostCenter;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class CostCenterGeneralDebitRealizationExport implements FromCollection, WithEvents, WithHeadings, ShouldAutoSize
{
    protected $year;
    protected $filter;

    public function __construct($year, $filter)
    {
        $this->year = $year;
        $this->filter = $filter;
    }

    public function collection()
    {
        $debitRealizations = CostCenter::where('type', 'department')
            ->when(Auth::user()->role_id == 3, function ($query) {
                $query->where('department_id', Auth::user()->department_id);
            })
            ->with('department')
            ->where('year', $this->year)
            ->when($this->filter, function ($query) {
                $query->whereBetween('year', [$this->filter['fromYear'], $this->filter['toYear']]);
            })
            ->when($this->filter['departmentFilter'], function ($query) {
                $query->where('department_id', $this->filter['departmentFilter']);
            })
            ->where('type', 'department')
            ->get();

        return $debitRealizations->map(function ($debitRealization, $index) {
            $detail = $debitRealization->detail ?? '';

            $step1 = explode('<br/>', $detail);
            $step2 = implode("\n", $step1);
            $step3 = explode('<hr style="margin:0"/>', $step2);
            $finalDetail = strip_tags(implode("\n\n", $step3));

            return [
                $index + 1,
                $debitRealization->name,
                $debitRealization->code_ref,
                $debitRealization->department?->name,
                $debitRealization->month_name,
                $debitRealization->year,
                $debitRealization->amount_debit == 0 ? '' : $debitRealization->amount_debit,
                $debitRealization->amount_credit == 0 ? '' : $debitRealization->amount_credit,
                $finalDetail
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Item',
            'Kode Transaksi',
            'Divisi',
            'Bulan Realisasi',
            'Tahun',
            'Debet',
            'Kredit (Limit Setiap RAB)',
            'Keterangan'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $headingRange = 'A1:' . $highestColumn . '1';
                $dataRange = 'A1:' . $highestColumn . $highestRow;

                $sheet->getStyle($headingRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);

                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'wrapText' => true,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                    ],
                ]);
            },
        ];
    }
}
