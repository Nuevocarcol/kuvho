@extends('layouts.compact-layout.master')

@section('title')Sign Up
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/sweetalert2.css') }}">
@endpush

@section('content')
    <section>
	    <div class="container-fluid p-0">
	        <div class="row m-0">
	            <div class="col-xl-5"><img class="bg-img-cover bg-center" src="{{ asset('assets/images/login/3.jpg') }}" alt="looginpage" /></div>
	            <div class="col-xl-7 p-0">
	                <div class="login-card">
	                    <form class="theme-form login-form" action="{{ route('register_user')}}" method="post">
							@csrf
	                        <h4>Create your account</h4>
							@if($errors->any())
								<div class="alert alert-danger dark alert-dismissible fade show" role="alert">
									<i data-feather="thumbs-down"></i>
									{{ implode('', $errors->all(':message')) }}
									<button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
								</div>
							@endif
	                        <div class="form-group">
	                            <label>Your Name</label>
	                            <div class="small-group">
	                                <div class="input-group">
	                                    <span class="input-group-text"><i class="icon-user"></i></span>
	                                    <input class="form-control" type="text" required="" name="fullname" placeholder="First Name" />
	                                </div>
	                                <div class="input-group">
	                                    <span class="input-group-text"><i class="icon-mobile"></i></span>
	                                    <input class="form-control" type="number" required="" name="mobileno" placeholder="Mobile Number" />
	                                </div>
	                            </div>
	                        </div>
	                        <div class="form-group">
	                            <label>Username</label>
	                            <div class="input-group">
	                                <span class="input-group-text"><i class="icon-user"></i></span>
	                                <input class="form-control" type="text" required="" name="username" placeholder="User Name" />
	                            </div>
	                        </div>
							<div class="form-group">
	                            <label>Email Address</label>
	                            <div class="input-group">
	                                <span class="input-group-text"><i class="icon-email"></i></span>
	                                <input class="form-control" type="email" required="" name="email" placeholder="Test@gmail.com" />
	                            </div>
	                        </div>
	                        <div class="form-group">
	                            <label>Password</label>
	                            <div class="input-group">
	                                <span class="input-group-text"><i class="icon-lock"></i></span>
	                                <input class="form-control" type="password" name="password" required="" placeholder="*********" />
	                                <div class="show-hide"><span class="show"> </span></div>
	                            </div>
	                        </div>
	                        <div class="form-group">
	                            <div class="checkbox">
	                                <input id="checkbox1" type="checkbox" />
	                                <label class="text-muted" for="checkbox1">Agree with <span>Privacy Policy </span></label>
	                            </div>
	                        </div>
	                        <div class="form-group">
	                            <button class="btn btn-primary btn-block" type="submit">Create Account</button>
	                        </div>
	                        <div class="login-social-title">
	                            <h5>Sign in with</h5>
	                        </div>
	                        <div class="form-group">
								<ul class="login-social">
	                                <li>
	                                    <a href="https://www.linkedin.com/login" target="_blank"><i class="fa fa-facebook fa-lg"></i></a>
	                                </li>
	                                <li>
	                                    <a href="https://www.google.com/" target="_blank"><i class="fa fa-google fa-lg"></i></a>
	                                </li>
	                            </ul>
	                        </div>
	                        <p>Already have an account?<a class="ms-2" href="{{ route('login') }}">Sign in</a></p>
	                    </form>
	                </div>
	            </div>
	        </div>
	    </div>
	</section>


    @push('scripts')
    <script src="{{ asset('assets/js/sweet-alert/sweetalert.min.js') }}"></script>
    @endpush

@endsection