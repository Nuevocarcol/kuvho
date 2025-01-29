@extends('layouts.compact-layout.master')

@section('title')login
@endsection

@push('css')
@endpush
@section('content')
    <section>
	    <div class="container-fluid">
	        <div class="row">
	            {{-- <div class="col-xl-5"><img class="bg-img-cover bg-center" src="{{ asset('assets/images/login/3.jpg') }}" alt="looginpage" /></div> --}}
	            <div class="col-xl-5"><img class="bg-img-cover bg-center" src="{{ asset('public/assets/images/login/3.jpg') }}" alt="looginpage" /></div>
	            <div class="col-xl-7 p-0">
					
	                <div class="login-card">
						
						<form class="theme-form login-form" action="{{ route('login_user') }}" method="post">
							@csrf
							{{-- {{ csrf_token() }} --}}
	                        <h4>Login</h4>
	                        <h6>Welcome back! Log in to your account.</h6>
	                        @if($errors->any())
								<div class="alert alert-danger dark alert-dismissible fade show" role="alert">
									<i data-feather="thumbs-down"></i>
									{{ implode('', $errors->all(':message')) }}
									<button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
								</div>
							@endif
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
	                                <input class="form-control" id="login_pass" type="password" name="password" required="" placeholder="*********" />
	                                
	                            </div>
								<i class="fa fa-light fa-eye" title="Show" id="eye_icon" style=" color: #465a59;position: absolute;right: 15px;top: 35px;font-size: 17px;cursor: pointer;z-index:33"></i>
	                        </div>
	                        <div class="form-group">
	                            
	                            <a class="link" href="{{ route('forget-password') }}">Forgot password?</a>
	                        </div>
	                        <div style="display: flex;
							align-items: center;
							justify-content: center;
							height: 5rem;
							margin-top: 2rem;" class="form-group"><button class="btn btn-primary btn-block" href="{{ route('login_user')}}" type="submit">Sign in</button></div>
	                        <div class="login-social-title">
	                            <h5>Sign in with</h5>
	                        </div>
	                        <div class="form-group">
								<ul class="login-social">
	                                <li>
	                                    <a href="https://www.facebook.com/login" target="_blank"><i class="fa fa-facebook fa-lg"></i></a>
	                                </li>
	                                <li>
	                                    <a href="https://www.google.com/" target="_blank"><i class="fa fa-google fa-lg"></i></a>
	                                </li>
	                            </ul>
	                        </div>
	                        <p>Don't have account?<a class="ms-2" href="{{ route('sign-up') }}">Create Account</a></p>
	                    </form>
	                </div>
	            </div>
	        </div>
	    </div>
	</section>

	
    @push('scripts')
	<script>
		let login_pass = document.getElementById("login_pass");
		let eye_icon = document.getElementById("eye_icon");
		eye_icon.addEventListener("click", function() {
  			// alert("clicked")
			if (eye_icon.classList.contains("fa-eye-slash")) {
				eye_icon.classList.remove("fa-eye-slash");
				eye_icon.classList.add("fa-eye");
				eye_icon.title= "show"
				login_pass.type="password"
			} else {
				eye_icon.classList.add("fa-eye-slash");
				eye_icon.classList.remove("fa-eye");
				eye_icon.title= "Hide"
				login_pass.type="text"
			}

		});
	</script>
    @endpush

@endsection