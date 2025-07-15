<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class CostCenterProjectRealizationExport implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $departmentId;
    protected $year;

    public function __construct($departmentId, $year)
    {
        $this->departmentId = $departmentId;
        $this->year = $year;
    }

    public function collection()
    {
        $projects = Project::whereHas(
            'costCenters',
            function ($query) {
                $query->where('type', 'project')
                    ->where('department_id', $this->departmentId)
                    ->where('year', date('Y'));
            }
        )->where('status', 'Finished')
            ->with(['financial', 'finalization', 'profit', 'client', 'pic'])
            ->get();

        return $projects->map(function ($project, $index) {
            $ppn = $project->financial->job_value * (((float) $project->financial->vat_percent) / 100);
            $pph = $project->financial->job_value * (((float) $project->financial->tax_percent) / 100);
            $sp2d = $project->financial->job_value - $ppn - $pph;
            $modal = $project->costCenters->sum('amount_debit');
            $margin = $project->financial->margin;
            $team = $margin * (($project->profit->percent_team_bonus) / 100);
            $depreciation = $margin * (($project->profit->percent_depreciation) / 100);
            $cash = $margin * (($project->profit->percent_cash_department) / 100);
            $company = $margin * (($project->profit->percent_company) / 100);

            return [
                $index + 1,
                $this->year,
                $project->name,
                $project->client->name,
                $project->financial->job_value,
                $ppn,
                $pph,
                $sp2d,
                $modal,
                $margin,
                $team,
                $depreciation,
                $cash,
                $company,
                $project->finalization->e_faktur,
                $project->finalization->id_billing_ppn,
                $project->finalization->id_billing_pph,
                $project->finalization->ntpn_ppn,
                $project->finalization->ntpn_pph,
                $project->pic->name,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tahun',
            'Nama Pekerjaan',
            'Pemberi Pekerjaan',
            'Nilai Pekerjaan',
            'PPN (11%/12%)',
            'PPH (1.5%/2%)',
            'SP2D',
            'Modal',
            'Margin',
            'Bonus Tim',
            'Penyusutan',
            'Kas',
            'Perusahaan',
            'No e-Faktur (Pajak)',
            'ID Billing PPN',
            'ID Billing PPH',
            'NTPN PPN',
            'NTPN PPH',
            'PIC',
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
