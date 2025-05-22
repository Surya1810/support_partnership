@extends('layouts.admin')

@section('title')
    Dashboard
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
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="content mt-3">
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
                <button class="btn btn-sm btn-primary" type="button" id="buttonAddJobModal">
                    <i class="fas fa-plus"></i>
                    Tambah
                </button>
            </div>
            <div class="table-responsive w-100">
                <table class="table table-bordered table-striped" id="jobTable" style="width:100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>No.</th>
                            <th>Pemberi</th>
                            <th>Penerima</th>
                            <th>Divisi</th>
                            <th>Judul</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Detail</th>
                            <th>Keterangan</th>
                            <th>Masukan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>

    {{-- Modal Tambah Penugasan --}}
    <div class="modal fade" id="modalAddJob" tabindex="-1" role="dialog" aria-labelledby="modalLabelJob"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
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
                                <option value="">Pilih Karyawan</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="title">Judul</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="detail">Detail Pekerjaan</label>
                            <textarea name="detail" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="start_date">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="end_date">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Penugasa --}}
    <div class="modal fade" id="editJobModal" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="formEditJob">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title">Edit Pekerjaan</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_job_id">

                        <div class="form-group">
                            <label>Judul</label>
                            <input type="text" id="edit_title" class="form-control" required>
                        </div>

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

                        <div class="form-group">
                            <label>Tanggal Mulai</label>
                            <input type="date" id="edit_start_date" class="form-control" disabled>
                        </div>

                        <div class="form-group">
                            <label>Tanggal Selesai</label>
                            <input type="date" id="edit_end_date" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Masukan</label>
                            <textarea id="edit_feedback" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Keterangan</label>
                            <input type="text" id="edit_notes" class="form-control muted" readonly>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
                order: [
                    [5, 'asc']
                ],
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "Semua"]
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
                        orderable: false,
                        render: function(data, type, row, meta) {
                            if (!data) return '';
                            return `<span class="text-ellipsis" data-toggle="tooltip" title="${data}">${data}</span>`;
                        }
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
                        data: 'detail',
                        name: 'detail',
                        class: 'text-center',
                        orderable: false,
                        render: function(data, type, row, meta) {
                            if (!data) return '';
                            return `<span class="text-ellipsis" data-toggle="tooltip" title="${data}">${data}</span>`;
                        }
                    },
                    {
                        data: 'notes',
                        name: 'notes',
                        class: 'text-center',
                        orderable: false
                    },
                    {
                        data: 'feedback',
                        name: 'feedback',
                        class: 'text-center',
                        orderable: false,
                        render: function(data, type, row, meta) {
                            if (!data) return '';
                            return `<span class="text-ellipsis" data-toggle="tooltip" title="${data}">${data}</span>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
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
                    error: function() {
                        showToast('error', 'Gagal menambahkan penugasan');
                    }
                });
            });

            $('#edit_end_date').on('change', function() {
                const newDate = $(this).val();
                if (!endDateChanged && newDate !== originalEndDate) {
                    $('#edit_notes').val('Pengerjaan ke-' + editCount);
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
                        title: $('#edit_title').val(),
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
                        showToast('error', 'Gagal memperbarui penugasan');
                    }
                });
            });
        });

        function modalEdit(button) {
            const jobId = $(button).data('id');

            $.get(`/jobs/${jobId}`, function(data) {
                $('#edit_job_id').val(data.id);
                $('#edit_title').val(data.title);
                $('#edit_detail').val(data.job_detail);
                $('#edit_start_date').val(data.start_date);
                $('#edit_end_date').val(data.end_date);
                $('#edit_feedback').val(data.feedback ?? '');
                $('#edit_notes').val(data.notes ?? '');

                originalEndDate = data.end_date;
                editCount = countNotesEdit(data.notes);
                endDateChanged = false;

                $('#editJobModal').modal('show');
            });
        }

        function countNotesEdit(notes) {
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
    </script>
@endpush
