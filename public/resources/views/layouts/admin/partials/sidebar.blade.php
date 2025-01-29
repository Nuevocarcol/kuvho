<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<style>
   #infowindow-content .title {
      font-weight: bold;
   }

   #infowindow-content {
      display: none;
   }

   #map #infowindow-content {
      display: inline;
   }

   .pac-card {
      background-color: #fff;
      border: 0;
      border-radius: 2px;
      box-shadow: 0 1px 4px -1px rgba(0, 0, 0, 0.3);
      margin: 10px;
      padding: 0 0.5em;
      font: 400 18px Roboto, Arial, sans-serif;
      overflow: hidden;
      font-family: Roboto;
      padding: 0;
   }

   #pac-container {
      padding-bottom: 12px;
      margin-right: 12px;
      z-index:9999 !important;

   }

   .pac-controls {
      display: inline-block;
      padding: 5px 11px;
   }

   .pac-controls label {
      font-family: Roboto;
      font-size: 13px;
      font-weight: 300;
   }

   #pac-input {
      /*background-color: #fff;*/
      font-family: Roboto;
      font-size: 15px;
      /*padding: 0 11px 0 13px;*/
      text-overflow: ellipsis;
      /*width: 400px;*/
   }

   #pac-input:focus {
      border-color: #4d90fe;
   }

   #title {
      color: #fff;
      background-color: #4d90fe;
      font-size: 25px;
      font-weight: 500;
      padding: 6px 12px;
   }

   #target {
      width: 345px;
   }

   #map{
      height: 300px;
   }
   .map-section {
      width: 100%;
   }

   .map-section input{
      border: none;
      background-color: #efefef;
      border-radius: 10px;
      padding: 15px;
      width: 100%;
   }
  .share_result_image {
    height: 40px;
    width: 40px !important;
    border-radius: 10rem;
    margin-right: 1rem;
    min-width: 40px;
  }
  .share_search_result {
    overflow-y: scroll;
    height: 25rem;
  }
  .hover_effoct:hover {
    background-color: rgba(0, 0, 0, 0.123);
    cursor: pointer;
  }

  .profile_home_contailer{
    display:flex;
    flex-direction:column;
    gap:2rem;
  }

  .profile_home_div {
    display: flex;
    justify-content:center;
    align-items:center;
    margin-top:1rem;
  }

  .profile_home_image {
      height: 4rem;
      width: 4rem;
      border-radius: 10rem;
      object-fit: cover;
      cursor: pointer;
      margin-right:1rem
  }
  @media screen and (max-width:425px) {

  }
  .notification {
    overflow-y: scroll;
    height: 30rem;
    padding-inline: .5rem;
  }
  .follow_btn{
  color: white;
  background-color: rgb(66, 167, 255);
  height: fit-content;
  padding-block: 4px ;
  padding-inline: 14px ;
  border-radius: .5rem;
  font-weight: 500;
  }
  .follow_btn:hover{
  background-color: rgb(48, 146, 232);
  }
  .following_btn{
  margin-left: 4px;
  color: black;
  opacity: .9;
  border: none;
  cursor: pointer;
  background-color: rgba(105, 105, 105, 0.205);
  height: fit-content;
  padding-block: 5px ;
  padding-inline: 14px ;
  border-radius: .5rem;
  font-weight: 500;
  }
  .following_btn:hover{
  background-color: rgba(105, 105, 105, 0.379);
  }
  .btn_div{
  margin-left: auto;
  margin-right: 8px;
  display: flex;
  justify-content: center;
  align-items: center;
  }
  .time{
  color: rgb(110, 110, 110);
  margin-left: .3rem;
  }
  .radio_button {
        border: 2px solid black;
        border-radius: 10rem;
        padding: 10px;

        margin-block: auto;
        margin-left: auto;
        margin-right: 10px;
    }

  .pac-logo{
    z-index: 56565;
  }
    
