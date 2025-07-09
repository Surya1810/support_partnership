@extends('layouts.admin')

@section('title')
    Cost Center Project Realizations {{ $department->name . ' ' . date('Y') }}
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
                <div class="col-sm-12">
                    <h1>Cost Center</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item">Finance</li>
                        <li class="breadcrumb-item">Cost Center</li>
                        <li class="breadcrumb-item active"><strong>Project Realizations {{ date('Y') }}</strong></li>
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
                    <h4><b>Project Realizations {{ $department->name . ' ' . date('Y') }}</b></h4>
                    <input type="hidden" id="department_id" value="{{ $department->id }}">
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
                                <h4><b>Project Realizations</b></h4>
                                <div class="card-body table-responsive w-100 px-0">
                                    <table class="table table-bordered table-striped text-sm" id="tableRAB"
                                        style="width:100%">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No.</th>
                                                <th>Nama Project</th>
                                                <th>Nilai Project</th>
                                                <th>Modal</th>
                                                <th>Keuntungan</th>
                                                <th>SP2D</th>
                                                <th>PPN (11%/12%)</th>
                                                <th>PPH (1.5%/2%)</th>
                                                <th>Team Bonus</th>
                                                <th>Penyusutan</th>
                                                <th>Kas</th>
                                                <th>PIC</th>
                                                <th>Status</th>
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
            $('#month, #year, #category, #department').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#modalAddRAB')
            });

            $('#targetEdit, #departmentEdit, #monthEdit, #yearEdit, #categoryEdit').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#modalEditRAB')
            });

            $('.price').inputmask({
                alias: 'numeric',
                prefix: 'Rp',
                digits: 0,
                groupSeparator: '.',
                autoGroup: true,
                removeMaskOnSubmit: true,
                rightAlign: false
            });

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
                ajax: {
                    url: "{{route('cost-center.departments.projects', ':id')}}".replace(':id', $('#department_id').val()),
                    method: 'GET'
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'job_value',
                        name: 'job_value'
                    },
                    {
                        data: 'rab',
                        name: 'rab'
                    },
                    {
                        data: 'margin',
                        name: 'margin'
                    },
                    {
                        data: 'sp2d',
                        name: 'sp2d'
                    },
                    {
                        data: 'ppn',
                        name: 'ppn'
                    },
                    {
                        data: 'pph',
                        name: 'pph'
                    },
                    {
                        data: 'team_bonus',
                        name: 'team_bonus'
                    },
                    {
                        data: 'depreciation',
                        name: 'depreciation'
                    },
                    {
                        data: 'cash_department',
                        name: 'cash_department'
                    },
                    {
                        data: 'pic',
                        name: 'pic'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    }
                ]
            });
        });
    </script>
@endpush
