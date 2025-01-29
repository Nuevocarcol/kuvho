@extends('layouts.admin.master')

@section('title')Chat App
@endsection

@push('css')
<style>
    .main_div {
        /* border: 2px solid gray; */
        width: 20rem;
        /* height: 25rem; */
        display: flex;
        flex-direction: column;
        border-radius: 1rem;
        background-color: rgba(207, 207, 207, 0.431);
      }
      .profile_image {
        width: 2.7rem;
        height: 2.7rem;
        object-fit: cover;
        border-radius: 10rem;
      }
      .profile_details {
        font-weight: 600;
        display: flex;
        /* justify-content: center; */
        align-items: center;
        gap: 0.7rem;
        padding: 0.5rem;
      }
      .post_image {
        height: 23rem;
        object-fit: cover;
        object-position: center;
      }
      .post_description {
        padding-block: 1rem;
        padding-inline: 1rem;
      }
    /* .custom-scrollbar{
        display: flex;
        flex-direction: column-reverse;
    } */

    .page-body{
        padding-top: 0px !important;
        margin-left: 0px !important;
    }
    .mesage-image{
        width: 100%;
        height: auto;
    }
    /* .mesage-image{
        width: 200px;
        height: 200px;
    } */
    #chatContent{
        /* width:50vw; */
        margin-top:100px;
        
    }
    .choosefile_opacity{
        opacity: 0;
    }
    .popup_image{
        position: absolute;
        padding: .1rem;
        bottom: 30px;
        height: 200px;
        width: 300px;
        /* border: 2px solid rgb(196, 196, 196); */
        
    }
    @media (min-width: 768px) and (max-width:1024px) {
        #chatContent{
        /* width:50vw; */
        margin-top:100px;
        
        /* position:relative; */
    }
    }
    @media (max-width: 768px) {
        #call-chat-sidebar{
            display: none; 
        }
        #chatContent{
            width:90vw;
            margin-top:100px;
            /* position:relative; */
        }
    }
    @media (max-width: 585px) {
        /* .mesage-image{
        width: 190px;
        height: 200px;
    } */
        .popup_image{
            position: absolute;
            padding: .1rem;
            bottom: 30px;
            height: 150px;
            width: 250px;
            /* border: 2px solid rgb(196, 196, 196); */
            
        }
        #chatContent{
            width:100vw;
            margin-top:100px;
            /* position:relative; */
        }
    }
    @media (min-width: 1024px) {
        #chatContent{
            margin-left: 300px;
            width: 70vw;
        }
    }
    @media (min-width: 1324px) {
        #chatContent{
            margin-left: 550px;
            width: 60vw;
        }
    }

    #call-chat-sidebar{
        /* display: none; */}



</style>
@endpush

