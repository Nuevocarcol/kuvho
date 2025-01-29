<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\Post;
use App\Models\Reel;

class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
     
     
      
    public function toArray(Request $request): array
    {
        
            
                         if ($this->post_pic) {
                             
                             $user_profile = array();
    $imageUrls = explode("::::", $this->post_pic);
    
    foreach($imageUrls as $value)

    if (!empty($imageUrls)) {
        $event_image = asset('assets/images/reel/'. $value);
        // $event_image = asset('public/assets/images/reel/'. $value);
        
          array_push($user_profile, $event_image);
    } else {
        $user_profile = []; // Handle the case when there are no images.
    }
} else {
    $user_profile = []; // Handle the case when $approve->image is empty.
}

     $post_profile = Post::select('post_id', 'text', 'image', 'video', 'video_thumbnail', 'location')->where('post_id', $this->post_id)->first();
     
      $reel_profile = Reel::select('id', 'title', 'post_pic', 'video_thumbnail', 'description')->where('id', $this->reel_id)->first();
     
     $all_image = [];
     $img_done = "";
     
      if (!empty($post_profile->image)) {
                    $url = explode(":", $post_profile->image);

                    if ($url[0] == "https" || $url[0] == "http") {
                        $image_url = [];
                        $image_url_a = $post_profile->image;
                        array_push($image_url, $image_url_a);

                        // $post->image = $image_url;
                        $all_image = $image_url;
                    } else {
                        $images = explode("::::", $post_profile->image);
                        
                          $img_done = asset('assets/images/posts/'. $images[0]);
                        //   $img_done = asset('public/assets/images/posts/'. $images[0]);
                        $imgs = [];
                        $imgsa = [];

                        foreach ($images as $key => $image) {
                            $imgs = asset('assets/images/posts/'. $image);
                            // $imgs = asset('public/assets/images/posts/'. $image);
                            
                           
                            array_push($imgsa, $imgs);
                        }

                        // $post->image = $imgsa;
                        $all_image = $imgsa;
                    }
                } 

        
         $to_user_profile = User::select('username', 'profile_pic')->where('id', $this->user_id)->first();
        
        return [
            'tag_id' => (string)$this->tag_id,
            'post_id' => (string)$this->post_id,
            'user_id' => (string)$this->user_id,
            'tag_users' => $this->tag_users ? $this->tag_users : "",
            'reel_id' => (string)$this->reel_id,
            // 'image' => ($this->post_pic) ? asset('public/assets/images/reel/'.$this->post_pic) : "",
            
            'image' => ($img_done) ?  $img_done : "",
            'all_images' => $all_image,
            'reel_description' => $this->description ? $this->description : "",
            'video' => ($post_profile && $post_profile->video) ? asset('assets/images/posts/'.$post_profile->video) : "",
            // 'video' => ($post_profile && $post_profile->video) ? asset('public/assets/images/posts/'.$post_profile->video) : "",
             'video' => $post_profile ? ( $post_profile->video ? asset('assets/images/posts/'.$post_profile->video) : "") : asset('assets/images/reel/'.$reel_profile->post_pic) ,
            //  'video' => $post_profile ? ( $post_profile->video ? asset('public/assets/images/posts/'.$post_profile->video) : "") : asset('public/assets/images/reel/'.$reel_profile->post_pic) ,
             
            //  'video_thumbnail' => $post_profile ? ( $post_profile->video_thumbnail ? asset('public/assets/images/posts/'.$post_profile->video_thumbnail) : "") : $reel_profile ?  ( $reel_profile->video_thumbnail ? asset('public/assets/images/posts/'. $reel_profile->video_thumbnail) : ""),
            'video_thumbnail' => 
    ($post_profile && $post_profile->video_thumbnail) 
        ? asset('assets/images/posts/'.$post_profile->video_thumbnail) 
        // ? asset('public/assets/images/posts/'.$post_profile->video_thumbnail) 
        : (($reel_profile && $reel_profile->video_thumbnail) 
            ? asset('assets/images/posts/'.$reel_profile->video_thumbnail) 
            // ? asset('public/assets/images/posts/'.$reel_profile->video_thumbnail) 
            : ""),
            
            // 'video_thumbnail' =>  ($post_profile && $post_profile->video_thumbnail) ? asset('public/assets/images/posts/'.$post_profile->video_thumbnail) : "",
            'location' => ($post_profile && $post_profile->location) ? $post_profile->location : "",
            'create_date' => $this->create_date,
            'created_at' => $this->created_at ? $this->created_at : "",
            // 'is_likes' => (string)$this->is_likes,
            // 'total_likes' => (int)$this->total_like,
            // 'total_comments' => (int)$this->total_comment,
            // 'bookmark' => (string)$this->bookmark,
            // 'comment' => $this->reels_comment,
            'profile_image' =>   url('public/images/user/'. $to_user_profile->profile_pic),
             'username' => $to_user_profile->username,
             'all_image' => $user_profile,
            
        ];
    }
}
