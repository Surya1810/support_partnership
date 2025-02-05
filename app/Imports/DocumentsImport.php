<?php

namespace App\Imports;

use App\Models\Document;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class DocumentsImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Document([
            'number'      => $row['number'],
            'type'        => $row['type'],
            'date'        => Date::excelToDateTimeObject($row['date'])->format('Y-m-d'),
            'purpose'     => $row['purpose'],
            'company'     => $row['company'],
            'desc' => $row['description'] ?? null,
        ]);
    }
}
