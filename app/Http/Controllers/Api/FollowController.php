<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\Friends_request;
use App\Models\User;
use App\Models\user_notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowController extends BaseController
{
    public function follow(Request $request)
    {

        $user_id = Auth::user()->token()->user_id;
        if ($request->to_user == "") {
            return response(['response_code' => 1, 'message' => "Enter data", 'status' => "failed"]);
        }
        $to_user = $request->to_user;
        $requested = Friends_request::where('from_user', $user_id)->where('to_user', $to_user)->count();
        if ($requested > 0) {
            Friends_request::where('from_user', $user_id)->where('to_user', $to_user)->delete();
            return response()->json([
                'message'   => "Follow",
            ]);
        } else {

            $you_follow = Follow::where('from_user', $user_id)->where('to_user', $to_user)->count();
            if ($you_follow > 0) {
                Follow::where('from_user', $user_id)->where('to_user', $to_user)->delete();
                return response()->json([
                    'message'   => "Follow",
                ]);
            } else {
                $is_private = User::findOrFail($to_user);

                $notification = array(
                    'from_user' => $user_id,
                    'to_user' => $to_user,
                    'post_id' => '',
                    'title' => 'Follow Requests',
                    'message' => $is_private->username . " requests to follow.",
                    'date' => date("d F, h:i:s A"),
                    'requests_status' => 'pending',
                );

                if ($is_private->is_Private == "private") {

                    $data = array(
                        'from_user' => $user_id,
                        'to_user' => $to_user,
                        'date' => time(),
                        'status' => 'Pending',
                    );
                    Follow::create($data);
                    $inserted = Friends_request::create($data);
                    $noti = user_notification::create($notification);
                    return response()->json([
                        'message'   => "Requested",
                    ]);
                } else {

                    $data = array(
                        'from_user' => $user_id,
                        'to_user' => $to_user,
                        'date' => time(),
                        'status' => 'follow',
                    );
                    $inserted = Follow::create($data);
                    $noti = user_notification::create($notification);
                    return response()->json([
                        'message'   => "Unfollow",
                    ]);
                }
            }
        }
    }

    public function my_followers(Request $request)
    {

        // $user_id = Auth::user()->token()->user_id;
        
        $user_id = $request->input('user_id');
        $followers = Follow::select('follow.follow_id','follow.from_user','follow.to_user','follow.friendType','follow.date','follow.status','users.username', 'users.profile_pic', 'users.id as follow_user_id')->join('users', 'users.id', '=', 'follow.from_user')->where('to_user', $user_id)->get();
        foreach ($followers as $user) {
            
            $user->follow_id = (string)$user->follow_id;
            $user->from_user = (string)$user->from_user;
            $user->to_user = (string)$user->to_user;
            $user->friendType = (string)$user->friendType;
            $user->status = (string)$user->status;
            $user->follow_user_id = (string)$user->follow_user_id;
            $user->profile_pic =  url('public/images/user/'. $user->profile_pic);
            
            
        }

        return response(['status' => 1, 'msg' => "Unlike successfull", 'follower' => $followers]);
    }

    public function my_following(Request $request)
    {

        // $user_id = Auth::user()->token()->user_id;
        $user_id = $request->input('user_id');
        
        $followers = Follow::select('follow.follow_id','follow.from_user','follow.to_user','follow.friendType','follow.date','follow.status','users.username', 'users.profile_pic', 'users.id as follow_user_id')->join('users', 'users.id', '=', 'follow.to_user')->where('from_user', $user_id)->get();
        foreach ($followers as $user) {
            
            $user->follow_id = (string)$user->follow_id;
            $user->from_user = (string)$user->from_user;
            $user->to_user = (string)$user->to_user;
            $user->friendType = (string)$user->friendType;
            $user->status = (string)$user->status;
            $user->follow_user_id = (string)$user->follow_user_id;
            
            $user->profile_pic =  url('public/images/user/'. $user->profile_pic);
        }

        return response(['status' => 1, 'msg' => "follower Found", 'follower' => $followers]);
    }

    public function follow_user(Request $request)
    {
        $user_id = Auth::user()->token()->user_id;
        $request->validate([
            'to_user' => 'required',
        ]);

        $toUserId = $request->input('to_user');
        $fromUserId = $user_id;

        $follow = new Follow();
        $follow->to_user = $toUserId;
        $follow->from_user = $fromUserId;
        $follow->date = round(microtime(true) * 1000);

        if ($follow->save()) {
            $fromUser = User::find($fromUserId);
            $toUser = User::find($toUserId);

            $post_id = 0;

            $title = "follow";
            $message = $fromUser->username . " started following you.";
            $FcmToken = User::select('device_token')->where('id', $toUserId)->first()->device_token;

            $fuser = User::select('username')->where('id', $toUserId)->first()->username;
            $tuser = User::select('username')->where('id', $fromUserId)->first()->username;

            $data = [
                "registration_ids" => array($FcmToken),
                "notification" => [
                    "title" => "Message",
                    "body" => "$tuser has sent a message.",
                    "is_type" => "follow",
                    "from_user" => $fromUser,
                    "to_user" => $toUser,
                ],
                "data" => [
                    "title" => "Message",
                    "body" => "$tuser has sent a message.",
                    'toUser' => $toUser,
                    'message' => $message,
                    'my_id' => $fromUser,
                    // 'my_secondid' => $toUser,
                    'my_secondid' => $fromUser,
                    "Message" => 'Message'
                ]
            ];
            $this->sendNotification($data);
            
             $create_date = round(microtime(true) * 1000);

            
             user_notification::create([
                    'from_user' => $user_id,
                    'to_user' => $toUserId,
                    'post_id' => '',
                    'not_type' => '0',
                    'message' => $message,
                    'title' => "Message",
                    'date' => $create_date,
                ]);


            // You need to implement the following methods in your Firebase model
            // $response = $this->firebase_model->send_user_notification($toUserId, $title, $message, "Message");
            // $this->firebase_model->save_user_notification($fromUserId, $toUserId, $title, $message, $post_id);

            return response()->json([
                'response_code' => 1,
                'message' => 'Follow added',
                'status' => 'success',
            ]);
        } else {
            return response()->json([
                'response_code' => 0,
                'message' => 'Database Error',
                'status' => 'failure',
            ]);
        }
    }
    public function unfollow_user(Request $request)
    {
        $user_id = Auth::user()->token()->user_id;
        $request->validate([
            'to_user' => 'required',
        ]);

        $toUserId = $request->input('to_user');
        // $fromUserId = $request->input('from_user');

        $fromUserId = $user_id;

        $unfollowQuery = Follow::where('to_user', $toUserId)
            ->where('from_user', $fromUserId);

        if ($unfollowQuery->delete()) {
            return response()->json([
                'response_code' => 1,
                'message' => 'Unfollow success',
                'status' => 'success',
            ]);
        } else {
            return response()->json([
                'response_code' => 0,
                'message' => 'Database Error',
                'status' => 'failure',
            ]);
        }
    }
}
