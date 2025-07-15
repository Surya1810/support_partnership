<?php

namespace App\Imports;

use App\Models\CostCenter;
use App\Models\CostCenterCategory;
use App\Models\Department;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class CostCenterGeneralImport implements ToCollection, WithCalculatedFormulas
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $noteKS = '';
        $idKS = null;

        foreach ($rows->skip(1) as $row) {
            if (!isset($row[4]) || trim($row[4]) === '') {
                continue;
            }

            $name = $row[1];
            $month = $row[2];
            $year = $row[3];
            $category = CostCenterCategory::where('code', strtoupper(trim($row[4])))->first();
            $department = Department::where('code', trim($row[5]))->first();
            $codeRef = $this->generateCodeRef($department, $category->code, [
                'year' => $year,
                'month' => $month,
                'category' => $category->id
            ]);

            $note = '<small>Dibuat oleh: ' . Auth::user()->username . '<br/>Tanggal: ' . date('d-m-Y') . '</small>';

            if ($category->code == 'KS') {
                $amountDebit = $row[6];
                $amountCredit = 0;
                $amountRemaining = 0;
                $noteKS = $note;

                $costCenter = CostCenter::create([
                    'name' => $name,
                    'department_id' => $department->id,
                    'cost_center_category_id' => $category->id,
                    'type' => 'department',
                    'code_ref' => $codeRef,
                    'amount_debit' => $amountDebit,
                    'amount_credit' => $amountCredit,
                    'amount_remaining' => $amountRemaining,
                    'month' => $month,
                    'year' => $year,
                    'note' => $note
                ]);

                $idKS = $costCenter->id;
            } else {
                $amountDebit = 0;
                $amountCredit = $row[7];
                $amountRemaining = $amountCredit;

                // untuk note uang kas
                $noteKS .= '<hr style="margin:0"/><small><span class="text-danger">RAB dikurangi: -'
                    . formatRupiah((int) $amountCredit)
                    . '</span><br/>Untuk RAB Baru: ' . $codeRef
                    . '<br/>Oleh: ' . Auth::user()->username
                    . '<br/>Tanggal: ' . date('d-m-Y')
                    . '</small>';

                $costCenter = CostCenter::create([
                    'name' => $name,
                    'department_id' => $department->id,
                    'cost_center_category_id' => $category->id,
                    'type' => 'department',
                    'code_ref' => $codeRef,
                    'amount_debit' => $amountDebit,
                    'amount_credit' => $amountCredit,
                    'amount_remaining' => $amountRemaining,
                    'month' => $month,
                    'year' => $year,
                    'detail' => $note
                ]);
            }

            // update detail untuk kategori ks
            if ($idKS) {
                CostCenter::where('id', $idKS)->update([
                    'detail' => $noteKS
                ]);
            }
        }
    }

    private function generateCodeRef($department, $costCenterCode, $request)
    {
        // Get last RAB Department
        $lastRAB = CostCenter::where('department_id', $department->id)
            ->where('type', 'department')
            ->where('year', $request['year'])
            ->orderBy('id', 'desc')
            ->first();

        if ($lastRAB) {
            $explodedLastCodeRef = explode('/', $lastRAB->code_ref);
            $lastTransactionNumber = $explodedLastCodeRef[1];
            $currentTransactionNumber = str_pad((int) $lastTransactionNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $currentTransactionNumber = '0001';
        }

        // Create Code Ref
        $monthIndex = str_pad($request['month'], 2, '0', STR_PAD_LEFT);
        $codeRef = $department->code . '.' .  $costCenterCode
            . '.' . $monthIndex . '-' . $request['year'] . '/' . $currentTransactionNumber;

        return $codeRef;
    }
}
