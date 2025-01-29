@extends('layouts.admin.master') @section('title') Home @endsection @push('css')
<link
    rel="stylesheet"
    type="text/css"
    href="{{ asset('assets/css/photoswipe.css') }}"
/>

@endpush

@push('css')
<style>
    .user-post {
        width: 482px;
        height: 320px;
    }

    .info_border {
        border: 1px solid rgb(211, 211, 211);
        border-inline: 0px solid;
    }
    .block_icon {
        width: fit-content !important;
    }
    @media screen and (min-width: 1950px) {
        .profile {
            padding: 30px 350px 0 !important;
        }
        /*
        .profile-header2 {
            padding: 30px 300px 0 !important;
        }
        */
    }

    @media screen and (min-width: 1250px) {
        .profile {
            padding: 30px 150px 0 !important;
        }
        .text_style {
            font-size: 0.9rem !important;
        }
        .heading_style {
            font-size: 1.2rem !important;
        }
        .maximum_width {
            padding-inline: 7rem;
        }
        /*
        .profile-header2 {
            padding: 30px 200px 0 !important;
        } */
    }

    @media screen and (min-width: 768px) {
        .profile_image {
            width: 100px;
            margin-right: 30px;
        }
        .media-body {
            margin-block: 10px;
        }
    }
    @media screen and (max-width: 768px) {
        .profile_image {
            width: 100px;
            margin-right: 30px;
        }
        .media-body {
            margin-block: 20px;
        }
    }
    /*
        .profile {
            padding: 30px 180px 0 !important;
        }

        .profile-header2 {
            padding: 30px 120px 0 !important;
        }
     */

    @media screen and (max-width: 576px) {
        .profile_image {
            width: 75px;
            margin-right: 20px;
        }
        .media-body {
            margin-block: 15px;
        }
    }
    /*
        .profile {
            padding: 10px 0px 0 !important;
        }

        .profile-header2 {
            padding: 30px 0px 0 !important;
        }
     */

    .comment-image {
        width: 600px;
        max-height: 100%;
        min-height: 650px;
    }

    .modal-xl {
        max-width: 1300px !important;
    }

    .product-box {
        width: 1300px !important;
    }

    .scoller-comment {
        overflow: auto;
    }

    #IMG {
        width: 100%;
        padding-inline: 2px;
        height: auto;
        object-fit: contain;
        background-color: black;
        max-height: 80vh;
    }
    #comments-scroll {
        height: 100%;
        /* overflow-y: scroll; */
    }
    #comments {
        overflow-y: scroll;
    height: fit-content;
    max-height: 23rem;
    }
    /* #cancel-btn{
        margin-right: 10px;
        margin-top: 10px;
    } */
    #model-image {
    }
    @media (max-width: 1200px) {
        #post-details {
            display: grid;
            place-content: center;
        }
        /* #IMG {
         width: 100%;
            height: auto;
            object-fit: cover;
     } */

        #comments {
            height: 100%;
            overflow: unset;
            padding-inline: 25px;
        }
    }
    @media (max-width: 575px) {
        #comments {
            padding-inline: 10px;
        }
    }

 /* highlight style */

    .story_container {
        /* color: white; */
        /* background-color: black; */
        height: fit-content;
        /* margin-inline: 26rem; */
        display: flex;
        overflow-x: scroll;
        gap: 1rem;
        margin-top: 2rem;
    }
    /* ------------------------------------------------------------------ */
    .story_image {
        height: 5rem;
        width: 5rem;
        border-radius: 10rem;
        margin-bottom: 0.4rem;
        object-fit: cover;
        cursor: pointer;
    }
    
    .story_image_border {
        /* background-image: linear-gradient(to bottom right, red, yellow); */
        padding: 3px;
        border: 1px solid rgba(0, 0, 0, 0.209);
    }
    @media (max-width: 575px) {
        #comments {
            padding-inline: 10px;
        }
        .story_image {
            height: 4rem;
            width: 4rem;
        }
        .story_container {
            /* color: white; */
            /* background-color: black; */
            height: fit-content;
            /* margin-inline: 0.7rem; */
            display: flex;
            overflow-x: scroll;
            gap: 0.7rem;
            font-size: smaller;
        }
    }
    @media (min-width: 575px) {
        .story_container {
            /* margin-inline: 2rem; */
            font-size: smaller;
        }
    }

    @media (min-width: 1220px) {
        .story_container {
            /* margin-inline: 10rem; */
            font-size: smaller;
            gap: 1rem;
        }
    }

    @media (min-width: 1440px) {
        .story_container {
            /* margin-inline: 10rem; */
            font-size: smaller;
            gap: 1.5rem;
        }
    }
    @media (min-width: 1740px) {
        .story_container {
            /* margin-inline: 26rem; */
            font-size: smaller;
            gap: 2rem;
        }
    }
    .story_container::-webkit-scrollbar {
        display: none;
    }

    .image-hover{
        position: relative;
    }
   
    .imagepost{
        opacity: 1;
        display: block;
        height: auto;
        transition: .5s ease;
        backface-visibility: hidden;
    }

   .middle {
        transition: .5s ease;
        opacity: 0;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        text-align: center;
    }

    .image-hover:hover .imagepost {
        opacity: 0.2;
    }

    .image-hover:hover .middle {
        opacity: 1;
    }

    .text {
        background-color: #04AA6D;
        color: white;
        font-size: 16px;
        padding: 16px 32px;
    }
