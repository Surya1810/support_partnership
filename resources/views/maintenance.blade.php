@extends('layouts.app')

@section('title')
    Maintenance
@endsection

@section('content')
    <div class="background">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-center" style="height: 100svh;">
                <div class="col-12">
                    <div class="d-flex justify-content-center mb-4">
                        <img src="{{ asset('assets/logo/icon_p_white.png') }}" alt="Partner_logo" width="216px">
                    </div>
                    <div class="text-white text-center">
                        <h2><strong>Oops! Website under maintenance</strong></h2>
                        <h4 class="mb-5">We'll be back soon!</h4>
                        <p>Developer sedang istirahat...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
