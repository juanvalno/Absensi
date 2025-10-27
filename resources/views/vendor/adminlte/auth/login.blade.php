@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <style>
        .login-page {
            background: #ffffff;
        }

        .login-card-body {
            border-radius: 10px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-top: 4px solid #ffc107;
        }

        .login-logo {
            font-weight: bold;
            color: #1e3c72;
        }

        .login-logo b {
            color: #ffc107;
        }

        .login-box-msg {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2a5298;
        }

        .btn-primary {
            background-color: #1e3c72;
            border-color: #1e3c72;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: #2a5298;
            border-color: #2a5298;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.4);
        }

        .input-group-text {
            background-color: #f8f9fa;
            color: #1e3c72;
        }

        .form-control:focus {
            border-color: #1e3c72;
            box-shadow: 0 0 0 0.2rem rgba(30, 60, 114, 0.25);
        }

        .auth-links a {
            color: #1e3c72;
        }

        .auth-links a:hover {
            color: #ffc107;
        }

        .text-center.mb-4 {
            color: #555;
        }

        .text-muted {
            color: #777 !important;
        }

        .fas.fa-sign-in-alt {
            color: #ffc107;
        }
    </style>
@stop

@php($login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login'))
@php($register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register'))
@php($password_reset_url = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset'))

@if (config('adminlte.use_route_url', false))
    @php($login_url = $login_url ? route($login_url) : '')
    @php($register_url = $register_url ? route($register_url) : '')
    @php($password_reset_url = $password_reset_url ? route($password_reset_url) : '')
@else
    @php($login_url = $login_url ? url($login_url) : '')
    @php($register_url = $register_url ? url($register_url) : '')
    @php($password_reset_url = $password_reset_url ? url($password_reset_url) : '')
@endif

@section('auth_header', 'Selamat Datang di Sistem E-Gaji')

@section('auth_body')
    <p class="text-center mb-4">Silahkan masuk untuk mengakses sistem</p>

    <form action="{{ $login_url }}" method="post">
        @csrf

        {{-- Email field --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="Email" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password field --}}
        <div class="input-group mb-4">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="Kata Sandi">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Login field --}}
        <div class="row">
            <div class="col-7">
                {{-- <div class="icheck-primary" title="{{ __('adminlte::adminlte.remember_me_hint') }}">
            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

            <label for="remember">
                {{ __('adminlte::adminlte.remember_me') }}
            </label>
        </div> --}}
            </div>

            <div class="col-5">
                <button type=submit class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-sign-in-alt mr-2"></span>
                    Masuk
                </button>
            </div>
        </div>

    </form>
@stop

@section('auth_footer')
    <p class="mt-3 mb-1 text-center text-muted">
        &copy; {{ date('Y') }} Sistem E-Gaji
    </p>
@stop
