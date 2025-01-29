@extends('layouts/layoutMaster')

@section('title', 'Followers-Following')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />
    <style>
        .position-relative {
            position: relative;
        }

        .position-absolute {
            position: absolute;
            top: 0;
            right: 0;
        }
    </style>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            $('#searchForm').submit(function(e) {
                e.preventDefault();
                var searchValue = $(this).find('input[name="search"]').val().trim();
                window.location.href = "{{ route('socialmates-list') }}" + "?search=" + searchValue;
            });

            $('#backButton').click(function() {
                window.location.href = "{{ route('socialmates-list') }}";
            });
        });
    </script>
@endsection

@section('content')
    <div class="container-fluid">
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Followers-Following /</span> User List
        </h4>

        <div class="row mb-3">
            <div class="col-md-2">
                <form id="searchForm">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search...">
                    </div>
                </form>
            </div>
            <div class="col-md-10 text-end">
                <button type="button" id="backButton" class="btn btn-outline-secondary">Back</button>
            </div>
        </div>

        <div class="row">
            @foreach ($users as $user)
                @if (!$search || stripos($user->username, $search) !== false)
                    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body text-center" style="height: 20rem;">
                                <div class="mx-auto mb-3">
                                    @if ($user->profile_pic)
                                        {{-- <img src="{{ asset('assets/images/user/' . $user->profile_pic) }}"
                                            class="rounded-circle" style="width: 100px; height: 100px;" /> --}}
                                        <img src="{{ asset('assets/images/user/' . $user->profile_pic) }}"
                                            class="rounded-circle" style="width: 100px; height: 100px;" />
                                    @else
                                        @php
                                            $firstLetter = strtoupper(substr($user->username, 0, 1));
                                            $colors = [
                                                'A' => 'bg-label-info',
                                                'B' => 'bg-label-success',
                                                'C' => 'bg-label-warning',
                                                'D' => 'bg-label-danger',
                                                'E' => 'bg-label-primary',
                                                'F' => 'bg-label-secondary',
                                                'G' => 'bg-label-dark',
                                                'H' => 'bg-label-info',
                                                'I' => 'bg-label-success',
                                                'J' => 'bg-label-warning',
                                                'K' => 'bg-label-danger',
                                                'L' => 'bg-label-primary',
                                                'M' => 'bg-label-secondary',
                                                'N' => 'bg-label-dark',
                                                'O' => 'bg-label-info',
                                                'P' => 'bg-label-success',
                                                'Q' => 'bg-label-warning',
                                                'R' => 'bg-label-danger',
                                                'S' => 'bg-label-primary',
                                                'T' => 'bg-label-secondary',
                                                'U' => 'bg-label-dark',
                                                'V' => 'bg-label-info',
                                                'W' => 'bg-label-success',
                                                'X' => 'bg-label-warning',
                                                'Y' => 'bg-label-danger',
                                                'Z' => 'bg-label-primary',
                                            ];
                                            $colorClass = $colors[$firstLetter] ?? 'bg-label-default';
                                        @endphp
                                        <div class="avatar-initial rounded-circle {{ $colorClass }} mx-auto mb-3"
                                            style="width: 100px; height: 100px; font-size: 2rem; display: flex; justify-content: center; align-items: center;">
                                            {{ $firstLetter }}
                                        </div>
                                    @endif
                                </div>

                                <h5 class="mb-1 card-title">{{ $user->username }}</h5>
                                <span>{{ $user->email }}</span>

                                <div class="d-flex align-items-center justify-content-around my-4 py-2">
                                    <div>
                                        <h4 class="mb-1">
                                            {{ isset($socialmates[$user->id]['totalfollowers']) ? $socialmates[$user->id]['totalfollowers'] : 0 }}
                                        </h4>
                                        <span>Followers</span>
                                    </div>
                                    <div>
                                        <h4 class="mb-1">
                                            {{ isset($socialmates[$user->id]['totalfollowing']) ? $socialmates[$user->id]['totalfollowing'] : 0 }}
                                        </h4>
                                        <span>Following</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} entries
                    </div>
                    <div>
                        {{ $users->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
