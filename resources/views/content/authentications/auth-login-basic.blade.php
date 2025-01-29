@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Login')

@section('vendor-style')
    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />
@endsection

@section('page-style')
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/pages-auth.js') }}"></script>
@endsection

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register -->
                <div class="card">
                    <div class="card-body">
                        <!-- Image instead of Logo -->
                        <div class="app-brand justify-content-center">
                            <img src="{{ asset('assets/images/Logo_kuvho.jpg') }}" alt="Logo_kuvho" style="width: 12rem; height: 12rem;">
                        </div>

                        {{-- Auth Login --}}
                        <form id="formAuthentication" class="mb-3" action="{{ route('auth-login') }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email or Username</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Enter your email" autofocus>
                                @if ($errors->has('email'))
                                    <p class="error-message">{{ $errors->first('email') }}</p>
                                @endif
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="•••••••••" aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                    @if ($errors->has('password'))
                                        <p class="error-message">{{ $errors->first('password') }}</p>
                                    @endif
                                </div>
                            </div>
                            @if (Session::has('success'))
                                <p class="success-message">{{ Session::get('success') }}</p>
                            @endif
                            @if ($errors->has('error'))
                                <p class="error-message" style="color: red;">{{ $errors->first('error') }}</p>
                            @endif
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary d-grid w-100"
                                    style="background-color: #000; color: white; border-color: #000;">Sign in</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection