@extends('layouts.admin')

@section('title')
    Approval
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
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Approval</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item">Finance</li>
                        <li class="breadcrumb-item active"><strong>Approval</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid text-sm">
            <div class="row">
                <!-- Tabel approval manajer-->
                @if (auth()->user()->role_id == 3)
                    <div class="col-12">
                        <div class="card card-outline rounded-partner card-warning">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <h3 class="card-title">Pending Approval</h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            <span class="badge badge-warning">Pending :
                                                {{ count($managerRequests) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <div class="mb-4">
                                    <button type="button" class="btn btn-success btn-sm rounded-partner"
                                        onclick="submitBulkAction('Menyetujui Masal')">
                                        <i class="fa fa-check"></i> Approve Selected
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm rounded-partner"
                                        onclick="submitBulkAction('Menolak Masal')">
                                        <i class="fa fa-times"></i> Reject Selected
                                    </button>
                                    <form id="bulkActionForm" method="POST" action="{{ route('application.bulkAction') }}">
                                        @csrf
                                        <input type="hidden" name="action" id="bulkActionType">
                                    </form>
                                </div>

                                <table id="managerTable" class="table table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 3%">
                                                <input type="checkbox" id="selectAll">
                                            </th>
                                            <th style="width: 10%">
                                                Pengaju
                                            </th>
                                            <th style="width: 15%">
                                                Judul
                                            </th>
                                            <th style="width: 20%">
                                                Kategori
                                            </th>
                                            <th style="width: 17%">
                                                Cost Center
                                            </th>
                                            <th style="width: 10%">
                                                Tanggal Digunakan
                                            </th>
                                            <th style="width: 15%">
                                                Total Amount
                                            </th>
                                            <th>
                                                Referensi
                                            </th>
                                            <th style="width: 5%">
                                                Status
                                            </th>
                                            @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2 || auth()->user()->role_id == 3)
                                                <th style="width: 5%">
                                                    Aksi
                                                </th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($managerRequests as $manager)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="select-box" name="selected_ids[]"
                                                        value="{{ $manager->id }}">
                                                </td>
                                                <td>{{ $manager->user->name }} <br>
                                                    <small><strong>{{ $manager->department->name }}</strong></small>
                                                </td>
                                                <td>{{ $manager->title }}</td>
                                                <td>
                                                    @if ($manager->category == 'project')
                                                        <strong>Project</strong> {{ $manager->project->name }}
                                                    @else
                                                        <strong>General</strong>
                                                    @endif
                                                </td>
                                                <td>{{ $manager->code_ref_request }}</td>
                                                <td>{{ $manager->use_date->toFormattedDateString('d/m/y') }}</td>
                                                <td>{{ formatRupiah($manager->total_amount) }}</td>
                                                <td>
                                                    @if ($manager->reference_file)
                                                        <a href="{{ asset('storage/' . $manager->reference_file) }}"
                                                            target="_blank" class="btn btn-sm btn-info"><i class="fa fa-file-pdf"></i></a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($manager->status == 'pending')
                                                        <span class="badge badge-secondary">{{ $manager->status }}</span>
                                                    @elseif ($manager->status == 'approved')
                                                        <span class="badge badge-secondary">{{ $manager->status }}</span>
                                                    @elseif ($manager->status == 'processing')
                                                        <span class="badge badge-info">{{ $manager->status }}</span>
                                                    @elseif ($manager->status == 'report')
                                                        <span class="badge badge-warning">{{ $manager->status }}</span>
                                                    @elseif ($my_expense->status == 'rejected')
                                                        <span class="badge badge-danger">{{ $my_expense->status }}</span>
                                                    @elseif ($manager->status == 'finish')
                                                        <span class="badge badge-success">{{ $manager->status }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2 || auth()->user()->role_id == 3)
                                                        @if ($manager->status == 'pending')
                                                            <button type="button"
                                                                class="btn btn-sm btn-info rounded-partner"
                                                                data-toggle="modal"
                                                                data-target="#editStepModal{{ $manager->id }}">
                                                                <i class="fa-solid fa-eye"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tabel approval direktur-->
                @if (auth()->user()->role_id == 2)
                    <div class="col-12">
                        <div class="card card-outline rounded-partner card-warning">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <h3 class="card-title">Pending Approval</h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            <span class="badge badge-warning">Pending :
                                                {{ $directorRequests->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <div class="mb-4">
                                    <button type="button" class="btn btn-success btn-sm rounded-partner"
                                        onclick="submitBulkAction('approve')">
                                        <i class="fa fa-check"></i> Approve Selected
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm rounded-partner"
                                        onclick="submitBulkAction('reject')">
                                        <i class="fa fa-times"></i> Reject Selected
                                    </button>
                                    <form id="bulkActionForm" method="POST"
                                        action="{{ route('application.bulkAction') }}">
                                        @csrf
                                        <input type="hidden" name="action" id="bulkActionType">
                                    </form>
                                </div>

                                <table id="direkturTable" class="table table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 2%">
                                                <input type="checkbox" id="selectAll">
                                            </th>
                                            <th style="width: 10%">
                                                Pengaju
                                            </th>
                                            <th style="width: 20%">
                                                Judul
                                            </th>
                                            <th style="width: 15%">
                                                Kategori
                                            </th>
                                            <th style="width: 17%">
                                                Cost Center
                                            </th>
                                            <th style="width: 10%">
                                                Tanggal Digunakan
                                            </th>
                                            <th style="width: 15%">
                                                Nominal Diajukan
                                            </th>
                                            <th style="width: 5%">
                                                Referensi
                                            </th>
                                            <th style="width: 5%">
                                                Status
                                            </th>
                                            @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2 || auth()->user()->role_id == 3)
                                                <th style="width: 5%">
                                                    Aksi
                                                </th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($directorRequests as $direktur)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="select-box" name="selected_ids[]"
                                                        value="{{ $direktur->id }}">
                                                </td>
                                                <td>{{ $direktur->user->name }} <br>
                                                    <small><strong>{{ $direktur->department->name }}</strong></small>
                                                </td>
                                                <td>{{ $direktur->title }}</td>
                                                <td>
                                                    @if ($direktur->category == 'project')
                                                        <strong>Project</strong> {{ $direktur->project->name }}
                                                    @else
                                                        <strong>General</strong>
                                                    @endif
                                                </td>
                                                <td>{{ $direktur->costCenter?->code_ref }}</td>
                                                <td>{{ $direktur->use_date->toFormattedDateString('d/m/y') }}</td>
                                                <td>{{ formatRupiah($direktur->total_amount) }}</td>
                                                <td>
                                                    @if ($direktur->reference_file)
                                                        <a href="{{ asset('storage/' . $direktur->reference_file) }}"
                                                            target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fa fa-file-pdf"></i>
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($direktur->status == 'pending')
                                                        <span class="badge badge-secondary">{{ $direktur->status }}</span>
                                                    @elseif ($direktur->status == 'approved')
                                                        <span class="badge badge-secondary">{{ $direktur->status }}</span>
                                                    @elseif ($direktur->status == 'processing')
                                                        <span class="badge badge-info">{{ $direktur->status }}</span>
                                                    @elseif ($direktur->status == 'report')
                                                        <span class="badge badge-warning">{{ $direktur->status }}</span>
                                                    @elseif ($direktur->status == 'rejected')
                                                        <span class="badge badge-danger">{{ $my_expense->status }}</span>
                                                    @elseif ($direktur->status == 'finish')
                                                        <span class="badge badge-success">{{ $direktur->status }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($direktur->status == 'pending')
                                                        <button type="button" class="btn btn-sm btn-info rounded-partner"
                                                            data-toggle="modal"
                                                            data-target="#editStepModal{{ $direktur->id }}">
                                                            <i class="fa-solid fa-eye"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Tabel general & proses finance & bypass approval admin-->
                @if (auth()->user()->role_id == 1 || (auth()->user()->role_id == 2 || auth()->user()->department_id == 8))
                    <div class="col-12">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <h3 class="card-title">Seluruh Pengajuan</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <table id="allTable" class="table table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 15%">
                                                Pengaju
                                            </th>
                                            <th style="width: {{  auth()->user()->role_id == 2 ? '25%' : '10%' }}">
                                                Judul
                                            </th>
                                            <th style="width: 15%">
                                                Kategori
                                            </th>
                                            <th style="width: {{  auth()->user()->role_id == 2 ? '20%' : '15%' }}">
                                                Kode Transaksi
                                            </th>
                                            <th style="width: 10%">
                                                Tanggal Digunakan
                                            </th>
                                            <th style="width: 10%">
                                                Nominal Diajukan
                                            </th>
                                            <th style="width: 5%">
                                                Status
                                            </th>
                                            <th style="width: 5%">
                                                Referensi
                                            </th>
                                            <th style="width: 5%">
                                                Report
                                            </th>
                                            @if (auth()->user()->role_id == 1 || auth()->user()->department_id == 8)
                                                <th style="width: 5%">
                                                    Aksi
                                                </th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($all_expenses as $all_expense)
                                            <tr>
                                                <td>{{ $all_expense->user->name }} <br>
                                                    <small><strong>{{ $all_expense->department->name }}</strong></small>
                                                </td>
                                                <td>{{ $all_expense->title }}</td>
                                                <td>
                                                    @if ($all_expense->category == 'project')
                                                        <strong>Project</strong> {{ $all_expense->project->name }}
                                                    @else
                                                        <strong>General</strong>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $all_expense->code_ref_request}}
                                                </td>
                                                <td>{{ $all_expense->use_date->toFormattedDateString('d/m/y') }}</td>
                                                <td>{{ formatRupiah($all_expense->total_amount) }}</td>
                                                <td>
                                                    @if ($all_expense->status == 'pending')
                                                        <span
                                                            class="badge badge-secondary">{{ $all_expense->status }}</span>
                                                    @elseif ($all_expense->status == 'approved')
                                                        <span class="badge badge-dark">{{ $all_expense->status }}</span>
                                                    @elseif ($all_expense->status == 'processing')
                                                        <span class="badge badge-info">{{ $all_expense->status }}</span>
                                                    @elseif ($all_expense->status == 'report')
                                                        <span
                                                            class="badge badge-warning">{{ $all_expense->status }}</span>
                                                    @elseif ($all_expense->status == 'checking')
                                                        <span class="badge"
                                                            style="background-color: #ee00ff">{{ $all_expense->status }}</span>
                                                    @elseif ($all_expense->status == 'rejected')
                                                        <span class="badge badge-danger">{{ $all_expense->status }}</span>
                                                    @elseif ($all_expense->status == 'finish')
                                                        <span
                                                            class="badge badge-success">{{ $all_expense->status }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($all_expense->reference_file)
                                                        <a class="btn btn-sm btn-info"
                                                            href="{{ asset('storage/' . $all_expense->reference_file) }}"
                                                            target="_blank"><i class="fa-solid fa-file-pdf"></i></a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($all_expense->report_file)
                                                        <a class="btn btn-sm btn-danger rounded-partner"
                                                            href="{{ asset('storage/' . $all_expense->report_file) }}"
                                                            target="_blank"><i class="fa-solid fa-file-pdf"></i></a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                @if (auth()->user()->role_id == 1|| auth()->user()->department_id == 8)
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-info rounded-partner"
                                                            data-toggle="modal"
                                                            data-target="#editStepModal{{ $all_expense->id }}">
                                                            <i class="fa-solid fa-eye"></i>
                                                        </button>
                                                        @if (auth()->user()->role_id == 1)
                                                            @if ($all_expense->status == 'pending')
                                                                <button class="btn btn-sm btn-success rounded-partner"
                                                                    onclick="bypassExpense({{ $all_expense->id }})"><i
                                                                        class="fa-solid fa-check"></i></button>
                                                                <form id="bypass-form-{{ $all_expense->id }}"
                                                                    action="{{ route('application.approve', $all_expense->id) }}"
                                                                    method="POST" style="display: none;">
                                                                    @csrf
                                                                    @method('PUT')
                                                                </form>
                                                            @endif

                                                            <button class="btn btn-sm btn-danger rounded-partner"
                                                                onclick="deleteExpense({{ $all_expense->id }})"><i
                                                                    class="fa-solid fa-trash"></i></button>
                                                            <form id="delete-form-{{ $all_expense->id }}"
                                                                action="{{ route('application.destroy', $all_expense->id) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @endif
                                                        @if (auth()->user()->department_id == 8 && $all_expense->status == 'processing')
                                                            <button class="btn btn-sm btn-success rounded-partner"
                                                                onclick="processExpense({{ $all_expense->id }})"><i
                                                                    class="fa-solid fa-check"></i></button>
                                                            <form id="process-form-{{ $all_expense->id }}"
                                                                action="{{ route('application.process', $all_expense->id) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                                @method('PUT')
                                                            </form>
                                                        @endif
                                                        @if (auth()->user()->department_id == 8 && $all_expense->status == 'checking')
                                                            <button class="btn btn-sm btn-success rounded-circle"
                                                                onclick="approveCheckExpense({{ $all_expense->id }})"><i
                                                                    class="fa-solid fa-check"></i></button>
                                                            <form id="approve-check-form-{{ $all_expense->id }}"
                                                                action="{{ route('application.checking', $all_expense->id) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                                @method('PUT')
                                                            </form>
                                                        @endif
                                                        @if (auth()->user()->department_id == 8 && $all_expense->status == 'checking')
                                                            <button class="btn btn-sm btn-danger rounded-circle"
                                                                onclick="rejectCheckExpense({{ $all_expense->id }})"><i
                                                                    class="fa-solid fa-x"></i></button>
                                                            <form id="reject-check-form-{{ $all_expense->id }}"
                                                                action="{{ route('application.checking', $all_expense->id) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                                @method('PUT')
                                                            </form>
                                                        @endif
                                                        @if ($all_expense->status == 'finish')
                                                            <a href="{{ route('application.pdf', $all_expense->id) }}"
                                                                class="btn btn-sm btn-info rounded-partner m-1"
                                                                target="_blank">
                                                                <i class="fa-regular fa-file-pdf"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Modal Manager Approval-->
    @foreach ($managerRequests as $manager)
        <!-- Modal Show Approval-->
        <div class="modal fade text-sm" id="editStepModal{{ $manager->id }}" tabindex="-1"
            aria-labelledby="editStepModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStepModalLabel">Approval Pengajuan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5><strong>Informasi Pengaju</strong></h5>
                                        <hr>
                                        <h6><strong>Nama</strong></h6>
                                        {{ $manager->user->name }}
                                        <hr>
                                        <h6><strong>Divisi</strong></h6>
                                        {{ $manager->department->name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5><strong>Jenis Pengajuan</strong></h5>
                                        <hr>
                                        <h6><strong>Kategori</strong></h6>
                                        @if ($manager->category == 'project')
                                            <p><strong>Project</strong> {{ $manager->project->name }}</p>
                                        @else
                                            <p><strong>General</strong></p>
                                        @endif
                                        <hr>
                                        <h6><strong>Tanggal Digunakan</strong></h6>
                                        {{ $manager->use_date->toFormattedDateString('d/m/y') }}
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-12 col-md-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5><strong>Informasi Rekening</strong></h5>
                                        <hr>
                                        <h6><strong>Bank Name</strong></h6>
                                        {{ $manager->bank_name }}
                                        <hr>
                                        <h6><strong>Account Number</strong></h6>
                                        {{ $manager->account_number }}
                                        <hr>
                                        <h6><strong>Account Holder Name</strong></h6>
                                        {{ $manager->account_holder_name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5><strong>Target Cost Center</strong></h5>
                                        <hr>
                                        <h6><strong>Nama</strong></h6>
                                        {{ $manager->costCenter?->name }}
                                        <hr>
                                        <h6><strong>Kode Target</strong></h6>
                                        {{ $manager->costCenter?->code_ref }}
                                        <hr>
                                        <div class="row">
                                            <div class="col-6">
                                                <h6><strong>Limit Saldo</strong></h6>
                                                {{ formatRupiah($manager->costCenter?->amount_credit) }}
                                            </div>
                                            <div class="col-6">
                                                <h6><strong>Saldo Tersisa</strong></h6>
                                                {{ formatRupiah($manager->costCenter?->amount_remaining) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5><strong>Pengajuan: {{ $manager->title }}</strong></h5>
                                        <div class="col-12 mb-3 px-0">
                                            <label for="codeRefRequest" class="mb-0 form-label col-form-label-sm">Kode
                                                Transaksi</label>
                                            <input type="text" id="codeRefRequest"
                                                class="form-control form-control-sm"
                                                value="{{ $manager->code_ref_request }}" readonly="readonly">
                                        </div>
                                        <table class="table table-bordered" id="items-table">
                                            <thead>
                                                <tr>
                                                    <th>Nama Item</th>
                                                    <th>Qty</th>
                                                    <th>Harga</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $actual_amount_manager = 0;
                                                @endphp
                                                @foreach ($manager->items as $item)
                                                    <tr>
                                                        <td>{{ $item->item_name }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ formatRupiah($item->unit_price) }}</td>
                                                    </tr>
                                                    @php
                                                        $actual_amount_manager += $item->actual_amount;
                                                    @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="row mt-3 px-3">
                                            <div class="col-6 text-left">
                                                <strong>
                                                    Total = {{ formatRupiah($manager->total_amount) }}
                                                </strong>
                                            </div>
                                            <div class="col-6 text-right">
                                                <strong>
                                                    Terpakai = {{ formatRupiah($actual_amount_manager) }}
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-danger rounded-partner"
                            onclick="closeModalAndReject({{ $manager->id }})">Reject</button>
                        <form id="reject-form-{{ $manager->id }}"
                            action="{{ route('application.reject', $manager->id) }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('PUT')
                        </form>

                        <button class="btn btn-sm btn-success rounded-partner"
                            onclick="approveExpense({{ $manager->id }})">Approve</button>
                        <form id="approve-form-{{ $manager->id }}"
                            action="{{ route('application.approve', $manager->id) }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('PUT')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal Director Approval-->
    @foreach ($directorRequests as $direktur)
        <!-- Modal Show Approval-->
        <div class="modal fade text-sm" id="editStepModal{{ $direktur->id }}" tabindex="-1"
            aria-labelledby="editStepModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStepModalLabel"><strong>Approval Pengajuan</strong></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5><strong>Informasi Pengaju</strong></h5>
                                        <hr>
                                        <h6><strong>Nama</strong></h6>
                                        {{ $direktur->user->name }}
                                        <hr>
                                        <h6><strong>Divisi</strong></h6>
                                        {{ $direktur->department->name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5><strong>Jenis Pengajuan</strong></h5>
                                        <hr>
                                        <h6><strong>Kategori</strong></h6>
                                        @if ($direktur->category == 'project')
                                            <p><strong>Project</strong> {{ $direktur->project->name }}</p>
                                        @else
                                            <p><strong>General</strong></p>
                                        @endif
                                        <hr>
                                        <h6><strong>Tanggal Digunakan</strong></h6>
                                        {{ $direktur->use_date->toFormattedDateString('d/m/y') }}
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-12 col-md-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5><strong>Informasi Rekening</strong></h5>
                                        <hr>
                                        <h6><strong>Bank Name</strong></h6>
                                        {{ $direktur->bank_name }}
                                        <hr>
                                        <h6><strong>Account Number</strong></h6>
                                        {{ $direktur->account_number }}
                                        <hr>
                                        <h6><strong>Account Holder Name</strong></h6>
                                        {{ $direktur->account_holder_name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5><strong>Target Cost Center</strong></h5>
                                        <hr>
                                        <h6><strong>Nama</strong></h6>
                                        {{ $direktur->costCenter?->name }}
                                        <hr>
                                        <h6><strong>Kode Target</strong></h6>
                                        {{ $direktur->costCenter?->code_ref }}
                                        <hr>
                                        <div class="row">
                                            <div class="col-6">
                                                <h6><strong>Limit Saldo</strong></h6>
                                                {{ formatRupiah($direktur->costCenter?->amount_credit) }}
                                            </div>
                                            <div class="col-6">
                                                <h6><strong>Saldo Tersisa</strong></h6>
                                                {{ formatRupiah($direktur->costCenter?->amount_remaining) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5><strong>Pengajuan: {{ $direktur->title }}</strong></h5>
                                        <div class="col-12 mb-3 px-0">
                                            <label for="codeRefRequest" class="mb-0 form-label col-form-label-sm">Kode
                                                Transaksi</label>
                                            <input type="text" id="codeRefRequest"
                                                class="form-control form-control-sm"
                                                value="{{ $direktur->code_ref_request }}" readonly="readonly">
                                        </div>
                                        <table class="table table-bordered" id="items-table">
                                            <thead>
                                                <tr>
                                                    <th>Nama Item</th>
                                                    <th>Jumlah</th>
                                                    <th>Harga</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $actual_amount_direktur = 0;
                                                @endphp
                                                @foreach ($direktur->items as $item)
                                                    <tr>
                                                        <td>{{ $item->item_name }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ formatRupiah($item->unit_price) }}</td>
                                                    </tr>
                                                    @php
                                                        $actual_amount_direktur += $item->actual_amount;
                                                    @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="row mt-3 px-3">
                                            <div class="col-6 text-left">
                                                <strong>
                                                    Total = {{ formatRupiah($direktur->total_amount) }}
                                                </strong>
                                            </div>
                                            <div class="col-6 text-right">
                                                <strong>
                                                    Terpakai = {{ formatRupiah($actual_amount_direktur) }}
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-danger rounded-partner"
                            onclick="closeModalAndReject({{ $direktur->id }})">Reject</button>
                        <form id="reject-form-{{ $direktur->id }}"
                            action="{{ route('application.reject', $direktur->id) }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('PUT')
                        </form>

                        <button class="btn btn-sm btn-success rounded-partner"
                            onclick="approveExpense({{ $direktur->id }})">Approve</button>
                        <form id="approve-form-{{ $direktur->id }}"
                            action="{{ route('application.approve', $direktur->id) }}" method="POST"
                            style="display: none;">
                            @csrf
                            @method('PUT')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal Finance-->
    @foreach ($all_expenses as $all_expense)
        <!-- Modal Show Approval-->
        <div class="modal fade text-sm" id="editStepModal{{ $all_expense->id }}" tabindex="-1"
            aria-labelledby="editStepModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStepModalLabel">Approval Pengajuan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5><strong>Informasi Pengaju</strong></h5>
                                        <hr>
                                        <h6><strong>Nama</strong></h6>
                                        {{ $all_expense->user->name }}
                                        <hr>
                                        <h6><strong>Divisi</strong></h6>
                                        {{ $all_expense->department->name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5><strong>Jenis Pengajuan</strong></h5>
                                        <hr>
                                        <h6><strong>Kategori</strong></h6>
                                        @if ($all_expense->category == 'project')
                                            <p><strong>Project</strong> {{ $all_expense->project->name }}</p>
                                        @else
                                            <p><strong>General</strong></p>
                                        @endif
                                        <hr>
                                        <h6><strong>Tanggal Digunakan</strong></h6>
                                        {{ $all_expense->use_date->toFormattedDateString('d/m/y') }}
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-12 col-md-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5><strong>Informasi Rekening</strong></h5>
                                        <hr>
                                        <h6><strong>Bank Name</strong></h6>
                                        {{ $all_expense->bank_name }}
                                        <hr>
                                        <h6><strong>Account Number</strong></h6>
                                        {{ $all_expense->account_number }}
                                        <hr>
                                        <h6><strong>Account Holder Name</strong></h6>
                                        {{ $all_expense->account_holder_name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5><strong>Target Cost Center</strong></h5>
                                        <hr>
                                        <h6><strong>Nama</strong></h6>
                                        {{ $all_expense->costCenter?->name }}
                                        <hr>
                                        <h6><strong>Kode Target</strong></h6>
                                        {{ $all_expense->costCenter?->code_ref }}
                                        <hr>
                                        <div class="row">
                                            <div class="col-6">
                                                <h6><strong>Limit Saldo</strong></h6>
                                                {{ formatRupiah($all_expense->costCenter?->amount_credit) }}
                                            </div>
                                            <div class="col-6">
                                                <h6><strong>Saldo Tersisa</strong></h6>
                                                {{ formatRupiah($all_expense->costCenter?->amount_remaining) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5><strong>Pengajuan: {{ $all_expense->title }}</strong></h5>
                                        <div class="col-12 mb-3 px-0">
                                            <label for="codeRefRequest" class="mb-0 form-label col-form-label-sm">Kode
                                                Transaksi</label>
                                            <input type="text" id="codeRefRequest"
                                                class="form-control form-control-sm"
                                                value="{{ $all_expense->code_ref_request }}" readonly="readonly">
                                        </div>
                                        <table class="table table-bordered" id="items-table">
                                            <thead>
                                                <tr>
                                                    <th>Nama Item</th>
                                                    <th>Jumlah</th>
                                                    <th>Harga</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $actual_amount = 0;
                                                @endphp
                                                @foreach ($all_expense->items as $item)
                                                    <tr>
                                                        <td>{{ $item->item_name }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ formatRupiah($item->unit_price) }}</td>
                                                    </tr>
                                                    @php
                                                        $actual_amount += $item->actual_amount;
                                                    @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="row mt-3 px-3">
                                            <div class="col-6 text-left">
                                                <strong>
                                                    Total = {{ formatRupiah($all_expense->total_amount) }}
                                                </strong>
                                            </div>
                                            <div class="col-6 text-right">
                                                <strong>
                                                    Terpakai = {{ formatRupiah($actual_amount) }}
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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

    <script type="text/javascript">
        $(function() {
            $('#managerTable').DataTable({
                "paging": true,
                'processing': true,
                "searching": false,
                "ordering": false,
                "info": true,
                "scrollX": true,
                "order": [],
                "columnDefs": [{
                    "orderable": true,
                }]
            });
            $('#direkturTable').DataTable({
                "paging": true,
                'processing': true,
                "searching": false,
                "ordering": false,
                "info": true,
                "scrollX": true,
                "order": [],
                "columnDefs": [{
                    "orderable": true,
                }]
            });
            $('#allTable').DataTable({
                "paging": true,
                'processing': true,
                "searching": true,
                "ordering": false,
                "info": true,
                "scrollX": true,
                "order": [],
                "columnDefs": [{
                    "orderable": true,
                }]
            });
        });

        function closeModalAndReject(id) {
            // Hapus fokus dari tombol agar tidak menyebabkan error aria-hidden
            document.activeElement.blur();

            const modal = $('#editStepModal' + id);

            // Tunggu sampai modal selesai ditutup
            modal.one('hidden.bs.modal', function() {
                rejectExpense(id);
            });

            modal.modal('hide');
        }

        function rejectExpense(id) {
            Swal.fire({
                title: 'Apa Anda Yakin?',
                text: 'Mohon masukan alasan penolakan.',
                icon: 'warning',
                input: 'textarea',
                inputPlaceholder: 'Tulis alasan penolakan disini...',
                inputAttributes: {
                    'aria-label': 'Rejection reason'
                },
                inputValidator: (value) => {
                    if (!value) {
                        return 'Anda harus menulis alasan penolakan!'
                    }
                },
                confirmButtonColor: '#d33',
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal',
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById('reject-form-' + id);
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'reason';
                    input.value = result.value;
                    form.appendChild(input);
                    form.submit();
                }
            });
        }

        function deleteExpense(id) {
            Swal.fire({
                title: 'Apa Anda Yakin?',
                text: 'Anda akan menolak pengajuan ini!',
                icon: 'warning',
                confirmButtonColor: '#d33',
                confirmButtonText: 'Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    document.getElementById('delete-form-' + id).submit();
                } else if (
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    swal(
                        'Cancelled',
                        'Your data is safe !',
                        'error'
                    )
                }
            })
        }

        function rejectCheckExpense(id) {
            Swal.fire({
                title: 'Apa Anda Yakin?',
                text: 'Mohon masukan alasan penolakan.',
                icon: 'warning',
                input: 'textarea',
                inputPlaceholder: 'Tulis alasan penolakan disini...',
                inputAttributes: {
                    'aria-label': 'Rejection reason'
                },
                inputValidator: (value) => {
                    if (!value) {
                        return 'Anda harus menulis alasan penolakan!'
                    }
                },
                confirmButtonColor: '#d33',
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal',
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    let form = document.getElementById('reject-check-form-' + id);
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'reason';
                    input.value = result.value;
                    form.appendChild(input);
                    form.submit();
                }
            });
        }

        function approveCheckExpense(id) {
            Swal.fire({
                title: 'Apa Anda Yakin?',
                text: 'Anda akan menyetujui laporan dari pengajuan ini!',
                icon: 'warning',
                confirmButtonColor: '#5cb85c',
                confirmButtonText: 'Setujui',
                cancelButtonText: 'Batal',
                showCancelButton: true
            }).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    document.getElementById('approve-check-form-' + id).submit();
                } else if (
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    swal(
                        'Cancelled',
                        'Pengajuan tidak jadi disetujui!',
                        'info'
                    )
                }
            })
        }

        function approveExpense(id) {
            Swal.fire({
                title: 'Apa Anda Yakin?',
                text: 'Anda akan menyetujui pengajuan ini!',
                icon: 'warning',
                confirmButtonColor: '#5cb85c',
                confirmButtonText: 'Setujui',
                cancelButtonText: 'Batal',
                showCancelButton: true
            }).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    document.getElementById('approve-form-' + id).submit();
                } else if (
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    swal(
                        'Cancelled',
                        'Your data is safe !',
                        'error'
                    )
                }
            })
        }

        function bypassExpense(id) {
            Swal.fire({
                title: 'Apa Anda Yakin?',
                text: 'Anda akan menyetujui pengajuan ini!',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#5cb85c',
                confirmButtonText: 'Setujui (bypass)',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    document.getElementById('bypass-form-' + id).submit();
                } else if (
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    swal(
                        'Cancelled',
                        'Your data is safe !',
                        'error'
                    )
                }
            })
        }

        function processExpense(id) {
            Swal.fire({
                title: 'Apa Anda Yakin?',
                text: 'Anda akan memproses pengajuan ini!',
                icon: 'warning',
                confirmButtonColor: '#5cb85c',
                confirmButtonText: 'Proses',
                showCancelButton: true,
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    document.getElementById('process-form-' + id).submit();
                } else if (
                    result.dismiss === swal.DismissReason.cancel
                ) {
                    swal(
                        'Cancelled',
                        'Your data is safe !',
                        'error'
                    )
                }
            })
        }

        document.getElementById('selectAll').addEventListener('click', function() {
            let checkboxes = document.querySelectorAll('.select-box');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        function submitBulkAction(actionType) {
            const selectedCheckboxes = document.querySelectorAll('.select-box:checked');
            if (selectedCheckboxes.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: 'Silahkan pilih pengajuan terlebih dahulu',
                    confirmButtonColor: '#5cb85c',
                    confirmButtonText: 'Oke',
                });
                return;
            }

            Swal.fire({
                title: `Apa Anda Yakin untuk ${actionType}?`,
                text: `Anda akan ${actionType == 'Menyetujui Masal' ? 'menyetujui' : 'menolak'} menyetujui semua pengajuan yang dipilih!`,
                icon: 'warning',
                confirmButtonColor: '#5cb85c',
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal',
                showCancelButton: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('bulkActionForm');
                    form.innerHTML = ''; // Clear existing input

                    // CSRF
                    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = token;
                    form.appendChild(csrf);

                    // Action type
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = actionType;
                    form.appendChild(actionInput);

                    // Selected IDs
                    selectedCheckboxes.forEach(cb => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'selected_ids[]';
                        input.value = cb.value;
                        form.appendChild(input);
                    });

                    form.submit();
                }
            });
        }
    </script>
@endpush
