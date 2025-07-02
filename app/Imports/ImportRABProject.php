<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Illuminate\Support\Collection;

class ImportRABProject implements ToCollection, WithCalculatedFormulas
{
    public Collection $rows;

    public function collection(Collection $collection)
    {
        $this->rows = $collection;
    }
}
