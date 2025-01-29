<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Bookmark;
use App\Models\Comment;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Models\Reel;
use App\Models\Reel_Like;
use App\Models\Reel_Comment;
use App\Models\post_user_tag;
use App\Models\Posts_report;
use App\Models\Profile_blocklist;
use App\Models\user_notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Validator;
use FFMpeg\FFMpeg;


class PostController extends BaseController
{

    public function index(Request $request)
    {
        $user_id = Auth::user()->token()->user_id;
        if ($request->per_page == "") {
            return response(['response_code' => 1, 'message' => "failed", 'status' => "failed"]);
        }
        $take = $request->per_page;

        $posts_Ar = array();
        # code...

        $posts = Post::select('posts.*')

            // ->join('follow', 'follow.to_user', '=', 'posts.user_id')
            ->leftjoin('follow', function ($join) use ($user_id) {
                $join->on('follow.to_user', '=', 'posts.user_id');
            })
            ->where('follow.from_user', '=', $user_id)
            ->orwhere('posts.user_id', Auth::id())
            ->with('comment', 'user')->orderBy('posts.post_id', 'DESC')->groupBy('posts.post_id')->skip($take)->take(10)->get()->transform(function ($ts) use ($user_id) {

                $ts['like'] = (Like::where('post_id', '=', $ts['post_id'])->where('user_id', '=', $user_id)->count()) ? true : false;
                $ts['total_like'] = Like::where('post_id', '=', $ts['post_id'])->count();
                $ts['total_comment'] = Comment::where('post_id', '=', $ts['post_id'])->count();
                $ts['bookmark'] = (Bookmark::where('post_id', '=', $ts['post_id'])->where('user_id', '=', $user_id)->count()) ? true : false;
                return $ts;
            });
        return response(['response_code' => 1, 'message' => "success", 'data' => PostResource::collection($posts), 'status' => "success"]);
    }

    public function store(Request $request)
    {

        $user_id = Auth::user()->token()->user_id;
        
        

        // if ($request->file('image')) {
        //     $file = $request->file('image');
        //     $filename = "posts " . date('YmdHis') . $file->getClientOriginalName();
        //     $filename = str_replace(" ", "", $filename);
        //     $file->move(public_path('assets/images/posts'), $filename);
        //     $update['image'] = $filename;
        // }
        
          $res_image = array();
        
        
        if (request()->hasFile('image')) {
    $files = request()->file('image');

    foreach ($files as $file) {
        $validator = Validator::make(['image' => $file], [
            'image' => 'required|mimes:gif,jpg,png,jpeg',
        ]);

        if ($validator->fails()) {
            $temp["response_code"] = "0";
            $temp["message"] = $validator->errors()->first();
            $temp["status"] = "failure";
            return response()->json($temp);
        }

        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        // $filePath = 'assets/images/posts';
        
         $file->move(public_path('assets/images/posts'), $fileName);

        // Move the uploaded file to the desired location
        // $file->move($filePath, $fileName);

        array_push($res_image, $fileName);
    }
} 

        if ($request->file('video')) {
            $file = $request->file('video');
            $filename = "posts " . date('YmdHis') . $file->getClientOriginalName();
            $filename = str_replace(" ", "", $filename);
            $file->move(public_path('assets/images/posts'), $filename);
            
            $update['video'] = $filename;
        }

        if ($request->file('video_thumbnail')) {
            $file = $request->file('video_thumbnail');
            $filename = "posts " . date('YmdHis') . $file->getClientOriginalName();
            $filename = str_replace(" ", "", $filename);
            $file->move(public_path('assets/images/posts'), $filename);
            $update['video_thumbnail'] = $filename;
        }

        $update['user_id'] = $user_id;
         
        $text = $request->input('text');
        if(!empty($text)){
        $update['text'] = $request->text;
        }
        $location = $request->input('location');
        if(!empty($location)){
        $update['location'] = $request->location;
        }
        $update['create_date'] = round(microtime(true) * 1000);
        
        $update['image'] = implode('::::', $res_image);
        
        $height = $request->input('height');
        
        if(!empty($height)){
        
        $update['height'] = $request->height;
        }

        $post = Post::create($update);

        if ($request->tag_users != "") {
            $tag_user = explode(',',$request->tag_users);
            
            

            foreach ($tag_user as $tag) {
                post_user_tag::create([
                    'user_id' => $user_id,
                    'post_id' => $post->post_id,
                    'tag_users' => $tag
                ]);
            }
            
            // print_r($tag);
            // die;
        }

        return response(['response_code' => "1", 'message' => "success", 'status' => "success"]);
    }

    public function update(Request $request)
    {
        $user_id = Auth::user()->token()->user_id;

        if ($request->post_id == "") {
            return response(['response_code' => 0, 'message' => "Enter Data", 'status' => "failure"]);
        }

        $post = Post::where('post_id', $request->post_id)->first();

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = "posts " . date('YmdHis') . $file->getClientOriginalName();
            $filename = str_replace(" ", "", $filename);
            $file->move(public_path('assets/images/posts'), $filename);
            $update['image'] = $filename;
            if (File::exists('assets/images/posts/' . $post->image)) {
                File::delete('assets/images/posts/' . $post->image);
            }
        }

        if ($request->file('video')) {
            $file = $request->file('video');
            $filename = "posts " . date('YmdHis') . $file->getClientOriginalName();
            $filename = str_replace(" ", "", $filename);
            $file->move(public_path('assets/images/posts'), $filename);
            $update['video'] = $filename;
            if (File::exists('assets/images/posts/' . $post->video)) {
                File::delete('assets/images/posts/' . $post->video);
            }
        }

        if ($request->file('video_thumbnail')) {
            $file = $request->file('video_thumbnail');
            $filename = "posts " . date('YmdHis') . $file->getClientOriginalName();
            $filename = str_replace(" ", "", $filename);
            $file->move(public_path('assets/images/posts'), $filename);
            $update['video_thumbnail'] = $filename;
            if (File::exists('images/profile_pic/' . $post->video_thumbnail)) {
                File::delete('images/profile_pic/' . $post->video_thumbnail);
            }
        }

        $update['text'] = ($request->text) ? $request->text : $post->text;
        $update['location'] = ($request->location) ? $request->location : $post->location;

