@extends('layouts.admin')

@section('title')
    Cost Center
@endsection

@push('css')
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
                        <li class="breadcrumb-item active"><strong>Cost Center</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-8">
                    <h4><b>General Report PT. Partnership Procurement Solution {{ date('Y') }}</b></h4>
                </div>
                <div class="col-4">
                    <a href="{{ route('cost-center.create.rab-department') }}" class="btn btn-sm btn-primary rounded-partner float-right">Buat RAB Divisi</a>
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
                <div class="col-12 text-center my-3">
                    <a href="{{ route('cost-center.transactions.rab-department.credit') }}" class="btn btn-sm btn-primary rounded-partner">View Transaksi</a>
                </div>
            </div>

            <hr>

            {{-- Department --}}
            <div class="row">
                <div class="col-12 col-md-3">
                    <div class="card rounded-partner bg-primary">
                        <div class="card-body">
                            <p><strong>Procurement</strong></p>
                            <div class="row">
                                <div class="col-6">
                                    <small><strong>Total Debet</strong></small><br>
                                    <small>{{ formatRupiah(1000000) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Total Kredit</strong></small><br>
                                    <small>{{ formatRupiah(1000000) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Total Sisa Saldo</strong></small><br>
                                    <small>{{ formatRupiah(1000000) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Tahun Berjalan</strong></small><br>
                                    <small>{{ formatRupiah(1000000) }}</small>
                                </div>
                            </div>
                        </div>
                        <a class="text-white" href="#">
                            <div class="card-footer rounded-partner">
                                View Recap
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card rounded-partner bg-indigo">
                        <div class="card-body">
                            <p><strong>Construction</strong></p>
                            <div class="row">
                                <div class="col-6">
                                    <small><strong>Total Debet</strong></small><br>
                                    <small>{{ formatRupiah(1000000) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Total Kredit</strong></small><br>
                                    <small>{{ formatRupiah(1000000) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Total Sisa Saldo</strong></small><br>
                                    <small>{{ formatRupiah(1000000) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Tahun Berjalan</strong></small><br>
                                    <small>{{ formatRupiah(1000000) }}</small>
                                </div>
                            </div>
                        </div>
                        <a class="text-white" href="#">
                            <div class="card-footer rounded-partner">
                                View Recap
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card rounded-partner bg-orange">
                        <div class="card-body">
                            <p><strong>Technology</strong></p>
                            <div class="row">
                                <div class="col-6">
                                    <small><strong>Total Debet</strong></small><br>
                                    <small>{{ formatRupiah(1000000) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Total Kredit</strong></small><br>
                                    <small>{{ formatRupiah(1000000) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Total Sisa Saldo</strong></small><br>
                                    <small>{{ formatRupiah(1000000) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Tahun Berjalan</strong></small><br>
                                    <small>{{ formatRupiah(1000000) }}</small>
                                </div>
                            </div>
                        </div>
                        <a class="text-white" href="#">
                            <div class="card-footer rounded-partner">
                                View Recap
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="card rounded-partner bg-secondary">
                        <div class="card-body">
                            <p><strong>General Affair</strong></p>
                            <div class="row">
                                <div class="col-6">
                                    <small><strong>Total Debet</strong></small><br>
                                    <small>{{ formatRupiah(1000000) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Total Kredit</strong></small><br>
                                    <small>{{ formatRupiah(1000000) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Total Sisa Saldo</strong></small><br>
                                    <small>{{ formatRupiah(1000000) }}</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Tahun Berjalan</strong></small><br>
                                    <small>{{ formatRupiah(1000000) }}</small>
                                </div>
                            </div>
                        </div>
                        <a class="text-white" href="#">
                            <div class="card-footer rounded-partner">
                                View Recap
                            </div>
                        </a>
                    </div>
                </div>
                <hr>
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
@endpush
