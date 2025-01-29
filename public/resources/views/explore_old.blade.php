@extends('layouts.admin.master')

@section('title')Exploer Posts
@endsection

@push('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/photoswipe.css')}}">
<style>
	.comment-image {
        width: 600px;
        max-height: 700px;
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
    }
    #comments-scroll {
        height: 70%;
        /* overflow-y: scroll; */
    }
    #comments {
        overflow-y: scroll;
        height: 50vh;
    }
    
    #model-image {
    }
    @media (max-width: 1200px) {
        #post-details {
            display: grid;
            place-content: center;
        }
       

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
</style>

@endpush

@section('content')
	
	<div class="container-fluid">
		@if (count($errors) > 0)
         <div class = "alert alert-danger">
            <ul>
               @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
               @endforeach
            </ul>
         </div>
      @endif
	    <div class="row">
	        <div class="col-sm-12 box-col-4">
	            <div class="card">
	                <div class="card-header pb-0">
	                    <h5>Exploer Posts</h5>
	                </div>
	                <div class="card-body photoswipe-pb-responsive">
	                    <div class="my-gallery row grid gallery" id="aniimated-thumbnials" itemscope="">
                            @foreach($explore_post as $value)
                                <div class="col-md-3 col-sm-6 grid-item" itemprop="associatedMedia" itemscope="">
                                    <a href="{{asset('assets/images/posts/'. $value->image)}}" itemprop="contentUrl" data-size="1600x950" onclick="comment({{ $value->post_id }})"><img class="img-thumbnail" src="{{asset('assets/images/posts/'. $value->image)}}" itemprop="thumbnail" alt="Image description" /></a>
                                    {{-- <figcaption itemprop="caption description">{{$value->text}}</figcaption> --}}
                                </div>
                            @endforeach
	                        
	                    </div>
	                </div>
					<div class="auto-load text-center">
                                <div class="loader-box">
                                    <div class="loader-18"></div>
                                </div>
                            </div>
	                <!-- Root element of PhotoSwipe. Must have class pswp.-->
	                <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
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
	                                <div class="pswp__counter"></div>
	                                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
	                                <button class="pswp__button pswp__button--share" title="Share"></button>
	                                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
	                                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
	                                <!-- Preloader demo https://codepen.io/dimsemenov/pen/yyBWoR-->
	                                <!-- element will get class pswp__preloader--active when preloader is running-->
	                                <div class="pswp__preloader">
	                                    <div class="pswp__preloader__icn">
	                                        <div class="pswp__preloader__cut">
	                                            <div class="pswp__preloader__donut"></div>
	                                        </div>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
	                                <div class="pswp__share-tooltip"></div>
	                            </div>
	                            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
	                            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
	                            <div class="pswp__caption">
	                                <div class="pswp__caption__center"></div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

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
								<div id="totallikes"></div>
								<ul class="d-flex my-1">
									<li class="">
										<i id="likedesing"></i>
									</li>
									
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
					<button class="btn-close rounded-circle btn-outline-light btn-info" id="cancel-btn"
						type="button"
						data-bs-dismiss="modal"
						aria-label="Close"
					></button>
				</div>
			</div>
		</div>
	</div>
	@push('scripts')
	<script src="{{asset('assets/js/isotope.pkgd.js')}}"></script>
    <script src="{{asset('assets/js/photoswipe/photoswipe.min.js')}}"></script>
    <script src="{{asset('assets/js/photoswipe/photoswipe-ui-default.min.js')}}"></script>
    <script src="{{asset('assets/js/photoswipe/photoswipe.js')}}"></script>
    <script src="{{asset('assets/js/masonry-gallery.js')}}"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
		</script>
	@endpush

@endsection