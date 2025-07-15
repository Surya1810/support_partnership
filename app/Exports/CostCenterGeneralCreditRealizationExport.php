<?php

namespace App\Exports;

use App\Models\ExpenseRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CostCenterGeneralCreditRealizationExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $year;

    public function __construct($year)
    {
        $this->year = $year;
    }

    public function collection()
    {
        $creditRealizations = ExpenseRequest::where('status', 'finish')
            ->with(['costCenter', 'items', 'user', 'department'])
            ->where('category', 'department')
            ->orderBy('created_at', 'desc')
            ->get();

        return $creditRealizations->map(function ($creditRealization, $index) {
            return [
                $index + 1,
                $creditRealization->title,
                $creditRealization->code_ref_request,
                $creditRealization->department->name,
                $creditRealization->user->name,
                $creditRealization->total_amount,
                $creditRealization->items->sum('actual_amount'),
                $creditRealization->total_amount - $creditRealization->items->sum('actual_amount')
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pengajuan',
            'Kode Transaksi',
            'Divisi',
            'Pengaju',
            'Diajukan',
            'Digunakan',
            'Dikembalikan'
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
