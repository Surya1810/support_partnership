@extends('layouts.admin')

@section('title')
    Cost Center Project Report of {{ $project->name }}
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
                        <li class="breadcrumb-item active"><strong>Project Report</strong></li>
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
                    <h4><b>Project Report</b></h4>
                    <input type="hidden" id="project_id" value="{{ $project->id }}">
                </div>
            </div>
            <div class="text-sm mt-3">
                <div class="row">
                    <div class="col-12 col-md-3">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Total Debet</strong></p>
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
                </div>
            </div>

            {{-- Department --}}
            <div class="content mt-3 text-sm">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-outline rounded-partner card-primary p-3">
                                <h4><b>Laporan Keuangan Project {{ $project->name }}</b></h4>
                                <div class="row my-3">
                                    @php
                                        $roleIds = [1, 2, 3];
                                        $deparmentsIds = [8];
                                    @endphp
                                    @if (in_array(auth()->user()->role_id, $roleIds) || in_array(auth()->user()->department_id, $deparmentsIds))
                                        @if ($project->status != 'Finished' && $project->department_id == auth()->user()->department_id)
                                            <div class="col-md-6 mb-2">
                                                <button type="button" class="btn btn-sm btn-primary rounded-partner"
                                                    id="buttonAddRAB">
                                                    <i class="fas fa-plus"@section('')

                                                    @show></i> Tambah RAB
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning rounded-partner mr-1"
                                                    id="buttonEditRAB">
                                                    <i class="fas fa-pencil"></i> Ubah RAB
                                                </button>
                                            </div>
                                        @endif
                                    @endif

                                    <div
                                        class="{{ $project->status == 'Finished' ? 'col-md-12' : ($project->department_id == auth()->user()->department_id ? 'col-md-6' : 'col-md-12' ) }} mb-2 text-right">
                                        {{-- @if (in_array(auth()->user()->role_id, $roleIds) || in_array(auth()->user()->department_id, $deparmentsIds))
                                            @if ($project->status != 'Finished' && $project->department_id == auth()->user()->department_id)
                                                <button type="button" class="btn btn-sm btn-danger rounded-partner mr-1"
                                                    id="buttonImport">
                                                    <i class="fas fa-upload"></i> Import
                                                </button>
                                            @endif
                                        @endif --}}
                                        <a href="{{ route('cost-center.export.project.budget-plan', $project->id) }}" class="btn btn-sm btn-success rounded-partner" target="_blank">
                                            <i class="fas fa-file-excel"></i> Export RAB
                                        </a>
                                        <a href="{{ route('cost-center.export.project.budget-plan.requests', $project->id) }}" class="btn btn-sm btn-success rounded-partner" target="_blank">
                                            <i class="fas fa-file-excel"></i> Export Pengajuan
                                        </a>
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
                                                        <th>Tanggal</th>
                                                        <th>Nama RAB</th>
                                                        <th>Kode Transaksi</th>
                                                        <th>Debet</th>
                                                        <th>Limit</th>
                                                        <th>Keterangan</th>
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

    {{-- Modal Tambah RAB --}}
    <div class="modal fade text-sm" id="modalAddRAB" tabindex="-1" role="dialog" aria-labelledby="modalAddRABTitle"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <form id="formAddRAB" action="{{ route('cost-center.departments.projects.budget-plan.store', $project->id) }}"
                method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title">Tambah RAB</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="department" class="col-sm-4 col-form-label">Divisi</label>
                            <div class="col-sm-8">
                                <select class="form-control" disabled>
                                    <option value="{{ $initialValues['department']->id }}">
                                        {{ $initialValues['department']->name }}
                                    </option>
                                </select>
                                <input type="hidden" name="department" id="department"
                                    value="{{ $initialValues['department']->id }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="name" class="col-sm-4 col-form-label">Nama Item</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control text-sm" id="name" name="name"
                                    placeholder="Masukan nama RAB" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="remaining" class="col-sm-4 col-form-label">Sisa Saldo Kas</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control text-sm price"
                                    value="{{ $initialValues['amount_remaining'] }}" disabled required>
                                <input type="hidden" id="remaining" name="remaining"
                                    value="{{ $initialValues['amount_remaining'] }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nominal" class="col-sm-4 col-form-label">Nominal Debet</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control text-sm price" id="nominal" name="nominal"
                                    placeholder="Rp0" required>
                                <small class="text-danger d-none" id="nominalAddAlert">Nominal melebihi saldo
                                    tersisa!</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="month" class="col-sm-4 col-form-label">Bulan Realisasi</label>
                            <div class="col-sm-8">
                                <select name="month" id="month" class="form-control text-sm select2" required>
                                    <option value="" selected disabled>-- Pilih Bulan --</option>
                                    @foreach ($initialValues['months'] as $index => $month)
                                        <option value="{{ $index + 1 }}">{{ $month }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="year" class="col-sm-4 col-form-label">Tahun Realisasi</label>
                            <div class="col-sm-8">
                                <select name="year" id="year" class="form-control text-sm select2" required>
                                    <option value="" selected disabled>-- Pilih Tahun --</option>
                                    @foreach ($initialValues['years'] as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
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

    {{-- Modal Edit RAB --}}
    <div class="modal fade text-sm" id="modalEditRAB" tabindex="-1" role="dialog" aria-labelledby="modalEditRABTitle"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <form id="formEditRAB" action="#" method="POST">
                @method('PUT')
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Edit RAB</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="departmentEdit" class="col-sm-4 col-form-label">Divisi</label>
                            <div class="col-sm-8">
                                <select class="form-control" disabled>
                                    <option value="{{ $initialValues['department']->id }}">
                                        {{ $initialValues['department']->name }}
                                    </option>
                                </select>
                                <input type="hidden" name="department" id="departmentEdit"
                                    value="{{ $initialValues['department']->id }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="targetEdit" class="col-sm-4 col-form-label">Target</label>
                            <div class="col-sm-8">
                                <select name="target" id="targetEdit" class="form-control select2" required>
                                    <option value="" selected disabled>-- Pilih Target --</option>
                                    @foreach ($initialValues['project_cost_centers'] as $target)
                                        <option value="{{ $target->id }}" data-name="{{ $target->name }}"
                                            data-debit="{{ $target->amount_debit }}"
                                            data-remaining="{{ $target->amount_remaining }}">
                                            {{ $target->code_ref . ' - ' . $target->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="nameEdit" class="col-sm-4 col-form-label">Nama</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control text-sm" id="nameEdit" name="name"
                                    placeholder="Pilih Target Terlebih Dahulu" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="remainingAmountEdit" class="col-sm-4 col-form-label">Saldo Target Tersisa</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control text-sm price" id="remainingAmountEdit"
                                    name="remaining_amount" readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="updateType" class="col-sm-4">Jenis</label>
                            <div class="col-sm-8">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="update_type" id="changeAmount"
                                        value="change_amount" disabled>
                                    <label class="form-check-label" for="changeAmount">Tambah Nominal</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="update_type"
                                        id="changeToNewRAB" value="change_to_new_rab" disabled>
                                    <label class="form-check-label" for="changeToNewRAB">Pecah ke RAB Baru</label>
                                </div>
                            </div>
                        </div>
                        <div id="changeAmountFields" class="d-none">
                            <div class="form-group row">
                                <label for="newNominalEdit" class="col-sm-4 col-form-label">Debet Tambahan</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control text-sm price" id="newNominalEdit"
                                        placeholder="Rp0">
                                    <input type="hidden" id="newNominalEditHidden" name="new_nominal">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="totalAmountEdit" class="col-sm-4 col-form-label">Total Saldo Tersisa</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control text-sm price" id="totalAmountEdit"
                                        name="total_amount" placeholder="Rp0" readonly>
                                </div>
                            </div>
                        </div>
                        <div id="splitToNewRABFields" class="d-none">
                            <div class="form-group row">
                                <label for="nameNewEdit" class="col-sm-4 col-form-label">Nama Item</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control text-sm" id="nameNewEdit" name="name_new"
                                        placeholder="Masukkan nama RAB baru">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="nominalNewEdit" class="col-sm-4 col-form-label">Nominal Debet</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control text-sm price" id="nominalNewEdit"
                                        placeholder="Rp0">
                                    <small class="text-danger d-none" id="nominalNewAlert">Nominal melebihi saldo
                                        tersisa!</small>
                                    <input type="hidden" id="nominalNewRABEditHidden" name="nominal_new_rab">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="monthEdit" class="col-sm-4 col-form-label">Bulan Realisasi</label>
                                <div class="col-sm-8">
                                    <select name="month" id="monthEdit" class="form-control text-sm select2">
                                        <option value="" selected disabled>-- Pilih Bulan --</option>
                                        @foreach ($initialValues['months'] as $index => $month)
                                            <option value="{{ $index + 1 }}">{{ $month }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="yearEdit" class="col-sm-4 col-form-label">Tahun Realisasi</label>
                                <div class="col-sm-8">
                                    <select name="year" id="yearEdit" class="form-control text-sm select2">
                                        <option value="" selected disabled>-- Pilih Tahun --</option>
                                        @foreach ($initialValues['years'] as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning rounded-partner">Update</button>
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
    <script src="{{ asset('js/loading-overlay.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#month, #year, #category').select2({
                theme: 'bootstrap4',
                width: '100%',
                dropdownParent: $('#modalAddRAB')
            });

            $('#targetEdit, #monthEdit, #yearEdit, #categoryEdit').select2({
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
                    url: "{{ route('cost-center.departments.projects.budget-plan', ':id') }}".replace(
                        ':id', $('#project_id').val()),
                    method: 'GET'
                },
                columns: [{
                        className: 'details-control text-center',
                        orderable: false,
                        searchable: false,
                        data: null,
                        defaultContent: '<button class="badge bg-info border-0" title="List Pengajuan"><i class="fas fa-dollar-sign"></i></button>',
                    },
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'code_ref',
                        name: 'code_ref'
                    },
                    {
                        data: 'debit',
                        name: 'debit'
                    },
                    {
                        data: 'credit',
                        name: 'credit'
                    },
                    {
                        data: 'detail',
                        name: 'detail'
                    }
                ]
            });

            // hide expand button untuk baris pertama
            table.on('draw', function () {
                const firstRow = $('#tableProject tbody tr').first();
                firstRow.find('td.details-control').html('');
            });

            $('#tableProject tbody').on('click', 'td.details-control button', function() {
                let tr = $(this).closest('tr');
                let row = table.row(tr);
                let projectId = row.data().project_id;
                let costCenterId = row.data().id;

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    $(this).html('<i class="fas fa-dollar-sign"></i>');
                } else {
                    // Tambahkan HTML tabel kosong dengan ID unik
                    let tableId = `tableProjectBudgetPlanRequests-${projectId}-${costCenterId}`;
                    let html = `
                    <table id="${tableId}" class="table table-sm table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>No.</th>
                                <th>Tanggal Digunakan</th>
                                <th>Judul</th>
                                <th>Kode Transaksi</th>
                                <th>Pengaju</th>
                                <th>Diajukan</th>
                                <th>Digunakan</th>
                                <th>Bukti</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                    `;
                    row.child(html).show();
                    tr.addClass('shown');
                    $(this).text('-');

                    // Inisialisasi DataTables untuk tabel profit
                    setTimeout(() => {
                        $(`#${tableId}`).DataTable({
                            processing: true,
                            serverSide: true,
                            deferRender: true,
                            destroy: true,
                            ordering: false,
                            searching: false,
                            paging: false,
                            info: false,
                            ajax: "{{ route('cost-center.departments.projects.budget-plan.requests', [':id', ':ccid']) }}"
                                .replace(':id', projectId)
                                .replace(':ccid', costCenterId),
                            columns: [
                                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center' },
                                { data: 'date', name: 'date' },
                                { data: 'title', name: 'title' },
                                { data: 'code', name: 'code' },
                                { data: 'user', name: 'user' },
                                { data: 'credit', name: 'credit' },
                                { data: 'used_amount', name: 'used_amount' },
                                { data: 'report_file', name: 'report_file' },
                                { data: 'status', name: 'status' }
                            ]
                        });
                    }, 100);
                }
            });

            $('#buttonAddRAB').on('click', function() {
                $('#formAddRAB')[0].reset();
                $('#modalAddRAB').modal('show');
            });

            $('#buttonEditRAB').on('click', function() {
                $('#formEditRAB')[0].reset();
                $('#changeAmountFields').addClass('d-none');
                $('#modalEditRAB').modal('show');

                $('.price').inputmask({
                    alias: 'numeric',
                    prefix: 'Rp',
                    digits: 0,
                    groupSeparator: '.',
                    autoGroup: true,
                    removeMaskOnSubmit: true,
                    rightAlign: false
                });
            });

            $('#targetEdit').on('change', function(e) {
                const id = e.target.value;

                if (id == '') {
                    $('#nameEdit').val('Pilih Cost Center Target Terlebih Dahulu').prop('disabled', true);
                    $('#remainingEdit').val('Rp0').prop('disabled', true);
                    $('#targetEdit').val('Pilih Divisi Terlebih Dahulu').prop('disabled', true);
                    return;
                }

                const selectedOption = $('#targetEdit option:selected');
                const name = selectedOption.data('name');
                const debit = selectedOption.data('debit');
                const remaining = selectedOption.data('remaining');

                console.log(name, debit, remaining);

                $('#nameEdit').prop('disabled', false)
                    .prop('readonly', false).prop('required', true).val(name);
                $('#debitAmountEdit').prop('disabled', true).val(debit);
                $('#remainingAmountEdit').prop('disabled', true).val(remaining);

                $('input[name="update_type"]').prop('disabled', false).prop('required', true);
            });

            $('input[name="update_type"]').on('change', function() {
                const changeAmount = $('#changeAmount').is(':checked');
                const splitNewRAB = $('#changeToNewRAB').is(':checked');

                $('#changeAmountFields').toggleClass('d-none', !changeAmount);
                $('#splitToNewRABFields').toggleClass('d-none', !splitNewRAB);

                if (!changeAmount) {
                    $('#newNominalEdit').val('').prop('required', false);
                    $('#totalAmountEdit').val('').prop('required', false);

                    $('#categoryEdit').prop('required', true);
                    $('#nameNewEdit').prop('required', true);
                    $('#nominalNewEdit').prop('required', true);
                }

                if (!splitNewRAB) {
                    $('#categoryEdit').val('').trigger('change').prop('required', false);
                    $('#nameNewEdit').val('').prop('required', false);
                    $('#nominalNewEdit').val('').prop('required', false);
                    $('#nominalNewAlert').addClass('d-none');

                    $('#newNominalEdit').prop('required', true);
                    $('#totalAmountEdit').prop('required', true);
                }
            });

            $('#nominal').on('input', function() {
                const nominalBaru = parseCurrency($(this).val());
                const saldoTersisa = parseFloat($('#remaining').val());

                if (nominalBaru > saldoTersisa) {
                    $('#nominalAddAlert').removeClass('d-none');
                } else {
                    $('#nominalAddAlert').addClass('d-none');
                }
            });

            $('#nominalNewEdit').on('input', function() {
                const nominalBaru = parseCurrency($(this).val());
                const saldoTersisa = parseCurrency($('#remainingAmountEdit').val());

                if (nominalBaru > saldoTersisa) {
                    $('#nominalNewAlert').removeClass('d-none');
                } else {
                    $('#nominalNewRABEditHidden').val(nominalBaru);
                    $('#nominalNewAlert').addClass('d-none');
                }
            });

            $('#newNominalEdit').on('input', function() {
                const nominalBaru = parseCurrency($(this).val());
                const saldoTersisa = parseCurrency($('#remainingAmountEdit').val());
                const total = nominalBaru + saldoTersisa;

                $('#newNominalEditHidden').val(nominalBaru);
                $('#totalAmountEdit').val(total);
            });

            $('#formEditRAB').on('submit', function(e) {
                e.preventDefault();
                const idRAB = $('#targetEdit').find(':selected').val();

                if (idRAB == '') {
                    showToast('error', 'Pilih Cost Center Target Terlebih Dahulu');
                    return;
                }

                const urlAction =
                    "{{ route('cost-center.departments.projects.budget-plan.update', ':id') }}".replace(
                        ':id', idRAB);

                $(this).attr('action', urlAction);
                this.submit();
            });
        });

        function parseCurrency(value) {
            return parseInt(value.replace(/[^0-9]/g, '')) || 0;
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
    </script>
@endpush
