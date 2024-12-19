@extends('layouts.admin')

@section('title')
    Document
@endsection

@push('css')
@endpush

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Document</h1>
                    <ol class="breadcrumb text-black-50">
                        <li class="breadcrumb-item"><a class="text-black-50" href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a class="text-black-50" href="#">File</a>
                        </li>
                        <li class="breadcrumb-item active"><strong>Document</strong></li>
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
                                    <a class="nav-link rounded active" id="tabs_document"
                                        href="{{ route('file.document') }}" role="tab" aria-controls="tabs_document"
                                        aria-selected="true">Document</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tabs_compro" href="{{ route('file.compro') }}" role="tab"
                                        aria-controls="tabs_compro" aria-selected="false">Company Profile</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tabs_template" href="{{ route('file.template') }}"
                                        role="tab" aria-controls="tabs_template" aria-selected="false">Template</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="custom-tabs-four-tabContent">
                                <div class="tab-pane fade show active" id="document" role="tabpanel"
                                    aria-labelledby="tabs_document">
                                    {{-- Isi Konten --}}
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
