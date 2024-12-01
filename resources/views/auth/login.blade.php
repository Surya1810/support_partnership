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

                                <div class="row mb-4">
                                    <div class="col-6">
                                        <div class="icheck-primary">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                                {{ old('remember') ? 'checked' : '' }}>
                                            <label for="remember">
                                                {{ __('Ingat Saya') }}
                                            </label>
                                        </div>
                                    </div>
                                    @if (Route::has('password.request'))
                                        <div class="col-6">
                                            <a href="{{ route('password.request') }}"
                                                class="text-decoration-none float-end">
                                                {{ __('Lupa Password?') }}
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-lg btn-primary px-3 rounded-5 w-100"
                                        style="font-weight: 500;font-size: 15px">
                                        {{ __('Login') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
