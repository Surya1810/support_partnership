@extends('layouts.admin')

@section('title')
    Constructor Report
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
                    <h1>Constructor Report</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item">Finance</li>
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('finance.index') }}">Report</a>
                        </li>
                        <li class="breadcrumb-item active"><strong>Construction</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="card rounded-partner bg-orange">
                        <div class="card-body">
                            <h4>{{ formatRupiah($cash_balance) }}</h4>
                            <small>Kas</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card rounded-partner bg-orange">
                        <div class="card-body">
                            <h4>{{ formatRupiah($household_expense + $project_expense) }}</h4>
                            <small>Kredit</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card rounded-partner bg-orange">
                        <div class="card-body">
                            <h4>{{ formatRupiah($saldo) }}</h4>
                            <small>Saldo</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card card-outline rounded-partner card-orange">
                        <div class="card-body">
                            <p><strong>Project</strong></p>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <h4 class="text-danger"><strong>{{ formatRupiah($project_expense) }}</strong></h4>
                                    <small>pengeluaran modal</small>
                                </div>
                                <div class="col-12 col-md-6">
                                    <h4 class="text-success"><strong>{{ formatRupiah($income) }}</strong></h4>
                                    <small>pendapatan kas</small>
                                </div>
                            </div>
                            <hr>
                            <table id="projectTable" class="table table-bordered text-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 50%">
                                            Project Name
                                        </th>
                                        <th style="width: 20%">
                                            Pengeluaran
                                        </th>
                                        <th style="width: 20%">
                                            SP2D
                                        </th>
                                        <th style="width: 10%">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($projects as $project)
                                        @php
                                            $finish = $project->expense->where('status', 'finish')->pluck('id');
                                            $expense_finish = App\Models\ExpenseItem::whereIn(
                                                'expense_request_id',
                                                $finish->toArray(),
                                            )->sum('actual_amount');
                                        @endphp
                                        <tr>
                                            <td class="text-nowrap">{{ $project->name }}</td>
                                            <td>
                                                {{ formatRupiah($project->expense->where('status', 'report')->sum('total_amount') + $expense_finish) }}
                                            </td>
                                            <td>
                                                @isset($project->income)
                                                    {{ formatRupiah($project->income->where('project_id', $project->id)->where('category', 'sp2d')->sum('amount')) }}
                                                @else
                                                    Rp0
                                                @endisset
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info rounded-partner"
                                                    data-toggle="modal" data-target="#projectModal{{ $project->id }}">
                                                    <i class="fa-solid fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card card-outline rounded-partner card-orange">
                        <div class="card-body">
                            <p><strong>Pengeluaran Rumah Tangga</strong></p>
                            <h4 class="text-danger"><strong>{{ formatRupiah($household_expense) }}</strong></h4>
                            <small>current</small>
                            <hr>
                            <table id="householdTable" class="table table-bordered text-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>
                                            Date
                                        </th>
                                        <th>
                                            Description
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($households as $household)
                                        <tr>
                                            <td class="text-nowrap">{{ $household->created_at->toDateString() }}</td>
                                            <td>{{ $household->title }}<br>
                                                <strong>{{ $household->category }}</strong>
                                                <br><span class="badge badge-danger">Debit</span> -
                                                <strong>{{ formatRupiah($household->total_amount) }}</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card card-outline rounded-partner card-orange">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <p><strong>Penyusutan</strong></p>
                                </div>
                                <div class="col-6">
                                    <button type="button" class="float-right btn btn-sm btn-primary rounded-partner"
                                        data-toggle="modal" data-target="#addDebt">
                                        <i class="fas fa-plus"></i> Tambah
                                    </button>
                                </div>
                            </div>
                            <h4 class="text-danger">
                                <strong>{{ formatRupiah($debts->whereIn('category', ['development', 'debt'])->sum('amount') - $debts->where('category', 'payment')->sum('amount')) }}</strong>
                            </h4>
                            <small>all time</small>
                            <hr>
                            <table id="assetTable" class="table table-bordered text-sm">
                                <thead class="table-dark">
                                    <tr>
                                        <th>
                                            Date
                                        </th>
                                        <th>
                                            Description
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($debts as $debt)
                                        <tr>
                                            <td>{{ $debt->created_at->toDateString() }}</td>
                                            <td>{{ $debt->title }}
                                                <br>
                                                <span class="badge badge-success">Kredit</span>
                                                -
                                                <strong>{{ formatRupiah($debt->amount) }}</strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
        </div>
    </section>

    <!-- Modal Add Debt-->
    <div class="modal fade" id="addDebt" tabindex="-1" aria-labelledby="addDebtLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDebtLabel">Tambah Nilai Penyusutan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('debt.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <input type="text" name="department" hidden value="3">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <label for="title" class="mb-0 form-label col-form-label-sm">Judul</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title') }}"
                                    placeholder="Tulis judul penyusutan" required>
                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="amount" class="mb-0 form-label col-form-label-sm">Nilai Penyusutan</label>
                                <input type="text" class="form-control price @error('amount') is-invalid @enderror"
                                    id="amount" name="amount" value="{{ old('amount') }}"
                                    placeholder="Input nilai penyusutan" min="0" step="0.01" required>
                                @error('amount')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary rounded-partner">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Project-->
    @foreach ($projects as $project)
        <div class="modal fade text-sm" id="projectModal{{ $project->id }}" tabindex="-1"
            aria-labelledby="projectModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="projectModalLabel">Alur Keuangan Project</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <h5><strong>{{ $project->name }}</strong></h5>
                                <hr>
                            </div>
                            <div class="col-6">
                                <p><strong>Pengeluaran</strong></p>
                                @foreach ($project->expense as $expense)
                                    @if ($expense->status == 'report')
                                        {{ $expense->title }} <span class="badge badge-warning">Belum Report</span>
                                        <br><span class="badge badge-danger">Debit</span> -
                                        <strong>{{ formatRupiah($expense->total_amount) }}</strong>
                                        <hr>
                                    @else
                                        {{ $expense->title }} <span class="badge badge-primary">Sudah Report</span>
                                        <br><span class="badge badge-danger">Debit</span> -
                                        <strong>{{ formatRupiah($expense->items->sum('actual_amount')) }}</strong>
                                        <hr>
                                    @endif
                                @endforeach
                            </div>
                            <div class="col-6">
                                <p><strong>SP2D</strong></p>
                                @isset($project->income)
                                    <strong>SP2D</strong>
                                    <br><span class="badge badge-success">Kredit</span> -
                                    <strong>
                                        {{ formatRupiah($project->income->where('project_id', $project->id)->where('category', 'sp2d')->sum('amount')) }}
                                    </strong>
                                    <hr>
                                @endisset
                            </div>
                            <div class="col-12">
                                <table>
                                    <tbody>
                                        <tr>
                                            <td>Pengeluaran</td>
                                            <td>:</td>
                                            <td>
                                                <strong>
                                                    @php
                                                        $finish = $project->expense
                                                            ->where('status', 'finish')
                                                            ->pluck('id');
                                                        $expense_finish = App\Models\ExpenseItem::whereIn(
                                                            'expense_request_id',
                                                            $finish->toArray(),
                                                        )->sum('actual_amount');
                                                    @endphp
                                                    {{ formatRupiah($project->expense->where('status', 'report')->sum('total_amount') + $expense_finish) }}
                                                </strong>
                                            </td>
                                        <tr>
                                            <td>Pendapatan</td>
                                            <td>:</td>
                                            <td>
                                                @isset($project->income)
                                                    <strong>{{ formatRupiah($project->income->where('project_id', $project->id)->where('category', 'sp2d')->sum('amount')) }}</strong>
                                                @else
                                                    <strong>Rp0</strong>
                                                @endisset
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Profit</td>
                                            <td>:</td>
                                            <td>
                                                @isset($project->income)
                                                    {{ formatRupiah($project->income->where('project_id', $project->id)->where('category', 'sp2d')->sum('amount') - $project->expense->where('status', 'report')->sum('total_amount') + $expense_finish) }}
                                                @else
                                                    <strong>Rp0</strong>
                                                @endisset
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @isset($project->income)
                            <hr>
                            <form action="{{ route('finance.pembagian') }}" method="POST" enctype="multipart/form-data"
                                autocomplete="off">
                                @csrf
                                <p><strong>Pendistribusian Profit</strong></p>
                                <input type="text" name="project" hidden value="{{ $project->id }}">
                                <input type="text" name="department" hidden value="3">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <label for="kas" class="mb-0 form-label col-form-label-sm">Kas</label>
                                        <input type="text" class="form-control price @error('kas') is-invalid @enderror"
                                            id="kas" name="kas" value="{{ old('kas') }}"
                                            placeholder="Input nilai kas" min="0" step="0.01" required>
                                        @error('kas')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="penyusutan" class="mb-0 form-label col-form-label-sm">Penyusutan</label>
                                        <input type="text"
                                            class="form-control price @error('penyusutan') is-invalid @enderror"
                                            id="penyusutan" name="penyusutan" value="{{ old('penyusutan') }}"
                                            placeholder="Input nilai penyusutan" min="0" step="0.01" required>
                                        @error('penyusutan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <button type="submit"
                                    class="btn btn-primary rounded-partner mt-3 float-right">Simpan</button>
                            </form>
                        @endisset
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

    <script>
        $('.price').inputmask({
            alias: 'numeric',
            prefix: 'Rp',
            digits: 0,
            groupSeparator: '.',
            autoGroup: true,
            removeMaskOnSubmit: true,
            rightAlign: false
        });

        $('#projectTable').DataTable({
            "paging": true,
            'processing': true,
            "searching": false,
            "info": true,
            "scrollX": true,
            "order": [],
            "columnDefs": [{
                "orderable": true,
            }]
        });
        $('#householdTable').DataTable({
            "paging": true,
            'processing': true,
            "lengthChange": true,
            "searching": false,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "order": [],
            "columnDefs": [{
                "orderable": true,
            }]
            // "scrollX": true,
        });

        $('#assetTable').DataTable({
            "paging": true,
            'processing': true,
            "lengthChange": true,
            "searching": false,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "order": [],
            "columnDefs": [{
                "orderable": true,
            }]
            // "scrollX": true,
        });
    </script>
@endpush
