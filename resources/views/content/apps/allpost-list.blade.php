@extends('layouts/layoutMaster')

@section('title', 'All Posts')

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            $('#searchForm').submit(function(e) {
                e.preventDefault();
                var searchValue = $(this).find('input[name="search"]').val().trim();
                window.location.href = "{{ route('allpost-list') }}" + "?search=" + searchValue;
            });

            $('#backButton').click(function() {
                window.location.href = "{{ route('allpost-list') }}";
            });
            $('#closeBtn').click(function() {
                window.location.href = "{{ route('allpost-list') }}";
            });
        });
    </script>
    <script>
        function openSidebar() {
            $('.kanban-update-item-sidebar').addClass('show');
        }
    </script>
@endsection

@section('content')
    <div class="container-fluid">
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Posts /</span> All Posts
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
            @foreach ($posts as $post)
                @if (!$search || stripos($post->username, $search) !== false)
                    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
                        <div class="card">
                            <div class="item-badges" style="margin-left: 12px; padding-top: 10px;">
                                <div class="badge rounded-pill bg-label-warning"> IMAGE</div>
                            </div>
                            <div class="">
                                <div class="dropdown"
                                    style="position: absolute; right: 21px !important; top: 15px !important; font-size: 1.2rem; cursor: pointer;">
                                    <div class="show" type="button" data-bs-toggle="dropdown" aria-expanded="true"><i
                                            class="fa-solid fa-ellipsis-vertical"></i></div>
                                    <ul class="dropdown-menu"
                                        style="position: absolute; inset: auto auto 0px 0px; margin: 0px; transform: translate(0px, -21px);">
                                        <li><a class="dropdown-item text-danger"
                                                onclick="deleteAllPost('{{ $post->post_id }}')">Delete</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card-body text-center" style="height: 30rem; padding:11px;">
                                <div class="mx-auto mb-3">
                                    @if (!empty($post->image))
                                        {{-- <img src="{{ asset('assets/images/posts/' . $post->image) }}"
                                            class="rounded" style="width: 100%; height: 15rem;" /> --}}
                                        <img src="{{ asset('assets/images/posts/' . $post->image) }}"
                                            class="rounded" style="width: 100%; height: 15rem;" />
                                    @elseif (!empty($post->video_thumbnail))
                                        {{-- <img src="{{ asset('assets/images/posts/' . $post->video_thumbnail) }}"
                                            class="rounded" style="width: 100%; height: 15rem;" /> --}}
                                        <img src="{{ asset('assets/images/posts/' . $post->video_thumbnail) }}"
                                            class="rounded" style="width: 100%; height: 15rem;" />
                                    @endif
                                </div>
                                <div class="d-flex gap-3 py-3">
                                    {{-- <img src="{{ asset('assets/images/user/' . $post->profile_pic) }}"
                                        alt="User Profile" style="width: 50px; height: 50px; border-radius: 50%;"> --}}
                                    <img src="{{ asset('assets/images/user/' . $post->profile_pic) }}"
                                        alt="User Profile" style="width: 50px; height: 50px; border-radius: 50%;">
                                    <div class="" style="text-align: start">
                                        <h5 class="mb-1 " style="font-size: 1rem">{{ $post->username }}</h5>
                                        <p>{{ $post->email }}</p>
                                    </div>
                                </div>
                                <div class="d-flex" style="gap:.6rem;">
                                    <h6 class="pt-1">{{ $post->text }}</h6>
                                </div>
                                <div class="d-flex" style="gap:.6rem;">
                                    <span>Posted At :</span>
                                    <h6 class="pt-1">{{ date('d, F Y', strtotime($post->formatted_created_at)) }}</h6>
                                </div>

                                <div class="d-flex">
                                    <a href="#" id="like_btn" class="d-flex align-items-center me-2 like-btn"
                                        value="{{ $post->post_id }}"
                                        onclick="openSidebar(); showLikes('{{ $post->post_id }}')" data-tab="like">
                                        <i class="bx bx-heart me-1"></i>
                                        <span>{{ $post->total_likes }}</span>&nbsp;<span>{{ $post->total_likes == 1 ? 'like' : 'likes' }}</span>
                                    </a>
                                    <a href="#" id="comment_btn" class="d-flex align-items-center"
                                        value="{{ $post->post_id }}"
                                        onclick="openSidebar(); showComments('{{ $post->post_id }}')" data-tab="comment">
                                        <i class="bx bx-chat me-1"></i>
                                        <span>{{ $post->total_comments }}</span>&nbsp;<span>{{ $post->total_comments == 1 ? 'comment' : 'comments' }}</span>
                                    </a>
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
                        Showing {{ $posts->firstItem() }} to {{ $posts->lastItem() }} of {{ $posts->total() }} entries
                    </div>
                    <div>
                        {{ $posts->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- DeleteAllPost --}}
    <script>
        function deleteAllPost(post_id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete post!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'allpost-delete/' + post_id,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(data) {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Post deleted successfully.',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'Failed to delete post.',
                                    icon: 'error',
                                    customClass: {
                                        confirmButton: 'btn btn-success'
                                    }
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Failed to delete post. Please try again later.',
                                icon: 'error',
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            });
                        }
                    });
                }
            });
        }
    </script>

    <div class="offcanvas offcanvas-end kanban-update-item-sidebar">
        <div class="offcanvas-header border-bottom py-3 my-1">
            <h5 class="offcanvas-title">Likes & Comments</h5>
            <button type="button" class="btn-close" id="closeBtn"></button>
        </div>
        <div class="offcanvas-body pt-4">
            <ul class="nav nav-pills tabs-line">
                <li class="nav-item">
                    <button class="nav-link active shadow-none tab-button" data-bs-toggle="tab" data-bs-target="#tab-update"
                        onclick="showlikelist()" id="like_btnnew">
                        <i class="bx bx-heart me-2"></i>
                        <span class="align-middle text-uppercase">Likes</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link shadow-none tab-button tab-button-comment" data-bs-toggle="tab"
                        data-bs-target="#tab-activity" onclick="showcommentlist()" id="comment_btnnew">
                        <i class="bx bx-chat me-2"></i>
                        <span class="align-middle text-uppercase">Comments</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content px-0 pb-0 border-0">
                <div class="tab-pane fade show active" id="tab-update" role="tabpanel">
                    <div id="likeList"></div>
                </div>
                <div class="tab-pane fade show active" id="tab-activity" role="tabpanel">
                    <div id="commentList"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