@section('content')
    <div class="container-fluid" id="chatContent">
        <div class="row">
            <div class="col call-chat-sidebar" id="call-chat-sidebar"> 
                <div class="card"  style="margin-bottom: 0px !important;">
                    <div class="card-body chat-body">
                        <div class="chat-box">
                            <!-- Chat left side Start-->
                            <div class="chat-left-aside">
                                <div class="media">
                                    @if(!empty($auth->profile_pic))
                                    <img class="rounded-circle user-image" src="{{asset('assets/images/user/'.$auth->profile_pic)}}" alt="" />
                                    @endif
                                    <div class="media-body">
                                        <div class="about">
                                            <div class="name f-w-600">{{$auth->username}}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="people-list" id="people-list">
                                    <div class="search">
                                        <form class="theme-form">
                                            <div class="form-group"><input class="form-control" type="text" placeholder="search"  onkeyup="search_user(this.value);"/><i class="fa fa-search"></i></div>
                                        </form>
                                    </div>
                                    <ul class="list custom-scrollbar" id="searched_user">
                                        @php 
                                $user_id = Auth::id();
                                $data = App\Models\User::select('id')
                                ->whereIn('id', function ($q) use ($user_id){
                                    $q->select('to_user')->from('chats')->where('from_user', $user_id);
                                })->orwhereIn('id', function ($q) use ($user_id){
                                    $q->select('from_user')->from('chats')->where('to_user', $user_id);
                                })
                                ->get();
                                $data_Ar = array();
                                foreach($data as $row){
                                    $last_message_query = App\Models\Chat::where(function ($q) use ($user_id, $row){
                                        $q->where('from_user', $user_id)
                                        ->where('to_user', $row->id);
                                    })->orwhere(function ($q) use ($user_id, $row){
                                        $q->where('from_user', $row->id)
                                        ->where('to_user', $user_id);
                                    })->orderBy('created_at', 'DESC')->first();
                                    
                                    $chat_list['id'] = $last_message_query->id;
                                    $chat_list['from_user'] = $last_message_query->from_user;
                                    $chat_list['to_user'] = $last_message_query->to_user;
                                    $chat_list['timestamp'] = $last_message_query->timestamp;
                                    if($last_message_query->url){
                                        $chat_list['last_message'] = "files";
                                    }
                                    elseif($last_message_query->message){
                                        $chat_list['last_message'] = $last_message_query->message;
                                    }
                                    elseif($last_message_query->send_story){
                                        $chat_list['last_message'] = "story";
                                    }
                                    elseif($last_message_query->send_post){
                                        $chat_list['last_message'] = "posts";
                                    }
                                
                                    $user = App\Models\User::where('id', $row->id)->first();
                                    
                                    $chat_list['user_id'] = $user->id;
                                    $chat_list['username'] = $user->username;
                                    $chat_list['email'] = $user->email;
                                    $chat_list['profile_pic'] = ($user->profile_pic) ? $user->profile_pic : "";
                                    $chat_list['time'] = $user->created_at;
                                    $chat_list['unread_message'] = App\Models\Chat::where('from_user', $row->id)->where('to_user', Auth::id())->where('read_message', 0)->count();
                                    
                                    array_push($data_Ar, $chat_list);
                                }
                                $data_Ar = collect($data_Ar)->sortByDesc('timestamp')->toArray();
                            @endphp
                            @foreach($data_Ar as $row)
                                        <li class="clearfix">
                                            <a href="{{url('chat/?eid='.$row['user_id'])}}">
                                            <div class="media">
                                                <img class="rounded-circle user-image" src="{{asset('assets/images/user/'.$row['profile_pic'])}}" alt="" />
                                                <div class="status-circle away"></div>
                                                <div class="media-body">
                                                    <div class="about">
                                                        <div class="name">{{$row['username']}}</div>
                                                        <div class="status"><?php echo strlen($row['last_message']) > 20 ? substr($row['last_message'], 0, 20) . '...' : $row['last_message']; ?></div>
                                                        
                                                    </div>
                                                </div>
                                                @if($row['unread_message'])
                                                <p style="margin-left:100px;  margin-top:10px !important; z-index:2; border-radius: 20px;background-color: #000000;padding: 0px !important;font-weight:400 !important;
                                                padding-inline: 5px !important;color:white !important;font-size: 12px !important;" id="ncounthide">{{ $row['unread_message'] }}</p>
                                                @endif
                                                
                                            </div>
                                            </a>
                                        </li>
                            @endforeach
                                    </ul>
                                </div>
                            </div>
                            <!-- Chat left side Ends-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="col call-chat-body">
                <div class="card h-100">
                    <div class="card-body p-0 h-100">
                        <div class="row chat-box h-100">
                            <!-- Chat right side start-->
                            @if(isset($_GET['eid']) && $_GET['eid'] != "")
                            <div class="col chat-right-aside h-100">
                                <!-- chat start-->
                                <div class="chat">
                                    <!-- chat-header start-->
                                    <div class="media chat-header clearfix">
                                        @if(isset($_GET['eid']) && $_GET['eid'] != "")
                                        @php 
                                            $to_user = App\Models\User::where('id', $_GET['eid'])->first();
                                            
                                        @endphp
                                        <img class="rounded-circle" src="{{asset('assets/images/user/'.$to_user->profile_pic)}}" alt=""  style="height: 50px; width:50px"/>
                                        <div class="media-body">
                                            <div class="about">
                                                
                                                <div class="name"><a href="{{ url('profile/'.$to_user->username) }}">{{$to_user->username}}</a><span class="font-primary f-12"></span></div>
                                            </div>
                                        </div>
                                        @endif
                                        <ul class="list-inline float-start float-sm-end chat-menu-icons">
                                            
                                            <li class="list-inline-item toogle-bar">
                                                <a href="javascript:void(0)"><i class="icon-menu"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- chat-header end-->
                                    

                                        @php 
                                            $messages = App\Models\Chat::select('*')->where([['from_user', Auth::id()], ['to_user', $_GET['eid']]])->orWhere([['from_user', $_GET['eid']], ['to_user', Auth::id()]])->get();
                                            
                                        @endphp
                                    <div class="chat-history chat-msg-box custom-scrollbar" style="display: flex; flex-direction: column-reverse;">
                                        <ul id="chat-messages"> 
                                        </ul>
                                        
                                    </div>
                                    
                                    <!-- end chat-history-->
                                    <form action="javascript:void(0)" method="get" accept-charset="UTF-8" id="send-to-message" enctype="multipart/form-data"> 
                                    <div class="chat-message clearfix">
                                        <div class="row">
                                            <div class="col-xl-12 d-flex">
                                                <div class="">
                                                    <div class="picker" style="width: 50px;padding-block: 3px;">
                                                        <!-- <img src="{{asset('assets/images/smiley.png')}}" alt="" /> -->
                                                        <div class="position-absolute">
                                                            <i class="fa fa-image h2 py-1 "></i>
                                                        </div>
                                                        <input class="form-control input-txt-bx choosefile_opacity  " id="message_image" type="file" name="message_image" placeholder="" onchange="loadImage(event)"/>
                                                    </div>
                                                </div>
                                                
                                                    <div class="input-group text-box">
                                                        <img id="sent_image"  alt="" class=""/>


                                                        <input class="form-control input-txt-bx col-10 f-w-600 py-0" style="height: 45px; " id="message" type="text" name="message"        placeholder="Type a message......" />
                                                        <div class='alert alert-danger mt-2 d-none text-danger' id="err_file"></div>
                                                        <input type="hidden" id="to_user" value="{{$to_user->id}}" name="to_user">
                                                        <button class="btn btn-primary input-group-text" type="submit" id="insert">SEND</button>
                                                    </div>
                                                
                                            </div>
                                        </div>
                                    </div></form>
                                    
                                    <!-- end chat-message-->
                                    <!-- chat end-->
                                    <!-- Chat right side ends-->
                                </div>
                            </div>
                            <div class="col chat-menu">
                                <ul class="nav nav-tabs border-tab nav-primary" id="info-tab" role="tablist">
                                    <li class="nav-item">
                                        {{-- <a class="nav-link active" id="info-home-tab" data-bs-toggle="tab" href="#info-home" role="tab" aria-selected="true">CALL</a> --}}
                                        <div class="material-border"></div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active" id="contact-info-tab" data-bs-toggle="tab" href="#info-contact" role="tab" aria-selected="false">PROFILE</a>
                                        <div class="material-border"></div>
                                    </li>
                                    <li class="nav-item">
                                        {{-- <a class="nav-link" id="contact-info-tab" data-bs-toggle="tab" href="#info-contact" role="tab" aria-selected="false">PROFILE</a> --}}
                                        <div class="material-border"></div>
                                    </li>
                                </ul>
                                <div class="tab-content" id="info-tabContent">
                                    
                                    <div class="tab-pane fade show active" id="info-contact" role="tabpanel" aria-labelledby="contact-info-tab">
                                        <div class="user-profile">
                                            <div class="image">
                                                <div class="avatar text-center"><img alt="" src="{{asset('assets/images/user/'.$to_user->profile_pic)}}" /></div>
                                                
                                            </div>
                                            <div class="user-content text-center">
                                                <h5 class="text"><a href="{{ url('profile/'.$to_user->username) }}">{{$user->username}}</a></h5>
                                                <div class="follow text-center">
                                                    <div class="row">
                                                        <div class="col border-right">
                                                            <span>Following</span>
                                                            <div class="follow-num">{{$following}}</div>
                                                        </div>
                                                        <div class="col">
                                                            <span>Follower</span>
                                                            <div class="follow-num">{{$followers}}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="text-center digits">
                                                    <p>{{$user->email}}</p>
                                                    <p>{{$user->bio}}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                                    <div class="card h-100 rounded-0">
                                        <div class="d-flex flex-column h-100 w-100 align-items-center justify-content-center">
                                            <div style="font-size:9rem" class="text-muted"><span class="fa fa-comments"></span></div>
                                            <div class="text-muted">Start a Conversation Now</div>
                                        </div>
                                    </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php 
    if(isset($_GET['eid']) && $_GET['eid'] != "")
    {
    $get_id = $_GET['eid'];
    $to_user = $to_user->id;
    }else{
        $get_id = "";
        $to_user = "";
    }
    @endphp
    @push('scripts')
    <script src="{{asset('assets/js/fullscreen.js')}}"></script>
    @endpush

    @push('scripts')

        <script>
            
            $(document).ready(function(){
                var to_user_url = "{{$get_id}}";
                var SITEURL = '{{URL::to('')}}';
                
                $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            });
                $.ajax({
                    type: "get",
                    url: SITEURL + "/chat/messages/"+to_user_url,
                    dataType:'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (dataResult) {
                        console.log(dataResult.data);
                        $('#chat-messages').html(dataResult.message);
                    },
                    error: function (dataResult) {
                    console.log(dataResult)
                    }
                });
            });
        </script>

        <script>
        var loadImage = function(event) {
            
            var image = document.getElementById('sent_image');
            image.src = URL.createObjectURL(event.target.files[0]);
            image.classList.add("popup_image");
            image.style.display = "block";

        };

        $('#send-to-message').on('submit',function(e){
                e.preventDefault();
                var image = document.getElementById('sent_image');
                image.style.display = "none";

                var message = $("#message").val();
                var message_image = document.getElementById('message_image').value;
                // var message_image = document.getElementById('message_image').value;
                
                //   var message = $("#message").val();
                //   alert(message_image)
            
            if((message == "") && (!message_image)){
                return false;
            }else{
            $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = new FormData($("#send-to-message")[0]);
                $.ajax({
                url:"{{route('chat/send')}}",
                method:"POST",
                data:formData,
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success:function(dataResult)
                {
                //    alert(1);
                //  console.log(dataResult.message);
                    $('#message').val('');
                    $('#message_image').val('');
                    $('#chat-messages').append(dataResult.message);
                },
                error:err=>{
                    console.log(err);
                }
            })
            
            }
            
        });
            
        </script>
        
        <script>
            function search_user(user){
                $(document).ready(function(){
                    var SITEURL = '{{URL::to('')}}';
                    $.ajax({
                        type: "get",
                        url: "{{url('chat/user')}}",
                        dataType : 'json',
                        data : {user : user},
                        success: function (dataResult){
                            //alert(dataResult.message);
                            $('#searched_user').html(dataResult.user_list);
                        },error:err=>{
                            console.log(err);
                        }
                    });
                });
            }
        </script>

        <script>
            // const element = document.getElementById("chat_scroll");
            // element.scrollTo(1000,0 );

            $(function(){
                var to_user = '{{$to_user}}';
                j = 0;
                setInterval(() => {
                    $.ajax({
                        type: "get",
                        url: "{{url('chat/real-time')}}",
                        dataType : 'json',
                        data : {user : to_user},
                        success: function (dataResult){
                            //alert(dataResult.message);
                            $('#chat-messages').append(dataResult.message);
                        },error:err=>{
                            console.log(err);
                        }
                    });
                    // console.log(j);
                    // j++;
                }, 1000);
            });
        </script>
    @endpush

@endsection