</style>
@php
$auth = Auth::user();
$total_post = App\Models\post::where('user_id', $auth->id)->count();
$following = App\Models\Follow::where('from_user', $auth->id)->count();
$followers = App\Models\Follow::where('to_user', $auth->id)->count();
@endphp
<header class="main-nav border-end " style="width:340px;">
  
    <div class="profile_home_contailer" >
        <div class="profile_home_div">
          @if($auth->profile_pic)
            <img class="profile_home_image" style="height" src="{{ asset('assets/images/user/'.$auth->profile_pic) }}" />
          @else
          <img class="profile_home_image" style="height" src="{{ asset('assets/images/dashboard/1.png') }}" />
          @endif
          <div style="width:13rem;">
            <div style="font-weight:700">{{ $auth->username }}</div>
            <div style="color:gray;font-size:11px;">{{ $auth->bio }}</div>
          </div>
        </div>
        <div class="text-center" style="display:flex;justify-content:space-evenly">
          <div style="display:flex;flex-direction:column;">
            <span>Post</span>
            <b>{{ $total_post }}</b>
          </div>
          <div style="display:flex;flex-direction:column;">
            <a href="#" data-bs-toggle="modal" data-bs-target="#followers">
            <span>Followers</span><br>
            <b>{{ $followers }}</b></a>
          </div>
          <div style="display:flex;flex-direction:column;">
            <a href="#" data-bs-toggle="modal" data-bs-target="#following">
            <span>Following</span><br>
            <b>{{ $following }}</b></a>
          </div>
        </div>
    </div>
    <nav>
      <hr style="background-color:#dee2e6;width:21.2rem;position:absolute;">
                      <div style="height:1px"></div>
        <div class="main-navbar pe-5">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="mainnav" >
                <ul class="nav-menu h- my-5 ">
                    <li class="back-btn ">
                        <div class="mobile-back text-end 	d-none"><span>Back</span><i  class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
                    </li>
                    <li class="dropdown">
                        <a style="" class="nav-link menu-title link-nav {{routeActive('home')}} my-2 flex" href="{{ route('home') }}">
                        @if(routeActive('home'))
                        <i class="fa fa-home" style="font-size:20px;"></i>
                        <span style="margin-left:.6rem;font-weight:800; ">
                          Home
                        </span></a>
                        @else
                        <i data-feather="home"></i>
                        <span >
                          Home
                        </span></a>
                        @endif
                        
                    </li>
                    <li class="dropdown">
                      <a class="nav-link menu-title link-nav my-2 flex" href="#" data-bs-toggle="modal" data-bs-target="#searchuser"><i data-feather="search"></i><span>Search</span></a>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav {{routeActive('explore')}} my-2 flex" href="{{ route('explore') }}">
                        @if(routeActive('explore'))
                        <i class="fa fa-solid fa-compass" style="font-size:19px;font-weight:800;"></i>
                        <span style="margin-left:.6rem;font-weight:700; ">
                        Expoler
                        </span></a>
                        @else
                        <i data-feather="compass"></i>
                        <span>
                        Expoler
                        </span></a>
                        @endif
                          <!-- <i data-feather="compass"></i><span>Expoler</span></a> -->
                    </li>
                      <li class="dropdown">
                        @php
                          $message_count = App\Models\chat::where('to_user', Auth::id())->where('read_message', 0)->count();
                        @endphp
                        <a style="position:absolute;margin-left:24px;  margin-top:4px !important; z-index:2;  border-radius: 20px;background-color: #ff3e3e;padding: 0px !important;font-weight:400 !important;
                        padding-inline: 5px !important;color:white !important;font-size: 12px !important;" id="mcounthide">{{ ($message_count != 0) ? $message_count : "" }}</a>
                        <a class="nav-link menu-title link-nav {{routeActive('chat')}} my-2 flex" href="{{ route('chat') }}">
                        @if(routeActive('chat'))
                        <i class='fa fa-comment'  style="font-weight:800;"></i>
                        <span style="margin-left:.6rem;font-weight:700; ">
                        Message
                        </span></a>
                        @else
                        <i data-feather="message-circle"></i>
                        <span>
                        Message
                        </span></a>
                        @endif
                    </li>
                    <!-- <li class="dropdown"> 
                        <a class="nav-link menu-title link-nav my-2 flex" href="#"><i data-feather="play-circle"></i><span>Reels</span></a>
                    </li> -->
                    <li class="dropdown"> 
                      @php
                        $notification_count = App\Models\user_notification::where('to_user', Auth::id())->where('read_status', 0)->count();
                      @endphp
                      <a style="position:absolute;margin-left:24px;  margin-top:4px !important; z-index:2; border-radius: 20px;background-color: #ff3e3e;padding: 0px !important;font-weight:400 !important;
                      padding-inline: 5px !important;color:white !important;font-size: 12px !important;" id="ncounthide">{{ ($notification_count != 0) ? $notification_count : "" }}</a>
                        <a class="nav-link menu-title link-nav my-2 flex" href="#" data-bs-toggle="modal" data-bs-target="#notificationbox" onclick="notifycount()"><i data-feather="heart"></i><span>Notifications</span></a>
                    </li>
                    <li>
                        <a class="nav-link menu-title link-nav my-2 flex" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"><i data-feather="plus-circle"></i><span>Create</span></a>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav my-2 flex" href="{{ url('profile/'.Auth::user()->username) }}">
                        @if(routeActive('get_profile'))
                        <i class='fa fa-user'  style="font-size:17px;"></i>
                        <span style="margin-left:.6rem;font-weight:700; ">
                          Profile
                        </span></a>
                        @else
                        <i data-feather="user"></i>
                        <span>
                          Profile
                        </span></a>
                        @endif
                    </li>
                    
                    <hr style="background-color:#dee2e6;width:21.2rem;position:absolute;">
                    <div style="height:30px"></div>
                    <li class="dropdown">
                        <a class="my-2"href="#"><span  style="padding-top:1rem;font-weight:600; font-size:17px;" >Terms</span></a>
                    </li>
                    <li class="dropdown">
                        <a  class="nav-link menu-title link-nav my-2 flex" href="#"><i data-feather="shield" ></i></i><span >Privacy and Policy</span></a>
                    </li>
                    <li class="dropdown">
                        <a class="nav-link menu-title link-nav my-2 flex" href="#"><i class="fa fa-file-text-o fa-lg pe-3"></i><span >Terms & Condition</span></a>
                    </li>
                    <!-- <li class="dropdown">
                        <a class=" " style="font-weight:500;" href="#" >Privacy and Policy</span></a>
                    </li> 
                    <li class="dropdown">
                        <a class=" " style="" href="#">Terms & Condition</span></a>
                    </li> -->
                </ul>

                <div>
                  <div class="">

                  </div>
                </div>
                <a href="logout">
                  <div class="ps-4" style="position:absolute; top: 84%;">
                    <div style=" padding-left:1rem;cursor:pointer; border:1px solid black; padding-right:8rem;padding-block:.4rem;border-radius:.5rem;" class="d-flex gap-3  " >
                      <i data-feather="log-out"></i>
                      <span style="font-weight:600;"> Log Out</span>
                    </div>
                  </div>
                </a>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </div>
    </nav>
