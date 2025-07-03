@extends('layouts.admin')

@section('title')
    Project Review
@endsection

@push('css')
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
                        <li class="breadcrumb-item active"><strong>Review</strong></li>
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
                                    <a class="nav-link active" id="tabs_review"
                                        href="{{ route('project.review', $project->kode) }}" role="tab"
                                        aria-controls="tabs_review" aria-selected="true">Review</a>
                                </li>
                                @if (auth()->user()->role_id != 5)
                                    <li class="nav-item">
                                        <a class="nav-link" id="tabs_finalization" href="{{ route('project.finalization', $project->kode) }}" role="tab"
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

                                    <div class="row mt-4">
                                        <div class="col-12">
                                            @if (empty($project->review))
                                                No Reviews
                                            @else
                                                {{ $project->review }}
                                            @endif
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
