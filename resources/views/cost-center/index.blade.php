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
                    <a href="{{ route('cost-center.create.rab-general') }}" class="btn btn-sm btn-primary rounded-partner float-right">Buat RAB Divisi</a>
                </div>
            </div>
            <div class="text-sm mt-3">
                <div class="row">
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Total Debet (Uang Kas Seluruh Divisi)</strong></p>
                                <h6>{{ $sums['debit'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Total Kredit (Limit Seluruh RAB Divisi)</strong></p>
                                <h6>{{ $sums['credit'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Sisa Saldo</strong></p>
                                <h6>{{ $sums['remaining'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Total Pendapatan Tahun Berjalan ({{ date('Y') }})</strong></p>
                                <h6>{{ $sums['yearly_margin'] }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-center my-3">
                    <a href="{{ route('cost-center.transactions.rab-general.credit') }}" class="btn btn-sm btn-primary rounded-partner">View Transaksi</a>
                </div>
            </div>

            <hr>

            {{-- Department --}}
            <div class="row">
                @php
                    $bgColors = ['bg-primary', 'bg-purple', 'bg-orange', 'bg-gray'];
                @endphp
                @foreach ($sums['departments'] as $index => $department)
                    <div class="col-12 col-md-3">
                        <div class="card rounded-partner {{ $bgColors[$index] }}">
                            <div class="card-body">
                                <p><strong>{{ $department['department_name'] }}</strong></p>
                                <div class="row">
                                    <div class="col-6">
                                        <small><strong>Total Debet</strong></small><br>
                                        <small>{{ $department['total_debit'] }}</small>
                                    </div>
                                    <div class="col-6">
                                        <small><strong>Total Kredit</strong></small><br>
                                        <small>{{ $department['total_credit'] }}</small>
                                    </div>
                                    <div class="col-6">
                                        <small><strong>Total Sisa Saldo</strong></small><br>
                                        <small>{{ $department['total_remaining'] }}</small>
                                    </div>
                                    <div class="col-6">
                                        <small><strong>Tahun Berjalan</strong></small><br>
                                        <small>{{ $department['yearly_margin'] }}</small>
                                    </div>
                                </div>
                            </div>
                            <a class="text-white" href="{{ route('cost-center.departments.index', $department['department_id']) }}">
                                <div class="card-footer rounded-partner">
                                    View Recap
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
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
