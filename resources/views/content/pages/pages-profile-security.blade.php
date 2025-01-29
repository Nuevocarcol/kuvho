@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Account settings - Security')

@section('vendor-style')
    {{-- <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" /> --}}
    {{-- <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" /> --}}

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />
@endsection

@section('page-style')
    {{-- <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-account-settings.css') }}" /> --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-account-settings.css') }}" />
@endsection

@section('vendor-script')
    {{-- <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script> --}}

    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
@endsection

@section('page-script')
    {{-- <script src="{{ asset('assets/js/pages-profile-security.js') }}"></script> --}}
    {{-- <script src="{{ asset('assets/js/modal-enable-otp.js') }}"></script> --}}
    
    <script src="{{ asset('assets/js/pages-profile-security.js') }}"></script>
    <script src="{{ asset('assets/js/modal-enable-otp.js') }}"></script>
@endsection

@section('content')
    <div class="row g-4 mb-4">
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
    </div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Account Settings /</span> Security
    </h4>

    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item"><a class="nav-link" href="{{ url('pages/profile-user') }}"><i
                            class="bx bx-user me-1"></i> Account</a></li>
                <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i
                            class="bx bx-lock-alt me-1"></i> Security</a></li>
            </ul>

            <!-- Change Password -->
            <div class="card mb-4">
                <h5 class="card-header">Change Password</h5>
                <div class="card-body">
                    <form id="formAccountSettings" method="post"
                        action="{{ route('pages-profile-updatesecurity', ['id' => $adminProfile->id]) }}"
                        onsubmit="return validateForm()">

                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-6 form-password-toggle">
                                <label class="form-label" for="currentPassword">Current Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" name="currentPassword" id="currentPassword"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        value="{{ old('currentPassword') }}"
                                        class="form-control @error('currentPassword') is-invalid @enderror">
                                    @error('currentPassword')
                                        <p class="invalid-feedback">{{ $message }}</p>
                                    @enderror
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                                <p id="currentPasswordError" class="text-danger"></p> <!-- Placeholder for error message -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="mb-3 col-md-6 form-password-toggle">
                                <label class="form-label" for="newPassword">New Password</label>
                                <div class="input-group input-group-merge">
                                    <input class="form-control" type="password" id="newPassword" name="newPassword"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        value="{{ old('newPassword') }}"
                                        class="form-control @error('newPassword') is-invalid @enderror">
                                    @error('newPassword')
                                        <p class="invalid-feedback">{{ $message }}</p>
                                    @enderror
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="mb-3 col-md-6 form-password-toggle">
                                <label class="form-label" for="password">Confirm New Password</label>
                                <div class="input-group input-group-merge">
                                    <input class="form-control" type="password" name="password" id="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        value="{{ old('password') }}"
                                        class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                        <p class="invalid-feedback">{{ $message }}</p>
                                    @enderror
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>

                            <div class="col-12 mb-4">
                                <p class="fw-medium mt-2">Password Requirements:</p>
                                <ul class="ps-3 mb-0">
                                    <li class="mb-1">
                                        Minimum 8 characters long - the more, the better
                                    </li>
                                    <li class="mb-1">At least one lowercase character</li>
                                    <li>At least one number, symbol, or whitespace character</li>
                                </ul>
                            </div>

                            <div class="col-12 mt-1">
                                <button type="submit" class="btn btn-primary me-2">Submit</button>
                                <button type="reset" class="btn btn-label-secondary"
                                    onclick="window.location='{{ route('dashboard') }}'">Cancel</button>
                            </div>
                        </div>

                    </form>
                    <script>
                        function validateForm() {
                            // Perform client-side validation
                            var currentPassword = document.getElementById('currentPassword').value;
                            var newPassword = document.getElementById('newPassword').value;
                            var password = document.getElementById('password').value;

                            // Clear previous error messages
                            document.getElementById('currentPasswordError').innerText = '';
                            document.getElementById('newPasswordError').innerText = '';
                            document.getElementById('passwordError').innerText = '';

                            if (currentPassword.trim() === '' || newPassword.trim() === '' || password.trim() === '') {
                                document.getElementById('currentPasswordError').innerText = 'Please fill in all password fields.';
                                return false;
                            }

                            if (newPassword !== password) {
                                document.getElementById('newPasswordError').innerText = 'New password and confirm password do not match.';
                                return false;
                            }

                            // If validation passes, the form will be submitted
                            return true;
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
@endsection
