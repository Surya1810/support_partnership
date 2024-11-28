@extends('layouts.app')

@section('title')
    Login
@endsection

@section('content')
    <div class="background">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-center" style="height: 100svh;">
                <div class="col-lg-3">
                    <div class="d-flex justify-content-center mb-4">
                        <img src="{{ asset('assets/logo/main-light.png') }}" alt="Partner_logo" width="216px">
                    </div>
                    <div class="card login_card rounded-4">
                        <div class="card-body">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="my-3">
                                    <label for="email"
                                        class="form-label col-form-label-sm m-0">{{ __('Email') }}</label>
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email" autofocus>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="my-3">
                                    <label for="password"
                                        class="form-label col-form-label-sm m-0">{{ __('Password') }}</label>
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary mt-3 px-3 float-end">
                                    {{ __('LOG IN') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
