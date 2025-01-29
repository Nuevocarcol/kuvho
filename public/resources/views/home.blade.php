@extends('layouts.admin.master') @section('title') Home @endsection @push('css')
<link
    rel="stylesheet"
    type="text/css"
    href="{{ asset('assets/css/photoswipe.css') }}"
/>
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"
></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<style>
      /* pawan thakur */
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
        height:100%;
        padding-inline: 2px;
        height: auto;
        object-fit: contain;
        background-color: black;
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

    #image-responsive {
        object-fit: cover;
        height: 90vh;
        border-radius: 0.3rem;
    }

    #card-dody {
        padding: 0;
    }

    .message_popup {

        z-index: 9999;
        /* width: 35rem;
        height: 40rem; */

        border-radius: 1rem;
    }

    .search_result_id {
    }

    .share_result_image {
        height: 40px;
        width: 40px;
        border-radius: 10rem;
        margin-right: 1rem;
    }

    .share_search_result {
        overflow-y: scroll;
        height: 25rem;
    }

    .hover_effoct:hover {
        background-color: rgba(0, 0, 0, 0.123);
        cursor: pointer;
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

        /* #comments {
            height: 100%;
            overflow: unset;
            padding-inline: 25px;
        } */
        #comments {
            overflow-y: scroll;
            height: fit-content;
            max-height: 23rem;}
        }

    .story_container {
        /* color: white; */
        /* background-color: black; */
        height: fit-content;
        margin-inline: 26rem;
        display: flex;
        overflow-x: scroll;
        gap: 1rem;
        padding-bottom: 1rem;
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
        padding: 3px;
        border: 1px solid black;
        /* background-image: linear-gradient(to bottom right, red, yellow);
        padding: 2.5px; */
        /* border: 2px solid ;
        border-top-color: red;
        border-bottom-color: yellow; */
    }

    .seen_story_border {
        padding: 3px;
        border: 1px solid rgba(0, 0, 0, 0.209);
    }

    @media (max-width: 575px) {
        #comments {
            padding-inline: 10px;
        }

        .story_container {
            /* color: white; */
            /* background-color: black; */
            height: fit-content;
            margin-inline: 0.7rem;
            display: flex;
            overflow-x: scroll;
            gap: 0.5rem;
            padding-bottom: 0rem;
            font-size: smaller;
        }
    }

    @media (min-width: 575px) {
        .story_container {
            margin-inline: 2rem;
            font-size: smaller;
        }
    }

    @media (min-width: 1220px) {
        .story_container {
            margin-inline: 10rem;
            font-size: smaller;
        }
    }

    @media (min-width: 1440px) {
        .story_container {
            margin-inline: 5rem;
            font-size: smaller;
        }
    }

    @media (min-width: 1740px) {
        .story_container {
            margin-inline: 17rem;
            font-size: smaller;
        }
    }

    .story_container::-webkit-scrollbar {
        display: none;
    }
