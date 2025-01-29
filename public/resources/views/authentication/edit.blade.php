@extends('layouts.admin.master')

@section('title')Edit Profile
@endsection

@push('css')
@endpush

@section('content')
	
	<div class="container-fluid">
	    <div class="edit-profile">
            <form action="{{route('update-profile')}}" method="post" enctype="multipart/form-data">
                @csrf
	        <div class="row">
	            <div class="col-xl-4">
	                <div class="card">
	                    <div class="card-header pb-0">
	                        <h4 class="card-title mb-0">My Profile</h4>
	                        <div class="card-options">
	                            <a class="card-options-collapse" href="#" data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a><a class="card-options-remove" href="#" data-bs-toggle="card-remove"><i class="fe fe-x"></i></a>
	                        </div>
	                    </div>
	                    <div class="card-body">
                            <div class="card">
	                            <div class="row mb-2">
	                                <div class="profile-title">
	                                    <div class="media">
											@if($auth->profile_pic)
	                                        	<img class="img-70 rounded-circle" alt="" src="{{asset('assets/images/user/'.$auth->profile_pic)}}" style="width: 50px; height:50px;"/>
											@else
												<img class="img-70 rounded-circle" alt="" src="{{asset('assets/images/user/avatar.png')}}" style="width: 50px; height:50px;"/>
											@endif
											<div class="media-body">
	                                            <h3 class="mb-1 f-20 txt-primary">{{$auth->username}}</h3>
	                                            <p class="f-12">{{$auth->bio}}</p>
	                                        </div>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="mb-3">
	                                <label class="form-label">Username</label>
	                                <input class="form-control" name="username" value="{{$auth->username}}"/>
	                            </div>
                                <div class="mb-3">
	                                <h6 class="form-label">Bio</h6>
	                                <textarea class="form-control" rows="5" name="bio">{{$auth->bio}}</textarea>
	                            </div>
	                            <div class="mb-3">
	                                <label class="form-label">Email-Address</label>
	                                <input class="form-control" name="email" value="{{$auth->email}}"/>
	                            </div>
	                            <div class="mb-3">
	                                <label class="form-label">Profile Pic</label>
	                                <input class="form-control" type="file" name="profile_pic"/>
	                            </div>
                            </div>
	                    </div>
	                </div>
	            </div>
	            <div class="col-xl-8">
                    <div class="card">
	                    <div class="card-header pb-0">
	                        <h4 class="card-title mb-0">Edit Profile</h4>
	                        <div class="card-options">
	                            <a class="card-options-collapse" href="#" data-bs-toggle="card-collapse"><i class="fe fe-chevron-up"></i></a><a class="card-options-remove" href="#" data-bs-toggle="card-remove"><i class="fe fe-x"></i></a>
	                        </div>
	                    </div>
	                    <div class="card-body">
	                        <div class="row">
	                            <div class="col-sm-6 col-md-6">
	                                <div class="mb-3">
	                                    <label class="form-label">Full Name</label>
	                                    <input class="form-control" type="text" placeholder="Full Name" name="fullname" value="{{$auth->fullname}}"/>
	                                </div>
	                            </div>
	                            <div class="col-sm-6 col-md-6">
	                                <div class="mb-3">
	                                    <label class="form-label">Mobile No.</label>
	                                    <input class="form-control" type="number" placeholder="Mobile Number" name ="phone" value="{{$auth->phone}}" />
	                                </div>
	                            </div>
                                <div class="col-sm-6 col-md-4">
	                                <div class="mb-3">
	                                    <label class="form-label">Gender</label>
	                                    <select class="form-control btn-square" name="gender">
	                                        <option value="0">--Select--</option>
	                                        <option value="Male" {{($auth->gender == "Male") ? 'selected' : "" }}>Male</option>
	                                        <option value="Female" {{($auth->gender == "Female") ? 'seleted' : "" }}>Female</option>
	                                    </select>
	                                </div>
	                            </div>
	                            <div class="col-sm-6 col-md-3">
	                                <div class="mb-3">
	                                    <label class="form-label">Age</label>
	                                    <input class="form-control" type="number" placeholder="Age" name="age" value="{{$auth->age}}"/>
	                                </div>
	                            </div>
	                            <div class="col-sm-6 col-md-5">
	                                <div class="mb-3">
	                                    <label class="form-label">DOB</label>
	                                    <input class="form-control" type="date" name="dob"  value="{{$auth->dob}}"/>
	                                </div>
	                            </div>
	                            <div class="col-md-4">
	                                <div class="mb-3">
	                                    <label class="form-label">Country</label>
	                                    <select class="form-control btn-square" name="country" id="country">
	                                        <option value="0">--Select--</option>
                                            @foreach($country as $value)
                                            <option value="{{$value->id}}" {{($value->id == $auth->country_id)? "selected" : ""}}>{{$value->name}}</option>
                                            @endforeach
	                                    </select>
	                                </div>
	                            </div>
                                <div class="col-md-4">
	                                <div class="mb-3">
	                                    <label class="form-label">State</label>
	                                    <select class="form-control btn-square" name="state" id="state">
                                            @if(!empty($state))
                                                <option value="{{$auth->state_id}}" selected>{{$state->name}}</option>
                                            @endif
	                                    </select>
	                                </div>
	                            </div>
                                <div class="col-md-4">
	                                <div class="mb-3">
	                                    <label class="form-label">City</label>
	                                    <select class="form-control btn-square" name="city" id="city">
                                            @if(!empty($city))
                                                <option value="{{$auth->city_id}}" selected>{{$city->name}}</option>
                                            @endif
	                                    </select>
	                                </div>
	                            </div>
                                <div class="col-md-4">
                                    <div class="media">
                                        <label class="col-form-label m-r-10">Private Account</label>
                                        <div class="media-body text-end icon-state switch-outline">
                                          <label class="switch">
                                            <input type="checkbox" {{($auth->is_Private == "private") ? 'checked' : "" }} name="private" value="private"><span class="switch-state bg-success"></span>
                                          </label>
                                        </div>
                                    </div>
                                </div>
	                        </div>
	                    </div>
	                    <div class="card-footer text-end">
	                        <button class="btn btn-primary" type="submit">Update Profile</button>
	                    </div>
                    </div>
	            </div>
	        </div></form>
	    </div>
	</div>

	
	@push('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#country').on('change', function () {
                var idCountry = this.value;
                $("#state").html('');
                $.ajax({
                    url: "{{url('get-states')}}",
                    type: "POST",
                    data: {
                        country_id: idCountry,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#state').html('<option value="">Select State</option>');
                        $.each(result.states, function (key, value) {
                            $("#state").append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                        $('#city').html('<option value="">Select City</option>');
                    },
                    error:err=>{
                        console.log(err);
                    }
                });
            });
            $('#state').on('change', function () {
                var idState = this.value;
                $("#city").html('');
                $.ajax({
                    url: "{{url('get-cities')}}",
                    type: "POST",
                    data: {
                        state_id: idState,
                        _token: '{{csrf_token()}}'
                    },
                    dataType: 'json',
                    success: function (res) {
                        $('#city').html('<option value="">Select City</option>');
                        $.each(res.cities, function (key, value) {
                            $("#city").append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            });
        });
    </script>
	@endpush

@endsection