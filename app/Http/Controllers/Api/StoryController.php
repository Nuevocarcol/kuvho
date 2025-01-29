<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Story;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StoryController extends Controller
{
    public function index(Request $request)
    {
        $user_id = Auth::user()->token()->user_id;
    }

    public function add_story(Request $request)
    {

        $user_id = Auth::user()->token()->user_id;

        if ($request->file('image')) {
            $file = $request->file('image');
            $filename = "posts " . date('YmdHis') . $file->getClientOriginalName();
            $filename = str_replace(" ", "", $filename);
            $file->move(public_path('assets/images/posts'), $filename);
            $update['url'] = $filename;
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
        $update['type'] = $request->type;
        // $update['location'] = $request->location;

        $post = Story::create($update);

        // if ($request->tag_users != "") {
        //     $tag_user = explode(',', $request->tag_user);

        //     foreach ($tag_user as $tag) {
        //         post_user_tag::create([
        //             'user_id' => $user_id,
        //             'post_id' => $post->id,
        //             'tag_users' => $tag
        //         ]);
        //     }
        // }

        return response(['response_code' => 1, 'message' => "success", 'status' => "success"]);
    }


    public function get_story_by_user2(Request $request)
    {
        $result = [];

        $user_id = Auth::user()->token()->user_id;

        $followCount = DB::table('follow')->where('from_user', $user_id)->count();

        if ($followCount > 0) {
            $allStory = DB::select("SELECT A.from_user AS from_user, A.to_user AS to_user, B.* 
            FROM follow A
            JOIN story B ON A.to_user = B.user_id
            WHERE A.from_user = '$user_id' AND B.is_delete = 0
            GROUP BY B.user_id
            ORDER BY B.story_id DESC");

            $twentyPost = DB::select("SELECT * FROM story 
            WHERE user_id = '$user_id' AND is_delete = 0 
            GROUP BY user_id 
            ORDER BY story_id DESC");

            $inputArray = array_merge($twentyPost, $allStory);

            $res = array_unique($inputArray, SORT_REGULAR);
        } else {
            $res = DB::select("SELECT * FROM story 
            WHERE user_id = '$user_id' AND is_delete = 0 
            GROUP BY user_id 
            ORDER BY story_id DESC");
        }

        $storyImageList = [];

        foreach ($res as $list) {
            $storyList = [
                'story_id' => (string)$list->story_id,
                'user_id' => (string)$list->user_id,
                'url' => $list->url,
                'type' => $list->type,
                'create_date' => $list->create_date,
            ];

            $user = DB::table('users')->where('id', $list->user_id)->first();

            if (!empty($user)) {
                $storyList['username'] = $user->username;

                $url = explode(":", $user->profile_pic);

                if ($url[0] == "https" || $url[0] == "http") {
                    $storyList['profile_pic'] = $user->profile_pic;
                } else {
                    $storyList['profile_pic'] =  url('public/images/user/'. $user->profile_pic);
                    
                    
                }
            } else {
                $storyList['profile_pic'] = "";
                $storyList['username'] = "";
            }

            $storyArr = [];

            $story = DB::table('story')->where('user_id', $list->user_id)->get();

            foreach ($story as $product) {
                $queryCount = DB::select("SELECT story.url, story.type FROM story WHERE story_id = '$product->story_id'");
                $storyRow = $queryCount[0];

                if (!empty($storyRow->url)) {
                    $storyRow->url = asset('public/assets/images/posts/' . $storyRow->url);
                    $storyRow->type = $storyRow->type;
                }

                if (!empty($storyRow->url)) {
                    $storyArr[] = $storyRow;
                }
            }

            $storyList['story_image'] = $storyArr;
            $storyImageList[] = $storyList;
        }

        if (!empty($storyImageList)) {
            $result['status'] = "1";
            $result['msg'] = "Story Found";
            $result['post'] = $storyImageList;
            return response()->json($result);
        } else {
            $result["status"] = "0";
            $result["msg"] = "Story Not Found";
            $result['post'] = $storyImageList;
            return response()->json($result);
        }
    
    
    }
    public function get_story_by_user(Request $request)
    {
        $result = [];

        $user_id = Auth::user()->token()->user_id;

        $followCount = DB::table('follow')->where('from_user', $user_id)->count();

        // if ($followCount > 0) {
        //     $allStory = DB::select("SELECT A.from_user AS from_user, A.to_user AS to_user, B.* 
        //     FROM follow A
        //     JOIN story B ON A.to_user = B.user_id
        //     WHERE A.from_user = '$user_id' AND B.is_delete = 0
        //     GROUP BY B.user_id
        //     ORDER BY B.story_id DESC");

        //     $twentyPost = DB::select("SELECT * FROM story 
        //     WHERE user_id = '$user_id' AND is_delete = 0 
        //     GROUP BY user_id 
        //     ORDER BY story_id DESC");

        //     $inputArray = array_merge($twentyPost, $allStory);

        //     $res = array_unique($inputArray, SORT_REGULAR);
        // } else {
        //     $res = DB::select("SELECT * FROM story 
        //     WHERE user_id = '$user_id' AND is_delete = 0 
        //     GROUP BY user_id 
        //     ORDER BY story_id DESC");
        // }
        
        if ($followCount > 0) {
    // $allStory = DB::select("SELECT A.from_user AS from_user, A.to_user AS to_user, B.* 
    //     FROM follow A
    //     JOIN story B ON A.to_user = B.user_id
    //     WHERE A.from_user = '$user_id' AND B.is_delete = 0
    //     ORDER BY B.story_id DESC");
    
    
  DB::statement("SET @row_number = 0, @prev_user_id = ''");

$allStory = DB::select("SELECT 
    from_user, 
    to_user, 
    story_id, 
    user_id, 
    url, 
    type,
    create_date,
    @row_number := CASE WHEN @prev_user_id = user_id THEN @row_number + 1 ELSE 1 END AS row_num,
    @prev_user_id := user_id AS dummy
FROM (
    SELECT DISTINCT 
        A.from_user AS from_user, 
        A.to_user AS to_user, 
        B.*
    FROM 
        follow A
    JOIN 
        story B ON A.to_user = B.user_id
    WHERE 
        A.from_user = '$user_id' AND B.is_delete = 0
    ORDER BY 
        B.user_id, B.story_id DESC
) AS ranked
ORDER BY 
    user_id, story_id DESC");


    // $twentyPost = DB::select("SELECT * FROM story 
    //     WHERE user_id = '$user_id' AND is_delete = 0 
    //     ORDER BY story_id DESC 
    //     LIMIT 20");
          DB::statement("SET @row_number = 0, @prev_user_id = ''");

        
      $twentyPost = DB::select("SELECT * FROM (
    SELECT *,
           (@row_number := IF(@user_id = user_id, @row_number + 1, 1)) AS row_num,
           (@user_id := user_id) AS dummy
    FROM story
    WHERE user_id = '$user_id' AND is_delete = 0
    ORDER BY user_id, story_id DESC
) AS ranked
WHERE row_num <= 20");


    // Combine the results of both queries
    $res = array_merge($twentyPost, $allStory);
} else {
    // If no follow count, fetch only user's stories
    // $res = DB::select("SELECT * FROM story 
    //     WHERE user_id = '$user_id' AND is_delete = 0 
    //     ORDER BY story_id DESC");
    
    $res = DB::select("
SELECT story.*
FROM story
JOIN (
    SELECT user_id, MAX(story_id) AS max_story_id
    FROM story
    WHERE user_id = '$user_id' AND is_delete = 0
    GROUP BY user_id
) AS max_stories ON story.user_id = max_stories.user_id AND story.story_id = max_stories.max_story_id
WHERE story.user_id = '$user_id' AND story.is_delete = 0
ORDER BY story.story_id DESC");
}



        $storyImageList = [];

        foreach ($res as $list) {
            $storyList = [
                'story_id' => (string)$list->story_id,
                'user_id' => (string)$list->user_id,
                'url' => $list->url,
                'type' => $list->type,
                'create_date' => $list->create_date,
            ];

            $user = DB::table('users')->where('id', $list->user_id)->first();

            if (!empty($user)) {
                $storyList['username'] = $user->username;

                $url = explode(":", $user->profile_pic);

                if ($url[0] == "https" || $url[0] == "http") {
                    $storyList['profile_pic'] = $user->profile_pic;
                } else {
                    $storyList['profile_pic'] =  url('public/images/user/'. $user->profile_pic);
                    
                    
                }
            } else {
                $storyList['profile_pic'] = "";
                $storyList['username'] = "";
            }

            $storyArr = [];

            $story = DB::table('story')->where('user_id', $list->user_id)->get();

            foreach ($story as $product) {
                $queryCount = DB::select("SELECT story.url, story.type FROM story WHERE story_id = '$product->story_id'");
                $storyRow = $queryCount[0];

                if (!empty($storyRow->url)) {
                    $storyRow->url = asset('public/assets/images/posts/' . $storyRow->url);
                    $storyRow->type = $storyRow->type;
                }

                if (!empty($storyRow->url)) {
                    $storyArr[] = $storyRow;
                }
            }

            $storyList['story_image'] = $storyArr;
            $storyImageList[] = $storyList;
        }

        if (!empty($storyImageList)) {
            $result['status'] = "1";
            $result['msg'] = "Story Found";
            $result['post'] = $storyImageList;
            return response()->json($result);
        } else {
            $result["status"] = "0";
            $result["msg"] = "Story Not Found";
            $result['post'] = $storyImageList;
            return response()->json($result);
        }
    
    
    }

    public function delete_story2(Request $request)
    {
        $result = array();
        header('Content-Type: application/json');

        $user_id = Auth::user()->token()->user_id;
        $story_id = $request->input('story_id');

        $user = Story::where('story_id', $story_id)->where('user_id', $user_id)->delete();

        if ($user) {

            $result["response_code"] = "1";
            $result["message"] = "Successfully Delete";
            $result["status"] = "success";
            return response()->json($result);
        } else {
            $result["response_code"] = "0";
            $result["message"] = "Database error";
            $result["status"] = "failure";
            return response()->json($result);
        }
    }
    
    public function delete_story(Request $request)
    {
        $likes = Story::get();
    
        foreach ($likes as $like) {
            $currentDateTime = Carbon::now();
            $createdAt = Carbon::parse($like->created_at);
    
            // Check if the difference is more than 72 hours (3 days)
            if ($currentDateTime->diffInHours($createdAt) >= 24) {
                // $like->delete();
                 Story::where('created_at', $like->created_at)->delete();
            }
        }
    
        return response()->json([
            "response_code" => "1",
            "message" => "Successfully Delete",
            "status" => "success",
        ]);
    }

}
