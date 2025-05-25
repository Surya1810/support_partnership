@extends('layouts.admin')

@section('title')
Cost Center
@endsection

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Saldo</h1>
                <ol class="breadcrumb text-black-50">
                    <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active"><strong>Saldo</strong></li>
                    <script src="https://cdn.tailwindcss.com"></script>
                </ol>
            </div>
        </div>
    </div>
</section>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <x-saldo-box label="Total Debit" value="{{ $totalDebit }}" color="blue" />
        <x-saldo-box label="Total Kredit" value="{{ $totalKredit }}" color="green" />
        <x-saldo-box label="Total Saldo" value="{{ $totalSaldo }}" color="yellow" />
        <x-saldo-box label="Total Pendapatan Tahun Berjalan" value="{{ $totalPendapatan }}" color="indigo" />
    </div>

    {{-- Filter & Export --}}
    <div class="flex justify-between mb-4">
        <select class="border px-2 py-1 rounded text-sm">
            <option value="">Filter by Kode Transaksi</option>
        </select>
        <button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Export</button>
    </div>

    {{-- Table --}}
    <div class="bg-white p-4 rounded shadow overflow-x-auto">
        <table id="saldo-table" class="min-w-full text-sm text-left">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">No.</th>
                    <th class="px-4 py-2">Tanggal Dibuat</th>
                    <th class="px-4 py-2">Nama Barang</th>
                    <th class="px-4 py-2">Debit</th>
                    <th class="px-4 py-2">Kredit</th>
                    <th class="px-4 py-2">Kwitansi</th>
                    <th class="px-4 py-2">PIC</th>
                    <th class="px-4 py-2">Kode Ref</th>
                    <th class="px-4 py-2">Keterangan</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#saldo-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('cost-center.saldo') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'created_at', name: 'created_at' },
                { data: 'name', name: 'name' },
                { data: 'debit', name: 'debit' },
                { data: 'kredit', name: 'kredit' },
                { data: 'kwitansi', name: 'kwitansi' },
                { data: 'pic', name: 'pic' },
                { data: 'kode_ref', name: 'kode_ref' },
                { data: 'note', name: 'note' },
            ]
        });
    });
</script>
@endpush