@extends('layouts.admin')

@section('title')
    Tugas Saya
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
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline rounded-partner card-primary">
                        <div class="card-body table-responsive w-100">
                            <div class="row mb-3 align-items-end">
                                <div class="col-12 col-md-2 mb-3 mb-md-0">
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

                                <div class="col-12 col-md-10 text-right">
                                    <form method="GET" action="{{ route('jobs.export.my_tasks') }}"
                                        class="form-inline justify-content-end">
                                        <input type="date" name="start_date" class="form-control mr-0 mr-md-2"
                                            placeholder="Tanggal Mulai" required>
                                        <input type="date" name="end_date" class="form-control mr-0 mr-md-2 mt-2 mt-md-0"
                                            placeholder="Tanggal Akhir" required>
                                        <button type="submit"
                                            class="btn btn-success rounded-partner col-12 col-md-2 mt-2 mt-md-0">
                                            <i class="fas fa-file-excel"></i> Export
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="w-100">
                                <div class="row mb-4">
                                    {{-- Total Point Wrapper --}}
                                    <div class="col-12 col-md-2 mt-3 mt-md-0">
                                        <div id="timeWrapper">
                                            <input type="text" id="time" class="form-control"
                                                value="Tue, 10 Jan 2022 00:00:00" disabled>
                                        </div>
                                    </div>
                                    <div id="totalEfficiencyWrapper" class="col-12 col-md-6 mt-3 mt-md-0">
                                        <form>
                                            <div class="form-group row">
                                                <label for="totalEfficiencyOutput" class="col-form-label ml-3">
                                                    Total Point
                                                </label>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" id="totalEfficiencyOutput"
                                                        disabled>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                @if ($isMobile)
                                    <table class="table table-bordered table-striped text-sm" id="jobTable"
                                        style="width:100%">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th style="vertical-align: middle">No.</th>
                                                <th style="vertical-align: middle">Aksi</th>
                                                <th style="vertical-align: middle">Pemberi</th>
                                                <th style="vertical-align: middle">Detail Pekerjaan</th>
                                                <th style="vertical-align: middle">Sisa Waktu<br />/Hari
                                                </th>
                                                <th style="vertical-align: middle">Divisi</th>
                                                <th style="vertical-align: middle">Point</th>
                                            </tr>
                                        </thead>
                                    </table>
                                @else
                                    <table class="table table-bordered table-striped text-sm" id="jobTable"
                                        style="width:100%">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th rowspan="2" style="vertical-align: middle">No.</th>
                                                <th rowspan="2" style="vertical-align: middle">Jangkrik?</th>
                                                <th colspan="2" style="text-align: center">Penugasan</th>
                                                <th rowspan="2" style="vertical-align: middle">Divisi</th>
                                                <th rowspan="2" style="vertical-align: middle">Detail Pekerjaan</th>
                                                <th colspan="3" style="text-align: center" id="dateHeaderColumn">
                                                    Tanggal
                                                </th>
                                                <th rowspan="2" style="vertical-align: middle">Sisa Waktu<br />/Hari
                                                </th>
                                                <th rowspan="2" style="vertical-align: middle">Report<br />Pekerjaan
                                                </th>
                                                <th rowspan="2" style="vertical-align: middle">Adendum<br />/Catatan
                                                </th>
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
                                @endif
                            </div>
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

    {{-- Modal Detail Job --}}
    <div class="modal fade text-sm" id="showJobModal" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="formDetailJob">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Penugasan</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="job_id">

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Pemberi</label>
                                    <input type="tecxt" id="job_assigner" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Penerima</label>
                                    <input type="text" id="job_assignee" class="form-control" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Divisi</label>
                            <input type="text" id="department" class="form-control muted" readonly>
                        </div>

                        <div class="form-group">
                            <label>Detail Pekerjaan</label>
                            <textarea id="job_detail" class="form-control" required disabled></textarea>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <input type="text" id="job_status" class="form-control muted" readonly>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Tanggal Mulai</label>
                                    <input type="date" id="start_date" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Tanggal Akhir</label>
                                    <input type="date" id="end_date" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Tanggal Selesai</label>
                                    <input type="date" id="completed_date" class="form-control" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="feedback">Adendum/Catatan</label>
                            <textarea id="feedback" class="form-control" disabled></textarea>
                        </div>

                        <div class="form-group">
                            <label for="notes">Revisi</label>
                            <textarea type="text" id="revision" class="form-control" disabled></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary rounded-partner"
                            data-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
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
    <script src="{{ asset('js/loading-overlay.js') }}"></script>

    <script type="text/javascript">
        let isMobile = @json($isMobile);
        let columns = null;

        if (isMobile) {
            columns = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    class: 'text-center',
                },
                {
                    data: 'actions',
                    name: 'actions',
                    class: 'text-center',
                    orderable: false,
                    searhable: false,
                },
                {
                    data: 'assigner',
                    name: 'assigner',
                    class: 'text-start',
                    orderable: false,
                },
                {
                    data: 'job_detail',
                    name: 'job_detail',
                    class: 'text-start',
                    orderable: false
                },
                {
                    data: 'time_remaining',
                    name: 'time_remaining',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'department',
                    name: 'department',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'completion_efficiency',
                    name: 'completion_efficiency',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                }
            ];
        } else {
            columns = [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false,
                    class: 'text-center',
                },
                {
                    data: 'priority',
                    name: 'priority',
                    class: 'text-center',
                    orderable: false,
                },
                {
                    data: 'assigner',
                    name: 'assigner',
                    class: 'text-center',
                    orderable: false,
                },
                {
                    data: 'assignee',
                    name: 'assignee',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'department',
                    name: 'department',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'job_detail',
                    name: 'job_detail',
                    class: 'text-center',
                    orderable: false
                },
                {
                    data: 'start_date',
                    name: 'start_date',
                    class: 'text-center',
                    orderable: false,
                    searchable: false,
                },
                {
                    data: 'end_date',
                    name: 'end_date',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'completed_at',
                    name: 'completed_at',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'time_remaining',
                    name: 'time_remaining',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'report_file',
                    name: 'report_file',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'feedback',
                    name: 'feedback',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'completion_efficiency',
                    name: 'completion_efficiency',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'status',
                    name: 'status',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'revisions',
                    name: 'revisions',
                    class: 'text-center',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    class: 'text-center'
                },
            ];
        };

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
                columns: columns
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

            /**
             * 25 June 2025
             **/
            $('#formUploadFile').on('submit', function(e) {
                $.LoadingOverlay("show");
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

        function modalDetailJob(jobId) {
            $.LoadingOverlay("show");
            $.get(`/jobs/${jobId}`, function(data) {
                $('#job_assigner').val(data.assigner.name);
                $('#job_assignee').val(data.assignee.name);
                $('#department').val(data.department.name);
                $('#job_status').val(data.status);
                $('#job_detail').val(data.job_detail);
                $('#start_date').val(data.start_date);
                $('#end_date').val(data.end_date);

                const feedback = data.feedback;
                const splittedFeedback = feedback.split('\n');

                data.completed_at ?
                    $('#completed_date').attr('type', 'date').val(data.completed_at) :
                    $('#completed_date').attr('type', 'text');

                $('#feedback').val(splittedFeedback[2]);
                $('#revision').val(data.notes);

                $.LoadingOverlay("hide");
                $('#showJobModal').modal('show');
            });
        }
    </script>
@endpush
