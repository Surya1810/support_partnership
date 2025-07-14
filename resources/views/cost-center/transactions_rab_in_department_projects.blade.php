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
                                <p><strong>Total Debet (Modal Project)</strong></p>
                                <h6>{{ $totalAmount['total_debit'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Total Kredit (Pengajuan Terealisasi)</strong></p>
                                <h6>{{ $totalAmount['total_actual_amount'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Sisa (Modal - Pengajuan Terealisasi)</strong></p>
                                <h6>{{ $totalAmount['total_remaining'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Pendapatan Tahun Berjalan</strong></p>
                                <h6>{{ $totalAmount['total_yearly_margin'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Total Limit (Seluruh RAB)</strong></p>
                                <h6>{{ $totalAmount['total_credit'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary bg-info">
                            <div class="card-body">
                                <p><strong>Total Perusahaan</strong></p>
                                <h6>{{ $totalAmount['total_company'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary bg-info">
                            <div class="card-body">
                                <p><strong>Total Penyusutan</strong></p>
                                <h6>{{ $totalAmount['total_depreciation'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary bg-info">
                            <div class="card-body">
                                <p><strong>Total Bonus Tim</strong></p>
                                <h6>{{ $totalAmount['total_team_bonus'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary bg-info">
                            <div class="card-body">
                                <p><strong>Total Kas Divisi</strong></p>
                                <h6>{{ $totalAmount['total_cash_department'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary bg-info">
                            <div class="card-body">
                                <p><strong>Total Kas Divisi</strong></p>
                                <h6>{{ $totalAmount['total_cash_department'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-dark bg-secondary">
                            <div class="card-body">
                                <p><strong>Total PPN</strong></p>
                                <h6>{{ $totalAmount['total_vat'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-dark bg-secondary">
                            <div class="card-body">
                                <p><strong>Total PPH</strong></p>
                                <h6>{{ $totalAmount['total_tax'] }}</h6>
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
                                <h4><b>Realisasi Project Tahun {{ date('Y') }}</b></h4>
                                <div class="row my-3">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-sm btn-success rounded-partner float-right"
                                            id="buttonExport">
                                            <i class="fas fa-file-excel"></i> Export
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="card-body w-100 px-0">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped text-sm" id="tableProject"
                                                style="width:100%">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th></th> {{-- untuk expand detail profit dari project --}}
                                                        <th>No.</th>
                                                        <th>Nama Project</th>
                                                        <th>Nilai Project</th>
                                                        <th>Modal</th>
                                                        <th>Keuntungan</th>
                                                        <th>SP2D</th>
                                                        <th>PPN (11%/12%)</th>
                                                        <th>PPH (1.5%/2%)</th>
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
            let table = $('#tableProject').DataTable({
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
                    url: "{{ route('cost-center.departments.projects', ':id') }}".replace(':id', $(
                        '#department_id').val()),
                    method: 'GET'
                },
                columns: [{
                        className: 'details-control text-center',
                        orderable: false,
                        searchable: false,
                        data: null,
                        defaultContent: '<button class="badge bg-info border-0" title="Detail Profit"><i class="fas fa-dollar-sign"></i></button>',
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
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
                        data: 'pic',
                        name: 'pic'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    }
                ]
            });

            $('#tableProject tbody').on('click', 'td.details-control button', function() {
                let tr = $(this).closest('tr');
                let row = table.row(tr);
                let projectId = row.data().id;

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    $(this).html('<i class="fas fa-dollar-sign"></i>');
                } else {
                    let tableId = `tableProjectProfit-${projectId}`;
                    let html = `
                        <table id="${tableId}" class="table table-sm table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Item</th>
                                    <th>Nilai (%)</th>
                                    <th>Nilai (Rp)</th>
                                </tr>
                            </thead>
                        </table>
                    `;
                    row.child(html).show();
                    tr.addClass('shown');
                    $(this).text('-');

                    $(`#${tableId}`).DataTable({
                        processing: true,
                        serverSide: true,
                        ordering: false,
                        searching: false,
                        paging: false,
                        info: false,
                        ajax: "{{ route('cost-center.departments.projects.profit', ':id') }}".replace(':id', projectId),
                        columns: [
                            {
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                className: 'text-center'
                            },
                            {
                                data: 'name',
                                name: 'name'
                            },
                            {
                                data: 'percent',
                                name: 'percent'
                            },
                            {
                                data: 'idr',
                                name: 'idr'
                            }
                        ]
                    });
                }
            });
        });
    </script>
@endpush