        post::where('post_id', $post->post_id)->update($update);
        return response(['response_code' => 1, 'message' => "updated success", 'status' => "success"]);
    }

    public function like_post(Request $request)
    {

        $user_id = Auth::user()->token()->user_id;
        if ($request->post_id == "") {
            return response(['response_code' => 0, 'message' => "Enter Data", 'status' => "failure"]);
        }

        $like = Like::where('user_id', $user_id)->where('post_id', $request->post_id)->count();
        
        $date = round(microtime(true) * 1000);


        if ($like == 0) {   
            
            Like::create(['user_id' => $user_id, 'post_id' => $request->post_id, 'date' => $date]);
            
               $post_id = $request->post_id;
            //   $posts = DB::get_where('posts', array('post_id' => $post_id))->row();
           
                
                
                $posts = DB::table('posts')->where('post_id', $post_id)->first();
                
                $to_user = $posts->user_id;
               $user = DB::table('users')->where('id', $user_id)->first();
                
                $FcmToken = User::select('device_token')->where('id', $to_user)->first()->device_token;
                $fuser = User::select('username')->where('id', $to_user)->first()->username;
                $tuser = User::select('username')->where('id', $user_id)->first()->username;
                
                    
                // print_r($FcmToken);
                // die;



                $data = [
                    "registration_ids" => array($FcmToken),
                    "notification" => [
                        "title" => "Snapta",
                        "body" => "$tuser Liked your post.",
                        "is_type" => $request->input('type'),
                        "from_user" => $user_id,
                        "to_user" => $to_user,
                    ],
                    "data" => [
                        "title" => "Snapta",
                        "body" => "$tuser Liked your post.",
                        "is_type" => $request->input('type'),
                        "from_user" => $user_id,
                        "to_user" => $to_user,
                    ]
                ];
                
                
                // print_r($data);
                // die;
                $this->sendNotification($data);
                
                 $create_date = round(microtime(true) * 1000);


                user_notification::create([
                    'from_user' => $user_id,
                    'to_user' => $to_user,
                    'post_id' => $request->post_id,
                    'not_type' => '0',
                    'message' => "$tuser Liked your post.",
                    'date' => $create_date,
                    'title' => "Message",
                ]);
            return response(['response_code' => "1", 'message' => "Like successfull", 'status' => "success"]);
        } else {
            
            return response(['response_code' => "1", 'message' => "Already Liked Post", 'status' => "success"]);
        }
    }
    
     public function unlike_post(Request $request)
    {

        $user_id = Auth::user()->token()->user_id;
        if ($request->post_id == "") {
            return response(['response_code' => "0", 'message' => "Enter Data", 'status' => "failure"]);
        }

        $like = Like::where('user_id', $user_id)->where('post_id', $request->post_id)->first();
        if ($like) {
            
             $like = Like::where('user_id', $user_id)->where('post_id', $request->post_id)->delete();
           
            return response(['response_code' => "1", 'message' => "Unlike successfull", 'status' => "success"]);
        } else {
            
            return response(['response_code' => "1", 'message' => "Don't Unlike successfull", 'status' => "success"]);
        }
    }
    
    
    
    

    public function likes_by_post(Request $request)
    {

        $user_id = Auth::user()->token()->user_id;
        if ($request->post_id == "") {
            return response(['response_code' => 0, 'message' => "Enter Data", 'status' => "failure"]);
        }

        $likes = Like::select('users.username', 'users.profile_pic', 'users.fullname')->join('users', 'users.id', '=', 'likes.user_id')->where('post_id', $request->post_id)->get();
        foreach ($likes as $like_user) {
            $like_user->profile_pic =   url('public/images/user/'. $like_user->profile_pic);
            
           
        }
        return response(['response_code' => 1, 'message' => "Unlike successfull", 'data' => $likes, 'status' => "success"]);
    }

    public function add_comment(Request $request)
    {
        $user_id = Auth::user()->token()->user_id;
        if ($request->post_id == "" || $request->text == "") {
            return response(['response_code' => 0, 'message' => "Enter Data", 'status' => "failure"]);
        }
        
        $date = round(microtime(true) * 1000);

        $comment = Comment::create([
            'user_id' => $user_id,
            'post_id' => $request->post_id,
            'text' => $request->text,
            'date' => $date,
        ]);

        if ($comment) {
            
            
             $post_id = $request->post_id;
            //   $posts = DB::get_where('posts', array('post_id' => $post_id))->row();
           
                
                
                $posts = DB::table('posts')->where('post_id', $post_id)->first();
                
                $to_user = $posts->user_id;
               $user = DB::table('users')->where('id', $user_id)->first();
                
                $FcmToken = User::select('device_token')->where('id', $to_user)->first()->device_token;
                $fuser = User::select('username')->where('id', $to_user)->first()->username;
                $tuser = User::select('username')->where('id', $user_id)->first()->username;



                $data = [
                    "registration_ids" => array($FcmToken),
                    "notification" => [
                        "title" => "Snapta",
                        "body" => "$tuser commented on your post",
                        "is_type" => $request->input('type'),
                        "from_user" => $user_id,
                        "to_user" => $to_user,
                    ],
                    "data" => [
                        "title" => "Snapta",
                        "body" => "$tuser commented on your post",
                        "is_type" => $request->input('type'),
                        "from_user" => $user_id,
                        "to_user" => $to_user,
                    ]
                ];
                $this->sendNotification($data);
                
                 $create_date = round(microtime(true) * 1000);


                user_notification::create([
                    'from_user' => $user_id,
                    'to_user' => $to_user,
                    'post_id' => $request->post_id,
                    'not_type' => '0',
                    'message' => "$tuser commented on your post.",
                    'title' => "Message",
                    'date' => $create_date,
                ]);
            return response(['response_code' => 1, 'message' => "Comment Add", 'status' => "success"]);
        } else {
            return response(['response_code' => 0, 'message' => "Databse Error", 'status' => "failure"]);
        }
    }

    public function delete_comment(Request $request)
    {
        $user_id = Auth::user()->token()->user_id;
        if ($request->comment_id == "") {
            return response(['response_code' => 0, 'message' => "Enter Data", 'status' => "failure"]);
        }

        Comment::where('comment_id', $request->comment_id)->where('user_id', $user_id)->delete();

        return response(['response_code' => 1, 'message' => "Successfully Deleted", 'status' => "success"]);
    }

    public function comments_by_post(Request $request)
    {
        $user_id = Auth::user()->token()->user_id;
        if ($request->post_id == "") {
            return response(['response_code' => 0, 'message' => "Enter Data", 'status' => "failure"]);
        }

        $comment = Comment::select('comments.comment_id', 'comments.post_id', 'comments.user_id',  'comments.date', 'users.username', 'comments.text', 'users.profile_pic')->join('users', 'users.id', '=', 'comments.user_id')->where('post_id', $request->post_id)->orderBy('comments.comment_id', 'desc')->get();
        foreach ($comment as $like_user) {
              $like_user->comment_id = (string)$like_user->comment_id;
              $like_user->post_id = (string)$like_user->post_id;
              $like_user->user_id = (string)$like_user->user_id;
            $like_user->profile_pic = url('public/images/user/'. $like_user->profile_pic);
              
        }
        return response(['status' => 1, 'message' => "Comments Found", 'likes' => $comment]);
    }

    public function get_all_latest_post2(Request $request)
    {

        $user_id = Auth::user()->token()->user_id;

        $post = Post::select('posts.*', 'users.username', 'users.profile_pic')->join('users', 'users.id', '=', 'posts.user_id')->where('image', '!=', '')->orderBy('post_id', 'DESC')->limit(20)->get()->transform(function ($ts) use ($user_id) {
            $ts['total_likes'] = Like::where('post_id', $ts['post_id'])->count();
            $ts['total_comments'] = Comment::where('post_id', $ts['post_id'])->count();
            $ts['is_likes'] = Like::where('post_id', $ts['post_id'])->where('user_id', $user_id)->count();
            $ts['bookmark'] = Bookmark::where('post_id', $ts['post_id'])->where('user_id', $user_id)->count();
            $ts['posts_report'] = Posts_report::where('blockedPostsId', $ts['post_id'])->where('blockedByUserId', $user_id)->count();
            $ts['profile_block'] = Profile_blocklist::where('blockedUserId', $ts['post_id'])->where('blockedByUserId', $user_id)->count();
            $ts['profile_pic'] = asset('assets/images/user/' . $ts['profile_pic']);
            $ts['text'] = $ts['text'] ? $ts['text'] : "";
            $ts['image'] = $ts['image'] ? asset('assets/images/posts/' . $ts['image']) : "";
            $ts['video'] = $ts['video'] ? asset('assets/videos/' . $ts['video']) : "";
             $ts['all_image'] = $ts['image'] ? asset('assets/images/posts/' . $ts['image']) : "";
            return $ts;
            
        })->toArray();

        return response(['response_code' => 1, 'message' => "Post Found", 'rescent_post' => $post, 'status' => "success"]);
    }
    
    public function get_all_latest_post()
    {
        $result = array();
        header('Content-Type: application/json');
        
         $user_id = Auth::user()->token()->user_id;

        // if (!isset($_POST['user_id'])) {
        //     $result["response_code"] = "0";
        //     $result["message"] = "Missing Fields";
        //     $result["status"] = "fail";
        //     echo json_encode($result);
        //     return;
        // }

        // $user_id = $this->input->post('user_id');
        // $res = Post::get();
        
      $res = Post::orderBy('post_id', 'desc')->limit(20)->get();


        // $query = $this->db->query("SELECT * FROM posts WHERE image != '' ORDER BY post_id DESC LIMIT 20");
        // $res = $query->result();

        for ($i = 0; $i < count($res); $i++) {
            if (!empty($res[$i]->image)) {

                $url = explode(":", $res[$i]->image);
                if ($url[0] == "https" || $url[0] == "http") {
                    $image_url = array();

                    $image_url_a = $res[$i]->image;

                    array_push($image_url, $image_url_a);

                    $res[$i]->image = $image_url;
                    $res[$i]->all_image = $image_url;
                } else {
                    $images = explode("::::", $res[$i]->image);
                    $imgs = array();
                    $imgsa = array();
                    foreach ($images as $key => $image) {
                        $imgs = asset('public/assets/images/posts/' . $image);
                        array_push($imgsa, $imgs);
                    }
                    $res[$i]->image = $imgsa;
                    $res[$i]->all_image = $imgsa;
                }
            } else {
                $res[$i]->image = [];
                $res[$i]->all_image = array();
            }
            if ($res[$i]->video == "") {
                $res[$i]->video = "";
            } else {

                $url = explode(":", $res[$i]->video);
                if ($url[0] == "https" || $url[0] == "http") {

                    $res[$i]->video = $res[$i]->video;
                } else {
                    $res[$i]->video = asset( 'public/assets/images/posts/' . $res[$i]->video);
                }
            }
            
             $res[$i]->post_id = (int)$res[$i]->post_id;
            
            $user = User::where('id', $res[$i]->user_id)->first();

            // $user = $this->db->get_where('users', array('id' => $res[$i]->user_id), 1)->row();
            if (!empty($user)) {
                $res[$i]->username = $user->username;
                if ($user->profile_pic == "") {
                    $res[$i]->profile_pic = "";
                } else {
                    // $res[$i]->profile_pic = base_url() . 'uploads/profile_pics/' . $user->profile_pic;

                    $url = explode(":", $user->profile_pic);
                    if ($url[0] == "https" || $url[0] == "http") {
                        $res[$i]->profile_pic = $user->profile_pic;
                    } else {

                        $res[$i]->profile_pic = url('public/images/user/'. $user->profile_pic);
                        
                        
                    }
                }
            } else {
                $res[$i]->profile_pic = "";
                $res[$i]->username = "";
            }
              $res[$i]->total_likes = Like::where('post_id', $res[$i]->post_id)->count();
              
              $res[$i]->total_comments = Comment::where('post_id', $res[$i]->post_id)->count();
                $res[$i]->total_likes = Like::where('post_id', $res[$i]->post_id)->count();
                
            $is_likes = Like::where('post_id', $res[$i]->post_id)->where('user_id', $user_id)->first();
            
            $res[$i]->is_likes = $is_likes ? "true" : "false" ;
            
              $is_bookmark = Bookmark::where('post_id', $res[$i]->post_id)->where('user_id', $user_id)->first();
            
            $res[$i]->bookmark = $is_bookmark ? "true" : "false" ;
            
            $is_posts_report = Posts_report::where('blockedPostsId', $res[$i]->post_id)->where('blockedByUserId', $user_id)->first();
            
            $res[$i]->posts_report = $is_posts_report ? "true" : "false" ;
            
            $is_profile_blocklist = Profile_blocklist::where('blockedByUserId', $user_id)->where('blockedUserId', $res[$i]->user_id)->first();
            
            $res[$i]->profile_block = $is_profile_blocklist ? "true" : "false" ;
            
            $res[$i]->text = $res[$i]->text ? $res[$i]->text : "";
             
            $res[$i]->height = $res[$i]->height ? (float)$res[$i]->height : 0.0;
            
            $res[$i]->created_at = $res[$i]->created_at ? $res[$i]->created_at : "";
            
            
            $res[$i]->updated_at = $res[$i]->updated_at ? $res[$i]->updated_at : "";

            // $total_likes = $this->db->get_where('likes', array('post_id' => $res[$i]->post_id))->num_rows();
            // $res[$i]->total_likes = $total_likes;

            // $total_comments = $this->db->get_where('comments', array('post_id' => $res[$i]->post_id))->num_rows();
            // $res[$i]->total_comments = $total_comments;

            // $is_likes = $this->front_model->likeCheck($user_id, $res[$i]->post_id);
            // if (!empty($is_likes)) {
            //     $res[$i]->is_likes = "false";
            // } else {
            //     $res[$i]->is_likes = "true";
            // }

            // $bookmark = $this->front_model->bookmarkCheck($user_id, $res[$i]->post_id);
            // if (!empty($bookmark)) {
            //     $res[$i]->bookmark = "false";
            // } else {
            //     $res[$i]->bookmark = "true";
            // }

            // $posts_report = $this->front_model->posts_reportCheck($user_id, $res[$i]->post_id);
            // if (!empty($posts_report)) {
            //     $res[$i]->posts_report = "false";
            // } else {
            //     $res[$i]->posts_report = "true";
            // }


            // $posts_user_id = $res[$i]->user_id;
            // $profile_block = $this->front_model->profile_block_Check($user_id, $posts_user_id);
            // if (!empty($profile_block)) {
            //     $res[$i]->profile_block = "false";
            // } else {
            //     $res[$i]->profile_block = "true";
            // }
        }

        if (!empty($res)) {
            $result['response_code'] = "1";
            $result['message'] = "Post Found";
            $result['rescent_post'] = $res;
            $result["status"] = "success";
            echo json_encode($result);
        } else {
            $result["response_code"] = "0";
            $result["message"] = "Post Not Found";
            $result['rescent_post'] = $res;
            $result["status"] = "failure";
            echo json_encode($result);
        }
    }

    public function trending_post(Request $request)
    {
        $result = [];
        // $posts = Post::select('follow.from_user', 'follow.to_user', 'posts.*')
        //     ->withCount('likes')
        //     ->orderByDesc('likes_count')
        //     ->join('likes', 'likes.post_id', '=', 'posts.post_id')
        //     ->join('follow', 'follow.to_user', '=', 'likes.user_id')
        //     ->groupBy('posts.post_id')
        //     ->get();
        $posts = Post::select('follow.from_user', 'follow.to_user', 'posts.*')
            ->withCount(['likes as likes_count' => function ($query) {
                $query->join('follow', 'follow.to_user', '=', 'likes.user_id');
            }])
            ->orderByDesc('likes_count')
            ->join('likes', 'likes.post_id', '=', 'posts.post_id')
            ->join('follow', 'follow.to_user', '=', 'likes.user_id')
            ->groupBy('posts.post_id')
            ->get();

        foreach ($posts as $post) {
            $post->all_image = [];
            if (!empty($post->image)) {
                $imageUrls = explode(":", $post->image);
                if ($imageUrls[0] == "https" || $imageUrls[0] == "http") {
                    $post->all_image[] = $post->image;
                } else {
                    $images = explode("::::", $post->image);
                    foreach ($images as $image) {
                        $post->all_image[] = asset('assets/images/post/' . $image);
                    }
                }
            }

            if (!empty($post->video)) {
                $videoUrl = explode(":", $post->video);
                if ($videoUrl[0] == "https" || $videoUrl[0] == "http") {
                    $post->video = $post->video;
                } else {
                    $post->video = asset('assets/images/post/' . $post->video);
                }
            }

            $user = User::find($post->user_id);
            if (!empty($user)) {
                $post->username = $user->username;
                $post->profile_pic = !empty($user->profile_pic) ?  url('public/images/user/'. $user->profile_pic) : "";
                
               
            } else {
                $post->profile_pic = "";
                $post->username = "";
            }

            $post->total_likes = $post->likes_count;

            $total_comments = $post->comments()->count();
            $post->total_comments = $total_comments;

            // $is_likes = $this->front_model->likeCheck($post->from_user, $post->post_id);
            // $post->is_likes = !empty($is_likes) ? "false" : "true";

            // $bookmark = $this->front_model->bookmarkCheck($post->from_user, $post->post_id);
            // $post->bookmark = !empty($bookmark) ? "false" : "true";
        }

        if (!empty($posts)) {
            $result['response_code'] = "1";
            $result['message'] = "Trending Post Found";
            $result['post'] = $posts;
            $result["status"] = "success";
            return response()->json($result);
        } else {
            $result["response_code"] = "0";
            $result["message"] = "Trending Post Not Found";
            $result['post'] = $posts;
            $result["status"] = "failure";
            return response()->json($result);
        }
    }


    public function bookmarkPost(Request $request)
    {
        $user_id = Auth::user()->token()->user_id;
        $request->validate([
            'post_id' => 'required',
            // 'user_id' => 'required',
        ]);

        $like = [
            'post_id' => $request->post_id,
            'user_id' => $user_id,
            'date' => round(microtime(true) * 1000),
        ];

        $bookmarkCheck = Bookmark::where('user_id', $like['user_id'])
            ->where('post_id', $like['post_id'])
            ->exists();

        if ($bookmarkCheck) {
            return response()->json([
                'response_code' => '0',
                'message' => 'Already Bookmark Post',
                'status' => 'fail',
            ]);
        }

        if (Bookmark::create($like)) {
            return response()->json([
                'response_code' => '1',
                'message' => 'Bookmark Post',
                'status' => 'success',
            ]);
        } else {
            return response()->json([
                'response_code' => '0',
                'message' => 'Database Error',
                'status' => 'failure',
            ]);
        }
    }

    public function delete_bookmarkpost(Request $request)
    {
        $user_id = Auth::user()->token()->user_id;
        if ($request->post_id == "") {
            return response(['response_code' => 0, 'message' => "Enter Data", 'status' => "failure"]);
        }

        Bookmark::where('post_id', $request->post_id)->where('user_id', $user_id)->delete();

        return response(['response_code' => 1, 'message' => "Successfully Deleted", 'status' => "success"]);
    }

    public function get_post_details(Request $request)
    {
        $result = [];

        $user_id = Auth::user()->token()->user_id;
        $post_id = $request->input('post_id');

        // $post = Post::with('user:id,username,profile_pic')->find($post_id);

        // $post = $this->db->get_where('posts', array('post_id' => $post_id))->row();

        $post = Post::where('post_id', $post_id)->first();
        
        // print_r($post);
        // die;

        if (!empty($post)) {
            
            $post->post_id = (string)$post->post_id;
            $post->user_id = (string)$post->user_id;
            
            $post->text = $post->text ? $post->text : "";
            $post->created_at = $post->created_at ? $post->created_at : "" ;
            $post->updated_at =  $post->updated_at ? $post->updated_at : "" ;
            $post->height = $post->height ? (float)$post->height : 0.0;
            $post->location = $post->location ? $post->location : "";

            if (!empty($post->image)) {

                $url = explode(":", $post->image);
                if ($url[0] == "https" || $url[0] == "http") {
                    $image_url = array();

                    $image_url_a = $post->image;

                    array_push($image_url, $image_url_a);

                    $post->image = $image_url;

                    $post->all_image = $image_url;
                } else {
                    $images = explode("::::", $post->image);
                    $imgs = array();
                    $imgsa = array();
                    foreach ($images as $key => $image) {
                        
                       
                        // $imgs =  asset('assets/images/post/'. $image);
                        
                       $imgs = asset('public/assets/images/posts/' . $image);

                        array_push($imgsa, $imgs);
                    }
                    $post->image = $imgsa;

                    $post->all_image = $imgsa;
                }
            } else {
                $post->all_image = array();
            }
            if ($post->video == "") {
                $post->video = "";
            } else {

                $url = explode(":", $post->video);
                if ($url[0] == "https" || $url[0] == "http") {

                    $post->video = $post->video;
                } else {
                    $post->video = asset('public/assets/images/posts/' . $post->video);
                    
                    
                }
            }
            
             if ($post->video_thumbnail == "") {
                    $post->video_thumbnail = "";
                } else {
                    $url = explode(":", $post->video_thumbnail);

                    if ($url[0] == "https" || $url[0] == "http") {
                        $post->video = $post->video_thumbnail;
                    } else {
                        $post->video = asset('public/assets/images/posts/' .$post->video_thumbnail);
                    }
                }
            
            // User::where('id', $post->user_id)->first();
            
             $user = User::where('id', $post->user_id)->first();
            if (!empty($user)) {
                $post->username = $user->username;
                if ($user->profile_pic == "") {
                    $post->profile_pic = "";
                } else {
                    // $post->profile_pic = base_url() . 'uploads/profile_pics/' . $user->profile_pic;

                    $url = explode(":", $user->profile_pic);
                    if ($url[0] == "https" || $url[0] == "http") {
                        $post->profile_pic = $user->profile_pic;
                    } else {

                        $post->profile_pic = url('public/images/user/'. $user->profile_pic);
                    }
                }
            } else {
                $post->profile_pic = "";
                $post->username = "";
            }


            $like = Like::where('post_id', $post_id)->get();
            $post->total_likes = $like->count();


            $comments = Comment::where('post_id', $post_id)->get();
            $post->total_comments = $comments->count();
            
            
             if ($user_id) {

                    if (Like::where('user_id', $user_id)->where('post_id', $post->post_id)->exists()) {
                        $post->is_likes = "true";
                    } else {
                        $post->is_likes = "false";
                    }
                }

                if ($user_id) {

                    if (Bookmark::where('user_id', $user_id)->where('post_id', $post->post_id)->exists()) {
                        $post->bookmark = "true";
                    } else {
                        $post->bookmark = "false";
                    }
                }

                if ($user_id) {

                    if (Posts_report::where('blockedByUserId', $user_id)->where('blockedPostsId', $post->post_id)->exists()) {
                        $post->posts_report = "true";
                    } else {
                        $post->posts_report = "false";
                    }
                }
                
                 if ($user_id) {

                    if (Profile_blocklist::where('blockedByUserId', $user_id)->where('blockedUserId', $post->user_id)->exists()) {
                        $post->profile_block = "true";
                    } else {
                        $post->profile_block = "false";
                    }
                }
            // $post->total_comments = $post->comments()->count();


            // if (!empty($post)) {
            // Process image and video URLs
            // $this->processImageAndVideoUrls($post);

            // Count likes and comments
            // $post->total_likes = $post->likes()->count();
            // $post->total_comments = $post->comments()->count();

            // Check if the user has liked the post
            // $post->is_likes = $post->likes()->where('user_id', $user_id)->exists();

            // // Check if the user has bookmarked the post
            // $post->bookmark = $post->bookmarks()->where('user_id', $user_id)->exists();

            // // Check if the user has reported the post
            // $post->posts_report = $post->postsReports()->where('user_id', $user_id)->exists();

            // Check if the user has blocked the post author
            // $profile_block = User::find($user_id)->blockedUsers()->where('blocked_user_id', $post->user_id)->exists();
            // $post->profile_block = !$profile_block;

            // Get the latest comment for the post
            $latestComment = $comments->first();

            // $latestComment->user = $latestComment->comment_id;

            if (!empty($latestComment)) {
                $user = User::select('id', 'username', 'profile_pic')->find($latestComment->user_id);
                $latestComment->post_id = (string)$latestComment->post_id;
                $latestComment->comment_id = (string)$latestComment->comment_id;
                $latestComment->text = $latestComment->text;
                $latestComment->user_id = (string)$user->id;
                $latestComment->username = $user->username;
                
                $latestComment->created_at = $latestComment->created_at ? $latestComment->created_at : "" ;
                $latestComment->updated_at =  $latestComment->updated_at ? $latestComment->updated_at : "" ;

                // $this->processImageUrls($latestComment->user);
                if (!empty($user->profile_pic)) {
                    $url = explode(":", $user->profile_pic);
                    if ($url[0] == "https" || $url[0] == "http") {
                        $latestComment->profile_pic = $user->profile_pic;
                    } elseif (!empty($user->profile_pic)) {
                        $latestComment->profile_pic =  url('public/images/user/'. $user->profile_pic);
                        
                        
                    } else {
                        $latestComment->profile_pic = $user->profile_pic;
                    }
                } else {
                    $latestComment->profile_pic = "";
                }
            }
            
            $result['response_code'] = '1';
            $result['message'] = 'Post Found';

            $result['post'] = $post;
            
            if(!empty($latestComment)){
            $result['comment'] = $latestComment;
            }
            
            
            $result['status'] = 'success';

            return response()->json($result);
        } else {
            $result['response_code'] = '0';
            $result['message'] = 'Post Not Found';
            $result['status'] = 'failure';

            return response()->json($result);
        }
    }

    public function search_post(Request $request)
    {
        $result = [];
        $res = [];

        $user_id = Auth::user()->token()->user_id;

        // if (!$request->has('user_id')) {
        //     $result["response_code"] = "0";
        //     $result["message"] = "Missing Fields";
        //     $result["status"] = "fail";
        //     return response()->json($result);
        // }

        $text = $request->input('text');

        if (empty($text)) {
            $result["response_code"] = "0";
            $result["message"] = "Post Not Found";
            $result['post'] = $res;
            $result["status"] = "failure";
            return response()->json($result);
        }

        $posts = DB::select("SELECT * FROM posts WHERE image != '' AND text LIKE '%$text%' ORDER BY post_id DESC");

        foreach ($posts as $post) {
            if (!empty($post->image)) {
                $url = explode(":", $post->image);

                if ($url[0] == "https" || $url[0] == "http") {
                    $image_url = [];
                    $image_url_a = $post->image;
                    array_push($image_url, $image_url_a);

                    $post->image = $image_url;
                    $post->all_image = $image_url;
                } else {
                    $images = explode("::::", $post->image);
                    $imgs = [];
                    $imgsa = [];

                    foreach ($images as $key => $image) {
                        $imgs = asset('assets/images/post/') . $image;
                        array_push($imgsa, $imgs);
                    }

                    $post->image = $imgsa;
                    $post->all_image = $imgsa;
                }
            } else {
                $post->image = [];
                $post->all_image = [];
            }

            if ($post->video == "") {
                $post->video = "";
            } else {
                $url = explode(":", $post->video);

                if ($url[0] == "https" || $url[0] == "http") {
                    $post->video = $post->video;
                } else {
                    $post->video = asset('assets/images/post/') . $post->video;
                }
            }

            $user = DB::table('users')->where('id', $post->user_id)->first();

            if (!empty($user)) {
                $post->username = $user->username;

                if ($user->profile_pic == "") {
                    $post->profile_pic = "";
                } else {
                    $url = explode(":", $user->profile_pic);

                    if ($url[0] == "https" || $url[0] == "http") {
                        $post->profile_pic = $user->profile_pic;
                    } else {
                        $post->profile_pic =  url('public/images/user/'. $user->profile_pic);
                    }
                }
            } else {
                $post->profile_pic = "";
                $post->username = "";
            }

            $total_likes = DB::table('likes')->where('post_id', $post->post_id)->count();
            $post->total_likes = $total_likes;

            $total_comments = DB::table('comments')->where('post_id', $post->post_id)->count();
            $post->total_comments = $total_comments;

            if ($user_id) {

                if (Like::where('user_id', $user_id)->where('post_id', $post->post_id)->exists()) {
                    $post->is_likes = "true";
                } else {
                    $post->is_likes = "false";
                }
            }

            if ($user_id) {

                if (Bookmark::where('user_id', $user_id)->where('post_id', $post->post_id)->exists()) {
                    $post->bookmark = "true";
                } else {
                    $post->bookmark = "false";
                }
            }

            if ($user_id) {

                if (Posts_report::where('blockedByUserId', $user_id)->where('blockedPostsId', $post->post_id)->exists()) {
                    $post->posts_report = "true";
                } else {
                    $post->posts_report = "false";
                }
            }


            // $is_likes = $this->frontModel->likeCheck($user_id, $post->post_id);

            // if (!empty($is_likes)) {
            //     $post->is_likes = "false";
            // } else {
            //     $post->is_likes = "true";
            // }

            // $bookmark = $this->frontModel->bookmarkCheck($user_id, $post->post_id);

            // if (!empty($bookmark)) {
            //     $post->bookmark = "false";
            // } else {
            //     $post->bookmark = "true";
            // }

            // $posts_report = $this->frontModel->postsReportCheck($user_id, $post->post_id);

            // if (!empty($posts_report)) {
            //     $post->posts_report = "false";
            // } else {
            //     $post->posts_report = "true";
            // }

            // $postsUserId = $post->user_id;
            // $profileBlock = $this->frontModel->profileBlockCheck($user_id, $postsUserId);

            // if (!empty($profileBlock)) {
            //     $post->profile_block = "false";
            // } else {
            //     $post->profile_block = "true";
            // }

            $res[] = $post;
        }

        if (!empty($res)) {
            $result['response_code'] = "1";
            $result['message'] = "Post Found";
            $result['post'] = $res;
            $result["status"] = "success";
            return response()->json($result);
        } else {
            $result["response_code"] = "0";
            $result["message"] = "Post Not Found";
            $result['post'] = $res;
            $result["status"] = "failure";
            return response()->json($result);
        }
    }

    public function get_user_bookmark_post(Request $request)
    {
        $result = [];

        $user_id = Auth::user()->token()->user_id;

        $query = DB::table('bookmark')->where('user_id', $user_id)->orderByDesc('bookmark_id')->get();

        $posts = DB::table('bookmark as A')
            ->join('posts as B', 'A.post_id', '=', 'B.post_id')
            ->select('A.post_id', 'B.text', 'B.image', 'B.video', 'B.location', 'B.user_id as post_user_id', 'B.create_date')
            ->where('A.user_id', $user_id)
            ->orderByDesc('A.bookmark_id')
            ->get();

        foreach ($posts as $post) {
            if (!empty($post->image)) {
                $url = explode(":", $post->image);

                if ($url[0] == "https" || $url[0] == "http") {
                    $image_url = [];
                    $image_url_a = $post->image;
                    array_push($image_url, $image_url_a);

                    $post->image = $image_url;
                    $post->all_image = $image_url;
                } else {
                    $images = explode("::::", $post->image);
                    $imgs = [];
                    $imgsa = [];

                    foreach ($images as $key => $image) {
                        $imgs = asset('public/assets/images/posts/'. $image);
                        array_push($imgsa, $imgs);
                    }

                    $post->image = $imgsa;
                    $post->all_image = $imgsa;
                }
            } else {
                $post->image = [];
                $post->all_image = [];
            }

            if ($post->video == "") {
                $post->video = "";
            } else {
                $url = explode(":", $post->video);

                if ($url[0] == "https" || $url[0] == "http") {
                    $post->video = $post->video;
                } else {
                    $post->video = asset('public/assets/images/posts/'. $post->video);
                }
            }

            $user = User::where('id', $post->post_user_id)->first();

            if (!empty($user)) {
                $post->username = $user->username;

                if ($user->profile_pic == "") {
                    $post->profile_pic = "";
                } else {
                    $url = explode(":", $user->profile_pic);

                    if ($url[0] == "https" || $url[0] == "http") {
                        $post->profile_pic = $user->profile_pic;
                    } else {
                        $post->profile_pic =  url('public/images/user/'. $user->profile_pic);
                    }
                }
            } else {
                $post->profile_pic = "";
                $post->username = "";
            }

            $total_likes = DB::table('likes')->where('post_id', $post->post_id)->count();
            $post->total_likes = $total_likes;

            $total_comments = DB::table('comments')->where('post_id', $post->post_id)->count();
            $post->total_comments = $total_comments;

            if ($user_id) {

                if (Like::where('user_id', $user_id)->where('post_id', $post->post_id)->exists()) {
                    $post->is_likes = "true";
                } else {
                    $post->is_likes = "false";
                }
            }

            if ($user_id) {

                if (Bookmark::where('user_id', $user_id)->where('post_id', $post->post_id)->exists()) {
                    $post->bookmark = "true";
                } else {
                    $post->bookmark = "false";
                }
            }

            if ($user_id) {

                if (Posts_report::where('blockedByUserId', $user_id)->where('blockedPostsId', $post->post_id)->exists()) {
                    $post->posts_report = "true";
                } else {
                    $post->posts_report = "false";
                }
            }


            // $is_likes = $this->frontModel->likeCheck($user_id, $post->post_id);

            // if (!empty($is_likes)) {
            //     $post->is_likes = "false";
            // } else {
            //     $post->is_likes = "true";
            // }

            // $bookmark = $this->frontModel->bookmarkCheck($user_id, $post->post_id);

            // if (!empty($bookmark)) {
            //     $post->bookmark = "false";
            // } else {
            //     $post->bookmark = "true";
            // }

            // $posts_report = $this->frontModel->postsReportCheck($user_id, $post->post_id);

            // if (!empty($posts_report)) {
            //     $post->posts_report = "false";
            // } else {
            //     $post->posts_report = "true";
            // }

            // $postsUserId = $post->user_id;
            // $profileBlock = $this->frontModel->profileBlockCheck($user_id, $postsUserId);

            // if (!empty($profileBlock)) {
            //     $post->profile_block = "false";
            // } else {
            //     $post->profile_block = "true";
            // }

            $res[] = $post;
        }

        if (empty($res)) {
            $result['response_code'] = "0";
            $result['message'] = "Post Not Found";
            $result['post'] = [];
            $result["status"] = "failure";
        } else {
            $result['response_code'] = "1";
            $result['message'] = "Post Found";
            $result['post'] = $res;
            $result["status"] = "success";
        }

        return response()->json($result);
    }

    public function notification_list(Request $request)
    {
        $user_id = Auth::user()->token()->user_id;

        if (!$user_id) {
            $result = [
                "response_code" => "0",
                "message" => "Enter Data",
                "status" => "failure",
            ];
            return response()->json($result);
        }

        $notifications = user_notification::where('to_user', $user_id)->orderBy('not_id', 'DESC')->get();

        $list_notification = [];

        foreach ($notifications as $notification) {
            $questions_list['not_id'] = $notification->not_id;
            $questions_list['from_user'] = $notification->from_user;
            $questions_list['to_user'] = $notification->to_user;
            $questions_list['post_id'] = $notification->post_id ?? "";
            $questions_list['reel_id'] = $notification->reel_id ?? "";
            $questions_list['title'] = $notification->title;
            $questions_list['message'] = $notification->message;
            $questions_list['read_status'] = $notification->read_status;
            $questions_list['date'] = $notification->date;
            // $questions_list['time'] = $notification->created_at->diffForHumans();
            // $questions_list['is_view'] = $notification->is_view ? $notification->is_view : "0" ;


            // $createdAt = $notification->created_at->startOfDay();
            // $today = Carbon::today();
            // $yesterday = Carbon::yesterday();
            // $date = "";
            // if ($createdAt->eq($today)) {
            //     $date = 'Today';
            // } elseif ($createdAt->eq($yesterday)) {
            //     $date = 'Yesterday';
            // } else {
            //     $date = $createdAt->format('d M');
            // }

            // $questions_list['time'] = $date;

            $user = User::find($notification->from_user);

            if (!empty($user)) {
                $questions_list['username'] = $user->username;

                // Assuming you store the profile_pic in the 'profile_pics' directory within 'public' folder
                $profile_pic = $user->profile_pic;
                if (!empty($profile_pic)) {
                    $url = explode(":", $profile_pic);
                    if ($url[0] == "https" || $url[0] == "http") {
                        $questions_list['profile_pic'] = $profile_pic;
                    } else {
                        $questions_list['profile_pic'] = url('public/images/user/'. $profile_pic);
                        
                        
                    }
                } else {
                    $questions_list['profile_pic'] = "";
                }
            } else {
                $questions_list['profile_pic'] = "";
                $questions_list['username'] = "";
            }

            $list_notification[] = $questions_list;
        }

        if ($list_notification) {
            $result = [
                "response_code" => "1",
                "message" => "found notification",
                "detail" => $list_notification,
                "status" => "success",
            ];
        } else {
            $result = [
                "response_code" => "0",
                "message" => "Not found notification",
                "detail" => $list_notification,
                "status" => "failure",
            ];
        }

        return response()->json($result);
    }

    public function add_reel(Request $request)
    {
        $result = [];

        $user_id = Auth::user()->token()->user_id;

        $title = $request->input('title');
        
        // $uploadpath = public_path() . '/post_pic/';

        // $validator = Validator::make($request->all(), [
        //     'post_pic' => 'required',
        //     'post_pic.*' => 'mimes:png,jpg,jpeg,mp4,web|mmax:10240',
        //     'thumbnail' => 'mimes:png,jpg,jpeg',
        //     function ($attribute, $value, $fail) {
        //         // Custom validation rule to check video length
        //         $video = $value->getPathname();
        //         $duration = shell_exec("ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 $video");
        //         $duration = intval($duration);
        //         // Check if video duration is between 30 and 60 seconds
        //         if ($duration < 30 || $duration > 60) {
        //             $fail("The $attribute must be between 30 and 60 seconds.");
        //         }
        //     }
        //     // 'image.*' => 'mimes:jpg,png,jpeg',
        //     // 'video' => 'mimes:mp4',
        //     // 'thumbnail' => 'mimes:jpeg,png,jpg',
        // ]);

        if ($user_id) {
            $resImage = "";
            $videoThumbnail = "";

            if ($request->hasFile('post_pic') && $request->file('post_pic')->isValid()) {
                // File upload configuration
                $postPic = $request->file('post_pic');
                $postPicName = uniqid() . '.' . $postPic->getClientOriginalExtension();
                $postPic->move(public_path('assets/images/reel'), $postPicName);
                $resImage = $postPicName;
            }
            
    //         if ($request->hasFile('post_pic') && $request->file('post_pic')->isValid()) {
    //     // File upload configuration
    //     $postPic = $request->file('post_pic');
    //     $postPicName = uniqid() . '.' . $postPic->getClientOriginalExtension();
    //     $postPic->move(public_path('assets/images/reel'), $postPicName);
    //     $resImage = $postPicName;

    //     // Compress video
    //     if ($postPic->getClientOriginalExtension() === 'mp4') {
    //         $videoPath = $postPic->path();
    //         $outputPath = public_path('assets/images/reel');
    //         $outputFilename = uniqid('compressed_') . '.mp4';
    //         $outputVideoPath = $outputPath . '/' . $outputFilename;

    //         $ffmpeg = FFMpeg::create();
    //         $video = $ffmpeg->open($videoPath);

    //         $video->filters()->resize(new FFMpeg\Coordinate\Dimension(640, 480))->synchronize();
    //         $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))->save($outputVideoPath);
    //     }
    // }

            if ($request->hasFile('video_thumbnail') && $request->file('video_thumbnail')->isValid()) {
                // File upload configuration
                $thumbnail = $request->file('video_thumbnail');
                $thumbnailName = uniqid() . '.' . $thumbnail->getClientOriginalExtension();
                $thumbnail->move(public_path('assets/images/post/video_thumbnail'), $thumbnailName);
                $videoThumbnail = $thumbnailName;
            }

            $data = [
                'user_id' => $user_id,
                'title' => $title,
                'description' => $request->input('description'),
                'post_pic' => $resImage,
                'location' => $request->input('location'),
                'video_thumbnail' => $videoThumbnail,
                'create_date' => round(microtime(true) * 1000),
            ];

            if ($title) {
                $data['title'] = $title;
            }

            try {
                // DB::beginTransaction();

                DB::table('reels')->insert($data);
                
              
                
                  if ($request->tag_users != "") {
                      
            $reelId = DB::table('reels')->insertGetId($data);
            $tag_user = explode(',',$request->tag_users);
            
            

            foreach ($tag_user as $tag) {
                post_user_tag::create([
                    'user_id' => $user_id,
                    'reel_id' => $reelId,
                    'tag_users' => $tag
                ]);
            }
            
            // print_r($tag);
            // die;
        }

                // DB::commit();

                $temp["response_code"] = "1";
                $temp["message"] = "Reel added successfully";
                $temp["status"] = "success";

                return response()->json($temp);
            } catch (\Exception $e) {
                // DB::rollBack();

                $temp["response_code"] = "0";
                $temp["message"] = "Database Error";
                $temp["status"] = "failure";

                return response()->json($temp);
            }
        } else {
            $temp["response_code"] = "0";
            $temp["message"] = "Enter Data";
            $temp["status"] = "failure";

            return response()->json($temp);
        }
    }

    public function get_all_reels(Request $request)
    {
        $result = [];
        $user_id = Auth::user()->token()->user_id;

        $reels = DB::table('reels')
            ->select('*')
            ->where('post_pic', '!=', '')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        $res = $reels->toArray();

        foreach ($res as $key => $reel) {
            if ($reel->post_pic != "") {
                $url = explode(":", $reel->post_pic);

                if ($url[0] == "https" || $url[0] == "http") {
                    $res[$key]->post_pic = $reel->post_pic;
                } else {
                    $res[$key]->post_pic = asset('public/assets/images/reel/' . $reel->post_pic);
                }
            }
            
            
              if ($reel->video_thumbnail == "") {
                    $res[$key]->video_thumbnail = "";
                } else {
                    $url = explode(":", $reel->video_thumbnail);

                    if ($url[0] == "https" || $url[0] == "http") {
                        $res[$key]->video_thumbnail = $user->video_thumbnail;
                    } else {
                        $res[$key]->video_thumbnail =  url('public/assets/images/post/video_thumbnail/' .$reel->video_thumbnail);
                    }
                }
            
            $reel->location =  $reel->location ? $reel->location : "";
            $reel->description =  $reel->description ? $reel->description : ""; 
            $reel->title =  $reel->title ? $reel->title : "";
            $reel->created_at =  $reel->created_at ? $reel->created_at : "";
            $reel->updated_at =  $reel->updated_at ? $reel->updated_at : "";
            
             if ($user_id) {

                    if (Reel_Like::where('user_id', $user_id)->where('reel_id', $reel->id)->exists()) {
                        $reel->is_likes = "true";
                    } else {
                        $reel->is_likes = "false";
                    }
                }
                
                

            $like = Reel_Like::where('reel_id', $reel->id)->get();
            $reel->total_likes = $like->count();


            $comments = Reel_Comment::where('reel_id', $reel->id)->get();
            $reel->total_comments = $comments->count();


            $user = DB::table('users')->where('id', $reel->user_id)->first();

            if ($user) {
                $res[$key]->username = $user->username;

                if ($user->profile_pic == "") {
                    $res[$key]->profile_pic = "";
                } else {
                    $url = explode(":", $user->profile_pic);

                    if ($url[0] == "https" || $url[0] == "http") {
                        $res[$key]->profile_pic = $user->profile_pic;
                    } else {
                        $res[$key]->profile_pic =  url('public/images/user/'. $user->profile_pic);
                    }
                }
                
               
            } else {
                $res[$key]->profile_pic = "";
                $res[$key]->username = "";
            }
        }

        if (!empty($res)) {
            $result["status"] = "success";
            $result["response_code"] = "1";
            $result["message"] = "Reels Found";
            $result["recent_reel"] = $res;

            return response()->json($result);
        } else {
            $result["status"] = "failure";
            $result["response_code"] = "0";
            $result["message"] = "Reels Not Found";
            $result["recent_reel"] = $res;

            return response()->json($result);
        }
    }
    
    public function like_reels(Request $request)
    {

        $user_id = Auth::user()->token()->user_id;
        if ($request->reel_id == "") {
            return response(['response_code' => 0, 'message' => "Enter Data", 'status' => "failure"]);
        }

        $like = Reel_Like::where('user_id', $user_id)->where('reel_id', $request->reel_id)->count();
        
        $date = round(microtime(true) * 1000);


        if ($like == 0) {
            Reel_Like::create(['user_id' => $user_id, 'reel_id' => $request->reel_id, 'date' => $date]);
            
               $post_id = $request->reel_id;
            //   $posts = DB::get_where('posts', array('post_id' => $post_id))->row();
           
                
                
                $posts = DB::table('reels')->where('id', $post_id)->first();
                
                $to_user = $posts->user_id;
                $user = DB::table('users')->where('id', $user_id)->first();
                
                $FcmToken = User::select('device_token')->where('id', $to_user)->first()->device_token;
                $fuser = User::select('username')->where('id', $to_user)->first()->username;
                $tuser = User::select('username')->where('id', $user_id)->first()->username;



                $data = [
                    "registration_ids" => array($FcmToken),
                    "notification" => [
                        "title" => "Snapta",
                        "body" => "$tuser Liked your reel.",
                        "is_type" => $request->input('type'),
                        "from_user" => $user_id,
                        "to_user" => $to_user,
                    ],
                    "data" => [
                        "title" => "Snapta",
                        "body" => "$tuser Liked your reel.",
                        "is_type" => $request->input('type'),
                        "from_user" => $user_id,
                        "to_user" => $to_user,
                    ]
                ];
                $this->sendNotification($data);
                
                 $create_date = round(microtime(true) * 1000);


                user_notification::create([
                    'from_user' => $user_id,
                    'to_user' => $to_user,
                    'post_id' => "",
                    'reel_id' => $request->reel_id,
                    'not_type' => '0',
                    'message' => "$tuser Liked your reel.",
                    'title' => "Message",
                    'date' => $create_date,
                ]);
            return response(['response_code' => "1", 'message' => "Like successfull", 'status' => "success"]);
        } else {
            
            return response(['response_code' => "1", 'message' => "Already Liked Post", 'status' => "success"]);
        }
    }
    
     public function unlike_reels(Request $request)
    {

        $user_id = Auth::user()->token()->user_id;
        if ($request->reel_id == "") {
            return response(['response_code' => "0", 'message' => "Enter Data", 'status' => "failure"]);
        }

        $like = Reel_Like::where('user_id', $user_id)->where('reel_id', $request->reel_id)->first();
        if ($like) {
            
             $like = Reel_Like::where('user_id', $user_id)->where('reel_id', $request->reel_id)->delete();
           
            return response(['response_code' => "1", 'message' => "Unlike successfull", 'status' => "success"]);
        } else {
            
            return response(['response_code' => "1", 'message' => "Don't Unlike successfull", 'status' => "success"]);
        }
    }
    
    public function add_reel_comment(Request $request)
    {
        $user_id = Auth::user()->token()->user_id;
        if ($request->reel_id == "" || $request->text == "") {
            return response(['response_code' => 0, 'message' => "Enter Data", 'status' => "failure"]);
        }
        
        $date = round(microtime(true) * 1000);

        $comment = Reel_Comment::create([
            'user_id' => $user_id,
            'reel_id' => $request->reel_id,
            'text' => $request->text,
            'date' => $date,
        ]);

        if ($comment) {
            
            $post_id = $request->reel_id;
            
             $posts = DB::table('reels')->where('id', $post_id)->first();
                
                $to_user = $posts->user_id;
                $user = DB::table('users')->where('id', $user_id)->first();
                
                $FcmToken = User::select('device_token')->where('id', $to_user)->first()->device_token;
                $fuser = User::select('username')->where('id', $to_user)->first()->username;
                $tuser = User::select('username')->where('id', $user_id)->first()->username;



                $data = [
                    "registration_ids" => array($FcmToken),
                    "notification" => [
                        "title" => "Snapta",
                        "body" => "$tuser commented your reel.",
                        "is_type" => $request->input('type'),
                        "from_user" => $user_id,
                        "to_user" => $to_user,
                    ],
                    "data" => [
                        "title" => "Snapta",
                        "body" => "$tuser commented your reel.",
                        "is_type" => $request->input('type'),
                        "from_user" => $user_id,
                        "to_user" => $to_user,
                    ]
                ];
                $this->sendNotification($data);
                
                 $create_date = round(microtime(true) * 1000);



                user_notification::create([
                    'from_user' => $user_id,
                    'to_user' => $to_user,
                    'post_id' => "",
                    'reel_id' => $request->reel_id,
                    'not_type' => '0',
                    'message' => "$tuser commented your reel.",
                    'title' => "Message",
                    'date' => $create_date,
                    
                ]);
            return response(['response_code' => 1, 'message' => "Comment Add", 'status' => "success"]);
        } else {
            return response(['response_code' => 0, 'message' => "Databse Error", 'status' => "failure"]);
        }
    }
    
    public function get_reel_details(Request $request)
    {
        $result = [];

        $user_id = Auth::user()->token()->user_id;
        $reel_id = $request->input('reel_id');

        // $post = Post::with('user:id,username,profile_pic')->find($post_id);

        // $post = $this->db->get_where('posts', array('post_id' => $post_id))->row();

        $post = Reel::where('id', $reel_id)->first();

        if (!empty($post)) {
            
            // $post->reel_id = (string)$post->id;
            $post->user_id = (string)$post->user_id;
            
            $post->description = $post->description ? $post->description : "";
            $post->title = $post->title ? $post->title : "";
            $post->location = $post->location ? $post->location : "";
            $post->created_at = $post->created_at ? $post->created_at : "" ;
            $post->updated_at =  $post->updated_at ? $post->updated_at : "" ;

            // if (!empty($post->image)) {

            //     $url = explode(":", $post->image);
            //     if ($url[0] == "https" || $url[0] == "http") {
            //         $image_url = array();

            //         $image_url_a = $post->image;

            //         array_push($image_url, $image_url_a);

            //         $post->image = $image_url;

            //         $post->all_image = $image_url;
            //     } else {
            //         $images = explode("::::", $post->image);
            //         $imgs = array();
            //         $imgsa = array();
            //         foreach ($images as $key => $image) {
                        
                       
            //             // $imgs =  asset('assets/images/post/'. $image);
                        
            //           $imgs = asset('public/assets/images/posts/' . $image);

            //             array_push($imgsa, $imgs);
            //         }
            //         $post->image = $imgsa;

            //         $post->all_image = $imgsa;
            //     }
            // } else {
            //     $post->all_image = array();
            // }
            if ($post->post_pic == "") {
                $post->post_pic = "";
            } else {

                $url = explode(":", $post->post_pic);
                if ($url[0] == "https" || $url[0] == "http") {

                    $post->post_pic = $post->post_pic;
                } else {
                    $post->post_pic = asset('public/assets/images/reel/' . $post->post_pic);
                    
                    
                }
            }
            
            
            if ($post->video_thumbnail == "") {
                $post->video_thumbnail = "";
            } else {

                $url = explode(":", $post->video_thumbnail);
                if ($url[0] == "https" || $url[0] == "http") {

                    $post->video_thumbnail = $post->video_thumbnail;
                } else {
                    $post->video_thumbnail = asset('public/assets/images/post/video_thumbnail/' . $post->video_thumbnail);
                    
                    
                }
            }
            
            // User::where('id', $post->user_id)->first();
            
             $user = User::where('id', $post->user_id)->first();
            if (!empty($user)) {
                $post->username = $user->username;
                if ($user->profile_pic == "") {
                    $post->profile_pic = "";
                } else {
                    // $post->profile_pic = base_url() . 'uploads/profile_pics/' . $user->profile_pic;

                    $url = explode(":", $user->profile_pic);
                    if ($url[0] == "https" || $url[0] == "http") {
                        $post->profile_pic = $user->profile_pic;
                    } else {

                        $post->profile_pic = url('public/images/user/'. $user->profile_pic);
                    }
                }
            } else {
                $post->profile_pic = "";
                $post->username = "";
            }


            $like = Reel_Like::where('reel_id', $reel_id)->get();
            $post->total_likes = $like->count();


            $comments = Reel_Comment::where('reel_id', $reel_id)->get();
            $post->total_comments = $comments->count();
            
            
             if ($user_id) {

                    if (Reel_Like::where('user_id', $user_id)->where('reel_id', $reel_id)->exists()) {
                        $post->is_likes = "true";
                    } else {
                        $post->is_likes = "false";
                    }
                }

                // if ($user_id) {

                //     if (Bookmark::where('user_id', $user_id)->where('post_id', $post->reel_id)->exists()) {
                //         $post->bookmark = "true";
                //     } else {
                //         $post->bookmark = "false";
                //     }
                // }

                // if ($user_id) {

                //     if (Posts_report::where('blockedByUserId', $user_id)->where('blockedPostsId', $post->post_id)->exists()) {
                //         $post->posts_report = "true";
                //     } else {
                //         $post->posts_report = "false";
                //     }
                // }
                
                //  if ($user_id) {

                //     if (Profile_blocklist::where('blockedByUserId', $user_id)->where('blockedUserId', $post->user_id)->exists()) {
                //         $post->profile_block = "true";
                //     } else {
                //         $post->profile_block = "false";
                //     }
                // }
            // $post->total_comments = $post->comments()->count();


            // if (!empty($post)) {
            // Process image and video URLs
            // $this->processImageAndVideoUrls($post);

            // Count likes and comments
            // $post->total_likes = $post->likes()->count();
            // $post->total_comments = $post->comments()->count();

            // Check if the user has liked the post
            // $post->is_likes = $post->likes()->where('user_id', $user_id)->exists();

            // // Check if the user has bookmarked the post
            // $post->bookmark = $post->bookmarks()->where('user_id', $user_id)->exists();

            // // Check if the user has reported the post
            // $post->posts_report = $post->postsReports()->where('user_id', $user_id)->exists();

            // Check if the user has blocked the post author
            // $profile_block = User::find($user_id)->blockedUsers()->where('blocked_user_id', $post->user_id)->exists();
            // $post->profile_block = !$profile_block;

            // Get the latest comment for the post
            $latestComment = $comments->first();

            // $latestComment->user = $latestComment->comment_id;

            if (!empty($latestComment)) {
                $user = User::select('id', 'username', 'profile_pic')->find($latestComment->user_id);
                $latestComment->post_id = (string)$latestComment->post_id;
                $latestComment->comment_id = (string)$latestComment->comment_id;
                $latestComment->text = $latestComment->text;
                $latestComment->user_id = (string)$user->id;
                $latestComment->username = $user->username;
                
                $latestComment->created_at = $latestComment->created_at ? $latestComment->created_at : "" ;
                $latestComment->updated_at =  $latestComment->updated_at ? $latestComment->updated_at : "" ;

                // $this->processImageUrls($latestComment->user);
                if (!empty($user->profile_pic)) {
                    $url = explode(":", $user->profile_pic);
                    if ($url[0] == "https" || $url[0] == "http") {
                        $latestComment->profile_pic = $user->profile_pic;
                    } elseif (!empty($user->profile_pic)) {
                        $latestComment->profile_pic =  url('public/images/user/'. $user->profile_pic);
                        
                        
                    } else {
                        $latestComment->profile_pic = $user->profile_pic;
                    }
                } else {
                    $latestComment->profile_pic = "";
                }
            }
            
            $result['response_code'] = '1';
            $result['message'] = 'Post Found';

            $result['reel_details'] = $post;
            
            // if(!empty($latestComment)){
            // $result['comment'] = $latestComment;
            // }
            
            
            $result['status'] = 'success';

            return response()->json($result);
        } else {
            $result['response_code'] = '0';
            $result['message'] = 'Post Not Found';
            $result['status'] = 'failure';

            return response()->json($result);
        }
    }
    
    public function comments_by_reel(Request $request)
    {
        $user_id = Auth::user()->token()->user_id;
        if ($request->reel_id == "") {
            return response(['response_code' => 0, 'message' => "Enter Data", 'status' => "failure"]);
        }

        $comment = Reel_Comment::select('reels_comment.reel_comment_id', 'reels_comment.reel_id', 'reels_comment.user_id',  'reels_comment.date', 'users.username', 'reels_comment.text', 'users.profile_pic')->join('users', 'users.id', '=', 'reels_comment.user_id')->where('reel_id', $request->reel_id)->orderBy('reels_comment.reel_comment_id', 'desc')->get();
        foreach ($comment as $like_user) {
              $like_user->comment_id = (string)$like_user->comment_id;
              $like_user->reel_id = (string)$like_user->reel_id;
              $like_user->user_id = (string)$like_user->user_id;
            $like_user->profile_pic =  $like_user->profile_pic ? url('public/images/user/'. $like_user->profile_pic) : "";
              
        }
        return response(['status' => 1, 'message' => "Comments Found", 'comments' => $comment]);
    }
    
}
