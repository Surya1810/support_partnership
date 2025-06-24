@extends('layouts.admin')

@section('title')
    Penugasan
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
            max-width: 150px;
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
                        <li class="breadcrumb-item active"><strong>Penugasan</strong></li>
                    </ol>
                    <a href="{{ route('jobs.index') }}" class="btn btn-sm btn-outline-secondary active mt-3" type="button"
                        id="buttonJobsPage">
                        Penugasan
                    </a>
                    <a href="{{ route('jobs.my_tasks') }}" class="btn btn-sm btn-outline-primary mt-3" type="button"
                        id="buttonMyTasksPage">
                        Tugas Saya
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
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

                                <div class="col-md-3">
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-primary rounded-partner" type="button"
                                            id="buttonAddJobModal">
                                            <i class="fas fa-plus"></i> Tambah
                                        </button>
                                        <button class="btn btn-warning rounded-partner" type="button"
                                            id="buttonOpenModalImportJobs">
                                            <i class="fas fa-upload"></i> Import
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-7 text-right">
                                    <form method="GET" action="{{ route('jobs.export') }}"
                                        class="form-inline justify-content-end">
                                        <input type="date" name="start_date" class="form-control mr-2" required>
                                        <input type="date" name="end_date" class="form-control mr-2" required>
                                        <button type="submit" class="btn btn-success rounded-partner">
                                            <i class="fas fa-file-excel"></i> Export Excel
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Total Point Wrapper --}}
                            <div class="row mb-4">
                                <div class="col-2">
                                    <div id="timeWrapper">
                                        <input type="text" id="time" class="form-control" value="Tue, 10 Jan 2022"
                                            disabled>
                                    </div>
                                </div>
                                <div id="totalEfficiencyWrapper" class="col-6" style="display: none">
                                    <form>
                                        <div class="form-group row">
                                            <label for="totalEfficiencyOutput" class="col-form-label">
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

                            <table class="table table-bordered table-striped text-sm" id="jobTable" style="width:100%">
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

    {{-- Modal Tambah Penugasan --}}
    <div class="modal fade" id="modalAddJob" tabindex="-1" role="dialog" aria-labelledby="modalLabelJob"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <form id="formAddJob">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Penugasan</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="assignee_id">Penerima Tugas</label>
                            <select name="assignee_id" class="form-control select2" required>
                                <option value="">Pilih Penerima</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="detail">Detail Pekerjaan</label>
                            <textarea name="detail" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="start_date">Tanggal Mulai</label>
                                    <input type="date" name="start_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="end_date">Tanggal Selesai</label>
                                    <input type="date" name="end_date" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary rounded-partner">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Penugasan --}}
    <div class="modal fade" id="editJobModal" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="formEditJob">
                    <div class="modal-header">
                        <h5 class="modal-title">Ubah Pekerjaan</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_job_id">

                        <div class="form-group">
                            <label>Detail Pekerjaan</label>
                            <textarea id="edit_detail" class="form-control" required></textarea>
                        </div>

                        <div class="form-group mt-3">
                            <label>Status Penugasan</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="edit_action" id="continueJob"
                                    value="continue" checked>
                                <label class="form-check-label" for="continueJob">Lanjutkan</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="edit_action" id="cancelJob"
                                    value="cancel">
                                <label class="form-check-label" for="cancelJob">Batalkan</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Tanggal Mulai</label>
                                    <input type="date" id="edit_start_date" class="form-control" disabled>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label>Tanggal Selesai</label>
                                    <input type="date" id="edit_end_date" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Adendum/Catatan</label>
                            <textarea id="edit_notes" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Keterangan</label>
                            <input type="text" id="edit_feedback" class="form-control muted" readonly>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning rounded-partner">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Import File --}}
    <div class="modal fade" id="modalImportFile" tabindex="-1" role="dialog" aria-labelledby="modalLabelImportFile"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <form id="formImportFile" method="POST" enctype="multipart/form-data" action="{{ route('jobs.import') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import File Penugasan</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <button class="btn btn-secondary rounded-partner" id="buttonDownloadTemplate" type="button">
                            <i class="fas fa-download"></i> Download Template
                        </button>
                        <div class="form-group mt-3 mx-2">
                            <input type="hidden" name="job_id">
                            <label for="jobsFile">Import File Penugasan</label>
                            <input type="file" class="form-control-file" id="jobsFile" name="jobs_file">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary rounded-partner">
                            <i class="fas fa-upload"></i> Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Pengecekan --}}
    <div class="modal fade" id="modalPengecekanJob" tabindex="-1" role="dialog"
        aria-labelledby="modalLabelPengecekan" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <form id="formPengecekan">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pengecekan</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" name="job_id" id="job_id_hidden">
                            <label>Hasil Pengecekan</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="action" id="approveRadio"
                                    value="approve" required>
                                <label class="form-check-label" for="approveRadio">Approve</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="action" id="revisiRadio"
                                    value="revisi">
                                <label class="form-check-label" for="revisiRadio">Revisi</label>
                            </div>
                        </div>

                        <div class="form-group d-none" id="notesGroup">
                            <label for="notes">Catatan Revisi</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Tulis revisi..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary rounded-partner">Submit</button>
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
    <script src="{{ asset('assets/adminLTE/plugins/inputmask/jquery.inputmask.min.js') }}"></script>

    <script type="text/javascript">
        let originalEndDate = null;
        let editCount = 1;
        let endDateChanged = false;

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
                    url: "{{ route('jobs.index') }}",
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
                        class: 'text-center',
                    },
                    {
                        data: 'assigner',
                        name: 'assigner',
                        class: 'text-center',
                        orderable: false,
                        searchable: false
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
                        searchable: false
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
                ]
            });

            $("#statusFilter").on('change', function() {
                table.ajax.reload();
            });

            $('#buttonAddJobModal').on('click', function() {
                $('#formAddJob')[0].reset();
                $('.select2').val(null).trigger('change');
                $('#modalAddJob').modal('show');

                setTimeout(function() {
                    $('.select2').select2({
                        dropdownParent: $('#modalAddJob'),
                        width: '100%',
                        placeholder: "Pilih Karyawan",
                        allowClear: true
                    });
                }, 200);
            });

            $('#formAddJob').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();
                $.ajax({
                    url: "{{ route('jobs.store') }}",
                    method: "POST",
                    data: formData,
                    success: function(res) {
                        $('#modalAddJob').modal('hide');
                        $('#jobTable').DataTable().ajax.reload(null, false);
                        showToast('success', res.message);
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        showToast('error', 'Gagal menambahkan penugasan');
                    }
                });
            });

            $('#edit_end_date').on('change', function() {
                const newDate = $(this).val();
                if (!endDateChanged && newDate !== originalEndDate) {
                    $('#edit_feedback').val('Pengerjaan ke-' + editCount);
                    endDateChanged = true;
                }
            });

            $('#formEditJob').on('submit', function(e) {
                e.preventDefault();

                const id = $('#edit_job_id').val();

                $.ajax({
                    url: `/jobs/${id}`,
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        job_detail: $('#edit_detail').val(),
                        end_date: $('#edit_end_date').val(),
                        feedback: $('#edit_feedback').val(),
                        notes: $('#edit_notes').val(),
                        action: $('input[name="edit_action"]:checked').val()
                    },
                    success: function(res) {
                        $('#editJobModal').modal('hide');
                        $('#jobTable').DataTable().ajax.reload(null, false);
                        showToast('success', res.message);
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        showToast('error', xhr.responseJSON.message);
                    }
                });
            });

            /**
             * 24 June 2025
             * Get total completion efficiency/point
             **/
            let searchBox = $('div.dataTables_filter input');

            searchBox.on('keyup', function() {
                table.on('xhr.dt', function(e, settings, json, xhr) {
                    let searchValue = table.search().trim();

                    if (searchValue.length > 0) {
                        $('#totalEfficiencyOutput').val(json.total_efficiency + '%');
                        $('#totalEfficiencyWrapper').show();
                    } else {
                        $('#totalEfficiencyWrapper').hide();
                    }
                });
            });

            $('#buttonDownloadTemplate').on('click', function() {
                window.open('{{ route('jobs.download_template') }}', '_blank');
            });

            $(document).ready(function() {
                $('#buttonOpenModalImportJobs').click(function() {
                    $('#modalImportFile').modal('show');
                });
            });

            $('input[name="action"]').on('change', function() {
                if ($(this).val() === 'revisi') {
                    $('#notesGroup').removeClass('d-none');
                    $('#notes').attr('required', true);
                } else {
                    $('#notesGroup').addClass('d-none');
                    $('#notes').removeAttr('required');
                }
            });

            $('#formPengecekan').on('submit', function(e) {
                e.preventDefault();

                const jobId = $('#job_id_hidden').val();
                const formData = $(this).serialize();

                console.log('Form Data:', formData);

                $.ajax({
                    url: `/jobs/${jobId}/mark-complete`,
                    method: "POST",
                    data: formData,
                    success: function(res) {
                        $('#modalPengecekanJob').modal('hide');
                        $('#jobTable').DataTable().ajax.reload(null, false);
                        showToast('success', res.message);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        let msg = 'Gagal menyelesaikan tugas';
                        if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showToast('error', msg);
                    }
                });
            });

            updateTime(); // sekali saat awal
            setInterval(updateTime, 1000); // update tiap 1 detik
        });

        function modalEdit(button) {
            const jobId = $(button).data('id');

            $.get(`/jobs/${jobId}`, function(data) {
                $('#edit_job_id').val(data.id);
                $('#edit_detail').val(data.job_detail);
                $('#edit_start_date').val(data.start_date);
                $('#edit_end_date').val(data.end_date);

                const feedback = data.feedback;
                const splittedFeedback = feedback.split('tgl');

                $('#edit_feedback').val(splittedFeedback[0]);

                originalEndDate = data.end_date;
                editCount = countFeedback(data.feedback);
                endDateChanged = false;

                $('#editJobModal').modal('show');
            });
        }

        function modalApprove(el) {
            const id = $(el).data('id');
            $('#job_id_hidden').val(id);
            $('#formPengecekan')[0].reset();
            $('#notesGroup').addClass('d-none');
            $('#modalPengecekanJob').modal('show');
        }

        function countFeedback(notes) {
            if (!notes) return 1;
            const matches = notes.match(/Pengerjaan ke-(\d+)/);
            return matches ? parseInt(matches[1]) + 1 : 2;
        }

        function showToast(icon, message) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        }

        function markJobDone(button) {
            const jobId = $(button).data('id');

            Swal.fire({
                title: 'Yakin ingin menandai pekerjaan ini selesai?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, tandai selesai',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/jobs/${jobId}/complete`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                            $('#jobTable').DataTable().ajax.reload();
                            showToast('success', res.message);
                        },
                        error: function(err) {
                            showToast('error', 'Gagal menandai pekerjaan ini selesai');
                        }
                    });
                }
            });
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
