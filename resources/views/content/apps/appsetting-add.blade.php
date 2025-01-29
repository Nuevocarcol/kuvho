@extends('layouts/layoutMaster')

@section('title', 'Settings')

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
    <script src="{{ asset('assets/js/settings-add.js') }}"></script>
@endsection

@section('content')
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Settings /</span><span> App Setting</span>
    </h4>

    <div class="row g-4 mb-4">
        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
    </div>

    <div class="app-ecommerce">
        <!-- App Setting -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div class="d-flex flex-column justify-content-center">
                <h4 class="mb-1 mt-3">App Setting</h4>
                <p class="text-muted">Manage your App Designs</p>
            </div>
        </div>

        <form class="add-service pt-0" id="addAppSetting" method="post" action="{{ route('appsetting-save') }}"
            enctype="multipart/form-data">
            @csrf
            <div class="row">
                <!-- App Details -->
                <div class="col-12 col-lg-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">App Details</h5>
                        </div>
                        <div class="card-body">
                            <!-- App Name -->
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="name">App Name<span
                                            style="color: red;">*</span></label>
                                    <input type="text" id="name" value="{{ $settings['name'] }}"
                                        placeholder="Enter App Name" name="name"
                                        class="form-control @error('name') is-invalid @enderror" />
                                    @error('name')
                                        <p class="invalid-feedback">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label" for="email">App Email<span
                                            style="color: red;">*</span></label>
                                    <input type="text" id="email" value="{{ $settings['email'] }}"
                                        placeholder="Enter App Email" name="email"
                                        class="form-control @error('email') is-invalid @enderror" />
                                    @error('email')
                                        <p class="invalid-feedback">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- App Text -->
                            <div class="mb-3">
                                <label class="form-label" for="text">App Text<span style="color: red;">*</span></label>
                                <textarea id="text" name="text" rows="4" placeholder="Enter App Text"
                                    class="form-control @error('text') is-invalid @enderror">{{ $settings['text'] }}</textarea>
                                @error('text')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- App Colour -->
                            <?php
                            // Assuming $settings['color'] contains RGB values in the format "52, 157, 249"
                            $hexColor = ''; // default color in case $settings['color'] is not set
                            if (isset($settings['color']) && $settings['color'] !== '') {
                                $rgb = explode(',', $settings['color']);
                                $hexColor = sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
                            }
                            ?>

                            <div class="mb-3">
                                <label class="form-label" for="color">App Colour<span
                                        style="color: red;">*</span></label>
                                <div class="input-group">
                                    <input type="color" id="color" name="color" value="<?= $hexColor ?>"
                                        class="form-control form-control-color @error('color')
is-invalid
@enderror"
                                        style="width: 50px; height: 25px; padding: 0.2rem;">
                                </div>
                            </div>


                            <!-- App Logo -->
                            <div class="mb-3" style="margin-top: 20px;">
                                <label class="form-label" for="logo">App Logo<span style="color: red;">*</span></label>
                                <input type="file" id="logo" name="logo" onchange="displaySelectedImage(this)"
                                    class="form-control @error('logo') is-invalid @enderror">
                                @error('logo')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                @enderror
                                <div id="selectedImageContainer" style="padding-top: 10px;">
                                    <!-- Add this input field to store the existing logo filename -->
                                    <input type="hidden" name="existing_logo" value="{{ $settings['logo'] ?? '' }}">
                                    @if (isset($settings['logo']))
                                        {{-- <img src="{{ asset('assets/images/' . $settings['logo']) }}"
                                            style="max-width: 100px; max-height: 100px;"> --}}
                                        <img src="{{ asset('assets/images/' . $settings['logo']) }}"
                                            style="max-width: 100px; max-height: 100px;">
                                    @endif
                                </div>
                            </div>

                            <input type="submit" class="btn btn-primary me-sm-3 me-1 data-submit" value="Submit"
                                onclick="submitForm(event)">
                            <button type="button" class="btn btn-secondary me-sm-3 me-1"
                                onclick="cancelForm()">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

<script>
    function displaySelectedImage(input) {
        var container = document.getElementById('selectedImageContainer');
        container.innerHTML = '';

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                var image = document.createElement('img');
                image.src = e.target.result;
                image.style.maxWidth = '100px';
                image.style.maxHeight = '100px';
                container.appendChild(image);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<script>
    function cancelForm() {
        window.location.href = "{{ route('dashboard') }}";
    }
</script>
