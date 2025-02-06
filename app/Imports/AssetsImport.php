<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AssetsImport implements ToModel, WithHeadingRow
{
    protected $userId;
    protected $existingRfids;

    public function __construct($userId)
    {
        $this->userId = $userId;
        $this->existingRfids = Tag::where('status', 'available')->pluck('rfid_number')->toArray();
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        if (!in_array($row['rfid'], $this->existingRfids)) {
            return null;
        }

        $asset = Asset::firstOrCreate(
            ['rfid_number' => $row['rfid']], // Unik
            [
                'name'          => $row['nama'],
                'code'          => $row['kode'],
                'type'          => $row['jenis'],

                'condition'         => $row['kondisi'],
                'tgl_perawatan'     => Date::excelToDateTimeObject($row['perawatan'])->format('Y-m-d'),

                'tahun_perolehan'   => $row['tahun'],
                'harga_perolehan'   => $row['harga'],
                'masa_guna'         => $row['masa'],

                'status'        => $row['status'] ?? null,
                'desc'          => $row['deskripsi'] ?? null,

                'gedung'          => $row['gedung'],
                'lantai'          => $row['lantai'],
                'ruangan'          => $row['ruangan'],
                'user_id'       => $this->userId,
            ]
        );
        Tag::where('rfid_number', $row['rfid'])->update(['status' => 'used']);

        return $asset;
    }
}