</style>

@endpush
@section('custom-meta')
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css"
/>
@endsection @section('content') 

<div class="container profile">
    <div class="user-profile social-app-profile">
        <div class="row">
            <!-- <div class="col"></div> -->
            <div class=" ">
                <div class="default-according style-1 faq-accordion m-0">
                    <div class="row">
                        <!-- <div class="col-xl-6"></div> -->
                        <div class="col-xl-12 col-md-12 col-sm-12">
                            <div class="card border-0">
                                <div
                                    class="collapse show"
                                    id="collapseicon"
                                    aria-labelledby="collapseicon"
                                >
                                    <div
                                        class="card-body socialprofile filter-cards-view w-full"
                                    >
                                        <div class="media">
                                            @if($user_detail->profile_pic)
                                                <img class="img-fluid rounded-circle profile_image" src="{{asset('assets/images/user/'.$user_detail->profile_pic)}}" alt="" style="width:100px; height:100px;"/>
                                            @else
                                                <img class="img-fluid rounded-circle profile_image" src="{{asset('assets/images/user/1.jpg')}}" alt="" style="width:100px; height:100px;"/>
                                            @endif

                                            <div class="media-body flex">
                                                <h5 class="font-primary f-w-600 heading_style">
                                                    {{$user_detail->username}}
                                                    @if($user_detail->id ==
                                                    Auth::id()) &nbsp;<a
                                                        href="edit"
                                                        class="btn btn-pill btn-light btn-sm"
                                                        >Edit profile</a
                                                    >
                                                </h5>
                                                @endif
                                                <span class="d-block my-1">
                                                    <span>
                                                        <span
                                                            class="text_style"
                                                            >{{$user_detail->fullname}}</span
                                                        >
                                                    </span>
                                                </span>
                                                <span class="d-block my-1">
                                                    <span>
                                                        <span
                                                            class="text_style f-w-400 h3"
                                                            >{{$user_detail->bio}}</span
                                                        >
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="social-btngroup d-flex maximum_width">
                                            @if($user_detail->id != Auth::id())
                                                @if($follow == 0)
                                                <button
                                                    type="button"
                                                    class="btn btn-primary text-center me-2"
                                                    id="follow"
                                                    onclick="follow({{$user_detail->id}})"
                                                >
                                                    Follow
                                                </button>
                                                @elseif($follow == 1)
                                                <button
                                                    type="button"
                                                    class="btn btn-primary text-center me-2"
                                                    id="follow"
                                                    onclick="follow({{$user_detail->id}})"
                                                >
                                                    Requested
                                                </button>
                                                @else
                                                <button
                                                    type="button"
                                                    class="btn btn-primary-light text-center me-2"
                                                    id="follow"
                                                    onclick="follow({{$user_detail->id}})"
                                                >
                                                    Unfollow
                                                </button>
                                                <button
                                                    class="btn btn-primary-light text-center me-2"
                                                    type="button"
                                                    onclick="window.location.href='{{url('chat/?eid='.$user_detail->id)}}';"
                                                >
                                                    Message
                                                </button>

                                                    @if($block_class == "btn-primary-light")
                                                        <button title="Block" class="btn {{ $block_class }} text-center block_icon" type="button" onclick="blockuser({{Auth::id()}})" id="block">
                                                            Block
                                                        </button>
                                                    @else
                                                        <button title="Block" class="btn {{ $block_class }} text-center block_icon" type="button" onclick="blockuser({{Auth::id()}})" id="block">
                                                            Blocked
                                                        </button>
                                                    @endif
                                                @endif 
                                            @endif
                                            
                                        </div>
                                        <div class="user-designation">
                                            <div class="follow">
                                                <ul
                                                    class="d-flex justify-content-evenly info_border py-2"
                                                >
                                                    <li class="text-center">
                                                        <div
                                                            class="follow-num counter"
                                                        >
                                                            {{ $posts }}
                                                        </div>
                                                        <span>Post</span>
                                                    </li>
                                                    <li class="text-center">
                                                        <div
                                                            class="follow-num counter"
                                                        >
                                                            {{ $followers }}
                                                        </div>
                                                        <span>Followers</span>
                                                    </li>
                                                    <li class="text-center">
                                                        <div
                                                            class="follow-num counter"
                                                        >
                                                            {{ $following }}
                                                        </div>
                                                        <span>Following</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <!-- Highlight start -->
                                        <div class="story_container">
                                            @foreach($highlight as $highlight_value)
                                            <div>
                                                <a href="{{ url('story-highlight/'.$highlight_value['highlight_id']) }}">
                                                    <img
                                                        src="{{ asset('assets/images/story/'.$highlight_value['cover_pic']) }}"
                                                        class="story_image story_image_border"
                                                        alt=""
                                                    />
                                                    <div
                                                        class="w-100 text-center"
                                                    >
                                                        {{ $highlight_value['title'] }}
                                                    </div>
                                                </a>
                                            </div>
                                            @endforeach

                                        </div>
                                        <!-- Highlight end -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($flag == 1)
            <div class="social-tab" style="justify-content: center">
                <ul class="nav nav-tabs" id="top-tab" role="tablist">
                    <li class="nav-item">
                        <a
                            class="nav-link"
                            id="top-photos"
                            data-bs-toggle="tab"
                            href="#photos"
                            role="tab"
                            aria-controls="photos"
                            aria-selected="false"
                            ><i data-feather="image"></i>POSTS</a
                        >
                    </li>
                    <li class="nav-item">
                        <a
                            class="nav-link"
                            id="top-friends"
                            data-bs-toggle="tab"
                            href="#friends"
                            role="tab"
                            aria-controls="friends"
                            aria-selected="false"
                            ><i data-feather="users"></i>TAGGED</a
                        >
                    </li>
                    @if($user_detail->id==Auth::id())
                        <li class="nav-item">
                            <a
                                class="nav-link"
                                id="top-friends"
                                data-bs-toggle="tab"
                                href="#saved"
                                role="tab"
                                aria-controls="friends"
                                aria-selected="false"
                                ><i data-feather="bookmark"></i>SAVED</a
                            >
                        </li>
                    @endif
                </ul>
            </div>
            <div class="tab-content" id="top-tabContent">
                <div
                    class="tab-pane fade show active"
                    id="photos"
                    role="tabpanel"
                    aria-labelledby="photos"
                >
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="my-gallery card-body" itemscope="">
                                    <div class="data-wrapper row gallery-with-description"></div>
                                    <div class="auto-load text-center">
                                        
                                    </div>
                                </div>
                                <!-- Root element of PhotoSwipe. Must have class pswp.-->
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="tab-pane fade"
                    id="friends"
                    role="tabpanel"
                    aria-labelledby="friends"
                >
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card">
                                <div class="my-gallery card-body" itemscope="">
                                    <div
                                        class="data-tag row gallery-with-description"
                                    ></div>
                                    <div class="auto-tag text-center">
                                        
                                    </div>
                                </div>
                                <!-- Root element of PhotoSwipe. Must have class pswp.-->
                               
                            </div>
                        </div>
                    </div>
                </div>

                @if($user_detail->id == Auth::id())
                    <div
                    class="tab-pane fade"
                    id="saved"
                    role="tabpanel"
                    aria-labelledby="photos"
                    >
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="my-gallery card-body" itemscope="">
                                        <div class="data-save row gallery-with-description"></div>
                                        <div class="auto-save text-center">
                                            <svg
                                                version="1.1"
                                                id="L9"
                                                xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink"
                                                x="0px"
                                                y="0px"
                                                height="60"
                                                viewBox="0 0 100 100"
                                                enable-background="new 0 0 0 0"
                                                xml:space="preserve"
                                            >
                                                <path
                                                    fill="#000"
                                                    d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50"
                                                >
                                                    <animateTransform
                                                        attributeName="transform"
                                                        attributeType="XML"
                                                        type="rotate"
                                                        dur="1s"
                                                        from="0 50 50"
                                                        to="360 50 50"
                                                        repeatCount="indefinite"
                                                    />
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <!-- Root element of PhotoSwipe. Must have class pswp.-->
                                    {{-- <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
                                        <!--
                                        Background of PhotoSwipe.
                                        It's a separate element, as animating opacity is faster than rgba().
                                        -->
                                        <div class="pswp__bg"></div>
                                        <!-- Slides wrapper with overflow:hidden.-->
                                        <div class="pswp__scroll-wrap">
                                            <!-- Container that holds slides. PhotoSwipe keeps only 3 slides in DOM to save memory.-->
                                            <!-- don't modify these 3 pswp__item elements, data is added later on.-->
                                            <div class="pswp__container">
                                                <div class="pswp__item"></div>
                                                <div class="pswp__item"></div>
                                                <div class="pswp__item"></div>
                                            </div>
                                            <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed.-->
                                            <div class="pswp__ui pswp__ui--hidden">
                                                <div class="pswp__top-bar">
                                                    <!-- Controls are self-explanatory. Order can be changed.-->
                                                    <div
                                                        class="pswp__counter"
                                                    ></div>
                                                    <button
                                                        class="pswp__button pswp__button--close"
                                                        title="Close (Esc)"
                                                    ></button>
                                                    <button
                                                        class="pswp__button pswp__button--share"
                                                        title="Share"
                                                    ></button>
                                                    <button
                                                        class="pswp__button pswp__button--fs"
                                                        title="Toggle fullscreen"
                                                    ></button>
                                                    <button
                                                        class="pswp__button pswp__button--zoom"
                                                        title="Zoom in/out"
                                                    ></button>
                                                    <!-- Presaveer demo https://codepen.io/dimsemenov/pen/yyBWoR-->
                                                    <!-- element will get class pswp__preloader--active when preloader is running-->
                                                    <div class="pswp__preloader">
                                                        <div
                                                            class="pswp__preloader__icn"
                                                        >
                                                            <div
                                                                class="pswp__preloader__cut"
                                                            >
                                                                <div
                                                                    class="pswp__preloader__donut"
                                                                ></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap"
                                                >
                                                    <div
                                                        class="pswp__share-tooltip"
                                                    ></div>
                                                </div>
                                                <button
                                                    class="pswp__button pswp__button--arrow--left"
                                                    title="Previous (arrow left)"
                                                ></button>
                                                <button
                                                    class="pswp__button pswp__button--arrow--right"
                                                    title="Next (arrow right)"
                                                ></button>
                                                <div class="pswp__caption">
                                                    <div
                                                        class="pswp__caption__center"
                                                    ></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Results -->
    <div class="modal fade" id="exampleModalCenter16">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header p-0">
                    <div class="product-box row" id="post-details">
                        <div class="product-img col-xl-6 p-0" id="model-image">
                            <img id="IMG" />
                        </div>
                        <div class="product-details col-xl-6 col-md-12 text-start comments-style px-0 py-0" id="comments-scroll">
                            <a href="# " style="display: flex;justify-content: space-between;padding: 1rem;">
                                <h4 style="" id="username"></h4>
                                <div><i id="open_delete" class="fa fa-ellipsis-h fa-lg" aria-hidden="true"></i></div>
                                <ul id="delete_button" style="position:absolute;display:none; right: 25px;top: 34px;background-color: #c3c3c373;padding: 5px;border-radius: 6px;width: 69px;text-align: center;">
                                    <li>Delete</li>
                                </ul>
                            </a>
                            <button class="btn-close rounded-circle btn-outline-light btn-info" style="position: fixed;top: 20px;right: 32px;opacity: 1 !important;background-color: white !important;color: white !important;height:21px !important; width:21px !important;" id="cancel-btn"
                            type="button"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                            <div class="mb-0 ps-3" id="text"></div>
                            <!-- Like commets and share , commet box -->
                            <div class="d-flex flex-column px-3">
                                 
                                <ul class="d-flex my-1">
                                    <li class="">
                                        <i id="likedesing"></i>
                                    </li>
                                    <li id="totallikes"></li>
                                    <li id="bookmarking" style="margin-left: auto; margin-right: 8px;">
                                        
                                    </li>
                                </ul>
                                <div class="form-outline d-flex gap-2 me-2">
                                    <input type="text" id="add_comment" class="form-control"
                                        placeholder="Add a commet" />
                                    <input type="hidden" id="post_id" />
                                    <button type="submit" class="btn btn-primary btn-submit">
                                        Post
                                    </button>
                                </div>
                            </div>
                            <hr />
                            <div class="social-chat scoller-comment ps-lg-4 ps-sm-4 " id="comments"></div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
    @push('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    {{-- Scoller  --}}
    <script>
        var ENDPOINT = "{{ url('/') }}";
        var page = 1;
        var page1 = 1;
        var page2 = 1;
        var id = {{$user_detail->id}};


            infinteLoadMore(page);
            infinteLoadMoretag(page1);
            infinteLoadMoresave(page2);
            $(window).scroll(function () {
                if ( $('#photos').hasClass('active') ) {
                    if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
                        page++;

                        infinteLoadMore(page);

                    }
                }else if( $('#friends').hasClass('active') ){
                    if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
                        page1++;
                        infinteLoadMoretag(page1);
                    }
                }else{
                    if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
                        alert(2);
                        page2++;
                        infinteLoadMoresave(page2);
                    }
                }
            });


        function infinteLoadMore(page) {
            $.ajax({
                    url: ENDPOINT + "/user-post?page=" + page + "&& id=" + id,
                    datatype: "html",
                    type: "get",
                    beforeSend: function () {
                        $('.auto-load').show();
                    }
                })
                .done(function (response) {
                    if (response.length == 0) {
                        $('.auto-load').html("We don't have more data to display");
                        return;
                    }
                    $('.auto-load').hide();
                    $('.data-wrapper').append(response);
                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {
                    console.log(ajaxOptions);
                });
        }

        function infinteLoadMoretag(page1) {
            $.ajax({
                    url: ENDPOINT + "/user-tag?page=" + page1 + "&& id=" + id,
                    datatype: "html",
                    type: "get",
                    beforeSend: function () {
                        $('.auto-tag').show();
                    }
                })
                .done(function (response) {
                    if (response.length == 0) {
                        $('.auto-tag').html("We don't have more data to display");
                        return;
                    }
                    $('.auto-tag').hide();
                    $('.data-tag').append(response);
                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {
                    console.log(ajaxOptions);
                });
        }

        function infinteLoadMoresave(page2) {
            
            $.ajax({
                    url: ENDPOINT + "/user-save?page=" + page1 + "&& id=" + id,
                    datatype: "html",
                    type: "get",
                    beforeSend: function () {
                        $('.auto-save').show();
                    }
                })
                .done(function (response) {
                    if (response.length == 0) {
                        $('.auto-save').html("We don't have more data to display");
                        return;
                    }
                    $('.auto-save').hide();
                    $('.data-save').append(response);
                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {
                    console.log(ajaxOptions);
                });
        }
    </script>

<script src="{{asset('assets/js/sweet-alert/sweetalert.min.js')}}"></script>
    <script>
        

        function blockuser(to_user){
            var block = document.getElementById("block");
            $.ajax({
                    type: "get",
                    url: "{{url('profile/block')}}/" + id,
                    dataType: "JSON",
                    success: function (dataResult) {
                        
                        block.classList.remove(dataResult.remove);
                        block.classList.add(dataResult.add);
                        $('#block').html(dataResult.status);
                    },
                }).fail(function (jqXHR, ajaxOptions, thrownError) {
                    console.log(jqXHR);
                });
        }
        
        $(document).on('click', '#delete_button', function(){  
            
            // let report = $(this).attr('title');
            let post_id = document.getElementById("post_id").value;
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this Data..!!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                    });
                    $.ajax({
                        url: "{{ url('post/delete') }}",
                        method: "GET",
                        data: {
                            post_id: post_id,
                        },
                        dataType: "html",
                        success: function (dataResult) {
                            swal("Poof! Your file has been deleted..!", {
                                icon: "success",
                            });
                            location.reload();
                        },
                    }).fail(function (jqXHR, ajaxOptions, thrownError) {
                        console.log(thrownError);
                    });
                } else {
                    swal("Cancel..!", "You file is safe..!", "info");
                }
            })
        });
    </script>
    <script>
        let delete_button = document.getElementById("delete_button");
        let open_delete = document.getElementById("open_delete");
        open_delete.addEventListener("click", function() {
            if (delete_button.style.display == "none") {
                delete_button.style.display="block";
            }
            else{
                delete_button.style.display="none";
            }
        });

    </script>
    
    
    @endpush @push('scripts')
    <script src="{{
            asset('assets/js/photoswipe/photoswipe.min.js')
        }}"></script>
    <script src="{{
            asset('assets/js/photoswipe/photoswipe-ui-default.min.js')
        }}"></script>
    <script src="{{ asset('assets/js/photoswipe/photoswipe.js') }}"></script>

    @endpush 

@endsection