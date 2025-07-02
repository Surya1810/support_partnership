@extends('layouts.admin')

@section('title')
    Project Overview
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
                    <h1>Project - {{ $project->kode }}</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('project.index') }}">Project</a>
                        </li>
                        <li class="breadcrumb-item active"><strong>Overview</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline card-outline-tabs rounded-partner card-primary">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link rounded active" id="tabs_project"
                                        href="{{ route('project.detail', $project->kode) }}" role="tab"
                                        aria-controls="tabs_project" aria-selected="true">Overview</a>
                                </li>
                                @if ($project->status != 'Finished')
                                    <li class="nav-item">
                                        <a class="nav-link" id="tabs_task"
                                            href="{{ route('project.task', $project->kode) }}" role="tab"
                                            aria-controls="tabs_task" aria-selected="false">Task Step</a>
                                    </li>
                                @endif
                                <li class="nav-item">
                                    <a class="nav-link" id="tabs_review"
                                        href="{{ route('project.review', $project->kode) }}" role="tab"
                                        aria-controls="tabs_review" aria-selected="false">Review</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="custom-tabs-four-tabContent">
                                <div class="tab-pane fade show active" id="project" role="tabpanel"
                                    aria-labelledby="tabs_project">
                                    <h5>Project - <strong>{{ $project->name }}</strong></h5>
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <div class="card rounded-partner">
                                                <div class="card-body">
                                                    <h6>Nama Project</h6>
                                                    {{ $project->name }}
                                                    <hr>
                                                    <h6>Nama Klien</h6>
                                                    {{ $project->client->name }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="card rounded-partner">
                                                <div class="card-body">
                                                    <b>PIC:</b> {{ $project->pic->name }}
                                                    <br>
                                                    <b>Divisi:</b> {{ $project->department->name }}
                                                    <hr>
                                                    <h5>Team</h5>
                                                    @foreach ($team as $data)
                                                        <span class="badge badge-dark">{{ $data->username }}</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-6">
                                            <div class="card rounded-partner">
                                                <div class="card-body">
                                                    <h6>Start Date</h6>
                                                    {{ $project->start->toFormattedDateString('d/m/y') }}
                                                    <hr>
                                                    <h6>Due Date</h6>
                                                    {{ $project->deadline->toFormattedDateString('d/m/y') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-6">
                                            <div class="card rounded-partner">
                                                <div class="card-body">
                                                    <h6>Nilai Pekerjaan</h6>
                                                    {{ formatRupiah($project->financial->job_value) }}
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <h6>PPN</h6>
                                                            {{ $project->financial->vat_percent . '%' }}
                                                        </div>
                                                        <div class="col-6">
                                                            <h6>PPH</h6>
                                                            {{ $project->financial->tax_percent . '%' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-lg-2">
                                            <div class="card rounded-partner">
                                                <div class="card-body">
                                                    <h6>SP2D</h6>
                                                    {{ formatRupiah($project->financial->sp2d_amount) }}
                                                    <hr>
                                                    <h6>Margin</h6>
                                                    {{ formatRupiah($project->financial->margin) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <hr />
                                            <h5>
                                                Rincian Rancangan Anggaran Biaya (RAB)
                                            </h5>
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped mt-3"
                                                    id="costCenterSubsTable">
                                                    <thead class="table-dark">
                                                        <tr>
                                                            <th style="width: 5%">No.</th>
                                                            <th>Nama Item</th>
                                                            <th>Debet</th>
                                                            <th>Limit</th>
                                                            <th>Kode Ref.</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($project->costCenters as $data)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $data->name }}</td>
                                                                <td>{{ $data->amount_debit != 0 ? formatRupiah($data->amount_debit) : '-' }}</td>
                                                                <td>{{ $data->amount_credit != 0 ? formatRupiah($data->amount_credit) : '-' }}</td>
                                                                <td>{{ $data->code_ref }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
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
        </div>
    </section>
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
    <script type="text/javascript">

    </script>
@endpush