</style>
@endpush @section('custom-meta')
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css"
/>
@endsection @section('content')
<div class="container-fluid">
    <div class="user-profile social-app-profile">
        <div class="tab-content" id="top-tabContent">
            <div
                class="tab-pane fade show active"
                id="timeline"
                role="tabpanel"
                aria-labelledby="timeline"
            >
                <!-- Story start -->
                <div class="story_container">
                    @foreach ($story as $story_value)
                    <div>
                        {{-- @dd($story_value) --}}
                        <a
                            href="{{
                                url('story/'.$story_value['user']['username'])
                            }}"
                        >
                            @if ($story_value['seen_story'] == 0)
                            {{-- <img
                                src="{{
                                    asset(
                                        'assets/images/story/'.$story_value[
                                            'url'
                                        ]
                                    )
                                }}"
                                class="story_image story_image_border"
                                alt=""
                            /> --}}
                            <img
                                src="{{
                                    asset(
                                        'public/assets/images/story/'.$story_value[
                                            'url'
                                        ]
                                    )
                                }}"
                                class="story_image story_image_border"
                                alt=""
                            />
                            @else
                            {{-- <img
                                src="{{
                                    asset(
                                        'assets/images/story/'.$story_value[
                                            'url'
                                        ]
                                    )
                                }}"
                                class="story_image seen_story_border seen_story_border"
                                alt=""
                            /> --}}
                            <img
                                src="{{
                                    asset(
                                        'public/assets/images/story/'.$story_value[
                                            'url'
                                        ]
                                    )
                                }}"
                                class="story_image seen_story_border seen_story_border"
                                alt=""
                            />
                            @endif
                            <div class="w-100 text-center">
                                {{ $story_value["user"]["username"] }}
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                <!-- Story end -->
                <div class="row">
                <div class="col-xl-2 col-xxl-3 xl-10 xxl-30 md-40 lg-40 col-lg-2  col-md-2 box-col-4"
                    ></div>
                    <div class="col-xl-6 col-xxl-5 xl-60 col-lg-10 col-md-10 box-col-8">
                        <div class="row">
                            <div id="data-wrapper">
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
                                                        <a href="product-page">
                                                            <h4 style="padding: 1rem;" id="username"></h4>
                                                        </a>

                                                        <div class="mb-0 ps-3" id="text"></div>
                                                        <!-- Like commets and share , commet box -->
                                                        <div class="d-flex flex-column px-3">
                                                            <ul class="d-flex my-1">
                                                                <li class="">
                                                                    <i id="likedesing"></i>
                                                                </li>
                                                                <li id="totallikes"></li>
                                                                
                                                                <li id="bookmarking" style="margin-left: auto; margin-right: 8px;">
                                                                    {{--
                                                                    <i
                                                                        class="fa fa-bookmark h3"
                                                                    ></i>
                                                                    --}}
                                                                </li>
                                                                <!-- <img style="height:1.5rem;margin-left: auto; margin-right: 8px;" src="{{ asset('assets/images/bookmark.png') }}" alt=""> -->
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
                                                    <button class="btn-close rounded-circle btn-outline-light btn-info" style="position: fixed;top: 20px;right: 32px;opacity: 1 !important;background-color: white !important;color: white !important;height:21px !important; width:21px !important;" id="cancel-btn"
                                                    type="button"
                                                    data-bs-dismiss="modal"
                                                    aria-label="Close"
                                                ></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="auto-load text-center">
                                <div class="loader-box">
                                    <div class="loader-18"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 xl-100 box-col-12"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sharemodel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="width: 800px">
        <div class="modal-content">
            <!-- vscode-file://vscode-app/c:/Users/PC/AppData/Local/Programs/Microsoft%20VS%20Code/resources/app/out/vs/code/electron-sandbox/workbench/workbench.html -->
            <div class="message_popup py-3">
                <div class="d-flex justify-content-center px-3">
                    <span class="h5">Share</span>
                    <!-- <i class="icon-close h4 ms-auto me-0"></i> -->
                </div>
                <hr />
                <div class="row px-1 align-items-center">
                    <span class="col-2 ms-3 h6">To &nbsp;: </span>
                    <input
                        class="col-9 rounded border-0"
                        type="text"
                        placeholder="Search.."
                    />
                </div>
                <hr />
                <div class="share_search_result px-3">
                    @foreach($follow as $user_v)

                    <div
                        class="d-flex ps-2 hover_effoct rounded-3 py-2 sharepost"
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

                    @endforeach
                </div>
                <hr />

                <div class="px-2">
                    <input type="hidden" id="share_user_id" />
                    <input type="hidden" id="sharepost_id" />
                    <button
                        class="btn btn-primary w-100 py-2"
                        id="text1"
                        onclick="sendpost()"
                    >
                        Send
                    </button>
                </div>
                <!-- </div>  -->
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="report" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="width: 30rem;">
        <div class="modal-content" style="border-radius:.8rem">
            <div class="message_popup py-3">
                <div class="d-flex justify-content-center px-3">
                    <span class="h5">Report</span>
                    <input type="hidden" name="reportpost_id" id="reportpost_id">
                    <!-- <i class="icon-close h4 ms-auto me-0"></i> -->
                </div>
                <hr />
                     <h6 class="ps-3">Why are you reporting this post?</h6>
                <hr />
                <div class="" >
                        <div class="px-3" style="cursor:pointer; " >
                            <span class="reportfunction" title="It's Spam">It's Spam</span>
                        </div>
                        <hr />
                        <div class="px-3 " style="cursor:pointer;">
                            <span class="reportfunction" title="Violence or dangerous organizations">Violence or dangerous organizations</span>
                        </div>
                        <hr />
                        <div class="px-3 " style="cursor:pointer;">
                            <span class="reportfunction" title="Sale of illegal or regulated goods">Sale of illegal or regulated goods</span>
                        </div>
                        <hr />
                        <div class="px-3 " style="cursor:pointer;">
                            <span class="reportfunction" title="Intellectual property violation">Intellectual property violation</span>
                        </div>
                        <hr />
                        <div class="px-3 " style="cursor:pointer;">
                            <span class="reportfunction" title="Suicide or self-injury">Suicide or self-injury</span>
                        </div>
                        <hr />
                        <div class="px-3 " style="cursor:pointer;">
                            <span class="reportfunction" title="I just don't like it">I just don't like it</span>
                        </div>
                </div>
                <div class="px-2"></div>
                <!-- </div>  -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="{{asset('assets/js/fullscreen.js')}}"></script>