</header>

<div class="modal fade" id="following" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="width:400px;" >
    <div class="modal-content">
      
    <div class="message_popup py-3">
              <div class="d-flex justify-content-center px-3">
                  <span class="h5">Following</span>
                  <!-- <i class="icon-close h4 ms-auto me-0"></i> -->
                  
              </div>
              <hr />
              <div class="row px-1 align-items-center">
                  <span class="col-1 ms-3 h6"> </span>
                  <input class="col-9 rounded border-0" type="text" placeholder="Search.." id="followinguser"/>
              </div>
              <hr />
              <div class="share_search_result px-3" id="serach_followinguser">
                @php
                  $user_searches = app\Models\Follow::select('users.id as user_id', 'users.username', 'users.profile_pic', 'follow.*')->join('users', 'users.id', '=', 'follow.to_user')->where('from_user', Auth::id())->where('follow.status', "follow")->get();
                @endphp
                <ul id="myList">
                @foreach($user_searches as $follwowers)
                  <li>
                    <div class="d-flex ps-2 hover_effoct rounded-3 py-2">
                        
                      <img src="{{ url('assets/images/user/'. $follwowers->profile_pic) }}" class="share_result_image" alt="" />
                      
                      <div class="d-flex flex-column align-items-center">
                        <a href="{{ url('profile/'.$follwowers->username) }}">
                          <b>{{ $follwowers->username }}</b>
                        </a>
                          {{-- <div>{{ $follwowers->fullname }}</div> --}}
                      </div>
                      @php
                        $ifollow = App\Models\Follow::where('from_user', $follwowers->user_id)->first();
                      @endphp
                        <div class="btn_div">
                          <div class="following_btn" id="follow{{$follwowers->user_id}}" onclick="follow({{$follwowers->user_id}})">Following</div>
                        </div>
                    </div>
                  </li>
                @endforeach
                </ul>
              </div>
              <hr />
              <div class="px-2">
                  
              </div>
              <!-- </div>  -->
          </div>
    </div>
  </div>