{{-- Comments --}}
<script>
    function fetchComments(postId) {
        $.ajax({
            url: '/get-comments/' + postId,
            type: 'GET',
            success: function(comments) {
                renderComments(comments);
            },
            error: function(xhr, status, error) {
                console.error('Failed to fetch comments:', error);
            }
        });
    }

    // Function to render comments in the comments tab
    function renderComments(comments) {
        if (comments.length === 0) {
            $('#commentList').html('<p>No comments found</p>');
            return;
        }

        let commentsHtml = '';
        comments.forEach(function(comment) {
            let profilePicUrl = 'assets/images/user/' + comment.user.profile_pic;
            commentsHtml += `
        <div class="media mb-4 d-flex align-items-start">
            <div class="avatar avatar-sm me-2 flex-shrink-0 mt-1">
                <img src="${profilePicUrl}" alt="User Image" class="rounded-circle" />
            </div>
            <div class="media-body">
                <p class="mb-0">
                    <span class="fw-medium">${comment.user.username}</span> commented <b>"${comment.text}"</b>
                </p>
                <small class="pt-1">${comment.formatted_created_at}</small>
            </div>
        </div>`;
        });
        $('#commentList').html(commentsHtml);
    }

    function showcommentlist() {
        let like_btnnew = document.getElementById("like_btnnew");
        let comment_btnnew = document.getElementById("comment_btnnew");

        like_btnnew.classList.remove("active");
        comment_btnnew.classList.add("active");
        let post_id = sessionStorage.getItem("post_id");
        fetchComments(post_id);
    }

    function showComments(post_id) {

        let like_btnnew = document.getElementById("like_btnnew");
        let comment_btnnew = document.getElementById("comment_btnnew");

        like_btnnew.classList.remove("active");
        comment_btnnew.classList.add("active");
        sessionStorage.setItem("post_id", post_id);
        fetchComments(post_id);
    }

    $(document).ready(function() {
        // Event binding for comment button
        $('.tab-button-comment').on('click', function() {
            const postId = $(this).data('post-id');
            showComments(postId);
        });

        $('a[data-bs-toggle="tab"][href="#tab-activity"]').on('shown.bs.tab', function(e) {
            let postId = $(e.target).closest('.nav-item').find('.tab-button-comment').data('post-id');
            fetchComments(postId);
        });
    });
</script>



{{-- Likes --}}
<script>
    function fetchLikes(postId) {
        $.ajax({
            url: '/get-likes/' + postId,
            type: 'GET',
            success: function(likes) {
                renderLikes(likes);
            },
            error: function(xhr, status, error) {
                console.error('Failed to fetch likes:', error);
            }
        });
    }

    // Function to render likes in the likes tab
    function renderLikes(likes) {
        let likesHtml = '';
        if (likes.length === 0) {
            likesHtml = '<p>No likes for this post.</p>';
        } else {
            likes.forEach(function(like) {
                if (like.user) {
                    let profilePicUrl = 'assets/images/user/' + (like.user.profile_pic || 'default.jpg');
                    likesHtml += `
        <div class="media mb-4 d-flex align-items-start">
            <div class="avatar avatar-sm me-2 flex-shrink-0 mt-1">
                <img src="${profilePicUrl}" alt="User Image" class="rounded-circle" />
            </div>
            <div class="media-body">
                <p class="mb-0">
                    <span class="fw-medium">${like.user.username}</span> <b>liked this post</b>
                </p>
                <small class="pt-1">${like.formatted_created_at}</small>
            </div>
        </div>`;
                }
            });
        }
        $('#likeList').html(likesHtml);
    }

    function showlikelist() {
        let like_btnnew = document.getElementById("like_btnnew");
        let comment_btnnew = document.getElementById("comment_btnnew");

        like_btnnew.classList.add("active");
        comment_btnnew.classList.remove("active");

        let post_id = sessionStorage.getItem("post_id");
        fetchLikes(post_id);
    }

    function showLikes(post_id) {
        let like_btnnew = document.getElementById("like_btnnew");
        let comment_btnnew = document.getElementById("comment_btnnew");

        like_btnnew.classList.add("active");
        comment_btnnew.classList.remove("active");

        sessionStorage.setItem("post_id", post_id);
        fetchLikes(post_id);
    }

    $(document).ready(function() {
        // Event binding for like button
        $('#like_btn').on('click', function() {
            const postId = $(this).data('post-id');
            showLikes(postId);
        });

        $('a[data-bs-toggle="tab"][href="#tab-update"]').on('shown.bs.tab', function(e) {
            let postId = $(e.target).closest('.nav-item').find('#like_btn').data('post-id');
            fetchLikes(postId);
        });
    });
</script>
