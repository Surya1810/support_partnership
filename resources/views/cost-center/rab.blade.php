@extends('layouts.admin')

@section('title')
Cost Center
@endsection

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Cost Center</h1>
                <ol class="breadcrumb text-black-50">
                    <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active"><strong>Cost Center</strong></li>
                    <script src="https://cdn.tailwindcss.com"></script>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container mx-auto px-6">

        {{-- Total Debit --}}
        <div class="bg-blue-100 border border-blue-300 rounded-xl p-6 text-center max-w-md mx-auto mb-6 shadow">
            <p class="text-sm text-blue-700 font-medium">Total Debit</p>
            <h2 class="text-3xl font-bold text-blue-900 mt-2">Rp300.000.000</h2>
        </div>

        {{-- Filter & Tambah --}}
        <div class="flex items-center justify-between mb-4">
            <div class="flex space-x-3">
                <select class="border border-gray-300 rounded px-3 py-2 text-sm">
                    <option>Filter by Status</option>
                    <!-- Tambah pilihan sesuai kebutuhan -->
                </select>
                <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 text-sm">+</button>
            </div>
            <button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 text-sm">Export</button>
        </div>

        {{-- Tabel --}}
        <div class="bg-white rounded-xl shadow overflow-x-auto">
            <table id="rab-table" class="min-w-full text-sm text-center">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-3">No.</th>
                        <th class="px-4 py-3">Tanggal Dibuat</th>
                        <th class="px-4 py-3">Nama Kegiatan</th>
                        <th class="px-4 py-3">Kode Ref</th>
                        <th class="px-4 py-3">Kebutuhan</th>
                        <th class="px-4 py-3">Realisasi Kegiatan</th>
                        <th class="px-4 py-3">PIC</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3">History</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- DataTables akan isi otomatis --}}
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    $(function () {
            $('#rab-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('cost-center.rab') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'name', name: 'name' },
                    { data: 'cost_center_category_code', name: 'cost_center_category_code' },
                    { data: 'cost_center_category_ref', name: 'cost_center_category_ref' },
                    { data: 'amount', name: 'amount' },
                    { data: 'pic', name: 'pic' },
                    { data: 'note', name: 'note' },
                    { data: 'history', name: 'history' },
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
                ]
            });
        });
</script>
@endpush