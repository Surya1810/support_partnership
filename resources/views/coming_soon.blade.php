@extends('layouts.admin')

@section('title')
    Coming Soon
@endsection

@push('css')
@endpush

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-8 mx-auto">
                    <div class="card bg-primary rounded-partner p-5">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-8">
                                    <h1 class="display-2" style="font-weight: 400;line-height: 0.8">launching <strong
                                            style="font-weight: 800">soon!</strong>
                                    </h1>
                                    <h5 class="my-4">This page is under construction</h5>

                                    <a href="{{ route('dashboard') }}"
                                        class="btn btn-light rounded-partner px-5 text-primary"><strong>GO HOME</strong></a>
                                </div>
                                <div class="col-4 d-flex align-items-center">
                                    <i class="fa-solid fa-rocket fa-2xl mx-auto" style="color: #ffffff;"></i>
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
