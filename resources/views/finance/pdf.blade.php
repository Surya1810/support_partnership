<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Application Report</title>
    <style>
        @page {
            size: 210mm 330mm;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            position: relative;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        /* Watermark full page */
        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("{{ public_path('assets/img/kop/F4.jpg') }}") no-repeat center center;

            background-size: cover;
            /* Agar memenuhi halaman */
            background-position: center;
            opacity: 1;
            /* Sesuaikan transparansi */
            z-index: -1;
        }

        /* Jarak agar teks tidak tertutup kop surat */
        .content {
            margin-top: 120px;
            /* Sesuaikan dengan tinggi kop surat */
            padding-top: 20px;
            padding-left: 60px;
            padding-right: 60px;
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }

        /* Tanda Tangan */
        .signature {
            margin-top: 50px;
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- Watermark -->
    <div class="watermark"></div>

    <div class="content">
        <h2 style="text-align:center;text-decoration: underline">Laporan Penggunaan Dana</h2>
        <br>

        <table style="border-collapse: collapse; width: 100%; margin-bottom: 10px;" class="table-info">
            <tr>
                <td style="width: 10%; text-align: left;border: none;"><strong>Pengaju</strong></td>
                <td style="width: 2%;border: none;">:</td>
                <td style="text-align: left;border: none;">{{ $expenseRequest->user->name }}</td>
            </tr>
            <tr>
                <td style="text-align: left;border: none;"><strong>Divisi</strong></td>
                <td style="border: none;">:</td>
                <td style="text-align: left;border: none;">{{ $expenseRequest->department->name }}</td>
            </tr>
            <tr>
                <td style="text-align: left;border: none;"><strong>Tanggal Penggunaan</strong></td>
                <td style="border: none;">:</td>
                <td style="text-align: left;border: none;">{{ $expenseRequest->created_at->format('d-m-Y') }}
                </td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="padding: 10px;width: 5%">No</th>
                    <th colspan="4" style="padding: 10px; text-align: center;">Kegiatan</th>
                    <th rowspan="2" style="padding: 10px; text-align: center;">Digunakan</th>
                    <th rowspan="2" style="padding: 10px; text-align: center;">Sisa</th>
                </tr>
                <tr>
                    <th style="padding: 10px; text-align: center;">Nama</th>
                    <th style="padding: 10px; text-align: center;">Kuantitas</th>
                    <th style="padding: 10px; text-align: center;">Harga Satuan</th>
                    <th style="padding: 10px; text-align: center;">Harga Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expenseRequest->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ formatRupiah($item->unit_price) }}</td>
                        <td>{{ formatRupiah($item->total_price) }}</td>
                        <td>{{ formatRupiah($item->actual_amount) }}</td>
                        <td>{{ formatRupiah($item->total_price - $item->actual_amount) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Tanda Tangan -->
        <div class="signature" style="display: flex; justify-content: space-between; margin-top: 50px;">
            <table width="100%" style="border-collapse: collapse">
                <tr>
                    <td style="text-align: center;border: none;">
                        <p>Finance</p>
                        <br><br><br><br>
                        <p>__________________</p>
                    </td>
                    <td style="text-align: center;border: none;">
                        <p>Manajer</p>
                        <br><br><br><br>
                        <p>__________________</p>
                    </td>
                    @if ($expenseRequest->total_amount > 150000)
                        <td style="text-align: center;border: none;">
                            <p>Direktur</p>
                            <br><br><br><br>
                            <p>__________________</p>
                        </td>
                    @endif
                </tr>
            </table>
        </div>
    </div>

</body>

</html>