</div>

<div class="modal fade" id="followers" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="width:400px;" >
    <div class="modal-content">
      
    <div class="message_popup py-3">
              <div class="d-flex justify-content-center px-3">
                  <span class="h5">Followers</span>
                  <!-- <i class="icon-close h4 ms-auto me-0"></i> -->
                  
              </div>
              <hr />
              <div class="row px-1 align-items-center">
                  <span class="col-1 ms-3 h6"> </span>
                  <input class="col-9 rounded border-0" type="text" placeholder="Search.." id="followersuser"/>
              </div>
              <hr />
              <div class="share_search_result px-3" id="serach_followersuser">
                @php
                  $user_searches = app\Models\Follow::select('users.id as user_id', 'users.username', 'users.profile_pic', 'follow.*')->join('users', 'users.id', '=', 'follow.from_user')->where('to_user', Auth::id())->get();
                @endphp
                <ul id="myList">
                @foreach($user_searches as $follwowers)
                  <li>
                    <div class="d-flex ps-2 hover_effoct rounded-3 py-2">
                        
                      <img src="{{ url('assets/images/user/'. $follwowers->profile_pic) }}" class="share_result_image" alt="" />
                      <div class="d-flex flex-column align-items-center">
                        <a href="{{ url('profile/'.$follwowers->username) }}">
                          <b>{{ $follwowers->username }}</b>
                        </a>
                          <div>{{ $follwowers->fullname }}</div>
                      </div>
                      @php
                      
                        $ifollow = App\Models\Follow::where('from_user', $follwowers->user_id)->where('to_user', Auth::id())->first();
                        
                      @endphp
                      @if($ifollow->status)
                        @if($ifollow->status == "follow")
                          <div class="btn_div">
                            <div class="following_btn" id="follow{{$follwowers->user_id}}" onclick="follow({{$follwowers->user_id}})">Following</div>
                          </div>
                        @else
                          <div class="btn_div">
                            <div class="following_btn" id="follow{{$follwowers->user_id}}" onclick="follow({{$follwowers->user_id}})">Requested</div>
                          </div>
                        @endif
                      @else
                        <div class="btn_div">
                          <div class="follow_btn" id="follow{{$follwowers->user_id}}" onclick="follow({{$follwowers->user_id}})">Follow</div>
                        </div>
                      @endif
                    </div>
                  </li>
                @endforeach
                </ul>
              </div>
              <hr />
              <div class="px-2">
                  
              </div>
              <!-- </div>  -->
          </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="width:800px" >
      <div class="modal-content">
        <div class="modal-body">
          <div class="card-body">
            <div class="stepwizard">
              <div class="stepwizard-row setup-panel">
                <div class="stepwizard-step"><a class="btn btn-primary" href="#step-1">1</a>
                  <p>Step 1</p>
                </div>
                <div class="stepwizard-step"><a class="btn btn-light" href="#step-2">2</a>
                  <p>Step 2</p>
                </div>
                <div class="stepwizard-step"><a class="btn btn-light" href="#step-3">3</a>
                  <p>Step 3</p>
                </div>
              </div>
            </div>
            <form action="{{url('add-post-image')}}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="setup-content" id="step-1">
                <div class="col-xs-12">
                  <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label">Chose file</label>
                    <input class="form-control" type="file" required="required" name="image" onchange="loadFile(event)"><br>
                    <p><img id="output" /></p>
                  </div>
                  <button class="btn btn-primary nextBtn pull-right" type="button">Next</button>
                  </div>
                </div>
              </div>
              <div class="setup-content" id="step-2">
                <div class="col-xs-12">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="control-label">Caption</label>
                      <textarea class="form-control" type="password" name="caption" placeholder="Write a Caption" cols="3" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                      <label class="control-label">Location</label>
                      {{-- <input class="form-control" type="text" name="location" placeholder="Add Location" required="required"> --}}
                      <input class="form-control" name="location" id="pac-input">
                    </div>
                  
                    {{-- <div class="form-group creat-map-img">
                      <div class="text-lg-center alert-danger" id="info"></div> 
                      <div id="map"></div>
                    </div> --}}
                  <button class="btn btn-primary nextBtn pull-right" type="button">Next</button>
                  </div>
                </div>
              </div>
              <div class="setup-content" id="step-3">
                <div class="col-xs-12">
                  <div class="col-md-12">
                    <div class="message_popup py-3">
                      <div class="d-flex justify-content-center px-3">
                          <span class="h5">Tag</span>
                          <!-- <i class="icon-close h4 ms-auto me-0"></i> -->
                      </div>
                      <hr />
                      <div class="row px-1 align-items-center">
                          <span class="col-2 ms-3 h6">To &nbsp;: </span>
                          <input
                              class="col-9 rounded border-0"
                              type="text"
                              placeholder="Search.."
                              id="inputuser"
                          />
                      </div>
                      <hr />
                      <div class="share_search_result px-3" id="serach_user_tag">
                          @php 
                            $follow = App\Models\Follow::select('users.id', 'users.profile_pic', 'users.username')->join('users', 'users.id', '=', 'follow.to_user')->where('from_user', '=', Auth::id())->get();
                          @endphp
                          <ul id="myList">
                          @foreach($follow as $user_v)
                            <li>
                              <div
                                  class="d-flex ps-2 hover_effoct rounded-3 py-2 tagpost"
                                  id="{{ $user_v->id }}"
                              >
                                  <img
                                      src="{{ asset('assets/images/user/'.$user_v->profile_pic) }}"
                                      class="share_result_image"
                                      alt=""
                                  />
                                  <div class="d-flex flex-column align-items-center">
                                      <b>{{ $user_v->username }}</b>
                                      <div>{{ $user_v->fullname }}</div>
                                  </div>
                                  <div class="radio_button"></div>
                              </div>
                            </li>
                          @endforeach
                          </ul>
                      </div>
      
                      <div class="px-2">
                          <input type="hidden" id="share_user_id" name="tag_user" />
                          {{-- <button
                              class="btn btn-primary w-100 py-2"
                              id="text1"
                              onclick="sendpost()"
                          >
                              Send
                          </button> --}}
                      </div>
                      <!-- </div>  -->
                  </div>
                  <input class="btn btn-secondary pull-right" type="submit" value="Share" name="submit">
                  </div>
                </div>
              </div>
              
            </form>
            </div>
                        
        </div>
      </div>
    </div>
