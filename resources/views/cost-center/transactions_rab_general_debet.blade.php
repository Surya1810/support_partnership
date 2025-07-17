@extends('layouts.admin')

@section('title')
    Cost Center Buat RAB Divisi
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
                        <li class="breadcrumb-item active"><strong>Buat RAB Divisi</strong></li>
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
                    <h4><b>RAB PT. Partnership Procurement Solution {{ date('Y') }}</b></h4>
                </div>
            </div>
            <div class="text-sm mt-3">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Total Debet</strong></p>
                                <h6>{{ $sums['debit'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Total Kredit (Limit Seluruh RAB)</strong></p>
                                <h6>{{ $sums['credit'] }}</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-body">
                                <p><strong>Sisa (Debet - Limit Seluruh RAB)</strong></p>
                                <h6>{{ $sums['remaining'] }}</h6>
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
                                <h4><b>Rancangan Anggaran Biaya {{ date('Y') }}</b></h4>
                                @php
                                    $roleIds = [1, 2, 3];
                                    $deparmentsIds = [8];
                                @endphp
                                @if (in_array(auth()->user()->role_id, $roleIds) || in_array(auth()->user()->department_id, $deparmentsIds))
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-sm btn-primary rounded-partner"
                                                id="buttonAddRAB">
                                                <i class="fas fa-plus"></i> Tambah Uang Kas
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning rounded-partner mr-1"
                                                id="buttonEditRAB">
                                                <i class="fas fa-pencil"></i> Ubah RAB
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <a href="{{ route('cost-center.export.general-debit.realizations') }}"
                                                class="btn btn-sm btn-success rounded-partner float-right" target="_blank">
                                                <i class="fas fa-file-excel"></i> Export
                                            </a>
                                            <button type="button" id="buttonOpenModalImport"
                                                class="btn btn-sm btn-danger rounded-partner mr-1 float-right">
                                                <i class="fas fa-upload"></i> Import
                                            </button>
                                        </div>
                                    </div>
                                @endif
                                <div class="card-body table-responsive w-100 px-0">
                                    <table class="table table-bordered table-striped text-sm" id="tableRAB"
                                        style="width:100%">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No.</th>
                                                <th>Nama Item</th>
                                                <th>Kode Transaksi</th>
                                                <th>Divisi</th>
                                                <th>Bulan Realisasi</th>
                                                <th>Tahun</th>
                                                <th>Debet</th>
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
    </section>

    {{-- Modal Tambah RAB --}}
    <div class="modal fade text-sm" id="modalAddRAB" tabindex="-1" role="dialog" aria-labelledby="modalAddRABTitle"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title">Tambah Uang Kas</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="formAddRAB" class="modal-body" action="{{ route('cost-center.store.rab-general') }}"
                    method="POST">
                    @csrf
                    <div>
                        <div class="form-group row">
                            <label for="department" class="col-sm-4 col-form-label">Divisi</label>
                            <div class="col-sm-8">
                                @if (auth()->user()->role_id == 3)
                                    <select class="form-control" disabled required>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ auth()->user()->department_id == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}</option>
                                        @endforeach
                                        <input type="hidden" name="department"
                                            value="{{ auth()->user()->department_id }}">
                                    </select>
                                @else
                                    <select name="department" id="department" class="form-control" required>
                                        <option value="" selected disabled>-- Pilih Divisi --</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="category" class="col-sm-4 col-form-label">Kategori</label>
                            <div class="col-sm-8">
                                @if (auth()->user()->role_id == 3)
                                    <div>
                                        <select class="form-control select2" disabled required>
                                            <option value="1" selected>(KS) Kas/Pemasukan</option>
                                        </select>
                                        <input type="hidden" name="category" value="1">
                                    </div>
                                    <div>
                                        <span class="text-sm text-danger">
                                            *Penambahan cost center lain atau suntik dana dilalukan melalui ubah RAB
                                        </span>
                                    </div>
                                @else
                                    <select name="category" id="category" class="form-control select2" required>
                                        <option value="" selected disabled>-- Pilih Cost Center --</option>
                                        <option value="1">(KS) Kas/Pemasukan</option>
                                        @foreach ($costCenterCategories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ '(' . $category->code . ') ' . $category->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
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
                            <label for="nominal" class="col-sm-4 col-form-label">Nominal Debet</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control text-sm price" id="nominal" name="nominal"
                                    placeholder="Rp0" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="month" class="col-sm-4 col-form-label">Bulan Realisasi</label>
                            <div class="col-sm-8">
                                <select name="month" id="month" class="form-control text-sm select2" required>
                                    <option value="" selected disabled>-- Pilih Bulan --</option>
                                    @foreach ($months as $index => $month)
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
                                    @foreach ($years as $year)
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
    <div class="modal fade text-sm" data-backdrop="static" data-keyboard="false" id="modalEditRAB" tabindex="-1"
        role="dialog" aria-labelledby="modalEditRABTitle" aria-hidden="true" data-backdrop="static"
        data-keyboard="false">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Edit RAB</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="formEditRAB" action="#" method="POST" class="modal-body">
                    @method('PUT')
                    @csrf
                    <div>
                        <div class="form-group row">
                            <label for="departmentEdit" class="col-sm-4 col-form-label">Divisi</label>
                            <div class="col-sm-8">
                                @if (auth()->user()->role_id == 3)
                                    <select class="form-control" disabled required>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}"
                                                {{ auth()->user()->department_id == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="department" id="departmentEdit"
                                        value="{{ auth()->user()->department_id }}">
                                @else
                                    <select name="department" id="departmentEdit" class="form-control" required>
                                        <option value="">-- Pilih Divisi --</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="targetEdit" class="col-sm-4 col-form-label">Target</label>
                            <div class="col-sm-8">
                                <select name="target" id="targetEdit" class="form-control select2" required disabled>
                                    <option value="" selected disabled>Pilih Divisi Terlebih Dahulu</option>
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
                                <label for="totalAmountEdit" class="col-sm-4 col-form-label">Total</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control text-sm price" id="totalAmountEdit"
                                        name="total_amount" placeholder="Rp0" readonly>
                                </div>
                            </div>
                        </div>
                        <div id="splitToNewRABFields" class="d-none">
                            <div class="form-group row">
                                <label for="categoryEdit" class="col-sm-4 col-form-label">Kategori</label>
                                <div class="col-sm-8">
                                    <select name="category" id="categoryEdit" class="form-control select2">
                                        <option value="" selected disabled>-- Pilih Cost Center --</option>
                                        @foreach ($costCenterCategories as $category)
                                            <option value="{{ $category->id }}">
                                                {{ '(' . $category->code . ') ' . $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="nameNewEdit" class="col-sm-4 col-form-label">Nama RAB Baru</label>
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
                                        @foreach ($months as $index => $month)
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
                                        @foreach ($years as $year)
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
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Import File --}}
    <div class="modal fade" id="modalImportFile" tabindex="-1" role="dialog" aria-labelledby="modalLabelImportFile"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="formImportFile" method="POST" enctype="multipart/form-data" action="{{ route('cost-center.import.rab-general.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import RAB General</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <button class="btn btn-secondary rounded-partner" id="buttonDownloadTemplate" type="button">
                            <i class="fas fa-download"></i> Download Template
                        </button>
                        <div class="form-group mt-3 mx-2">
                            <label for="costCenterFile">Import File Excel</label>
                            <input type="file" class="form-control-file" id="costCenterFile" name="file" required>
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
                    url: "{{ route('cost-center.create.rab-general') }}",
                    type: "GET"
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
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
                        data: 'department',
                        name: 'department'
                    },
                    {
                        data: 'month',
                        name: 'month'
                    },
                    {
                        data: 'year',
                        name: 'year'
                    },
                    {
                        data: 'debit',
                        name: 'debit'
                    },
                    {
                        data: 'detail',
                        name: 'detail'
                    }
                ]
            });

            $('#buttonOpenModalImport').click(function() {
                $('#modalImportFile').modal('show');
            });

            $('#buttonDownloadTemplate').on('click', function() {
                $.LoadingOverlay('show');

                setTimeout(() => {
                    window.open('{{ route('cost-center.import.rab-general') }}', '_blank');
                }, 1500);

                $.LoadingOverlay('hide');
            });

            $('#formImportFile').on('submit', function(e) {
                $.LoadingOverlay('show');
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
                    $('#debitEdit').val('Rp0').prop('disabled', true);
                    $('#remainingEdit').val('Rp0').prop('disabled', true);
                    $('#targetEdit').val('Pilih Divisi Terlebih Dahulu').prop('disabled', true);
                    return;
                }

                const selectedOption = $('#targetEdit option:selected');
                const name = selectedOption.data('name');
                const debit = selectedOption.data('debit');
                const remaining = selectedOption.data('remaining');

                console.log(name, debit, remaining);

                $('#nameEdit').prop('disabled', false).prop('readonly', false).prop('required', true).val(
                    name);

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

                const urlAction = "{{ route('cost-center.edit.rab-general.update', ':id') }}"
                    .replace(':id', idRAB);

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

    {{-- Role Director and Finance --}}
    @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2 || auth()->user()->department_id == 8)
        <script type="text/javascript">
            $(document).ready(function() {
                $('#departmentEdit').select2({
                    theme: 'bootstrap4',
                    width: '100%',
                    dropdownParent: $('#modalEditRAB')
                });

                $('#departmentEdit').on('change', function(e) {
                    const id = e.target.value;

                    if (id == '') {
                        $('#targetEdit').prop('disabled', true).html(
                            '<option value="" selected disabled>Pilih Divisi Terlebih Dahulu</option>');
                        return;
                    }

                    $.ajax({
                        url: "{{ route('cost-center.edit.rab-general.list', ':id') }}".replace(
                            ':id', id),
                        type: 'GET',
                        beforeSend: function() {
                            $('#targetEdit').html(
                                '<option value="" selected disabled>Loading...</option>');
                        },
                        success: function(response) {
                            console.log(response);
                            let html =
                                '<option value="" selected disabled>-- Pilih Cost Center Target --</option>';

                            if (response.data.length == 0) {
                                html =
                                    '<option value="" selected disabled>RAB Belum Tersedia</option>';
                                $('#targetEdit').prop('disabled', true).html(html);
                            } else {
                                response.data.forEach(item => {
                                    html +=
                                        `<option value="${item.id}" data-name="${item.name}" data-debit="${Number(item.amount_debit)}" data-remaining="${Number(item.amount_remaining)}">${item.code_ref} - ${item.name}</option>`;
                                });
                                $('#targetEdit').prop('disabled', false).prop('required', true)
                                    .html(html);
                            }
                        },
                        error: function(xhr) {
                            console.log(xhr);
                            $('#targetEdit').prop('disabled', true).html(
                                '<option value="" selected disabled>Error</option>');
                            showToast('error', 'Gagal memuat data');
                        }
                    });
                });
            });
        </script>
    @endif

    @if (auth()->user()->role_id == 3)
        <script type="text/javascript">
            $(document).ready(function() {
                const id = $('#departmentEdit').val();

                if (id == '') {
                    $('#targetEdit').prop('disabled', true).html(
                        '<option value="" selected disabled>Pilih Divisi Terlebih Dahulu</option>');
                    return;
                }

                $.ajax({
                    url: "{{ route('cost-center.edit.rab-general.list', ':id') }}".replace(
                        ':id', id),
                    type: 'GET',
                    beforeSend: function() {
                        $('#targetEdit').html(
                            '<option value="" selected disabled>Loading...</option>');
                    },
                    success: function(response) {
                        console.log(response);
                        let html =
                            '<option value="" selected disabled>-- Pilih Cost Center Target --</option>';

                        if (response.data.length == 0) {
                            html =
                                '<option value="" selected disabled>RAB Belum Tersedia</option>';
                            $('#targetEdit').prop('disabled', true).html(html);
                        } else {
                            response.data.forEach(item => {
                                html +=
                                    `<option value="${item.id}" data-name="${item.name}" data-debit="${Number(item.amount_debit)}" data-remaining="${Number(item.amount_remaining)}">${item.code_ref} - ${item.name}</option>`;
                            });
                            $('#targetEdit').prop('disabled', false).prop('required', true)
                                .html(html);
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr);
                        $('#targetEdit').prop('disabled', true).html(
                            '<option value="" selected disabled>Error</option>');
                        showToast('error', 'Gagal memuat data');
                    }
                });
            });
        </script>
    @endif
@endpush