{{-- For Scoller --}}
<script>
    var ENDPOINT = "{{ url('/') }}";
    var page = 1;
    infinteLoadMore(page);
    $(window).scroll(function () {
        if (
            $(window).scrollTop() + $(window).height() >=
            $(document).height()
        ) {
            page++;
            infinteLoadMore(page);
        }
    });

    function infinteLoadMore(page) {
        $.ajax({
            url: ENDPOINT + "/home?page=" + page,
            datatype: "html",
            type: "get",
            beforeSend: function () {
                $(".auto-load").show();
            },
        })
            .done(function (response) {
                if (response.length == 0) {
                    $(".auto-load").html(
                        "We don't have more data to display"
                    );
                    return;
                }
                $(".auto-load").hide();
                $("#data-wrapper").append(response);
            })
            .fail(function (jqXHR, ajaxOptions, thrownError) {
                console.log(jqXHR);
            });
    }
</script>

<script>
    

    function postreport(id){
        document.getElementById("reportpost_id").value = id;
        $("#report").modal("show");
    }

    $(document).on('click', '.reportfunction', function(){  
        let report = $(this).attr('title');
        let post_id = document.getElementById("reportpost_id").value;
       
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "{{ url('post/report') }}",
            method: "POST",
            data: {
                report: report,
                post_id: post_id,
            },
            dataType: "html",
            success: function (dataResult) {
                $("#report").modal("hide");
            },
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            console.log(thrownError);
        });
    });
</script>
<script>
    

    function sharemodelfn(id) {
        document.getElementById("sharepost_id").value = id;
        $("#sharemodel").modal("show");
    }

    function sendpost() {
        var share_uid = document.getElementById("share_user_id").value;
        var post_id = document.getElementById("sharepost_id").value;

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "{{ url('post/share') }}",
            method: "POST",
            data: {
                share_uid: share_uid,
                post_id: post_id,
            },
            dataType: "html",
            success: function (dataResult) {
                $("#sharemodel").modal("hide");
            },
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            console.log(thrownError);
        });
    }

    function homecomment(id){
        var add_comment = $("#add_comment"+id).val();
        $.ajax({
            type: 'POST',
            url: "{{ url('post/comment') }}",
            data: {
                add_comment: add_comment,
                post_id: id
            },
            dataType: "html",
            success: function(data) {
                console.log(data);
                $("#comments").prepend(data);
                $("#add_comment"+id).val('');
            }
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            console.log(ajaxOptions);
        });
    }

    
    let arr1 = [];
    $(document).on('click', '.sharepost', function(){  
    var ex = $(this).attr("id"); 
        
        if (arr1.includes(ex)) {
            //alert(ex);
            let child = this.childNodes;
            child[5].style.backgroundColor = "";
            let index = arr1.indexOf(ex);
            if (index >= 0) {
                arr1.splice( index, 1 );
            }
        } else {
            // alert(ex);
            let child = this.childNodes;
            child[5].style.backgroundColor = "#347060";
            arr1.push(ex); 
        }
        let shareid = arr1.toString(); 
        $("#text1").val(shareid);
        document.getElementById('share_user_id').value = shareid;  
        //  console.log(arr);
    });
</script>
    @endpush
@endsection
