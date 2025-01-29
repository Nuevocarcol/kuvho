@extends('layouts/layoutMaster')

@section('title', 'Push Notifications')

@section('vendor-style')

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/tagify/tagify.css') }}" />
@endsection

@section('vendor-script')

    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/tagify/tagify.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/notifications-add.js') }}"></script>
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Notifications /</span><span> Push Notification</span>
    </h4>

    <div class="app-ecommerce">
        <!-- Add Notification -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">

            <div class="d-flex flex-column justify-content-center">
                <h4 class="mb-1 mt-3">Notification Settings</h4>
                <p class="text-muted">Manage your notification preferences</p>
            </div>
        </div>

        <form class="add-service pt-0" id="addNotificationForm" method="post" action="{{ route('notifications-save') }}">
            @csrf
            <div class="row">
                <!-- Send Notifications -->
                <div class="col-12 col-lg-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-tile mb-0">Send Notifications</h5>
                        </div>
                        <div class="card-body">
                            <!-- Notifications Title -->
                            <div class="row">
                                <div class="col-md-12" style="padding-bottom: 20px;">
                                    <label class="form-label" for="title">Notification Title<span style="color: red;">
                                            *</span></label>
                                    <input type="text" id="title" value="{{ old('title') }}"
                                        placeholder="Enter Notifications Title" name="title"
                                        class="form-control @error('title') is-invalid @enderror" />
                                    @error('title')
                                        <p class="invalid-feedback">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Notifications Message --}}
                            <div style="padding-bottom: 20px;">
                                <label class="form-label">Notification Message<span style="color: red;">*</span> <span
                                        class="text-muted"></span></label>
                                <textarea id="message" name="message" rows="4" placeholder="Enter Notifications Message"
                                    class="form-control @error('message') is-invalid @enderror">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                @enderror
                            </div>

                            <input type="submit" class="btn btn-primary me-sm-3 me-1 data-submit" value="Submit"
                                onclick="submitForm(event)">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
