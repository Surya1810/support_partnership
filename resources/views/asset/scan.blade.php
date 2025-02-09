@extends('layouts.admin')

@section('title')
    Scan
@endsection

@push('css')
    <!-- DataTables -->
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
                    <h1>Scan</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><strong>Scan</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-6">
                    <div class="card card-outline rounded-partner card-primary">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h3>Asset List</h3>
                                </div>
                                <div class="col-6">
                                    <div class="float-right">
                                        <p>Found : <span id="totalIsThereTrue">0</span>
                                        </p>
                                        <p>Missing : <span id="totalIsThereFalse">0</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="assetTable" class="table table-bordered text-nowrap text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th rowspan="2" class="align-middle">
                                            Status
                                        </th>
                                        <th rowspan="2" class="align-middle">
                                            RFID
                                        </th>
                                        <th rowspan="2" class="align-middle">
                                            Code
                                        </th>
                                        <th rowspan="2" class="align-middle">
                                            Name
                                        </th>
                                        <th rowspan="2" class="align-middle">
                                            Condition
                                        </th>
                                        <th rowspan="2" class="align-middle">
                                            PIC
                                        </th>
                                        <th colspan="3" class="align-middle">
                                            Location
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Building</th>
                                        <th>Floor</th>
                                        <th>Room</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data Asset masuk real-time -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card card-outline rounded-partner card-primary">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h3 class="card-title">Total :<span id="totalRFID">0</span>
                                    </h3>
                                </div>
                                <div class="col-6">
                                    <h3 class="float-right">Scanned Data</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="rfidTable" class="table table-bordered text-nowrap text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 70%">
                                            RFID
                                        </th>
                                        <th style="width: 30%">
                                            Timestamp
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data RFID masuk real-time -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('assets/adminLTE/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/adminLTE/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>

    <script src="{{ asset('build/assets/app.js') }}"></script>
@endpush
