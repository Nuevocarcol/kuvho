<!-- component -->
<script src="https://cdn.tailwindcss.com"></script>
<div class="bg-gray-100 p-4">
  <div class="bg-white border rounded-sm max-w-md">
    <div class="flex items-center px-4 py-3">
      <img class="h-8 w-8 rounded-full" src="https://picsum.photos/id/1027/150/150"/>
      <div class="ml-3 ">
        <span class="text-sm font-semibold antialiased block leading-tight">8fact</span>
        <span class="text-gray-600 text-xs block">Asheville, North Carolina</span>
      </div>
    </div>
    <img src="https://picsum.photos/id/244/900/900"/>
    <div class="flex items-center justify-between mx-4 mt-3 mb-2">
      <div class="flex gap-5">
        <svg fill="#262626" height="24" viewBox="0 0 48 48" width="24"><path d="M34.6 6.1c5.7 0 10.4 5.2 10.4 11.5 0 6.8-5.9 11-11.5 16S25 41.3 24 41.9c-1.1-.7-4.7-4-9.5-8.3-5.7-5-11.5-9.2-11.5-16C3 11.3 7.7 6.1 13.4 6.1c4.2 0 6.5 2 8.1 4.3 1.9 2.6 2.2 3.9 2.5 3.9.3 0 .6-1.3 2.5-3.9 1.6-2.3 3.9-4.3 8.1-4.3m0-3c-4.5 0-7.9 1.8-10.6 5.6-2.7-3.7-6.1-5.5-10.6-5.5C6 3.1 0 9.6 0 17.6c0 7.3 5.4 12 10.6 16.5.6.5 1.3 1.1 1.9 1.7l2.3 2c4.4 3.9 6.6 5.9 7.6 6.5.5.3 1.1.5 1.6.5.6 0 1.1-.2 1.6-.5 1-.6 2.8-2.2 7.8-6.8l2-1.8c.7-.6 1.3-1.2 2-1.7C42.7 29.6 48 25 48 17.6c0-8-6-14.5-13.4-14.5z"></path></svg>
        <svg fill="#262626" height="24" viewBox="0 0 48 48" width="24"><path clip-rule="evenodd" d="M47.5 46.1l-2.8-11c1.8-3.3 2.8-7.1 2.8-11.1C47.5 11 37 .5 24 .5S.5 11 .5 24 11 47.5 24 47.5c4 0 7.8-1 11.1-2.8l11 2.8c.8.2 1.6-.6 1.4-1.4zm-3-22.1c0 4-1 7-2.6 10-.2.4-.3.9-.2 1.4l2.1 8.4-8.3-2.1c-.5-.1-1-.1-1.4.2-1.8 1-5.2 2.6-10 2.6-11.4 0-20.6-9.2-20.6-20.5S12.7 3.5 24 3.5 44.5 12.7 44.5 24z" fill-rule="evenodd"></path></svg>
        <svg fill="#262626" height="24" viewBox="0 0 48 48" width="24"><path d="M47.8 3.8c-.3-.5-.8-.8-1.3-.8h-45C.9 3.1.3 3.5.1 4S0 5.2.4 5.7l15.9 15.6 5.5 22.6c.1.6.6 1 1.2 1.1h.2c.5 0 1-.3 1.3-.7l23.2-39c.4-.4.4-1 .1-1.5zM5.2 6.1h35.5L18 18.7 5.2 6.1zm18.7 33.6l-4.4-18.4L42.4 8.6 23.9 39.7z"></path></svg>
      </div>
      <div class="flex">
        <svg fill="#262626" height="24" viewBox="0 0 48 48" width="24"><path d="M43.5 48c-.4 0-.8-.2-1.1-.4L24 29 5.6 47.6c-.4.4-1.1.6-1.6.3-.6-.2-1-.8-1-1.4v-45C3 .7 3.7 0 4.5 0h39c.8 0 1.5.7 1.5 1.5v45c0 .6-.4 1.2-.9 1.4-.2.1-.4.1-.6.1zM24 26c.8 0 1.6.3 2.2.9l15.8 16V3H6v39.9l15.8-16c.6-.6 1.4-.9 2.2-.9z"></path></svg>
      </div>
    </div>
    <div class="font-semibold text-sm mx-4 mt-2 mb-4">92,372 likes</div>
  </div>
</div>
@extends('layouts.admin.master')

@section('title')
    Home
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/photoswipe.css') }}">
    <style>
        .comment-image{
            width: 95%%;
            max-height: 700px;
            min-height: 650px;
        }

        .modal-xl{
            max-width: 1300px !important;
        }

        .product-box{
            width: 1300px !important;
        }

        .scoller-comment{
            overflow: auto;
        }
    </style>
@endpush

@section('custom-meta')

@endsection
@section('content')


    @push('scripts')
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->

    <script>
        var ENDPOINT = "{{ url('/') }}";
        var page = 1;
        infinteLoadMore(page);
        $(window).scroll(function () {

            if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
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
                        $('.auto-load').show();
                    }
                })
                .done(function (response) {
                    if (response.length == 0) {
                        $('.auto-load').html("We don't have more data to display :(");
                        return;
                    }
                    $('.auto-load').hide();
                    $("#data-wrapper").append(response);
                })
                .fail(function (jqXHR, ajaxOptions, thrownError) {
                    console.log(jqXHR);
                });
        }
    </script>

    <script>
        function comment(post_id){
            var ENDPOINT = "{{ url('/') }}";
            document.getElementById('IMG').remove();
            $(document).ready(function(){
                
                $.ajax({
                    url: ENDPOINT + "/home/comment/"+post_id,
                    datatype: "html",
                    type: "get",
                    success: function (dataResult){
                    //alert(dataResult.message);
                        $('#exampleModalCenter16').modal('show');
                        var url = '{{asset("assets/images/posts")}}';
                        console.log(dataResult.comments_json);
                        var img = document.createElement("IMG");
                        img.src = url + "/"+ dataResult.post.image;
                        img.id = "IMG";
                        document.getElementById('model-image').appendChild(img).classList.add("comment-image");
                        $('#username').html(dataResult.post.username);
                        $('#bio').html(dataResult.post.bio);
                        $('#text').html(dataResult.post.text);
                        $('#comments').html(dataResult.comments);
                    },error:err=>{
                        console.log(err);
                    }
                });
            });
        }
    </script>
@endpush

@endsection
