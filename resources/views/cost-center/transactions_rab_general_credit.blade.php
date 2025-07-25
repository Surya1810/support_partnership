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
                        <li class="breadcrumb-item active"><strong>General Report Credit {{ date('Y') }}</strong></li>
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
                    <h4><b>General Report Credit {{ date('Y') }}</b></h4>
                </div>
            </div>
            <div class="text-sm mt-3">
                <div class="row">
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Total Debet (Semua Divisi)</strong></p>
                                <h6>{{ $sums['debit'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Total Kredit (Pengajuan Terealisasi)</strong></p>
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
                                <p><strong>Total Pendapatan Tahun Berjalan</strong></p>
                                <h6>{{ $sums['yearly_margin'] }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr />

            <div class="text-sm mt-3">
                <div class="row">
                    @foreach ($sums['categories'] as $category)
                        <div class="col-12 col-md-3">
                            <div class="card card-outline rounded-partner card-info bg-primary">
                                <div class="card-body">
                                    <p><strong>{{ $category['name'] }}</strong></p>
                                    @if ($category['code'] == 'KS')
                                        <h6>{{ $category['total_debit'] }}</h6>
                                    @else
                                        <h6>{{ $category['total_credit'] }}</h6>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Department --}}
            <div class="content mt-3 text-sm">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-outline rounded-partner card-primary p-3">
                                <h4><b>Realisasi General Report Credit {{ date('Y') }}</b></h4>
                                <div class="row mt-3">
                                    <div class="row col-6">
                                        <div class="col-md-2 mt-2 mt-md-0">
                                            <select class="form-control" id="fromYear">
                                                <option value="" disabled selected>Pilih Tahun</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 mt-2 mt-md-0">
                                            <select class="form-control" id="toYear">
                                                <option value="" disabled selected>Pilih Tahun</option>
                                                @foreach ($years as $year)
                                                    <option value="{{ $year }}"
                                                        {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if (auth()->user()->role_id != 3)
                                            <div class="col-md-4 mt-2 mt-md-0">
                                                <select class="form-control" id="departmentFilter">
                                                    <option value="">Pilih Semua</option>
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department->id }}">{{ $department->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-6">
                                        <button type="button" id="buttonExport"
                                            class="btn btn-sm btn-success rounded-partner px-4 float-right">
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
                                                <th>Nama Pengajuan</th>
                                                <th>Kode Transaksi</th>
                                                <th>Divisi</th>
                                                <th>Pengaju</th>
                                                <th>Diajukan</th>
                                                <th>Dikembalikan</th>
                                                <th>Bukti</th>
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
                ajax: {
                    url: "{{ route('cost-center.transactions.rab-general.credit') }}",
                    type: "GET",
                    data: function(data) {
                        data.fromYear = $('#fromYear').find(':selected').val();
                        data.toYear = $('#toYear').find(':selected').val();
                        data.departmentFilter = $('#departmentFilter').find(':selected').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'code_ref_request',
                        name: 'code_ref_request'
                    },
                    {
                        data: 'department',
                        name: 'department'
                    },
                    {
                        data: 'user',
                        name: 'user'
                    },
                    {
                        data: 'credit',
                        name: 'credit'
                    },
                    {
                        data: 'remaining',
                        name: 'remaining'
                    },
                    {
                        data: 'report_file',
                        name: 'report_file'
                    }
                ]
            });

            $('#fromYear, #toYear, #departmentFilter').on('change', function() {
                table.ajax.reload();
            });

            $('#buttonExport').on('click', function(e) {
                const url = '{{ route('cost-center.export.general-credit.realizations') }}';
                const fromYear = $('#fromYear').find(':selected').val();
                const toYear = $('#toYear').find(':selected').val();
                const departmentFilter = $('#departmentFilter').find(':selected').val();

                window.open(url + '?fromYear=' + fromYear + '&toYear=' + toYear + '&departmentFilter=' +
                    departmentFilter, '_blank');
            });
        });
    </script>
@endpush
