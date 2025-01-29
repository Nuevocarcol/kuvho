{{-- <script src="{{asset('assets/js/jquery-3.5.1.min.js')}}"></script> --}}
<script src="{{asset('public/assets/js/jquery-3.5.1.min.js')}}"></script>
<!-- feather icon js-->
{{-- <script src="{{asset('assets/js/icons/feather-icon/feather.min.js')}}"></script> --}}
<script src="{{asset('public/assets/js/icons/feather-icon/feather.min.js')}}"></script>
{{-- <script src="{{asset('assets/js/icons/feather-icon/feather-icon.js')}}"></script> --}}
<script src="{{asset('public/assets/js/icons/feather-icon/feather-icon.js')}}"></script>
<!-- Sidebar jquery-->
{{-- <script src="{{asset('assets/js/sidebar-menu.js')}}"></script> --}}
<script src="{{asset('public/assets/js/sidebar-menu.js')}}"></script>
{{-- <script src="{{asset('assets/js/config.js')}}"></script> --}}
<script src="{{asset('public/assets/js/config.js')}}"></script>
<!-- Bootstrap js-->
{{-- <script src="{{asset('assets/js/bootstrap/popper.min.js')}}"></script> --}}
<script src="{{asset('public/assets/js/bootstrap/popper.min.js')}}"></script>
{{-- <script src="{{asset('assets/js/bootstrap/bootstrap.min.js')}}"></script> --}}
<script src="{{asset('public/assets/js/bootstrap/bootstrap.min.js')}}"></script>
<!-- Plugins JS start-->
@push('scripts')

<script>
    function find_user(event) {
        
        var user = event.target.value;
        // alert(user);
        $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
       });
        
        $.ajax({
        url:"{{route('user-search')}}",
        method:"POST",
        data: {user : user},
        dataType:'JSON',
        success:function(dataResult)
        {
            
            //  console.log(dataResult.user);
            var users = "";
            var text = "";
            dataResult.user.map((e)=>{
            
            var image = "{{ url('assets/images/user') }}";
            var tag_a = "{{ url('/') }}";
            text = '<a href="'+tag_a+'/profile/'+e.username+'">'+
                    '<div class="d-flex ps-2 hover_effoct rounded-3 py-2">'+
                        
                            '<img src="'+image+'/'+e.profile_pic+'" class="share_result_image" alt="" />'+
                            '<div class="d-flex flex-column align-items-center">'+
                                '<b>'+e.username+'</b>'+
                                '<div>'+e.fullname+'</div>'+
                            '</div>'+
                        
                    '</div>'+
                    '</a>';
                users = users.concat(text);
            })
            
            $("#find_users").html(users);
            
        },
        error:err=>{
            console.log(err);
        }
      })
       
      }
     
