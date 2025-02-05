@extends('layouts.admin')

@section('title')
    Application
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
                    <h1>Application</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item">Finance</li>
                        <li class="breadcrumb-item active"><strong>Application</strong></li>
                    </ol>
                </div>
                <div class="col-6 text-right my-auto">
                    <a type="button" class="btn btn-primary rounded-partner py-2" data-toggle="modal"
                        data-target="#addApplication"><i class="fas fa-plus"></i> Create Application</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid text-sm">
            <div class="row">
                <!-- Tabel on going application-->
                <div class="col-12 col-md-6">
                    <div class="card card-outline rounded-partner card-primary collapsed-card">
                        <div class="card-header">
                            <div class="card-title">
                                <h6>My Application</h6>
                            </div>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="myexpenseTable" class="table table-bordered text-nowrap">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 70%">
                                            Title
                                        </th>
                                        <th style="width: 10%">
                                            Category
                                        </th>
                                        <th style="width: 5%">
                                            Usage Date
                                        </th>
                                        <th style="width: 10%">
                                            Total Amount
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
                                            <td>{{ $my_expense->category }}</td>
                                            <td>{{ $my_expense->use_date->format('d/m/y') }}</td>
                                            <td>{{ formatRupiah($my_expense->total_amount) }}</td>
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
                    <div class="card card-outline rounded-partner card-primary collapsed-card">
                        <div class="card-header">
                            <div class="card-title">
                                <h6>Report Application</h6>
                            </div>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-tool" data-card-widget="remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="reportTable" class="table table-bordered text-nowrap">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 65%">
                                            Title
                                        </th>
                                        <th style="width: 10%">
                                            Category
                                        </th>
                                        <th style="width: 5%">
                                            Usage Date
                                        </th>
                                        <th style="width: 10%">
                                            Total Amount
                                        </th>
                                        <th style="width: 5%">
                                            Status
                                        </th>
                                        <th style="width: 5%">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reports as $report)
                                        <tr>
                                            <td>{{ $report->title }}</td>
                                            <td>{{ $report->category }}</td>
                                            <td>{{ $report->use_date->toFormattedDateString('d/m/y') }}</td>
                                            <td>{{ formatRupiah($report->total_amount) }}</td>
                                            <td>
                                                @if ($report->status == 'report')
                                                    <span class="badge badge-warning">{{ $report->status }}</span>
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
                                                @elseif ($report->status == 'finish')
                                                    <a href="{{ route('application.pdf', $report->id) }}"
                                                        class="btn btn-sm btn-info rounded-partner" target="_blank">
                                                        <i class="fa-regular fa-file-pdf"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tabel approval manajer-->
                @if (auth()->user()->role_id == 3)
                    <div class="col-12">
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <h3 class="card-title">Application Approval</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <table id="managerTable" class="table table-bordered text-nowrap">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 10%">
                                                User
                                            </th>
                                            <th style="width: 45%">
                                                Title
                                            </th>
                                            <th style="width: 10%">
                                                Category
                                            </th>
                                            <th style="width: 10%">
                                                Usage Date
                                            </th>
                                            <th style="width: 15%">
                                                Total Amount
                                            </th>
                                            <th style="width: 5%">
                                                Status
                                            </th>
                                            @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2 || auth()->user()->role_id == 3)
                                                <th style="width: 5%">
                                                    Action
                                                </th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($managerRequests as $manager)
                                            <tr>
                                                <td>{{ $manager->user->name }} <br>
                                                    <small><strong>{{ $manager->department->name }}</strong></small>
                                                </td>
                                                <td>{{ $manager->title }}</td>
                                                <td>{{ $manager->category }}</td>
                                                <td>{{ $manager->use_date->toFormattedDateString('d/m/y') }}</td>
                                                <td>{{ formatRupiah($manager->total_amount) }}</td>
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
                        <div class="card card-outline rounded-partner card-primary">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <h3 class="card-title">Application Approval</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <table id="direkturTable" class="table table-bordered text-nowrap">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 10%">
                                                User
                                            </th>
                                            <th style="width: 45%">
                                                Title
                                            </th>
                                            <th style="width: 10%">
                                                Category
                                            </th>
                                            <th style="width: 10%">
                                                Usage Date
                                            </th>
                                            <th style="width: 15%">
                                                Total Amount
                                            </th>
                                            <th style="width: 5%">
                                                Status
                                            </th>
                                            @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2 || auth()->user()->role_id == 3)
                                                <th style="width: 5%">
                                                    Action
                                                </th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($directorRequests as $direktur)
                                            <tr>
                                                <td>{{ $direktur->user->name }} <br>
                                                    <small><strong>{{ $direktur->department->name }}</strong></small>
                                                </td>
                                                <td class="text-wrap">{{ $direktur->title }}</td>
                                                <td>{{ $direktur->category }}</td>
                                                <td>{{ $direktur->use_date->toFormattedDateString('d/m/y') }}</td>
                                                <td>{{ formatRupiah($direktur->total_amount) }}</td>
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
                                        <h3 class="card-title">All Application</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body table-responsive">
                                <table id="allTable" class="table table-bordered text-nowrap">
                                    <thead class="table-dark">
                                        <tr>
                                            <th style="width: 10%">
                                                User
                                            </th>
                                            <th style="width: 45%">
                                                Title
                                            </th>
                                            <th style="width: 10%">
                                                Category
                                            </th>
                                            <th style="width: 10%">
                                                Usage Date
                                            </th>
                                            <th style="width: 15%">
                                                Total Amount
                                            </th>
                                            <th style="width: 5%">
                                                Status
                                            </th>
                                            @if (auth()->user()->role_id == 1 || auth()->user()->department_id == 8)
                                                <th style="width: 5%">
                                                    Action
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
                                                <td class="text-wrap">{{ $all_expense->title }}</td>
                                                <td>{{ $all_expense->category }}</td>
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
                                                    @elseif ($all_expense->status == 'rejected')
                                                        <span class="badge badge-danger">{{ $all_expense->status }}</span>
                                                    @elseif ($all_expense->status == 'finish')
                                                        <span
                                                            class="badge badge-success">{{ $all_expense->status }}</span>
                                                    @endif
                                                </td>
                                                @if (auth()->user()->role_id == 1 || auth()->user()->department_id == 8)
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
                                                        @if ($all_expense->status == 'finish')
                                                            <a href="{{ route('application.pdf', $all_expense->id) }}"
                                                                class="btn btn-sm btn-info rounded-partner"
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

    <!-- Modal Add Application-->
    <div class="modal fade" id="addApplication" tabindex="-1" aria-labelledby="addApplicationLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addApplicationLabel">Add New Application</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('application.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                        </div>

                        <div class="row w-100">
                            <div class="col-12 col-md-6">
                                <label for="user" class="mb-0 form-label col-form-label-sm">Name</label>
                                <input type="text" class="form-control @error('user') is-invalid @enderror"
                                    id="user" name="user" value="{{ auth()->user()->name }}" readonly>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="department_id" class="mb-0 form-label col-form-label-sm">Department</label>
                                <select class="form-control department" style="width: 100%;" id="department_id"
                                    name="department_id" required>
                                    <option></option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="use_date" class="mb-0 form-label col-form-label-sm">Use Date</label>
                                <input type="date" class="form-control @error('use_date') is-invalid @enderror"
                                    id="use_date" name="use_date" value="{{ old('use_date') }}" required>
                                @error('use_date')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="category" class="mb-0 form-label col-form-label-sm">Category</label>
                                <select class="form-control category" style="width: 100%;" id="category"
                                    name="category" required>
                                    <option></option>
                                    <option value="Reimbursement"
                                        {{ old('category') == 'Reimbursement' ? 'selected' : '' }}>
                                        Reimbursement
                                    </option>
                                    <option value="Maintanance" {{ old('category') == 'Maintanance' ? 'selected' : '' }}>
                                        Maintanance
                                    </option>
                                    <option value="Business Travel"
                                        {{ old('category') == 'Business Travel' ? 'selected' : '' }}>
                                        Business Travel
                                    </option>
                                    <option value="Business Travel"
                                        {{ old('category') == 'Business Travel' ? 'selected' : '' }}>
                                        Cost of Goods Sold (CoGS)
                                    </option>
                                    <option value="Taxes" {{ old('category') == 'Taxes' ? 'selected' : '' }}>
                                        Taxes
                                    </option>
                                    <option value="Marketing & Ads"
                                        {{ old('category') == 'Marketing & Ads' ? 'selected' : '' }}>
                                        Marketing & Ads
                                    </option>
                                    <option value="Office" {{ old('category') == 'Office' ? 'selected' : '' }}>
                                        Office
                                    </option>
                                    <option value="Utilities" {{ old('category') == 'Utilities' ? 'selected' : '' }}>
                                        Utilities
                                    </option>
                                    <option value="Internet Services"
                                        {{ old('category') == 'Internet Services' ? 'selected' : '' }}>
                                        Internet Services
                                    </option>
                                    <option value="Rent / Mortgage"
                                        {{ old('category') == 'Rent / Mortgage' ? 'selected' : '' }}>
                                        Rent / Mortgage
                                    </option>
                                    <option value="Training & Education"
                                        {{ old('category') == 'Training & Education' ? 'selected' : '' }}>
                                        Training & Education
                                    </option>
                                    <option value="Employee Wages"
                                        {{ old('category') == 'Employee Wages' ? 'selected' : '' }}>
                                        Employee Benefits
                                    </option>
                                    <option value="Employee Wages"
                                        {{ old('category') == 'Employee Wages' ? 'selected' : '' }}>
                                        Employee Wages
                                    </option>
                                </select>
                                @error('category')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <label for="pencairan" class="mb-0 form-label col-form-label-sm">Payment Method</label>
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
                                        placeholder="Enter bank name" autocomplete="off" value="{{ old('bank1') }}"
                                        oninput="this.value = this.value.toUpperCase()">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="rekening1" class="form-control" id="lain2"
                                        placeholder="Enter account number" autocomplete="off"
                                        value="{{ old('rekening1') }}">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="atas_nama" class="form-control" id="lain3"
                                        placeholder="Enter account holder name" autocomplete="off"
                                        value="{{ old('atas_nama') }}">
                                </div>
                            </div>
                            <div class="col-md-12 d-none" id="virtual_account">
                                <div class="form-group">
                                    <input type="text" name="bank" class="form-control" id="va1"
                                        placeholder="Enter bank name" autocomplete="off" value="{{ old('bank') }}"
                                        oninput="this.value = this.value.toUpperCase()">
                                </div>
                                <div class="form-group">
                                    <input type="text" name="rekening" class="form-control" id="va2"
                                        placeholder="Enter VA number" autocomplete="off" value="{{ old('rekening') }}">
                                </div>
                            </div>
                        </div>


                        <label for="title" class="mb-0 form-label col-form-label-sm">Title</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                            name="title" value="{{ old('title') }}" placeholder="Enter application title" required>
                        @error('title')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror

                        <label for="items" class="mb-0 form-label col-form-label-sm">Items</label>
                        <table class="table table-bordered" id="items-table">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" name="items[0][item_name]" class="form-control"
                                            placeholder="Enter item" required></td>
                                    <td><input type="number" name="items[0][quantity]" class="form-control"
                                            placeholder="Enter quantity" min="1" value="1" required></td>
                                    <td><input type="text" name="items[0][unit_price]" class="form-control price"
                                            placeholder="Enter Price" min="0" step="0.01" required></td>
                                    <td><button type="button"
                                            class="btn btn-danger btn-sm remove-item rounded-partner"><i
                                                class="fas fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" id="add-item" class="btn btn-primary btn-sm rounded-partner"><i
                                class="fas fa-plus"></i></button>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary rounded-partner">Apply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Manager Approval-->
    @foreach ($managerRequests as $manager)
        <!-- Modal Show Approval-->
        <div class="modal fade text-sm" id="editStepModal{{ $manager->id }}" tabindex="-1"
            aria-labelledby="editStepModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStepModalLabel">Approval Application</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5>Name</h5>
                                        {{ $manager->user->name }}
                                        <hr>
                                        <h5>Department</h5>
                                        {{ $manager->department->name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5>Category</h5>
                                        {{ $manager->category }}
                                        <hr>
                                        <h5>Use Date</h5>
                                        {{ $manager->use_date->toFormattedDateString('d/m/y') }}
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-lg-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5>Bank Name</h5>
                                        {{ $manager->bank_name }}
                                        <hr>
                                        <h5>Account Number</h5>
                                        {{ $manager->account_number }}
                                        <hr>
                                        <h5>Account Holder Name</h5>
                                        {{ $manager->account_holder_name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <table class="table table-bordered" id="items-table">
                                            <thead>
                                                <tr>
                                                    <th>Item Name</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($manager->items as $item)
                                                    <tr>
                                                        <td>{{ $item->item_name }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ formatRupiah($item->unit_price) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="w-100 text-center mt-3">
                                            <strong class="text-center">Total =
                                                {{ formatRupiah($manager->total_amount) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-danger rounded-partner"
                            onclick="rejectExpense({{ $manager->id }})">Reject</button>
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
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStepModalLabel">Approval Application</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5>Name</h5>
                                        {{ $direktur->user->name }}
                                        <hr>
                                        <h5>Department</h5>
                                        {{ $direktur->department->name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5>Category</h5>
                                        {{ $direktur->category }}
                                        <hr>
                                        <h5>Use Date</h5>
                                        {{ $direktur->use_date->toFormattedDateString('d/m/y') }}
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-lg-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5>Bank Name</h5>
                                        {{ $direktur->bank_name }}
                                        <hr>
                                        <h5>Account Number</h5>
                                        {{ $direktur->account_number }}
                                        <hr>
                                        <h5>Account Holder Name</h5>
                                        {{ $direktur->account_holder_name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <table class="table table-bordered" id="items-table">
                                            <thead>
                                                <tr>
                                                    <th>Item Name</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($direktur->items as $item)
                                                    <tr>
                                                        <td>{{ $item->item_name }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ formatRupiah($item->unit_price) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="w-100 text-center mt-3">
                                            <strong class="text-center">Total =
                                                {{ formatRupiah($direktur->total_amount) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-danger rounded-partner"
                            onclick="rejectExpense({{ $direktur->id }})">Reject</button>
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
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStepModalLabel">Approval Application</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5>Name</h5>
                                        {{ $all_expense->user->name }}
                                        <hr>
                                        <h5>Department</h5>
                                        {{ $all_expense->department->name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5>Category</h5>
                                        {{ $all_expense->category }}
                                        <hr>
                                        <h5>Use Date</h5>
                                        {{ $all_expense->use_date->toFormattedDateString('d/m/y') }}
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="col-lg-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <h5>Bank Name</h5>
                                        {{ $all_expense->bank_name }}
                                        <hr>
                                        <h5>Account Number</h5>
                                        {{ $all_expense->account_number }}
                                        <hr>
                                        <h5>Account Holder Name</h5>
                                        {{ $all_expense->account_holder_name }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card rounded-partner">
                                    <div class="card-body">
                                        <table class="table table-bordered" id="items-table">
                                            <thead>
                                                <tr>
                                                    <th>Item Name</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($all_expense->items as $item)
                                                    <tr>
                                                        <td>{{ $item->item_name }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ formatRupiah($item->unit_price) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="w-100 text-center mt-3">
                                            <strong class="text-center">Total =
                                                {{ formatRupiah($all_expense->total_amount) }}</strong>
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

    <!-- Modal Report-->
    @foreach ($reports as $report)
        <div class="modal fade" id="reportModal{{ $report->id }}" tabindex="-1" aria-labelledby="reportModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reportModalLabel">Report Application</h5>
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
                                            <table class="table table-bordered" id="items-table">
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th>Budget</th>
                                                        <th>Used</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($report->items as $item)
                                                        <tr>
                                                            <td>{{ $item->item_name }}</td>
                                                            <td>{{ formatRupiah($item->total_price) }}</td>
                                                            <td>
                                                                <input type="text"
                                                                    name="actual_amounts[{{ $item->id }}]"
                                                                    class="form-control price_report"
                                                                    placeholder="Enter Nominal" min="0"
                                                                    step="0.01"
                                                                    value="{{ old('actual_amounts.' . $item->id, $item->actual_amount) }}"
                                                                    required>
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


    <script type="text/javascript">
        $(function() {
            //Initialize Select2 Elements
            $('.department').select2({
                placeholder: "Select Department",
                allowClear: true,
            })
            $('.category').select2({
                placeholder: "Select Category",
                allowClear: true,
            })
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

        $(function() {
            $('#myexpenseTable').DataTable({
                "paging": true,
                'processing': true,
                "lengthChange": true,
                "searching": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "order": [],
                "columnDefs": [{
                    "orderable": true,
                }]
                // "scrollX": true,
                // width: "700px",
                // columnDefs: [{
                //     className: 'dtr-control',
                //     orderable: false,
                //     targets: -8
                // }]
            });
            $('#reportTable').DataTable({
                "paging": true,
                'processing': true,
                "lengthChange": true,
                "searching": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "order": [],
                "columnDefs": [{
                    "orderable": true,
                }]
                // "scrollX": true,
                // width: "700px",
                // columnDefs: [{
                //     className: 'dtr-control',
                //     orderable: false,
                //     targets: -8
                // }]
            });
            $('#managerTable').DataTable({
                "paging": true,
                'processing': true,
                "lengthChange": true,
                "searching": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "order": [],
                "columnDefs": [{
                    "orderable": true,
                }]
                // "scrollX": true,
                // width: "700px",
                // columnDefs: [{
                //     className: 'dtr-control',
                //     orderable: false,
                //     targets: -8
                // }]
            });
            $('#direkturTable').DataTable({
                "paging": true,
                'processing': true,
                "lengthChange": true,
                "searching": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "order": [],
                "columnDefs": [{
                    "orderable": true,
                }]
                // "scrollX": true,
                // width: "700px",
                // columnDefs: [{
                //     className: 'dtr-control',
                //     orderable: false,
                //     targets: -8
                // }]
            });
            $('#allTable').DataTable({
                "paging": true,
                'processing': true,
                "lengthChange": true,
                "searching": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "order": [],
                "columnDefs": [{
                    "orderable": true,
                }]
                // "scrollX": true,
                // width: "700px",
                // columnDefs: [{
                //     className: 'dtr-control',
                //     orderable: false,
                //     targets: -8
                // }]
            });
        });

        function rejectExpense(id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Reject'
            }).then((result) => {
                if (result.value) {
                    event.preventDefault();
                    document.getElementById('reject-form-' + id).submit();
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

        function deleteExpense(id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Delete'
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

        function approveExpense(id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#5cb85c',
                confirmButtonText: 'Approve'
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
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#5cb85c',
                confirmButtonText: 'Approve (bypass)'
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
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: false,
                confirmButtonColor: '#5cb85c',
                confirmButtonText: 'Process'
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
                        <td><input type="text" name="items[${itemIndex}][item_name]" class="form-control" placeholder="Enter item"></td>
                        <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control" placeholder="Enter quantity" min="1" value="1"></td>
                        <td><input type="text" name="items[${itemIndex}][unit_price]" class="form-control price" placeholder="Enter Price" min="0" step="0.01"></td>
                        <td><button type="button" class="btn btn-danger btn-sm rounded-partner remove-item"><i class="fas fa-trash"></i></button></td>
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
            });

            // Hapus baris
            $(document).on('click', '.remove-item', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>
@endpush
