@extends('layouts.admin')

@section('title')
    Pengajuan
@endsection

@push('css')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('assets/adminLTE/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/adminLTE/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/adminLTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/adminLTE/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endpush

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <h1>Pengajuan</h1>
            <ol class="breadcrumb text-black-50">
                <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item">Finance</li>
                <li class="breadcrumb-item active"><strong>Pengajuan</strong></li>
            </ol>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid text-sm">
            <div class="row">
                <!-- Tabel on going application-->
                <div class="col-12 col-md-6">
                    <div class="card card-outline rounded-partner card-primary">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <h3 class="card-title">Pengajuan Saya <span
                                            class="badge badge-info">{{ $limit }}</span></h3>
                                </div>
                                <div class="col-6">
                                    {{-- @if ($limit > 3)
                                        <small class="text-danger float-right">*Harap selesaikan 3 pengajuan sebelumnya di
                                            divisimu.</small>
                                    @else --}}
                                    <button type="button" class="btn btn-sm btn-primary rounded-partner float-right"
                                        data-toggle="modal" data-target="#addApplication"><i class="fas fa-plus"></i>
                                        Buat
                                        Pengajuan</button>
                                    {{-- @endif --}}
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="myexpenseTable" class="table table-bordered text-nowrap">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 60%">
                                            Judul
                                        </th>
                                        <th style="width: 10%">
                                            Kategori
                                        </th>
                                        <th style="width: 10%">
                                            Kode Transaksi
                                        </th>
                                        <th style="width: 5%">
                                            Tanggal Digunakan
                                        </th>
                                        <th style="width: 10%">
                                            Nominal
                                        </th>
                                        <th style="width: 10%">
                                            Referensi
                                        </th>
                                        <th style="width: 5%">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($my_expenses as $my_expense)
                                        <tr>
                                            <td>{{ $my_expense->title }}</td>
                                            <td>
                                                @if ($my_expense->category == 'project')
                                                    <strong>Project</strong>
                                                @else
                                                    <strong>General</strong>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $my_expense->code_ref_request }}
                                            </td>
                                            <td>{{ $my_expense->use_date->format('d/m/y') }}</td>
                                            <td>{{ formatRupiah($my_expense->total_amount) }}</td>
                                            <td>
                                                @if ($my_expense->reference_file)
                                                    <a href="{{ asset('storage/' . $my_expense->reference_file) }}"
                                                        target="_blank" class="btn btn-sm btn-info">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($my_expense->status == 'pending')
                                                    <span class="badge badge-secondary">{{ $my_expense->status }}</span>
                                                @elseif ($my_expense->status == 'approved')
                                                    <span class="badge badge-secondary">{{ $my_expense->status }}</span>
                                                @elseif ($my_expense->status == 'processing')
                                                    <span class="badge badge-info">{{ $my_expense->status }}</span>
                                                @elseif ($my_expense->status == 'rejected')
                                                    <span class="badge badge-danger">{{ $my_expense->status }}</span>
                                                @elseif ($my_expense->status == 'finish')
                                                    <span class="badge badge-success">{{ $my_expense->status }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tabel report-->
                <div class="col-12 col-md-6">
                    <div class="card card-outline rounded-partner card-primary">
                        <div class="card-header">
                            <div class="card-title">
                                <h6>Laporan Pertanggungjawaban Pengajuan</h6>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="reportTable" class="table table-bordered text-nowrap">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 50%">
                                            Judul
                                        </th>
                                        <th style="width: 10%">
                                            Kategori
                                        </th>
                                        <th style="width: 15%">
                                            Kode Transaksi
                                        </th>
                                        <th style="width: 5%">
                                            Tanggal Digunakan
                                        </th>
                                        <th style="width: 10%">
                                            Nominal
                                        </th>
                                        <th style="width: 5%">
                                            Status
                                        </th>
                                        <th style="width: 5%">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reports as $report)
                                        <tr>
                                            <td>{{ $report->title }}</td>
                                            <td>
                                                @if ($report->category == 'project')
                                                    <strong>Project</strong> {{ $report->project->name }}
                                                @else
                                                    <strong>General</strong>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $report->code_ref_request }}
                                            </td>
                                            <td>{{ $report->use_date->toFormattedDateString('d/m/y') }}</td>
                                            <td>{{ formatRupiah($report->total_amount) }}</td>
                                            <td>
                                                @if ($report->status == 'report')
                                                    <span class="badge badge-warning">{{ $report->status }}</span>
                                                @elseif ($report->status == 'checking')
                                                    <span class="badge"
                                                        style="background-color: #ee00ff">{{ $report->status }}</span>
                                                @elseif ($report->status == 'finish')
                                                    <span class="badge badge-success">{{ $report->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($report->status == 'report')
                                                    <button type="button" class="btn btn-sm btn-warning rounded-partner"
                                                        data-toggle="modal" data-target="#reportModal{{ $report->id }}">
                                                        <i class="fa-regular fa-flag"></i>
                                                    </button>
                                                @elseif ($report->status == 'checking' && $report->report_file != null)
                                                    <a href="{{ asset('storage/' . $report->report_file) }}"
                                                        target="_blank" class="btn btn-sm btn-info rounded-partner">
                                                        <i class="fa-regular fa-file-pdf"></i>
                                                    </a>
                                                @elseif ($report->status == 'finish')
                                                    <a href="{{ route('application.pdf', $report->id) }}"
                                                        class="btn btn-sm btn-info rounded-partner" target="_blank">
                                                        <i class="fa-regular fa-file-pdf"></i>
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal Add Pengajuan --}}
    <div class="modal fade" id="addApplication" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="addApplicationLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addApplicationLabel">Buat Pengajuan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="modal-body pt-0" action="{{ route('application.store') }}" method="POST"
                    enctype="multipart/form-data" id="formApplication">
                    @csrf
                    <div class="form-group">
                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    </div>

                    <div class="row w-100">
                        <div class="col-12 col-md-4">
                            <label for="user" class="mb-0 form-label col-form-label-sm">Nama</label>
                            <input type="text" class="form-control @error('user') is-invalid @enderror" id="user"
                                name="user" value="{{ auth()->user()->name }}" readonly>
                        </div>

                        <div class="col-12 col-md-8">
                            <label for="department_id" class="mb-0 form-label col-form-label-sm">Divisi</label>
                            <select class="form-control department" style="width: 100%;" required disabled>
                                <option></option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}"
                                        {{ auth()->user()->department_id == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="department_id" id="department_id"
                                value="{{ auth()->user()->department_id }}">
                            @error('department_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-12 col-md-4 mt-3">
                            <label for="use_date" class="mb-0 form-label col-form-label-sm">Tanggal Digunakan</label>
                            <input type="date" class="form-control @error('use_date') is-invalid @enderror"
                                id="use_date" name="use_date" value="{{ old('use_date') }}" required>
                            @error('use_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="col-12 col-md-8 mt-3">
                            <label for="category" class="mb-0 form-label col-form-label-sm">Kategori</label>
                            <select class="form-control category text-small" style="width: 100%;" id="category"
                                name="category" required>
                                <option></option>
                                <optgroup label="Kebutuhan Project">
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}#project" data-type="project">
                                            {{ $project->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="Kebutuhan General Divisi">
                                    @foreach ($generalCostCenters as $costCenter)
                                        <option value="{{ $costCenter->id }}#department" data-type="department"
                                            data-remaining="{{ $costCenter->amount_remaining }}">
                                            {{ $costCenter->code_ref . ' - ' . $costCenter->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @error('category')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        {{-- Cost Center Project Wrapper --}}
                        <div class="col-12 col-md-12 mt-3 d-none" id="costCentersProjectWrapper">
                            <label for="project_cost_center" class="mb-0 form-label col-form-label-sm">Cost Center
                                Project</label>
                            <select name="project_cost_center_id" id="project_cost_center" class="form-control">
                                <option value="">Pilih Cost Center</option>
                            </select>
                        </div>
                        <small id="remaining-balance" class="col-12 text-muted d-block mt-3" style="display: none;">
                            Sisa saldo: <span id="remaining-amount" class="text-danger">-</span>
                        </small>
                    </div>

                    <label for="pencairan" class="mb-0 form-label col-form-label-sm mt-3">Metode Pembayaran</label>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-check m-3">
                                <input class="form-check-input" type="radio" name="pencairan" id="pencairan1"
                                    value="saya" required onclick="rekening_saya();">
                                <label class="form-check-label ml-3" for="pencairan1">
                                    My Account
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check m-3">
                                <input class="form-check-input" type="radio" name="pencairan" id="pencairan2"
                                    value="lain" onclick="rekening_lain();">
                                <label class="form-check-label ml-3" for="pencairan2">
                                    Another Account
                                </label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-check m-3">
                                <input class="form-check-input" type="radio" name="pencairan" id="pencairan3"
                                    value="va" onclick="virtual_account();">
                                <label class="form-check-label ml-3" for="pencairan3">
                                    Virtual Account
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 d-none" id="rekening_lain">
                            <div class="form-group">
                                <input type="text" name="bank1" class="form-control" id="lain1"
                                    placeholder="Tulis nama bank" autocomplete="off" value="{{ old('bank1') }}"
                                    oninput="this.value = this.value.toUpperCase()">
                            </div>
                            <div class="form-group">
                                <input type="text" name="rekening1" class="form-control" id="lain2"
                                    placeholder="Tulis nomor rekening" autocomplete="off"
                                    value="{{ old('rekening1') }}">
                            </div>
                            <div class="form-group">
                                <input type="text" name="atas_nama" class="form-control" id="lain3"
                                    placeholder="Tulis atas nama" autocomplete="off" value="{{ old('atas_nama') }}">
                            </div>
                        </div>
                        <div class="col-md-12 d-none" id="virtual_account">
                            <div class="form-group">
                                <input type="text" name="bank" class="form-control" id="va1"
                                    placeholder="Tulis nama bank" autocomplete="off" value="{{ old('bank') }}"
                                    oninput="this.value = this.value.toUpperCase()">
                            </div>
                            <div class="form-group">
                                <input type="text" name="rekening" class="form-control" id="va2"
                                    placeholder="Tulis nomor virtual account" autocomplete="off"
                                    value="{{ old('rekening') }}">
                            </div>
                        </div>
                    </div>

                    <label for="title" class="mb-0 form-label col-form-label-sm">Keterangan <small
                            class="text-danger">*Wajib Detail & Lengkap</small></label>
                    <textarea type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                        value="{{ old('title') }}" placeholder="Tulis tujuan atau hal yang diajukan" required></textarea>
                    @error('title')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                    {{-- File referensi --}}
                    <div class="col-12 my-3">
                        <label for="reference_file" class="mb-0 form-label col-form-label-sm">File Pendukung <span
                                class="text-sm text-danger">*wajib (gambar atau pdf)</span></label>
                        <input type="file" class="form-control @error('reference_file') is-invalid @enderror"
                            id="reference_file" name="reference_file" value="{{ old('reference_file') }}"
                            placeholder="Upload dokumen pendukung"
                            accept="image/jpeg, image/png, image/jpg, image/webp, application/pdf" required />
                    </div>

                    <label for="items" class="mb-0 form-label col-form-label-sm mt-3">Rincian Item</label>
                    <div class="table-responsive">
                        <table class="table table-sm" id="items-table">
                            <tbody>
                                <tr>
                                    <td>
                                        <label for="title" class="mb-0 form-label col-form-label-sm">Nama
                                            Item</label>
                                        <input type="text" name="items[0][item_name]"
                                            class="form-control form-control-sm" placeholder="Tulis nama item" required>
                                        <div class="row">
                                            <div class="col-4">
                                                <label for="title"
                                                    class="mb-0 form-label col-form-label-sm">Jumlah</label>
                                                <input type="number" name="items[0][quantity]"
                                                    class="form-control form-control-sm" placeholder="Jumlah"
                                                    min="1" value="1" required>
                                            </div>
                                            <div class="col-8">
                                                <label for="title" class="mb-0 form-label col-form-label-sm">Nilai
                                                    Satuan</label>
                                                <input type="text" name="items[0][unit_price]"
                                                    class="form-control form-control-sm price" placeholder="Nilai satuan"
                                                    min="0" step="0.01" required>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-danger btn-sm remove-item rounded-partner">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" id="add-item" class="btn btn-primary btn-sm rounded-partner"><i
                            class="fas fa-plus"></i></button>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary rounded-partner">Apply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Report-->
    @foreach ($reports as $report)
        <div class="modal fade" id="reportModal{{ $report->id }}" tabindex="-1" aria-labelledby="reportModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reportModalLabel">Laporan Pertanggungjawaban Pengajuan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('application.report', $report->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card rounded-partner">
                                        <div class="card-body">
                                            <h6><strong>{{ $report->title }}</strong></h6>
                                            @if ($report->reason_reject_report)
                                                <div class="px-2">
                                                    <label class="mb-0 form-label col-form-label-sm">
                                                        Laporan Ditolak Karena:
                                                    </label>
                                                    <div class="d-block">
                                                        <p>{{ $report->reason_reject_report }}</p>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="row px-3 mb-3">
                                                <label class="mb-0 form-label col-form-label-sm">Bukti Pembayaran <small
                                                        class="text-danger">*Gambar atau file PDF</small></label>
                                                <input type="file" name="report_file"
                                                    class="form-ontrol form-control-sm muted"
                                                    accept="image/jpeg, image/png, image/jpg, image/webp, application/pdf">
                                            </div>
                                            <table class="table table-bordered" id="items-table">
                                                <thead>
                                                    <tr>
                                                        <th>Rincian Item</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($report->items as $item)
                                                        <tr>
                                                            <td>
                                                                <h6 class="mb-0 form-label col-form-label-sm"><strong>Nama
                                                                        item</strong></h6>
                                                                {{ $item->item_name }}
                                                                <div class="row mt-3">
                                                                    <div class="col-6">
                                                                        <label
                                                                            class="mb-0 form-label col-form-label-sm">Diajukan</label>
                                                                        <input type="text"
                                                                            class="form-ontrol form-control-sm muted"
                                                                            value="{{ formatRupiah($item->total_price) }}"
                                                                            disabled>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <label for="actual_amounts[{{ $item->id }}]"
                                                                            class="mb-0 form-label col-form-label-sm">Terpakai</label>
                                                                        <input type="text"
                                                                            name="actual_amounts[{{ $item->id }}]"
                                                                            class="form-control form-control-sm price_report"
                                                                            placeholder="Tulis Nominal" min="0"
                                                                            step="0.01"
                                                                            value="{{ old('actual_amounts.' . $item->id, $item->actual_amount) }}"
                                                                            required>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-sm btn-primary rounded-partner">Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <!-- DataTables  & Plugins -->
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
            //Initialize Select2 Elements
            $('.department').select2({
                placeholder: "Pilih Divisi",
                allowClear: true,
                dropdownParent: $('#addApplication')
            })
            $('.category').select2({
                placeholder: "Pilih Kategori",
                allowClear: true,
                dropdownParent: $('#addApplication')
            })

            $('.price_report').inputmask({
                alias: 'numeric',
                prefix: 'Rp',
                digits: 0,
                groupSeparator: '.',
                autoGroup: true,
                removeMaskOnSubmit: true,
                rightAlign: false
            });

            $('#myexpenseTable').DataTable({
                "paging": true,
                'processing': true,
                "searching": false,
                "info": true,
                "scrollX": true,
                "headerScroll": true,
                "order": [],
                "columnDefs": [{
                    "orderable": true,
                }]
            });

            $('#reportTable').DataTable({
                "paging": true,
                'processing': true,
                "searching": false,
                "info": true,
                "scrollX": true,
                "headerScroll": true,
                "order": [],
                "columnDefs": [{
                    "orderable": true,
                }]
            });

            $('#category').on('change', function() {
                let selected = $(this).val();
                let parts = selected.split('#');
                let id = parts[0];
                let type = parts[1];

                $('#remaining-balance').hide();
                $('#remaining-amount').text('-');

                if (type === 'project') {
                    $('#costCentersProjectWrapper').removeClass('d-none');

                    $.ajax({
                        url: '{{ route('cost-center.departments.projects.budget-plan.json', ':id') }}'
                            .replace(':id', id),
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            let $select = $('#project_cost_center');
                            $select.prop('required', true).empty();
                            $select.append('<option value="">Pilih Cost Center</option>');

                            $.each(response.data.cost_centers, function(i, item) {
                                if (i > 0) {
                                    $select.append('<option value="' + item.id +
                                        '" data-remaining="' + item
                                        .amount_remaining + '">' +
                                        item.code_ref + ' - ' + item.name +
                                        '</option>');
                                }
                            });
                        }
                    });
                } else {
                    // Ambil selected option
                    let selectedOption = $(this).find('option:selected');
                    let remaining = selectedOption.data('remaining');

                    // Tampilkan sisa saldo
                    if (remaining !== undefined) {
                        $('#remaining-amount').text(formatCurrency(remaining));
                        $('#remaining-balance').show();
                    }

                    $('#costCenterWrapper').addClass('d-none');
                    $('#project_cost_center').prop('required', false)
                        .empty().append('<option value="">Pilih Cost Center</option>');
                }
            });

            $('#project_cost_center').on('change', function() {
                let remaining = $(this).find('option:selected').data('remaining');

                if (remaining !== undefined) {
                    $('#remaining-amount').text(formatCurrency(remaining));
                    $('#remaining-balance').show();
                } else {
                    $('#remaining-balance').hide();
                }
            });

        });

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        }

        function rekening_saya() {
            $('#virtual_account,#rekening_lain').hide();
            //false
            $('#lain1,#lain2,#lain3')
                .prop('required', false);
            $('#va1,#va2')
                .prop('required', false);
        }

        function rekening_lain() {
            $('#rekening_lain').removeClass("d-none").show();
            $('#virtual_account').hide();
            //true
            $('#lain1,#lain2,#lain3')
                .prop('required', true);
            //false
            $('#va1,#va2')
                .prop('required', false);
        }

        function virtual_account() {
            $('#virtual_account').removeClass("d-none").show();
            $('#rekening_lain').hide();
            //false
            $('#lain1,#lain2,#lain3')
                .prop('required', false);
            //true
            $('#va1,#va2')
                .prop('required', true);
        }
    </script>

    <script>
        $(document).ready(function() {
            $('.price').inputmask({
                alias: 'numeric',
                prefix: 'Rp',
                digits: 0,
                groupSeparator: '.',
                autoGroup: true,
                removeMaskOnSubmit: true,
                rightAlign: false
            });

            let itemIndex = 1;

            // Tambahkan baris baru
            $('#add-item').click(function() {
                const newRow = `
                    <tr>
                        <td>
                            <label for="title" class="mb-0 form-label col-form-label-sm">Nama
                                Item</label>
                            <input type="text" name="items[${itemIndex}][item_name]" class="form-control form-control-sm" placeholder="Tulis nama item">
                            <div class="row">
                                <div class="col-4">
                                    <label for="title"
                                        class="mb-0 form-label col-form-label-sm">Jumlah</label>
                                    <input type="number" name="items[${itemIndex}][quantity]" class="form-control form-control-sm" placeholder="Tulis jumlah" min="1" value="1">
                                </div>
                                <div class="col-8">
                                    <label for="title" class="mb-0 form-label col-form-label-sm">Nilai
                                        Satuan</label>
                                    <input type="text" name="items[${itemIndex}][unit_price]" class="form-control form-control-sm price" placeholder="Tulis nilai satuan" min="0" step="0.01">
                                </div>
                            </div>
                        </td>
                        <td class="text-center align-middle">
                            <button type="button" class="btn btn-danger btn-sm rounded-partner remove-item"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                $('#items-table tbody').append(newRow);
                $('.price').inputmask({
                    alias: 'numeric',
                    prefix: 'Rp',
                    digits: 0,
                    groupSeparator: '.',
                    autoGroup: true,
                    removeMaskOnSubmit: true,
                    rightAlign: false
                });
                itemIndex++;
                calculateTotalAndCompare();
            });

            // Hapus baris
            $(document).on('click', '.remove-item', function() {
                $(this).closest('tr').remove();
                calculateTotalAndCompare();
            });

            /**
             * * Hitung total yang diajukan
             * */
            $(document).on('keyup change', 'input[name*="[quantity]"], input[name*="[unit_price]"]', function() {
                calculateTotalAndCompare();
            });

            function parseRupiahToNumber(value) {
                if (!value) return 0;
                return parseFloat(value.replace(/[Rp. ]/g, '').replace(',', '.')) || 0;
            }

            function calculateTotalAndCompare() {
                let total = 0;

                $('#items-table tbody tr').each(function() {
                    let qty = parseFloat($(this).find('input[name*="[quantity]"]').val()) || 0;
                    let unitPriceRaw = $(this).find('input[name*="[unit_price]"]').val();
                    let unitPrice = parseRupiahToNumber(unitPriceRaw);
                    total += qty * unitPrice;
                });

                let remainingRaw = $('#remaining-amount').text();
                let remaining = parseRupiahToNumber(remainingRaw);

                if (remaining > 0 && total > remaining) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'warning',
                        title: 'Total pengajuan melebihi sisa saldo!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            }

            $('#formApplication').submit(function(e) {
                $.LoadingOverlay("show");
            });
        });
    </script>
@endpush