</script>
<script>
    $(document).on('click', '.screen', function(){  
        const div = document.querySelector('.screen');
        if(div.classList.contains("fa-moon-o") == true){
            mode = "light-only";
        }else{
            mode = "dark-only";
        };
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            });
        $.ajax({
            url:"{{route('screen')}}",
            method:"post",
            data: {mode : mode},
            dataType:'HTML',
            success:function(dataResult)
            {
            },error: function (dataResult) {
               console.log(dataResult)
            }
        })
    });


    let flag = "1";
    let arr = [];
    $(document).on('click', '.tagpost', function(){  
    var ex = $(this).attr("id"); 
        
        if (arr.includes(ex)) {
            //alert(ex);
            let child = this.childNodes;
            child[5].style.backgroundColor = "";
            let index = arr.indexOf(ex);
            if (index >= 0) {
                arr.splice( index, 1 );
            }
        } else {
            // alert(ex);
            let child = this.childNodes;
            child[5].style.backgroundColor = "#347060";
            arr.push(ex); 
        }
        let shareid = arr.toString(); 
        $("#text1").val(shareid);
        document.getElementById('share_user_id').value = shareid;  
        //  console.log(arr);
    });
  </script>

  <script>
    function comment(post_id) {
        var ENDPOINT = "{{ url('/') }}";
        document.getElementById("IMG").remove();
        $(document).ready(function() {
            $.ajax({
                url: ENDPOINT + "/home/comment/" + post_id,
                datatype: "html",
                type: "get",
                success: function(dataResult) {
                    //alert(dataResult.message);
                    $("#exampleModalCenter16").modal("show");
                    var url = '{{ asset('assets/images/posts') }}';
                    console.log(dataResult.comments_json);
                    var img = document.createElement("IMG");
                    img.src = url + "/" + dataResult.post.image;
                    img.id = "IMG";
                    document
                        .getElementById("model-image")
                        .appendChild(img)
                        .classList.add("comment-image");
                    $("#username").html(dataResult.post.username);
                    $("#bio").html(dataResult.post.bio);
                    $("#text").html(dataResult.post.text);
                    $("#comments").html(dataResult.comments);
                    $("#likedesing").html(dataResult.likedesing);
                    $("#totallikes").html(dataResult.count + " likes");
                    $("#bookmarking").html(dataResult.bookmark);
                    $("#post_id").val(post_id);
                    // console.log(dataResult.bookmark);
                },
                error: (err) => {
                    console.log(err);
                },
            });
        });
    }

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".btn-submit").click(function(e) {

        e.preventDefault();
        
        var add_comment = $("#add_comment").val();
        var post_id = $("#post_id").val();

        $.ajax({
            type: 'POST',
            url: "{{ url('post/comment') }}",
            data: {
                add_comment: add_comment,
                post_id: post_id
            },
            dataType: "html",
            success: function(data) {
                console.log(data);
                $("#comments").prepend(data);
            }
        }).fail(function(jqXHR, ajaxOptions, thrownError) {
            console.log(ajaxOptions);
        });

    });

    function likePost(id) {
        // alert(id);
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "{{ url('post/like') }}",
            method: "POST",
            data: {
                id: id,
            },
            dataType: "json",
            success: function (data) {
                if (data.like == "like") {
                    $("#postlike" + id).html(
                        '<i class="fa fa-heart h4 me-3" style="color:red; cursor:pointer;"></i>'
                    );
                    $("#likedesing").html(
                        '<i class="fa fa-heart h4 me-3" style="color:red; cursor:pointer;" onclick="likePost(' +
                            id +
                            ')"></i>'
                    );
                } else {
                    $("#postlike" + id).html(
                        '<i style="cursor:pointer;"  class="fa fa-heart-o h4 me-3"></i>'
                    );
                    $("#likedesing").html(
                        '<i style="cursor:pointer;"  class="fa fa-heart-o h4 me-3" onclick="likePost(' +
                            id +
                            ')"></i>'
                    );
                }
                $("#totallikes" + id).html(data.count);
                $("#totallikes").html(data.count + " likes");
                //console.log(data.count);
            },
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            console.log(ajaxOptions);
        });
    }

    function bookmark(id) {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            url: "{{ url('post/bookmark') }}",
            method: "POST",
            data: {
                id: id,
            },
            dataType: "json",
            success: function (data) {
                if (data.bookmark == "bookmark") {
                    $("#bookmark" + id).html(
                        '<i class="fa fa-bookmark h3" style="cursor:pointer;"></i>'
                    );
                    $("#bookmarking").html(
                        '<i class="fa fa-bookmark h3" style="cursor:pointer;" onclick="bookmark(' +
                            id +
                            ')"></i>'
                    );
                } else {
                    $("#bookmark" + id).html(
                        '<i class="fa fa-bookmark-o h3" style="cursor:pointer;"></i>'
                    );
                    $("#bookmarking").html(
                        '<i class="fa fa-bookmark-o h3" style="cursor:pointer;" onclick="bookmark(' +
                            id +
                            ')"></i>'
                    );
                }
                //console.log(data.count);
            },
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            console.log(ajaxOptions);
        });
    }

    function follow(id) {
        $(document).ready(function () {
            $.ajax({
                type: "get",
                url: "{{url('account/follow')}}/" + id,
                dataType: "JSON",
                success: function (dataResult) {
                    $("#follow").html(dataResult.message);
                    $("#follow"+id).html(dataResult.message);
                },
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                console.log(jqXHR);
            });
        });
    }
    
  </script>
@endpush
@stack('scripts')
<!-- Plugins JS Ends-->
<!-- Theme js-->
<script src="{{asset('assets/js/script.js')}}"></script>
<script src="{{asset('assets/js/dropzone/dropzone.js')}}"></script>
<script src="{{asset('assets/js/dropzone/dropzone-script.js')}}"></script>
<script src="{{asset('assets/js/form-wizard/form-wizard-two.js')}}"></script>
<script src="{{asset('assets/js/theme-customizer/customizer.js')}}"></script>
{{-- <script src="{{asset('public/assets/js/script.js')}}"></script>
<script src="{{asset('public/assets/js/dropzone/dropzone.js')}}"></script>
<script src="{{asset('public/assets/js/dropzone/dropzone-script.js')}}"></script>
<script src="{{asset('public/assets/js/form-wizard/form-wizard-two.js')}}"></script>
<script src="{{asset('public/assets/js/theme-customizer/customizer.js')}}"></script> --}}
<!-- Plugin used--> 

