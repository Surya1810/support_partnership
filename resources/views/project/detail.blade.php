@extends('layouts.admin')

@section('title')
    Project Overview
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
                                        <div class="col-lg-5">
                                            <div class="card rounded-partner">
                                                <div class="card-body">
                                                    <h5>Project Name</h5>
                                                    {{ $project->name }}
                                                    <hr>
                                                    <h5>Client Name</h5>
                                                    {{ $project->client->name }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="card rounded-partner">
                                                <div class="card-body">
                                                    <h5>PIC</h5>
                                                    {{ $project->pic->username }}
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
                                                    <h5>Start Date</h5>
                                                    {{ $project->start->toFormattedDateString('d/m/y') }}
                                                    <hr>
                                                    <h5>Due Date</h5>
                                                    {{ $project->deadline->toFormattedDateString('d/m/y') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-6">
                                            <div class="card rounded-partner">
                                                <div class="card-body">
                                                    <h5>Urgency</h5>
                                                    {{ $project->urgency }}
                                                    <hr>
                                                    <h5>Status</h5>
                                                    {{ $project->status }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="card rounded-partner">
                                                <div class="card-body">
                                                    <h4>Creative Brief</h4>
                                                    {!! html_entity_decode($project->creative_brief) !!}
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
        </div>
    </section>
@endsection

@push('scripts')
@endpush
