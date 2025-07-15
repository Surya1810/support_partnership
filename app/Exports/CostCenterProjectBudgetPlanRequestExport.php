<?php

namespace App\Exports;

use App\Models\ExpenseRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CostCenterProjectBudgetPlanRequestExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        $requests = ExpenseRequest::where('project_id', $this->projectId)
            ->whereIn('status', ['finish', 'report', 'checking'])
            ->where('category', 'project')
            ->with(['user', 'items'])
            ->get();

        return $requests->map(function ($item, $index) {
            $remaining = $item->status == 'finish' ? ($item->total_amount - $item->items->sum('actual_amount') == 0
                ? '' : $item->total_amount - $item->items->sum('actual_amount')) : '';
            return [
                'No' => $index + 1,
                'Tanggal Digunakan' => $item->use_date->format('Y-m-d'),
                'Judul' => $item->title,
                'Kode Transaksi' => $item->code_ref_request,
                'Pengaju' => $item->user->name,
                'Diajukan' => $item->total_amount == 0 ? '' : $item->total_amount,
                'Digunakan' => $item->items->sum('actual_amount') == 0 ? '' : $item->items->sum('actual_amount'),
                'Dikembalikan' => $remaining,
                'Status' => $item->status
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Digunakan',
            'Judul',
            'Kode Transaksi',
            'Pengaju',
            'Diajukan (Rp)',
            'Digunakan (Rp)',
            'Dikembalikan (Rp)',
            'Status',
        ];
    }
}
