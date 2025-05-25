@extends('layouts.admin')

@section('title', 'Cost Center')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css" />
@endpush

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="text-2xl font-semibold text-gray-800">Cost Center</h1>
                <ol class="breadcrumb text-gray-500">
                    <li class="breadcrumb-item"><a class="text-blue-600" href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active"><strong>Cost Center</strong></li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container mx-auto px-4">
        {{-- Ringkasan Saldo --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-blue-100 p-4 rounded shadow text-center">
                <p class="text-sm font-medium text-blue-700">Total Debit</p>
                <p class="text-xl font-bold text-blue-900">Rp{{ number_format($totalDebit, 0, ',', '.') }}</p>
            </div>
            <div class="bg-green-100 p-4 rounded shadow text-center">
                <p class="text-sm font-medium text-green-700">Total Kredit</p>
                <p class="text-xl font-bold text-green-900">Rp{{ number_format($totalKredit, 0, ',', '.') }}</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded shadow text-center">
                <p class="text-sm font-medium text-yellow-700">Sisa Saldo</p>
                <p class="text-xl font-bold text-yellow-900">Rp{{ number_format($totalSaldo, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Tabel Project --}}
        <div class="bg-white p-6 rounded shadow overflow-x-auto">
            <table id="project-table" class="min-w-full text-sm text-left">
                <thead class="bg-white-100 font-semibold text-dark-700">
                    <tr>
                        <th class="px-4 py-2">No</th>
                        <th class="px-4 py-2">Tanggal Dibuat</th>
                        <th class="px-4 py-2">Department</th>
                        <th class="px-4 py-2">PIC</th>
                        <th class="px-4 py-2">Nama Project</th>
                        <th class="px-4 py-2">Banyak RAB</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Keterangan</th>
                        <th class="px-4 py-2">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    {{-- Data akan diisi lewat AJAX DataTables --}}
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

<script>
    $(document).ready(function() {
        $('#project-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('cost-center.project') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'department', name: 'department.name' },
                { data: 'pic', name: 'user.name' },
                { data: 'name', name: 'name' },
                { data: 'rab_count', name: 'rab_count', orderable: false, searchable: false },
                { data: 'status', name: 'status' },
                { data: 'creative_brief', name: 'creative_brief' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false },
            ],
        });
    });
</script>
@endpush