</div>

<!-- Search box -->
<div class="modal fade" id="searchuser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="width:400px;" >
      <div class="modal-content">
        
      <div class="message_popup py-3">
                <div class="d-flex justify-content-center px-3">
                    <span class="h5">Search</span>
                    <!-- <i class="icon-close h4 ms-auto me-0"></i> -->
                    
                </div>
                <hr />
                <div class="row px-1 align-items-center">
                    <span class="col-1 ms-3 h6"> </span>
                    <input class="col-9 rounded border-0" type="text" placeholder="Search.." onkeyup="find_user(event)"/>
                </div>
                <hr />
                <div class="share_search_result px-3" id="find_users">
                  @php
                    $user_searches = app\Models\User::where('id', '!=', Auth::id())->get();
                  @endphp

                  @foreach($user_searches as $users)
                    <a href="{{ url('profile/'.$users->username) }}">
                      <div class="d-flex ps-2 hover_effoct rounded-3 py-2">
                          
                        <img src="{{ url('assets/images/user/'. $users->profile_pic) }}" class="share_result_image" alt="" />
                        <div class="d-flex flex-column align-items-center">
                            <b>{{ $users->username }}</b>
                            <div>{{ $users->fullname }}</div>
                        </div>
                          
                      </div>
                    </a>
                  @endforeach
                </div>
                <hr />
                <div class="px-2">
                    
                </div>
                <!-- </div>  -->
            </div>
      </div>
    </div>
</div>

