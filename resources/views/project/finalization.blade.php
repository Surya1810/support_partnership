@extends('layouts.admin')

@section('title')
    Project Finalization
@endsection

@push('css')
@endpush

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Project Finalization</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('project.index') }}">Project</a>
                        </li>
                        <li class="breadcrumb-item active"><strong>Finalization</strong></li>
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
                                    <a class="nav-link rounded" id="tabs_project"
                                        href="{{ route('project.detail', $project->kode) }}" role="tab"
                                        aria-controls="tabs_project" aria-selected="false">Overview</a>
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
                                        aria-controls="tabs_review" aria-selected="true">Review</a>
                                </li>
                                @if (auth()->user()->role_id != 5)
                                    <li class="nav-item">
                                        <a class="nav-link active" id="tabs_finalization"
                                            href="{{ route('project.finalization', $project->kode) }}" role="tab"
                                            aria-controls="tabs_finalization" aria-selected="false">Finalization</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="custom-tabs-four-tabContent">
                                <div class="tab-pane fade show active" id="project" role="tabpanel"
                                    aria-labelledby="tabs_project">
                                    <h5>Project - <strong>{{ $project->name }}</strong></h5>
                                    @if (auth()->user()->department_id != 8)
                                    <small class="text-danger">
                                        * Data penyelesaian project hanya dapat diisi oleh bagian keuangan
                                    </small>
                                    @endif

                                    <div class="row mt-4">
                                        <div class="col-6">
                                            <form action="{{ route('project.finalization', $project->id) }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-group row">
                                                    <label for="inputInvoice"
                                                        class="col-sm-2 col-form-label">Invoice</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="inputInvoice"
                                                            name="invoice"
                                                            value="{{ $project->finalization?->invoice_number }}" {{ auth()->user()->department_id == 8 ? '' : ' disabled' }} required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="inputBilling"
                                                        class="col-sm-2 col-form-label">ID-Billing</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="inputBilling"
                                                            name="id_billing"
                                                            value="{{ $project->finalization?->id_billing }}" required {{ auth()->user()->department_id == 8 ? '' : ' disabled' }}>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="inputFaktur"
                                                        class="col-sm-2 col-form-label">e-Faktur</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="inputFaktur"
                                                            name="e_faktur" value="{{ $project->finalization?->e_faktur }}"
                                                            required {{ auth()->user()->department_id == 8 ? '' : ' disabled' }}>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="uploadBAST">Upload BAST</label>

                                                    <div class="d-flex align-items-center gap-2">
                                                        <input type="file" class="form-control-file" id="uploadBAST"
                                                            name="file_bast" accept=".pdf"
                                                            @if ($project->finalization?->bast_file) @else required @endif
                                                            {{ auth()->user()->department_id == 8 ? '' : ' disabled' }}
                                                        >

                                                        @if ($project->finalization?->bast_file)
                                                            <a href="{{ asset('storage/' . $project->finalization->bast_file) }}"
                                                                target="_blank" class="btn btn-sm btn-info ml-2" title="Lihat File">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="form-group float-right">
                                                    {{-- Hanya Bagian Keuangan --}}
                                                    @if (auth()->user()->department_id == 8)
                                                        <button class="btn btn-primary rounded-partner">Simpan</button>
                                                    @endif
                                                </div>
                                            </form>
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
@endpush
