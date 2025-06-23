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
            <div class="row gap-2 mb-3">
                <div id="filterWrapper" class="col-4 col-md-2">
                    <select class="form-control" id="statusFilter">
                        <option value="all" disabled selected>Pilih Status</option>
                        <option value="all">Semua</option>
                        <option value="planning">Planning</option>
                        <option value="in_progress">In Progress</option>
                        <option value="overdue">Overdue</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                @include('jobs.buttons')
            </div>
            <div class="table-responsive w-100">
                <table class="table table-bordered table-striped" id="jobTable" style="width:100%">
                    <thead class="thead-dark">
                        <tr>
                            <th rowspan="2" style="vertical-align: middle">No.</th>
                            <th colspan="2" style="text-align: center">Penugasan</th>
                            <th rowspan="2" style="vertical-align: middle">Divisi</th>
                            <th rowspan="2" style="vertical-align: middle">Detail Pekerjaan</th>
                            <th colspan="2" style="text-align: center">Tanggal</th>
                            <th rowspan="2" style="vertical-align: middle">Sisa Waktu<br/>/Hari</th>
                            <th rowspan="2" style="vertical-align: middle">Report<br/>Pekerjaan</th>
                            <th rowspan="2" style="vertical-align: middle">Adendum<br/>/Catatan</th>
                            <th rowspan="2" style="vertical-align: middle">Point</th>
                            <th rowspan="2" style="vertical-align: middle">Status</th>
                            <th rowspan="2" style="vertical-align: middle">Revisi</th>
                            <th rowspan="2" style="vertical-align: middle">Aksi</th>
                        </tr>
                        <tr>
                            <th>Pemberi</th>
                            <th>Penerima</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                        </tr>
                    </thead>
                </table>
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

    <script type="text/javascript">
        $(document).ready(function() {
            let table = $('#jobTable').DataTable({
                scrollX: true,
                autoWidth: true,
                pageLength: 10,
                order: [
                    [5, 'asc']
                ],
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
                    },
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
                        orderable: false
                    },
                    {
                        data: 'assignee',
                        name: 'assignee',
                        class: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'department',
                        name: 'department',
                        class: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'title',
                        name: 'title',
                        class: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'start_date',
                        name: 'start_date',
                        class: 'text-center'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date',
                        class: 'text-center'
                    },
                    {
                        data: 'time_remaining',
                        name: 'time_remaining',
                        class: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'feedback',
                        name: 'feedback',
                        class: 'text-center',
                        orderable: false
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
                        orderable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        class: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'feedback',
                        name: 'feedback',
                        class: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        class: 'text-center'
                    },
                ],
                drawCallback: function() {
                    $('[data-toggle="tooltip"]').tooltip({
                        container: 'body',
                        trigger: 'hover'
                    });
                }
            });

            $("#statusFilter").on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endpush
