@extends('layouts.admin')

@section('title')
    Tugas Saya
@endsection

@push('css')
    <style>
        #jobTable_filter,
        #jobTable_paginate {
            float: right !important;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .text-ellipsis {
            display: inline-block;
            max-width: 250px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            vertical-align: bottom;
            cursor: pointer;
        }
    </style>
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
                    <h1>Penugasan</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active"><strong>Tugas Saya</strong></li>
                    </ol>
                    <a href="{{ route('jobs.index') }}" class="btn btn-sm btn-outline-secondary mt-3" type="button"
                        id="buttonJobsPage">
                        Penugasan
                    </a>
                    <a href="{{ route('jobs.my_tasks') }}" class="btn btn-sm btn-outline-primary active mt-3" type="button"
                        id="buttonMyTasksPage">
                        Tugas Saya
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="content">
        <div class="container-fluid">
            <div class="col-12">
                <div class="card card-outline rounded-partner card-primary">
                    <div class="card-body table-responsive w-100">
                        <div class="row mb-3 align-items-end">
                            <div class="col-md-2">
                                <select class="form-control" id="statusFilter">
                                    <option value="all" disabled selected>Pilih Status</option>
                                    <option value="all">Semua</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="overdue">Overdue</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="checking">Checking</option>
                                    <option value="completed">Completed</option>
                                    <option value="revision">Revision</option>
                                </select>
                            </div>

                            <div class="col-md-10 text-right">
                                <form method="GET" action="{{ route('jobs.export.my_tasks') }}"
                                    class="form-inline justify-content-end">
                                    <input type="date" name="start_date" class="form-control mr-2" required>
                                    <input type="date" name="end_date" class="form-control mr-2" required>
                                    <button type="submit" class="btn btn-success rounded-partner">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive w-100">
                            <div class="row mb-4">
                                {{-- Total Point Wrapper --}}
                                <div class="col-2">
                                    <div id="timeWrapper">
                                        <input type="text" id="time" class="form-control"
                                            value="Tue, 10 Jan 2022 00:00:00" disabled>
                                    </div>
                                </div>
                                <div id="totalEfficiencyWrapper" class="col-6">
                                    <form>
                                        <div class="form-group row">
                                            <label for="totalEfficiencyOutput" class="col-form-label ml-3">
                                                Total Point
                                            </label>
                                            <div class="col-md-1">
                                                <input type="text" class="form-control" id="totalEfficiencyOutput"
                                                    disabled>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <table class="table table-bordered table-striped" id="jobTable" style="width:100%">
                                <thead class="thead-dark">
                                    <tr>
                                        <th rowspan="2" style="vertical-align: middle">No.</th>
                                        <th colspan="2" style="text-align: center">Penugasan</th>
                                        <th rowspan="2" style="vertical-align: middle">Divisi</th>
                                        <th rowspan="2" style="vertical-align: middle">Detail Pekerjaan</th>
                                        <th colspan="3" style="text-align: center">Tanggal</th>
                                        <th rowspan="2" style="vertical-align: middle">Sisa Waktu<br />/Hari</th>
                                        <th rowspan="2" style="vertical-align: middle">Report<br />Pekerjaan</th>
                                        <th rowspan="2" style="vertical-align: middle">Adendum<br />/Catatan</th>
                                        <th rowspan="2" style="vertical-align: middle">Point</th>
                                        <th rowspan="2" style="vertical-align: middle">Status</th>
                                        <th rowspan="2" style="vertical-align: middle">Revisi</th>
                                        <th rowspan="2" style="vertical-align: middle">Aksi</th>
                                    </tr>
                                    <tr>
                                        <th>Pemberi</th>
                                        <th>Penerima</th>
                                        <th>Mulai</th>
                                        <th>Akhir</th>
                                        <th>Selesai</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal Upload File --}}
    <div class="modal fade" id="modalUploadFile" tabindex="-1" role="dialog" aria-labelledby="modalLabelUploadFile"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <form id="formUploadFile" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload File Laporan Pekerjaan</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" name="job_id">
                            <label for="reportFile">Upload File Laporan Pekerjaan</label>
                            <input type="file" class="form-control-file" id="reportFile" name="report_file">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary rounded-partner">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
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

    <script type="text/javascript">
        $(document).ready(function() {
            let table = $('#jobTable').DataTable({
                scrollX: true,
                autoWidth: true,
                pageLength: 10,
                ordering: false,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                processing: true,
                serverSide: true,
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
                    zeroRecords: 'Data tidak ditemukan'
                },
                ajax: {
                    url: "{{ route('jobs.my_tasks') }}",
                    type: "GET",
                    data: function(data) {
                        data.status = $('#statusFilter').find(':selected').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        class: 'text-center'
                    },
                    {
                        data: 'assigner',
                        name: 'assigner',
                        class: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'assignee',
                        name: 'assignee',
                        class: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'department',
                        name: 'department',
                        class: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'detail',
                        name: 'detail',
                        class: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'start_date',
                        name: 'start_date',
                        class: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'end_date',
                        name: 'end_date',
                        class: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'completed_at',
                        name: 'completed_at',
                        class: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'time_remaining',
                        name: 'time_remaining',
                        class: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'report_file',
                        name: 'report_file',
                        class: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'feedback',
                        name: 'feedback',
                        class: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'completion_efficiency',
                        name: 'completion_efficiency',
                        class: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'status',
                        name: 'status',
                        class: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'revisions',
                        name: 'revisions',
                        class: 'text-center',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        class: 'text-center'
                    },
                ]
            });

            $("#statusFilter").on('change', function() {
                table.ajax.reload();
            });

            /**
             * 24 June 2025
             **/
            $('#jobTable').on('xhr.dt', function(e, settings, json, xhr) {
                if (json.total_efficiency != null) {
                    $('#totalEfficiencyOutput').val(json.total_efficiency + '%');
                } else {
                    $('#totalEfficiencyOutput').val('0%');
                }
            });

            updateTime(); // sekali saat awal
            setInterval(updateTime, 1000); // update tiap 1 detik
        });

        function modalUploadReportFile(id) {
            $('#formUploadFile').attr('action', "{{ route('jobs.upload_report', ':id') }}".replace(':id', id));
            $('#modalUploadFile').modal('show');
        }

        function updateTime() {
            const now = new Date();

            const options = {
                weekday: 'long',
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
                timeZone: 'Asia/Jakarta' // waktu WIB
            };

            const formatted = now.toLocaleString('id-ID', options).replace(',', '');
            const parts = formatted.split(' ');
            const final = `${parts[0]}, ${parts.slice(1).join(' ')}`;

            $('#time').val(final);
        }
    </script>
@endpush
