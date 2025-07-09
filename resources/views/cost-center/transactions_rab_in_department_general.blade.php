@extends('layouts.admin')

@section('title')
    Cost Center Cash Data of {{ $department->name }}
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/adminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/adminLTE/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/adminLTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/adminLTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Cost Center</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item">Finance</li>
                        <li class="breadcrumb-item">Cost Center</li>
                        <li class="breadcrumb-item active"><strong>Cash Data of {{ $department->name }}</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <h4><b>Laporan Keuangan Divisi {{ $department->name }} Tahun {{ date('Y') }}</b></h4>
                </div>
            </div>
            <div class="text-sm mt-3">
                <div class="row">
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Total Debet</strong></p>
                                <h6>{{ formatRupiah(0) }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Total Kredit</strong></p>
                                <h6>{{ formatRupiah(0) }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Sisa Saldo</strong></p>
                                <h6>{{ formatRupiah(0) }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Pendapatan Tahun Berjalan</strong></p>
                                <h6>{{ formatRupiah(0) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Department --}}
            <div class="content mt-3 text-sm">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-outline rounded-partner card-primary p-3">
                                {{-- <h4><b>Laporan Keuangan Divisi {{ $department->name }} Tahun {{ date('Y') }}</b></h4> --}}
                                <div class="col-12 float-right">
                                    <button type="button" class="btn btn-sm btn-success rounded-partner float-right">
                                        <i class="fas fa-file-excel"></i> Export
                                    </button>
                                </div>
                                <div class="card-body table-responsive w-100 px-0">
                                    <table class="table table-bordered table-striped text-sm" id="tableTransactions"
                                        style="width:100%">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No.</th>
                                                <th>Tanggal</th>
                                                <th>Nama Barang</th>
                                                <th>Kode Transaksi</th>
                                                <th>PIC</th>
                                                <th>Debet</th>
                                                <th>Kredit</th>
                                                <th>Kwitansi</th>
                                                <th>Keterangan</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/adminLTE/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/inputmask/jquery.inputmask.min.js') }}"></script>
    <script src="{{ asset('js/loading-overlay.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            let table = $('#tableTransactions').DataTable({
                scrollX: true,
                headerScroll: true,
                autoWidth: true,
                pageLength: 10,
                ordering: false,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, 'Semua']
                ],
                language: {
                    processing: '<i class="fa fa-spinner fa-spin"></i><span class="sr-only">Loading...</span>',
                    paginate: {
                        previous: '<i class="fas fa-angle-left"></i>',
                        next: '<i class="fas fa-angle-right"></i>'
                    },
                    emptyTable: 'Tidak ada data',
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 data',
                    infoFiltered: '(filtered from _MAX_ total records)',
                    search: 'Cari:',
                    lengthMenu: 'Tampilkan _MENU_ data',
                    zeroRecords: 'Data tidak ditemukan',
                },
            })
        })

        function parseCurrency(value) {
            return parseInt(value.replace(/[^0-9]/g, '')) || 0;
        }

        function showToast(icon, message) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        }
    </script>
@endpush
