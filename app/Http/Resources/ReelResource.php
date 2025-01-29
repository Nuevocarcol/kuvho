<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;

class ReelResource extends JsonResource
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
        
         $to_user_profile = User::select('username', 'profile_pic')->where('id', $this->user_id)->first();
        
        return [
            'reel_id' => (string)$this->id,
            'user_id' => (string)$this->user_id,
            'title' => $this->title ? $this->title : "",
            'description' => $this->description ? $this->description : "",
            'video' => ($this->post_pic) ? asset('public/assets/images/reel/'.$this->post_pic) : "",
            // 'video' => ($this->video) ? asset('public/assets/images/posts/'.$this->video) : "",
            'video_thumbnail' => ($this->video_thumbnail) ? asset('assets/images/post/video_thumbnail/'.$this->video_thumbnail) : "",
            // 'video_thumbnail' => ($this->video_thumbnail) ? asset('public/assets/images/post/video_thumbnail/'.$this->video_thumbnail) : "",
            // 'location' => $this->location,
            'create_date' => $this->create_date,
            'created_at' => $this->created_at ? $this->created_at : "",
            'is_likes' => (string)$this->is_likes,
            'total_likes' => (int)$this->total_like,
            'total_comments' => (int)$this->total_comment,
            // 'bookmark' => (string)$this->bookmark,
            'comment' => $this->reels_comment,
            'profile_image' =>   url('public/images/user/'. $to_user_profile->profile_pic),
             'username' => $to_user_profile->username,
             'all_image' => $user_profile,
            
        ];
    }
}