<!-- Notification box -->
<div class="modal fade" id="notificationbox" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="width:1100px" >
      <div class="modal-content">
      <div class="message_popup py-3">
                <div class="d-flex justify-content-center px-3">
                    <span class="h5">Notifications</span>
                    <!-- <i class="icon-close h4 ms-auto me-0"></i> -->
                </div>

                <hr />
                <div class="notification ">
                  @php
                    $user_notification = App\Models\user_notification::select('users.id', 'users.username', 'users.profile_pic', 'user_notification.*')->where('to_user', Auth::id())->join('users', 'users.id', '=', 'user_notification.from_user')->orderBy('not_id', 'DESC')->get();
                  @endphp
                  
                  @foreach($user_notification as $res_notify)
                    
                    <div class="d-flex ps-2 hover_effoct rounded-3 py-2">
                        <img
                            src="{{ url('assets/images/user/'. $res_notify->profile_pic) }}"
                            class="share_result_image my-auto"
                            alt="" style="width: 56px"
                        />
                        <div class="d-flex flex-column ">
                            <a href="{{ url('profile/'.$res_notify->username) }}"><b>{{ $res_notify->username }}</b></a>
                            <div>{{ $res_notify->title }} : {{ $res_notify->message }} 
                              <span class="time">
                                
                                {{ $res_notify->created_at->diffForHumans()}}
                            </span>
                          </div>
                        </div>  
                        
                        @if($res_notify->title == "Follow Requests")
                            <div class="btn_div" id="follow_proccess{{$res_notify->not_id}}">
                              <div class="follow_btn" onclick="follow_notify({{$res_notify->id}}, 'confirm', {{$res_notify->not_id}})">Confirm</div>
                              <div class="following_btn" onclick="follow_notify({{$res_notify->id}}, 'delete', {{$res_notify->not_id}})">Delete</div>
                            </div> 
                        @elseif($res_notify->title == "Follow" || $res_notify->title == "Requests Accepted")  
                            @php
                              $follow_check = DB::table('follow')->where('from_user', Auth::id())->where('to_user', $res_notify->from_user)->count();
                            @endphp
                            
                            @if($follow_check == 1)
                              <div class="btn_div" id="follow_proccess{{$res_notify->not_id}}">
                                <div class="following_btn" onclick="follow_notify({{$res_notify->id}}, 'following', {{$res_notify->not_id}})">Following</div>
                              </div> 
                            @else    
                              <div class="btn_div" id="follow_proccess{{$res_notify->not_id}}">
                                <div class="follow_btn" onclick="follow_notify({{$res_notify->id}}, 'follow', {{$res_notify->not_id}})">Follow</div>
                              </div> 
                            @endif
                          @elseif($res_notify->title == "like" || $res_notify->title == "comment")
                            <div class="btn_div">
                              @php
                                $post = App\Models\Post::select('image')->where('post_id', $res_notify->post_id)->first();
                              @endphp
                              <img src="{{ url('assets/images/posts/'. $post->image) }}" alt="" style="width:50px; height:50px;">
                            </div>
                          @endif
                    </div>
                  @endforeach
                    
                </div>
                <hr />
                <div class="px-2">
                    
                </div>
                <!-- </div>  -->
            </div>
      </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $("#followinguser").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#serach_followinguser li").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

$(document).ready(function(){
  $("#followersuser").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#serach_followersuser li").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

$(document).ready(function(){
  $("#inputuser").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#serach_user_tag li").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

  var loadFile = function(event) {
    var image = document.getElementById('output');
    image.src = URL.createObjectURL(event.target.files[0]);
  };

  function notifycount(){
    $.ajax({
        url:"{{route('notifycount')}}",
        method:"GET",
        dataType:'JSON',
        success:function(dataResult)
        {
            $('#counthide').hide();
            
        },error: function (dataResult) {
            console.log(dataResult)
        }
    });
  }

  function follow_notify(id, type, not_id) {
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            });
          
          $.ajax({
            url:"{{route('follow_notify')}}",
            method:"POST",
            data: {id : id, type : type, not_id : not_id},
            dataType:'HTML',
            success:function(dataResult)
            {
                if(dataResult == "deleted"){
                    $('#follow_proccess'+not_id).hide();
                }else{
                    $('#follow_proccess'+not_id).html(dataResult);
                }
            },error: function (dataResult) {
               console.log(dataResult)
            }
        });
    }
  </script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAMZ4GbRFYSevy7tMaiH5s0JmMBBXc0qBA&callback=initAutocomplete&libraries=places&v=weekly" async></script>
<script type="text/javascript">

  // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
    let autocomplete;

/* ------------------------- Initialize Autocomplete ------------------------ */
function initAutocomplete() {
    const input = document.getElementById("pac-input");
    const options = {
        componentRestrictions: { country: "IN" }
    }
    autocomplete = new google.maps.places.Autocomplete(input, options);
    autocomplete.addListener("place_changed", onPlaceChange)
}

/* --------------------------- Handle Place Change -------------------------- */
function onPlaceChange() {
    const place = autocomplete.getPlace();
    console.log(place.formatted_address)
    console.log(place.geometry.location.lat())
    console.log(place.geometry.location.lng())
}
</script>

