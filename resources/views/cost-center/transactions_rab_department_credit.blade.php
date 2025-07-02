@extends('layouts.admin')

@section('title')
    Cost Center General Report Credit
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
                        <li class="breadcrumb-item active"><strong>General Report Credit</strong></li>
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
                    <h4><b>General Report Credit</b></h4>
                </div>
            </div>
            <div class="text-sm mt-3">
                <div class="row">
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Total Debet</strong></p>
                                <h6>{{ formatRupiah(1000000) }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Total Kredit</strong></p>
                                <h6>{{ formatRupiah(1000000) }}</h6>
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
                                <p><strong>Total Pendapatan Tahun Berjalan</strong></p>
                                <h6>{{ formatRupiah(0) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr />

            <div class="text-sm mt-3">
                <div class="row">
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-info bg-primary">
                            <div class="card-body">
                                <p><strong>(BF) Belanja Follow Up</strong></p>
                                <h6>{{ formatRupiah(1000000) }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-info bg-primary">
                            <div class="card-body">
                                <p><strong>(BF) Belanja Follow Up</strong></p>
                                <h6>{{ formatRupiah(1000000) }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-info bg-primary">
                            <div class="card-body">
                                <p><strong>(BO) Belanja Overhead</strong></p>
                                <h6>{{ formatRupiah(1000000) }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-info bg-primary">
                            <div class="card-body">
                                <p><strong>(BG) Belanja Gaji</strong></p>
                                <h6>{{ formatRupiah(1000000) }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-info bg-primary">
                            <div class="card-body">
                                <p><strong>(BA) Belanja Aset</strong></p>
                                <h6>{{ formatRupiah(1000000) }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-info bg-primary">
                            <div class="card-body">
                                <p><strong>(BP) Belanja Project</strong></p>
                                <h6>{{ formatRupiah(1000000) }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-info bg-primary">
                            <div class="card-body">
                                <p><strong>(BH) Biaya Hutang</strong></p>
                                <h6>{{ formatRupiah(1000000) }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-info bg-primary">
                            <div class="card-body">
                                <p><strong>(PP) Pemasukan Piutang</strong></p>
                                <h6>{{ formatRupiah(1000000) }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3"></div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-info bg-primary">
                            <div class="card-body">
                                <p><strong>(PR) Pemeliharaan Rutin</strong></p>
                                <h6>{{ formatRupiah(1000000) }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-info bg-primary">
                            <div class="card-body">
                                <p><strong>(BR) Belanja Rembes</strong></p>
                                <h6>{{ formatRupiah(1000000) }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3"></div>
                </div>
            </div>

            {{-- Department --}}
            <div class="content mt-3 text-sm">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-outline rounded-partner card-primary p-3">
                                <h4><b>Realisasi General Report Credit</b></h4>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-sm btn-success rounded-partner px-4 float-right">
                                            <i class="fas fa-file-excel"></i> Export
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body table-responsive w-100 px-0">
                                    <table class="table table-bordered table-striped text-sm" id="tableRAB"
                                        style="width:100%">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No.</th>
                                                <th>Nama Item</th>
                                                <th>Kode Transaksi</th>
                                                <th>Divisi</th>
                                                <th>Pengaju</th>
                                                <th>Kredit</th>
                                                <th>Sisa Saldo</th>
                                                <th>Bukti Transaksi</th>
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
            let table = $('#tableRAB').DataTable({
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
            });
        })
    </script>
@endpush
