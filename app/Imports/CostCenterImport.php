<?php

namespace App\Imports;

use App\Models\CostCenter;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

use Carbon\Carbon;

class CostCenterImport implements ToCollection, WithHeadingRow
{
    protected $departmentId;

    public function __construct($departmentId)
    {
        $this->departmentId = $departmentId;
    }

    public function collection(Collection $rows)
    {
        $countTotalCostCenter = CostCenter::where('department_id', $this->departmentId)->count();
        $currentYear = Carbon::now()->format('y');

        foreach ($rows as $index => $row) {
            $code = str_pad($this->departmentId, 2, '0', STR_PAD_LEFT)
                . '-' . $currentYear
                . '-' . ($countTotalCostCenter + $index + 1); // urutan +1 dari count

            CostCenter::create([
                'department_id' => $this->departmentId,
                'name'          => $row['nama'],
                'amount'        => $row['amount'],
                'code'          => $code,
            ]);
        }
    }
}
