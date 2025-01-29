<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\Friends_request;
use App\Models\User;
use App\Models\Chat;
use App\Models\user_notification;
use App\Models\Notification;
use RtcTokenBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ChatController extends BaseController
{
    public function chat_api(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'to_user' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        // if (request('message') == "" || request('url') == "") {
        //     return $this->sendError(['error' => "message, url is required."]);
        // }
        $user_id = Auth::user()->token()->user_id;
        $input['from_user'] = $user_id;
        // $input['from_user'] = $request->user()->token()->user_id;
        // $input['seen'] = 0;
        $input['message'] = (request('message')) ? request('message') : "";
        $input['type'] = (request('type')) ? request('type') : "";
        $input['url'] = (request('url')) ? request('url') : "";
        
        $input['date'] = round(microtime(true) * 1000);
        // $input['thumbnail'] = (request('thumbnail')) ? request('thumbnail') : "";
        $input['time'] = (request('time')) ? request('time') : "";
        if ($request->file('url')) {
            if ($request->file('url')) {
                $file = $request->file('url');
                $filename = "chat_" . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/chat/'), $filename);
                $input['url'] = $filename;
            }
            // if ($request->file('thumbnail')) {
            //     $file = $request->file('thumbnail');
            //     $filename = "thumbnail" . uniqid() . '.' . $file->getClientOriginalExtension();
            //     $file->move(public_path('/files/chats_img'), $filename);
            //     $input['thumbnail'] = $filename;
            // }
        } else {
            $input['message'] = request('message');
        }
        $chat = Chat::create($input);
        if (!empty($chat)) {
            
            $user_id = Auth::user()->token()->user_id;
            $to_user = $request->to_user;
            $message = $request->message;
            // $user = Auth::guard('sanctum')->user();

            $fUser = User::select('fullname')->where('id', $user_id)->first()->fullname;
            $FcmToken = User::select('device_token')->where('id', $request->to_user)->first()->device_token;
            // $data = [
            //     "registration_ids" => array($FcmToken),
            //     "notification" => [
            //         "title" => "Message",
            //         "body" => "$fUser send you message.",
            //     ]
            // ];
            // $this->sendNotification($data);
            
            $fImage = User::select('profile_pic')->where('id', $user_id)->first()->profile_pic;



                $data = [
                    "registration_ids" => array($FcmToken),
                    "notification" => [
                        "title" => "Message",
                        "body" => "$fUser send message",
                        "is_type" => "message",
                        "from_user" => $user_id,
                        "to_user" => $to_user,
                    ],
                    "data" => [
                        "title" => "Message",
                        "body" => "$fUser is message now.",
                        'toUser' => $to_user,
                        'message' => $message,
                        'my_id' => $user_id,
                        'my_secondid' => $to_user,
                        'profile_image' => url('public/images/user/' . $fImage),
                        
                        'isType' => "message",
                        "Message" => 'Message'
                    ]
                ];
                $this->sendNotification($data);
                
                // print_r($data);
                // die;

            return response()->json(['success' => "true", 'message' => "Message Send successfully..!"]);
        } else {
            return response()->json(['error' => "message not send"]);
        }
    }
    
    public function message_list(request $request)
    {
        $validator = Validator::make($request->all(), ['to_user' => 'required',]);

        if ($validator->fails()) {
            //pass validator errors as errors object for ajax response
            return $this->sendError('Validation Error.', $validator->errors());
        }
        // $from_user = Auth::guard('sanctum')->user()->id;
         $from_user = Auth::user()->token()->user_id;

        $to_user = request('to_user');
        // $data = chat::where('from_user', $from_user)->where('to_user', $to_user)->orwhere('from_user', $to_user)->where('to_user', $from_user)->get();

        $user = User::select('id', 'fullname', 'profile_pic')->where('id', '=', $to_user)->get()->transform(function ($ts) {
            $ts['profile_pic'] = $ts['profile_pic'] ? url('public/images/user/' . $ts['profile_pic']) : asset('public/files/profile_pic/images.jpeg');
            return $ts;
        })->toArray();
        
        //   $message_read = $request->input('message_read');
        
          $data = array(
            "read_message" =>  "1",
        );
        
        // Updating rows in the Chat table based on the conditions
        Chat::where('to_user', $from_user)
            ->where('from_user', $to_user)
            // ->where('message_read', "1")
            ->update($data);

        // $data = chat::where([['from_user', $from_user], ['to_user', $to_user]])->orWhere([['from_user', $to_user], ['to_user', $from_user], ['msg_del_from', 0], ['msg_del_to', 0]])->get()->transform(function ($ts) {
        
    //      $data = chat::where(function ($query) use ($from_user, $to_user) {
    //     $query->where('from_user', $from_user)->where('to_user', $to_user)->where('snap_from', '!=' , $from_user)
    //         ->orWhere('from_user', $to_user)->where('to_user', $from_user)->where('snap_to', '!=' , $from_user);
    // })
    
    
    //  $data = Chat::where(function ($query) use ($from_user, $to_user) {
    //     $query->where('from_user', $from_user)->where('to_user', $to_user)
    //         ->orWhere('from_user', $to_user)->where('to_user', $from_user);
    // })
    // ->get()
    $data = Chat::where(function ($query) use ($from_user, $to_user) {
        $query->where('from_user', $from_user)->where('to_user', $to_user)
            ->orWhere('from_user', $to_user)->where('to_user', $from_user);
    })
    ->whereIn('type', ['image', 'text']) // type image & text lava mate
    ->orderBy('created_at', 'desc') // Order by created_at column in descending order
    ->get()
        ->transform(function ($ts) use ($from_user) {
            // with('post', 'story', 'user')-> get with relation ship..
            $ts['id'] = (string)$ts['id'];
            // $ts['my_id'] = (string)$ts['from_user'];
            $ts['to_user'] = (string)$ts['to_user'];
            
            //  $from_user = Auth::guard('sanctum')->user()->id;
             $from_user = Auth::user()->token()->user_id;
             
            //   if ($otherUser = ($ts->from_user == $from_user)) {
            //         $ts['my_id'] = (string)$from_user;
            //         $ts['second_id'] = (string)$ts->to_user;
            //     }else{
            //          $ts['my_id'] = (string)$from_user;
            //         $ts['second_id'] = (string)$ts->from_user;
            //     }
                
              if($ts->from_user == $from_user){
            // if(chat::where('from_user', '==', $user_id )){
            // if($row->id == $user_id ){
            
                
                 $ts['type'] = $ts['type'];
            
            }else{
                
               $ts['type'] = $ts['type'];
               
            }
            
             $ts['send_post'] =  $ts['send_post'] ?? "";
             $ts['send_story'] =  $ts['send_story'] ?? "";
            
            
            // if ($otherUser = ($ts->from_user == $from_user)) {
            //      $ts['msg_delivered'] = (string)$ts->msg_delivered;
            //  }else{
            //      $ts['msg_delivered'] = "";
            //  }
             
            //   if ($otherUser = ($ts->from_user == $from_user)) {
            //      $ts['msg_seen'] = (string)$ts->msg_seen;
            //  }else{
            //      $ts['msg_seen'] = "";
            //  }
            
            // if($users){
                
            //      $chat_list['msg_delivered'] = $last_message_query->msg_delivered;
            // }else{
            //     $chat_list['msg_delivered'] = "";
                
            // }
            
            
           

            
            
            // $ts['type'] = $ts['type'];
            $ts['media'] =  "";
            $ts['user_name'] = $ts['fullname'] ?? "";
            $ts['user_pic'] = "";
            $ts['is_verify'] = "";
            // $ts['seen'] = (string)$ts['seen'];
            // $ts['time'] = (string)$ts['time'] ? (string)$ts['time'] : "";

            // $ts['video_time'] = (string)$ts['time'] ? (string)$ts['time'] : "";
            // unset($ts['time']); // Remove the old "time" key
            // $ts['is_selected'] = "0";
            unset($ts['read_message']);
            // unset($ts['created_at']);
            unset($ts['updated_at']);
            unset($ts['timestamp']);
            // unset($ts['snap_from']);
            // unset($ts['snap_to']);
            
            //  $to_user = request('to_user');
            
       
           

            // $story = story::where('id', $ts->story_id)->get();
            // $ts['story'] = $story ? $story : "";

            $ts['profile_pic'] = $ts['profile_pic'] ? url('public/images/user/'. $ts['profile_pic']) : "";
            $ts['url'] = $ts['url'] ? url('public/images/chat/' . $ts['url']) : "";
            // $ts['thumbnail'] = ($ts['thumbnail']) ? url('public/files/chats_img/' . $ts['thumbnail']) : "";
            $ts['message'] = ($ts['message']) ? $ts['message'] : "";
            // $ts['call_type'] = $ts['call_type'];
            // $ts['msg_delivered'] = $ts['msg_delivered'];
            // $ts['msg_seen'] = $ts['msg_seen'];
            // $ts['call_type'] = $ts->call_type  ? "call_type" : "";

            // $ts['type'] = $ts->url ? "files" : $ts['type'];


            $carbon = Carbon::createFromFormat('Y-m-d H:i:s', $ts['created_at']);
            $ts['chat_time'] = $carbon->format('h:i A');
            return $ts;
        })->toArray();


        // $chat_accept = chat_accept::where('from_user', $from_user)->where('to_user', $to_user)->where('is_status', '1')->exists();
        // // $chat_accept = "1";
        // // } else {
        // //     $chat_accept = "0";

        // $block_user = block_user::where('user_id', $from_user)->where('blocked_user_id', $to_user)->exists();

        // $block_user_me = block_user::where('user_id', $to_user)->where('blocked_user_id', $from_user)->exists();
        
        //  $chat_type = chat_status::where('to_user', $from_user)->where('from_user', $to_user)->first();
        
        // if($chat_type){
            
        //      $is_typing = $chat_type->is_type;
        // }else{
            
        //     $is_typing = "0";
        // }
        
        
        // $chat_online = User::where('id', $to_user)->first();
        
        //  $time = $chat_online->updated_at->diffForHumans();
        
        // if($chat_online){
            
        //      $ts['is_online'] = (string)$chat_online->is_status;
        // }
        
      
        
       
        
        

        // if (ProfileBlock::where('blockedByUserId', $to_user)->where('blockedUserId', $from_user)->exists()) {
        //     $result['is_meblocked'] = "1";
        // } else {
        //     $result['is_meblocked'] = "0";
        // }
        // }

        $response = [
            'success' => "true",
            'message' => "Message list.",
            // 'chat_accept' => $chat_accept ? true : false,
            // 'is_block'  => $block_user ? "1" : "0",
            // 'is_meblocked' => $block_user_me ? "1" : "0",
            // 'is_typing' => $is_typing,
            // 'is_online' => (string)$chat_online->is_status,
            // 'last_seen' => $time,
            'chat' => $data

        ];
        return response()->json($response, 200);
        // if ($data) {
        //     chat::where('from_user', $to_user)->where('to_user', $from_user)->update(['seen' => 1]);
        //     return $this->sendResponse($data, "message list");
        //     // return response()->json(array('message' => 'true', 'user' => $user, 'data' => $data));
        // } else {
        //     return $this->sendError("No message found..!");
        //     // return response()->json(array('message' => 'false'));
        // }
    }
    
     public function user_chat_list(Request $request)
    {
        // $user_id = $request->user()->token()->user_id;

        // $user_id = Auth::guard('sanctum')->user()->id;
        $user_id = Auth::user()->token()->user_id;

        $data = User::select('id')->whereIn('id', function ($q) use ($user_id) {
            $q->select('to_user')->from('chats')->where('from_user', $user_id);
        })->orwhereIn('id', function ($q) use ($user_id) {
            $q->select('from_user')->from('chats')->where('to_user', $user_id);
        })->get();

        $data_Ar = array();
        foreach ($data as $row) {
            $last_message_query = Chat::where(function ($q) use ($user_id, $row) {
                $q->where('from_user', $user_id)
                    ->where('to_user', $row->id);
            })->orwhere(function ($q) use ($user_id, $row) {
                $q->where('from_user', $row->id)
                    ->where('to_user', $user_id);
            })->orderBy('created_at', 'DESC')->first();

            $chat_list['id'] = (string)$last_message_query->id;
            // if($user_id){
            $chat_list['my_id'] = (string)$user_id;
            // }else{
            $chat_list['second_id'] = (string)$row->id;
            // }
            // $chat_list['second_id'] = (string)$last_message_query->to_user;
            if ($last_message_query->url) {
                $chat_list['last_message'] = "files";
            } elseif ($last_message_query->message) {
                $chat_list['last_message'] = "messages";
            } elseif ($last_message_query->story_id) {
                $chat_list['last_message'] = "story";
            } elseif ($last_message_query->post_id) {
                $chat_list['last_message'] = "posts";
            }
            $chat_list['message'] = $last_message_query->message ?? "";
            $chat_list['url'] = $last_message_query->url ? url('public/images/chat/' . $last_message_query->url) : "";
            $chat_list['type'] = $last_message_query->type ? $last_message_query->type : "";
            $user = $last_message_query->to_user == $user_id ?  user::where('id', $last_message_query->from_user)->first() : user::where('id', $last_message_query->to_user)->first();
            $chat_list['user_id'] = (string)$user->id;
            $chat_list['username'] = $user->username ? $user->username : "";
            $chat_list['fullname'] = $user->fullname ? $user->fullname : "";
            $chat_list['profile_pic'] = ($user->profile_pic) ? url('public/images/user/'. $user->profile_pic) :  "";
            $chat_list['is_online'] = (string)$user->is_online;
            $chat_list['last_seen'] = (string)$user->updated_at;
            // $chat_list['is_verify'] = (string)$user->is_verify;
            $chat_list['date'] = $last_message_query->date ? $last_message_query->date : "";
            $chat_list['time'] = $last_message_query->created_at->diffForHumans();

            // $block = lock_user::where('user_id', $user_id)->where('locked_user_id', $user->id)->first();
        $chat_list['unread_message'] = (string)Chat::where('to_user', $user_id)->where('from_user', $row->id)->where('read_message', 0)->count();

            // if($last_message_query->to_user == $user_id){
            // $chat_list['unread_message'] = (string)chat::where('to_user', $last_message_query->to_user)->where('read_message', 0)->count();
            // }else{
            //       $chat_list['unread_message'] = "0";
            // }
            // $chat_list['chat_time'] = $this->created_at->diffForHumans();
            // $chat_list['is_blocked'] = block_user::where('user_id', $user_id)->where('blocked_user_id', $user->id)->orwhere('user_id', $user->id)->where('blocked_user_id', $user_id)->exists() ? "1" : "0";
            // $chat_list['is_meblocked'] = block_user::where('user_id', $user->id)->where('blocked_user_id', $user_id)->orwhere('user_id', $user->id)->where('blocked_user_id', $user_id)->exists() ? "1" : "0";
            // if ($block) {
            //     $chat_list['password'] = $block->password ? $block->password : "";
            // } else {
            //     $chat_list['password'] = "";
            // }
            // $chat_list['is_lock'] = lock_user::where('user_id', $user_id)->where('locked_user_id', $user->id)->exists() ? "1" : "0";

            // $chat_list['chat_accept'] = chat_accept::where('from_user', $user_id)->where('to_user', $row->id)->where('is_status', '1')->exists()  ? true : false;

            array_push($data_Ar, $chat_list);
        }

        $chat = array();
        foreach ($data_Ar as $key => $row) {
            $chat[$key] = $row['id'];
        }
        array_multisort($chat, SORT_DESC, $data_Ar);


        // if (chat_accept::where('from_user', $user_id)->where('to_user', $to_user)->exists()) {
        //         $result['is_block'] = "1";
        //     } else {
        //         $result['is_block'] = "0";
        //     }

        $response = [
            'success' => "true",
            'message' => "Message list.",
            'chat_list' => $data_Ar
        ];
        return response()->json($response, 200);
        // if ($data_Ar) {
        //     return response()->json(array('message' => 'true', 'data' => $data_Ar));
        // } else {
        //     return response()->json(array('message' => 'false'));
        // }
    }
    
    public function videoCall(Request $request)
    {
         $fromUser = Auth::user()->token()->user_id;
        // $fromUser = $request->input('from_user');
        $toUser = $request->input('to_user');
        $appID = "8784d381ea7e48b292531332e3946c7c";
        $appCertificate = "fe18db70fbee49fa9ee22dc20f30c494";
        $channelName = $toUser;
        $uid = "";
        $uidStr = "";
        $role = RtcTokenBuilder::RoleAttendee;
        $expireTimeInSeconds = 3600;
        $currentTimestamp = now()->timestamp;
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        $token = RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);

        $data = [
            'from_user' => $fromUser,
            'to_user' => $toUser,
            'channelName' => $channelName,
            'call_type' => "video_call",
        ];

        $fuName = User::where('id', $fromUser)->select('id', 'fullname')->first();
        $tuName = User::where('id', $toUser)->select('id', 'fullname')->first();
        $msg = "";

        // $getMobNo = Chat::where(function ($query) use ($fromUser, $toUser) {
        //     $query->where('from_user', $fromUser)
        //         ->where('to_user', $toUser);
        // })->orWhere(function ($query) use ($fromUser, $toUser) {
        //     $query->where('to_user', $fromUser)
        //         ->where('from_user', $toUser);
        // })->get();

        // $res = $getMobNo->count();

        // if ($res == 0) {
        //     $str = Chat::orderByDesc('id')->limit(1)->select('id')->first();
        //     $idata['chat_id'] = $str->id + 1;
        // } else {
        //     $oldChatId = Chat::where(function ($query) use ($fuName, $toUser) {
        //         $query->where('from_user', $fuName->id)
        //             ->where('to_user', $toUser);
        //     })->orWhere(function ($query) use ($fuName, $toUser) {
        //         $query->where('to_user', $fuName->id)
        //             ->where('from_user', $toUser);
        //     })->limit(1)->pluck('chat_id');

        //     $idata['chat_id'] = $oldChatId->first();
        //     $chId = $idata['chat_id'];
        // }

        // $msg = $fuName->name . " videocall " . $tuName->name . "..!";
        $time = now()->format('H:i');
        $times = str($time);
        // $msg = $fuName->name . "Call Receive " . $tuName->name . "..!";
        // $msg = $fuName->name . " Not Answer "  . "at $times";

        // $idata =  Chat::create([
        //     'from_user' => $request->input('from_user'),
        //     'to_user' => $request->input('to_user'),
        //     'chat_id' => $chId ?? null,
        //     'message' => $msg,
        //     'type' => "call",
        //     'date' => now()->format('Y-m-d'),
        //     'time' => now()->format('H:i:s'),
        //     'call_type' => "1",
        //     'call_done' => "2",
        // ]);

        // $reg = $this->front_model->new_chat($idata);

        if (Notification::create($data)) {
            $callId = DB::getPdo()->lastInsertId();
            User::where('id', $fromUser)->update(['video_token' => $token]);

            // $fromUser = $request->input('from_user');
            $toUser = $request->input('to_user');
            $channel = $channelName;
            $call = "1";
            $isType = "video_call";

            $user = User::find($fromUser);

            $title = "Video call";
            $message = "calling";
            // $name = $user->username;
            $name = $user->fullname;
            $mobile = $user->phone;

            if ($user->profile_pic != "") {
                $url = explode(":", $user->profile_pic);
                if ($url[0] == "https" || $url[0] == "http") {
                    $profile_image = $user->profile_pic;
                } else {
                    $profile_image = url('/public/images/user/' . $user->profile_pic);
                }
            } else {
                $profile_image = "";
            }

            // $notified = array(
            //   'toUser' => $toUser,
            //   'title' => $title,
            //   'message' => $message,
            //   'token' => $token,
            //   'name' => $name,
            //   'profile_image' => $profile_image,
            //   'mobile' => $mobile,
            //   'channel' => $channel,
            //   'call' => '1',
            //   'isType' => 'video_call',
            //     "Message" => 'calling'

            // );
            // $this->sendNotification($notified);
            
           

            // $fUser = User::select('username')->where('id', $fromUser)->first()->username;
            
            // $tUser = User::select('username')->where('id', $toUser)->first()->username;
            
            $fUser = User::select('fullname')->where('id', $fromUser)->first()->fullname;
            
            $tUser = User::select('fullname')->where('id', $toUser)->first()->fullname;
            $fImage = User::select('profile_pic')->where('id', $fromUser)->first()->profile_pic;
            $tImage = User::select('profile_pic')->where('id', $toUser)->first()->profile_pic;
            $FcmToken = User::select('device_token')->where('id', $toUser)->first()->device_token;
            
            

            $data = [
                "registration_ids" => array($FcmToken),
                "notification" => [
                    "title" => "Video call",
                    "body" => "$fUser is calling now.",
                    'toUser' => (string)$toUser,
                    'message' => $message,
                    'token' => $token,
                    'caller_name' => $fUser ?? "",
                    'receiver_name' => $tUser ?? "",
                    'caller_profile_pic' => $fImage ? url('public/images/user/' . $fImage) : "",
                    'receiver_profile_pic' => $tImage ? url('public/images/user/' . $tImage) : "",
                    'mobile' => $mobile ?? "",
                    'channel' => $channel,
                    'call' => '1',
                    'isType' => 'video_call',
                    "call_id" => $callId,
                    // "Message" => 'calling'
                ],
                "data" => [
                    "title" => "Video call",
                    "body" => "$fUser is calling now.",
                    'toUser' => (string)$toUser,
                    'message' => $message,
                    'token' => $token,
                    'caller_name' => $fUser ?? "",
                    'receiver_name' => $tUser ?? "",
                    'caller_profile_pic' => $fImage ? url('public/images/user/' . $fImage) : "",
                    'receiver_profile_pic' => $tImage ? url('public/images/user/' . $tImage) : "",
                    'mobile' => $mobile ?? "",
                    'channel' => $channel,
                    'call' => '1',
                    'my_id' => (string)$fromUser,
                    'my_secondid' => (string)$toUser,
                    'isType' => 'video_call',
                    "call_id" => $callId,
                    // "Message" => 'calling'
                ]
            ];
            $this->sendNotification($data);

            // dd($data);
            // die;

            $result = [
                "response_code" => "1",
                "message" => "Video Call Connected",
                "token" => $token,
                "call_id" => $callId,
                "caller_name" => $fUser ?? "",
                "receiver_name" => $tUser ?? "",
                "caller_profile_pic" => $fImage ? url('public/images/user/' . $fImage) : "",
                "receiver_profile_pic" => $tImage ? url('public/images/user/' . $tImage) : "",
                'caller_id' => (string)$fromUser,
                'receiver_id' => (string)$toUser,
                // "caller_profile_pic" => $fImage ?? "",
                // "receiver_profile_pic" => $tImage ?? "",
                "status" => "success",
            ];

            return response()->json($result);
        }
    }
    
    public function audioCall(Request $request)
    {
        // $fromUser = $request->input('from_user');
        $fromUser = Auth::user()->token()->user_id;
        $toUser = $request->input('to_user');
        $appID = "8784d381ea7e48b292531332e3946c7c";
        $appCertificate = "fe18db70fbee49fa9ee22dc20f30c494";
        $channelName = $toUser;
        $uid = "";
        $uidStr = "";
        $role = RtcTokenBuilder::RoleAttendee;
        $expireTimeInSeconds = 3600;
        $currentTimestamp = now()->timestamp;
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        $token = RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, $uid, $role, $privilegeExpiredTs);
        
        
        // print_r($token);
        // die;

        $data = [
            'from_user' => $fromUser,
            'to_user' => $toUser,
            'channelName' => $channelName,
            'call_type' => "audio_call",
        ];

        $fuName = User::where('id', $fromUser)->select('id', 'fullname')->first();
        $tuName = User::where('id', $toUser)->select('id', 'fullname')->first();
        $msg = "";

        // $getMobNo = Chat::where(function ($query) use ($fromUser, $toUser) {
        //     $query->where('from_user', $fromUser)
        //         ->where('to_user', $toUser);
        // })->orWhere(function ($query) use ($fromUser, $toUser) {
        //     $query->where('to_user', $fromUser)
        //         ->where('from_user', $toUser);
        // })->get();

        // $res = $getMobNo->count();

        // if ($res == 0) {
        //     $str = Chat::orderByDesc('id')->limit(1)->select('id')->first();
        //     $idata['chat_id'] = $str->id + 1;
        // } else {
        //     $oldChatId = Chat::where(function ($query) use ($fuName, $toUser) {
        //         $query->where('from_user', $fuName->id)
        //             ->where('to_user', $toUser);
        //     })->orWhere(function ($query) use ($fuName, $toUser) {
        //         $query->where('to_user', $fuName->id)
        //             ->where('from_user', $toUser);
        //     })->limit(1)->pluck('chat_id');

        //     $idata['chat_id'] = $oldChatId->first();
        //     $chId = $idata['chat_id'];
        // }

        // $msg = $fuName->name . " videocall " . $tuName->name . "..!";
        $time = now()->format('H:i');
        $times = str($time);
        // $msg = $fuName->name . "Call Receive " . $tuName->name . "..!";
        // $msg = $fuName->name . " Not Answer "  . "at $times";

        // $idata =  Chat::create([
        //     'from_user' => $request->input('from_user'),
        //     'to_user' => $request->input('to_user'),
        //     'chat_id' => $chId ?? null,
        //     'message' => $msg,
        //     'type' => "call",
        //     'date' => now()->format('Y-m-d'),
        //     'time' => now()->format('H:i:s'),
        //     'call_type' => "1",
        //     'call_done' => "2",
        // ]);

        // $reg = $this->front_model->new_chat($idata);

        if (Notification::create($data)) {
            $callId = DB::getPdo()->lastInsertId();
            User::where('id', $fromUser)->update(['video_token' => $token]);

            // $fromUser = $request->input('from_user');
            $toUser = $request->input('to_user');
            $channel = $channelName;
            $call = "1";
            $isType = "audio_call";

            $user = User::find($fromUser);

            $title = "Audio call";
            $message = "calling";
            $name = $user->fullname;
            $mobile = $user->phone;

            if ($user->profile_pic != "") {
                $url = explode(":", $user->profile_pic);
                if ($url[0] == "https" || $url[0] == "http") {
                    $profile_image = $user->profile_pic;
                } else {
                    $profile_image = url('/public/images/user/' . $user->profile_pic);
                }
            } else {
                $profile_image = "";
            }

            // $notified = array(
            //   'toUser' => $toUser,
            //   'title' => $title,
            //   'message' => $message,
            //   'token' => $token,
            //   'name' => $name,
            //   'profile_image' => $profile_image,
            //   'mobile' => $mobile,
            //   'channel' => $channel,
            //   'call' => '1',
            //   'isType' => 'video_call',
            //     "Message" => 'calling'

            // );
            // $this->sendNotification($notified);

            // $fUser = User::select('username')->where('id', $fromUser)->first()->username;
            // $tUser = User::select('username')->where('id', $toUser)->first()->username;
            
            $fUser = User::select('fullname')->where('id', $fromUser)->first()->fullname;
            
            $tUser = User::select('fullname')->where('id', $toUser)->first()->fullname;
            
            $fImage = User::select('profile_pic')->where('id', $fromUser)->first()->profile_pic;
            $tImage = User::select('profile_pic')->where('id', $toUser)->first()->profile_pic;
            $FcmToken = User::select('device_token')->where('id', $toUser)->first()->device_token;

            $data = [
                "registration_ids" => array($FcmToken),
                "notification" => [
                    "title" => "Audio call",
                    "body" => "$fUser is calling now.",
                    'toUser' => (string)$toUser,
                    'message' => $message,
                    'token' => $token,
                    'caller_name' => $fUser ?? "",
                    'receiver_name' => $tUser ?? "",
                    'caller_profile_pic' => $fImage ? url('public/images/user/' . $fImage) : "",
                    'receiver_profile_pic' => $tImage ? url('public/images/user/' . $tImage) : "",
                    'mobile' => $mobile ?? "",
                    'channel' => $channel,
                    'call' => '1',
                    'isType' => 'audio_call',
                    "call_id" => $callId,
                    // "Message" => 'calling'
                ],
                "data" => [
                    "title" => "Audio call",
                    "body" => "$fUser is calling now.",
                    'toUser' => (string)$toUser,
                    'message' => $message,
                    'token' => $token,
                    'caller_name' => $fUser ?? "",
                    'receiver_name' => $tUser ?? "",
                    'caller_profile_pic' => $fImage ? url('public/images/user/' . $fImage) : "",
                    'receiver_profile_pic' => $tImage ? url('public/images/user/' . $tImage) : "",
                    'mobile' => $mobile ?? "",
                    'channel' => $channel,
                    'call' => '1',
                    'my_id' => (string)$fromUser,
                    'my_secondid' => (string)$toUser,
                    'isType' => 'audio_call',
                    "call_id" => $callId,
                    // "Message" => 'calling'
                ]
            ];
            $this->sendNotification($data);

            // print_r($data);
            // die;

            $result = [
                "response_code" => "1",
                "message" => "Audio Call Connected",
                "token" => $token,
                "call_id" => $callId,
                "caller_name" => $fUser ?? "",
                "receiver_name" => $tUser ?? "",
                "caller_profile_pic" => $fImage ? url('public/images/user/' . $fImage) : "",
                "receiver_profile_pic" => $tImage ? url('public/images/user/' . $tImage) : "",
                'caller_id' => (string)$fromUser,
                'receiver_id' => (string)$toUser,
                "status" => "success",
            ];

            return response()->json($result);
        }
    }
    
    public function call_cut_from_user_01_04(Request $request)
    {
    // $fromUser = $request->input('from_user');
    $fromUser = Auth::user()->token()->user_id;
    $toUser = $request->input('to_user');

    $user = User::find($fromUser);

    $title = "Call Decline";
    $message = "Missed call .";

    $data = [
      'from_user' => $fromUser,
      'to_user' => $toUser,
      'call_type' => "video_call",
      'message' =>  'missed call'
    ];

    $fuName = User::where('id', $fromUser)->select('id', 'fullname')->first();
    $tuName = User::where('id', $toUser)->select('id', 'fullname')->first();
    $msg = "";

    // $getMobNo = Chat::where(function ($query) use ($fromUser, $toUser) {
    //   $query->where('from_user', $fromUser)
    //     ->where('to_user', $toUser);
    // })->orWhere(function ($query) use ($fromUser, $toUser) {
    //   $query->where('to_user', $fromUser)
    //     ->where('from_user', $toUser);
    // })->get();

    // $res = $getMobNo->count();

    // if ($res == 0) {
    //   $str = Chat::orderByDesc('id')->limit(1)->select('id')->first();
    //   $idata['chat_id'] = $str->id + 1;
    // } else {
    //   $oldChatId = Chat::where(function ($query) use ($fuName, $toUser) {
    //     $query->where('from_user', $fuName->id)
    //       ->where('to_user', $toUser);
    //   })->orWhere(function ($query) use ($fuName, $toUser) {
    //     $query->where('to_user', $fuName->id)
    //       ->where('from_user', $toUser);
    //   })->limit(1)->pluck('chat_id');

    //   $idata['chat_id'] = $oldChatId->first();
    //   $chId = $idata['chat_id'];
    // }

    $usTime = Carbon::now('America/New_York');


    $indiaTimeFormatted = $usTime->format('Y-m-d H:i:s');

    $time = $usTime->format('H:i');

    // $time = now()->format('H:i');
    $times = str($time);
    // $msg = $fuName->name . "Call Receive " . $tuName->name . "..!";
    // $msg = "Missed call at";
    // $msg = "Missed call at $time";
    
    $utcTime = Carbon::now();

// Format the time in the desired format
$formattedTime = $utcTime->format('Y-m-d H:i:s');

// Extract the hour and minute parts
$time = $utcTime->format('H:i');

 $msg = "Missed call at $time";

    // $time = now()->format('H:i');
    // $times = str($time);
    // // $msg = $fuName->name . "Call Receive " . $tuName->name . "..!";
    // $msg = " Missed call "  . "at $times";
    // // $msg = $fuName->username . " Missed call " . $tuName->name . "..!";
    
       $date = round(microtime(true) * 1000);

    $idata =  Chat::create([
      'from_user' => $fromUser,
      'to_user' => $request->input('to_user'),
      'message' => $msg,
      'type' => "call",
      'date' => $date,
      'time' => $indiaTimeFormatted,
    ]);

    // $reg = $this->front_model->new_chat($idata);

    if (Notification::create($data)) {
      $callId = DB::getPdo()->lastInsertId();

      $fromUser = $fromUser;
      $toUser = $request->input('to_user');
      $call = "1";
      $isType = "video_call";

      $user = User::find($fromUser);

      $title = "call_not_recived";
      $message = "calling.";
      $name = $user->fullname;
      $mobile = $user->phone;
      
      
            if ($user->profile_pic != "") {
                $url = explode(":", $user->profile_pic);
                if ($url[0] == "https" || $url[0] == "http") {
                    $profile_image = $user->profile_pic;
                } else {
                    $profile_image = url('/public/images/user/' . $user->profile_pic);
                }
            } else {
                $profile_image = "";
            }

    //   $fUser = User::select('username')->where('id', $fromUser)->first()->username;
    //   $fImage = User::select('profile_pic')->where('id', $fromUser)->first()->profile_pic;
    //   $FcmToken = User::select('device_token')->where('id', $toUser)->first()->device_token;
      
            $fUser = User::select('fullname')->where('id', $fromUser)->first()->fullname;
            $tUser = User::select('fullname')->where('id', $toUser)->first()->fullname;
            $fImage = User::select('profile_pic')->where('id', $fromUser)->first()->profile_pic;
            $tImage = User::select('profile_pic')->where('id', $toUser)->first()->profile_pic;
            $FcmToken = User::select('device_token')->where('id', $toUser)->first()->device_token;

      $data = [
        "registration_ids" => array($FcmToken),
        "notification" => [
          "title" => "Call Decline",
          "body" => "You're receiving a call from $fUser.",
          'toUser' => $toUser,
          'message' => $message,
          'caller_name' => $fUser ?? "",
          'receiver_name' => $tUser ?? "",
          'caller_profile_pic' => $fImage ? url('public/images/user/' . $fImage) : "",
         'receiver_profile_pic' => $tImage ? url('public/images/user/' . $tImage) : "",
          'mobile' => $mobile,
          // 'channel' => $channel,
          'call' => '1',
          'isType' => 'video_call',
          "Message" => 'calling'
        ],
        "data" => [
          "title" => "Call Decline",
          "body" => "You're receiving a call from $fUser.",
          'toUser' => $toUser,
          'message' => $message,
          'caller_name' => $fUser ?? "",
          'receiver_name' => $tUser ?? "",
          'caller_profile_pic' => $fImage ? url('public/images/user/' . $fImage) : "",
          'receiver_profile_pic' => $tImage ? url('public/images/user/' . $tImage) : "",
          'mobile' => $mobile,
          // 'channel' => $channel,
          'call' => '1',
          'my_id' => $toUser,
          'my_secondid' => $fromUser,
          'isType' => 'video_call',
          "Message" => 'calling'
        ]
      ];
      $this->sendNotification($data);

      // print_r($data);
      // die;

      $result = [
        "response_code" => "1",
        "message" => "Video Call Connected",
        // "token" => $token,
        "call_id" => $callId,
        "status" => "success",
      ];

      return response()->json($result);
    }
  }
  
    public function call_cut_from_user(Request $request)
    {
    // $fromUser = $request->input('from_user');
    $fromUser = Auth::user()->token()->user_id;
    $toUser = $request->input('to_user');
    $type = $request->input('type');
    $call_id = $request->input('call_id');

    $user = User::find($fromUser);

    $title = "Call Decline";
    $message = "Missed call .";

    // $data = [
    //   'from_user' => $fromUser,
    //   'to_user' => $toUser,
    //   'call_type' => "video_call",
    //   'message' =>  'missed call'
    // ];
    
     $data = [
            // 'from_user' => $fromUser,
            // 'to_user' => $toUser,
            // 'call_type' => "video_call",
            'call_type' => $type,
            'message' =>  'missed call'
        ];
        
     $datas = Notification::where('not_id', $request->call_id)
            ->update($data);

    $fuName = User::where('id', $fromUser)->select('id', 'fullname')->first();
    $tuName = User::where('id', $toUser)->select('id', 'fullname')->first();
    $msg = "";

    // $getMobNo = Chat::where(function ($query) use ($fromUser, $toUser) {
    //   $query->where('from_user', $fromUser)
    //     ->where('to_user', $toUser);
    // })->orWhere(function ($query) use ($fromUser, $toUser) {
    //   $query->where('to_user', $fromUser)
    //     ->where('from_user', $toUser);
    // })->get();

    // $res = $getMobNo->count();

    // if ($res == 0) {
    //   $str = Chat::orderByDesc('id')->limit(1)->select('id')->first();
    //   $idata['chat_id'] = $str->id + 1;
    // } else {
    //   $oldChatId = Chat::where(function ($query) use ($fuName, $toUser) {
    //     $query->where('from_user', $fuName->id)
    //       ->where('to_user', $toUser);
    //   })->orWhere(function ($query) use ($fuName, $toUser) {
    //     $query->where('to_user', $fuName->id)
    //       ->where('from_user', $toUser);
    //   })->limit(1)->pluck('chat_id');

    //   $idata['chat_id'] = $oldChatId->first();
    //   $chId = $idata['chat_id'];
    // }

    $usTime = Carbon::now('America/New_York');


    $indiaTimeFormatted = $usTime->format('Y-m-d H:i:s');

    $time = $usTime->format('H:i');

    // $time = now()->format('H:i');
    $times = str($time);
    // $msg = $fuName->name . "Call Receive " . $tuName->name . "..!";
    // $msg = "Missed call at";
    // $msg = "Missed call at $time";
    
    $utcTime = Carbon::now();

// Format the time in the desired format
$formattedTime = $utcTime->format('Y-m-d H:i:s');

// Extract the hour and minute parts
$time = $utcTime->format('H:i');

 $msg = "Missed call at $time";

    // $time = now()->format('H:i');
    // $times = str($time);
    // // $msg = $fuName->name . "Call Receive " . $tuName->name . "..!";
    // $msg = " Missed call "  . "at $times";
    // // $msg = $fuName->username . " Missed call " . $tuName->name . "..!";
    
       $date = round(microtime(true) * 1000);

    $idata =  Chat::create([
      'from_user' => $fromUser,
      'to_user' => $request->input('to_user'),
      'message' => $msg,
      'type' => "call",
      'date' => $date,
      'time' => $indiaTimeFormatted,
    ]);

    // $reg = $this->front_model->new_chat($idata);

    // if (Notification::create($data)) {
    if ($datas) {
      $callId = DB::getPdo()->lastInsertId();

      $fromUser = $fromUser;
      $toUser = $request->input('to_user');
      $call = "1";
    //   $isType = "video_call";
     $isType = $request->input('type');

      $user = User::find($fromUser);

      $title = "call_not_recived";
      $message = "calling.";
      $name = $user->fullname;
      $mobile = $user->phone;
      
      
            if ($user->profile_pic != "") {
                $url = explode(":", $user->profile_pic);
                if ($url[0] == "https" || $url[0] == "http") {
                    $profile_image = $user->profile_pic;
                } else {
                    $profile_image = url('/public/images/user/' . $user->profile_pic);
                }
            } else {
                $profile_image = "";
            }

    //   $fUser = User::select('username')->where('id', $fromUser)->first()->username;
    //   $fImage = User::select('profile_pic')->where('id', $fromUser)->first()->profile_pic;
    //   $FcmToken = User::select('device_token')->where('id', $toUser)->first()->device_token;
      
            $fUser = User::select('fullname')->where('id', $fromUser)->first()->fullname;
            $tUser = User::select('fullname')->where('id', $toUser)->first()->fullname;
            $fImage = User::select('profile_pic')->where('id', $fromUser)->first()->profile_pic;
            $tImage = User::select('profile_pic')->where('id', $toUser)->first()->profile_pic;
            $FcmToken = User::select('device_token')->where('id', $toUser)->first()->device_token;

      $data = [
        "registration_ids" => array($FcmToken),
        "notification" => [
          "title" => "Call Decline",
          "body" => "You're receiving a call from $fUser.",
          'toUser' => $toUser,
          'message' => $message,
          'caller_name' => $fUser ?? "",
          'receiver_name' => $tUser ?? "",
          'caller_profile_pic' => $fImage ? url('public/images/user/' . $fImage) : "",
         'receiver_profile_pic' => $tImage ? url('public/images/user/' . $tImage) : "",
          'mobile' => $mobile,
          // 'channel' => $channel,
          'call' => '1',
        //   'isType' => 'video_call',
         'isType' => $isType,
          "Message" => 'calling'
        ],
        "data" => [
          "title" => "Call Decline",
          "body" => "You're receiving a call from $fUser.",
          'toUser' => $toUser,
          'message' => $message,
          'caller_name' => $fUser ?? "",
          'receiver_name' => $tUser ?? "",
          'caller_profile_pic' => $fImage ? url('public/images/user/' . $fImage) : "",
          'receiver_profile_pic' => $tImage ? url('public/images/user/' . $tImage) : "",
          'mobile' => $mobile,
          // 'channel' => $channel,
          'call' => '1',
          'my_id' => $toUser,
          'my_secondid' => $fromUser,
        //   'isType' => 'video_call',
          'isType' => $isType,
          "Message" => 'calling'
        ]
      ];
      $this->sendNotification($data);

      // print_r($data);
      // die;

      $result = [
        "response_code" => "1",
        "message" => "Video Call Connected",
        // "token" => $token,
        "call_id" => $callId,
        "status" => "success",
      ];

      return response()->json($result);
    }
  }
  
    public function cut_call_01_04(Request $request)
  {
    // $fromUser = $request->input('from_user');
    $fromUser = Auth::user()->token()->user_id;
    $toUser = $request->input('to_user');

    $user = User::find($fromUser);

    $title = "Call Decline";
    $message = "Missed call .";

    $data = [
    //   'from_user' => $fromUser,
    //   'to_user' => $toUser,
      'from_user' => $toUser,
      'to_user' => $fromUser,
      'call_type' => "video_call",
      'message' =>  'missed call'
    ];

    $fuName = User::where('id', $fromUser)->select('id', 'fullname')->first();
    $tuName = User::where('id', $toUser)->select('id', 'fullname')->first();
    $msg = "";

    // $getMobNo = Chat::where(function ($query) use ($fromUser, $toUser) {
    //   $query->where('from_user', $fromUser)
    //     ->where('to_user', $toUser);
    // })->orWhere(function ($query) use ($fromUser, $toUser) {
    //   $query->where('to_user', $fromUser)
    //     ->where('from_user', $toUser);
    // })->get();

    // $res = $getMobNo->count();

    // if ($res == 0) {
    //   $str = Chat::orderByDesc('id')->limit(1)->select('id')->first();
    //   $idata['chat_id'] = $str->id + 1;
    // } else {
    //   $oldChatId = Chat::where(function ($query) use ($fuName, $toUser) {
    //     $query->where('from_user', $fuName->id)
    //       ->where('to_user', $toUser);
    //   })->orWhere(function ($query) use ($fuName, $toUser) {
    //     $query->where('to_user', $fuName->id)
    //       ->where('from_user', $toUser);
    //   })->limit(1)->pluck('chat_id');

    //   $idata['chat_id'] = $oldChatId->first();
    //   $chId = $idata['chat_id'];
    // }

    $usTime = Carbon::now('America/New_York');


    $indiaTimeFormatted = $usTime->format('Y-m-d H:i:s');

    $time = $usTime->format('H:i');

    // $time = now()->format('H:i');
    $times = str($time);
    // $msg = $fuName->name . "Call Receive " . $tuName->name . "..!";
    // $msg = "Missed call at $time";
    
       $utcTime = Carbon::now();

// Format the time in the desired format
$formattedTime = $utcTime->format('Y-m-d H:i:s');

// Extract the hour and minute parts
$time = $utcTime->format('H:i');

 $msg = "Missed call at $time";

    // $time = now()->format('H:i');
    // $times = str($time);
    // // $msg = $fuName->name . "Call Receive " . $tuName->name . "..!";
    // $msg = " Missed call "  . "at $times";
    // // $msg = $fuName->username . " Missed call " . $tuName->name . "..!";
    
       $date = round(microtime(true) * 1000);

    $idata =  Chat::create([
    //   'from_user' => $fromUser,
    //   'to_user' => $request->input('to_user'),
      'from_user' => $request->input('to_user'),
      'to_user' => $fromUser,
      'message' => $msg,
      'type' => "call",
      'date' => $date,
      'time' => $indiaTimeFormatted,
    ]);

    // $reg = $this->front_model->new_chat($idata);

    if (Notification::create($data)) {
      $callId = DB::getPdo()->lastInsertId();

      $fromUser = $fromUser;
      $toUser = $request->input('to_user');
      $call = "1";
      $isType = "video_call";

      $user = User::find($fromUser);

      $title = "Call Decline";
      $message = "calling.";
      $name = $user->fullname;
      $mobile = $user->phone;
      
      
            if ($user->profile_pic != "") {
                $url = explode(":", $user->profile_pic);
                if ($url[0] == "https" || $url[0] == "http") {
                    $profile_image = $user->profile_pic;
                } else {
                    $profile_image = url('/public/images/user/' . $user->profile_pic);
                }
            } else {
                $profile_image = "";
            }

    //   $fUser = User::select('username')->where('id', $fromUser)->first()->username;
    //   $fImage = User::select('profile_pic')->where('id', $fromUser)->first()->profile_pic;
    //   $FcmToken = User::select('device_token')->where('id', $toUser)->first()->device_token;
      
            $fUser = User::select('fullname')->where('id', $fromUser)->first()->fullname;
            $tUser = User::select('fullname')->where('id', $toUser)->first()->fullname;
            $fImage = User::select('profile_pic')->where('id', $fromUser)->first()->profile_pic;
            $tImage = User::select('profile_pic')->where('id', $toUser)->first()->profile_pic;
            $FcmToken = User::select('device_token')->where('id', $toUser)->first()->device_token;

      $data = [
        "registration_ids" => array($FcmToken),
        "notification" => [
          "title" => "Call Decline",
          "body" => "You're receiving a call from $fUser.",
          'toUser' => $toUser,
          'message' => $message,
          'caller_name' => $fUser ?? "",
          'receiver_name' => $tUser ?? "",
          'caller_profile_pic' => $fImage ? url('public/images/user/' . $fImage) : "",
         'receiver_profile_pic' => $tImage ? url('public/images/user/' . $tImage) : "",
          'mobile' => $mobile,
          // 'channel' => $channel,
          'call' => '1',
          'isType' => 'video_call',
          "Message" => 'calling'
        ],
        "data" => [
          "title" => "Call Decline",
          "body" => "You're receiving a call from $fUser.",
          'toUser' => $toUser,
          'message' => $message,
          'caller_name' => $fUser ?? "",
          'receiver_name' => $tUser ?? "",
          'caller_profile_pic' => $fImage ? url('public/images/user/' . $fImage) : "",
          'receiver_profile_pic' => $tImage ? url('public/images/user/' . $tImage) : "",
          'mobile' => $mobile,
          // 'channel' => $channel,
          'call' => '1',
          'my_id' => $toUser,
          'my_secondid' => $fromUser,
          'isType' => 'video_call',
          "Message" => 'calling'
        ]
      ];
      $this->sendNotification($data);

      // print_r($data);
      // die;

      $result = [
        "response_code" => "1",
        "message" => "Video Call Connected",
        // "token" => $token,
        "call_id" => $callId,
        "status" => "success",
      ];

      return response()->json($result);
    }
  }
  
    public function cut_call(Request $request)
  {
    // $fromUser = $request->input('from_user');
    $fromUser = Auth::user()->token()->user_id;
    $toUser = $request->input('to_user');
    $type = $request->input('type');
    $call_id = $request->input('call_id');

    $user = User::find($fromUser);

    $title = "Call Decline";
    $message = "Missed call .";

    // $data = [
    // //   'from_user' => $fromUser,
    // //   'to_user' => $toUser,
    //   'from_user' => $toUser,
    //   'to_user' => $fromUser,
    //   'call_type' => "video_call",
    //   'message' =>  'missed call'
    // ];
    
     $data = [
            // 'from_user' => $fromUser,
            // 'to_user' => $toUser,
            // 'call_type' => "video_call",
            'call_type' => $type,
            'message' =>  'missed call'
        ];
    
     $datas = Notification::where('not_id', $request->call_id)
            ->update($data);

    $fuName = User::where('id', $fromUser)->select('id', 'fullname')->first();
    $tuName = User::where('id', $toUser)->select('id', 'fullname')->first();
    $msg = "";

    // $getMobNo = Chat::where(function ($query) use ($fromUser, $toUser) {
    //   $query->where('from_user', $fromUser)
    //     ->where('to_user', $toUser);
    // })->orWhere(function ($query) use ($fromUser, $toUser) {
    //   $query->where('to_user', $fromUser)
    //     ->where('from_user', $toUser);
    // })->get();

    // $res = $getMobNo->count();

    // if ($res == 0) {
    //   $str = Chat::orderByDesc('id')->limit(1)->select('id')->first();
    //   $idata['chat_id'] = $str->id + 1;
    // } else {
    //   $oldChatId = Chat::where(function ($query) use ($fuName, $toUser) {
    //     $query->where('from_user', $fuName->id)
    //       ->where('to_user', $toUser);
    //   })->orWhere(function ($query) use ($fuName, $toUser) {
    //     $query->where('to_user', $fuName->id)
    //       ->where('from_user', $toUser);
    //   })->limit(1)->pluck('chat_id');

    //   $idata['chat_id'] = $oldChatId->first();
    //   $chId = $idata['chat_id'];
    // }

    $usTime = Carbon::now('America/New_York');


    $indiaTimeFormatted = $usTime->format('Y-m-d H:i:s');

    $time = $usTime->format('H:i');

    // $time = now()->format('H:i');
    $times = str($time);
    // $msg = $fuName->name . "Call Receive " . $tuName->name . "..!";
    // $msg = "Missed call at $time";
    
       $utcTime = Carbon::now();

// Format the time in the desired format
$formattedTime = $utcTime->format('Y-m-d H:i:s');

// Extract the hour and minute parts
$time = $utcTime->format('H:i');

 $msg = "Missed call at $time";

    // $time = now()->format('H:i');
    // $times = str($time);
    // // $msg = $fuName->name . "Call Receive " . $tuName->name . "..!";
    // $msg = " Missed call "  . "at $times";
    // // $msg = $fuName->username . " Missed call " . $tuName->name . "..!";
    
       $date = round(microtime(true) * 1000);

    $idata =  Chat::create([
    //   'from_user' => $fromUser,
    //   'to_user' => $request->input('to_user'),
      'from_user' => $request->input('to_user'),
      'to_user' => $fromUser,
      'message' => $msg,
      'type' => "call",
      'date' => $date,
      'time' => $indiaTimeFormatted,
    ]);

    // $reg = $this->front_model->new_chat($idata);

    // if (Notification::create($data)) {
     if ($datas) {
      $callId = DB::getPdo()->lastInsertId();

      $fromUser = $fromUser;
      $toUser = $request->input('to_user');
      $call = "1";
    //   $isType = "video_call";
     $isType = $request->input('type');

      $user = User::find($fromUser);

      $title = "Call Decline";
      $message = "calling.";
      $name = $user->fullname;
      $mobile = $user->phone;
      
      
            if ($user->profile_pic != "") {
                $url = explode(":", $user->profile_pic);
                if ($url[0] == "https" || $url[0] == "http") {
                    $profile_image = $user->profile_pic;
                } else {
                    $profile_image = url('/public/images/user/' . $user->profile_pic);
                }
            } else {
                $profile_image = "";
            }

    //   $fUser = User::select('username')->where('id', $fromUser)->first()->username;
    //   $fImage = User::select('profile_pic')->where('id', $fromUser)->first()->profile_pic;
    //   $FcmToken = User::select('device_token')->where('id', $toUser)->first()->device_token;
      
            $fUser = User::select('fullname')->where('id', $fromUser)->first()->fullname;
            $tUser = User::select('fullname')->where('id', $toUser)->first()->fullname;
            $fImage = User::select('profile_pic')->where('id', $fromUser)->first()->profile_pic;
            $tImage = User::select('profile_pic')->where('id', $toUser)->first()->profile_pic;
            $FcmToken = User::select('device_token')->where('id', $toUser)->first()->device_token;

      $data = [
        "registration_ids" => array($FcmToken),
        "notification" => [
          "title" => "Call Decline",
          "body" => "You're receiving a call from $fUser.",
          'toUser' => $toUser,
          'message' => $message,
          'caller_name' => $fUser ?? "",
          'receiver_name' => $tUser ?? "",
          'caller_profile_pic' => $fImage ? url('public/images/user/' . $fImage) : "",
         'receiver_profile_pic' => $tImage ? url('public/images/user/' . $tImage) : "",
          'mobile' => $mobile,
          // 'channel' => $channel,
          'call' => '1',
        //   'isType' => 'video_call',
          'isType' => $isType,
          "Message" => 'calling'
        ],
        "data" => [
          "title" => "Call Decline",
          "body" => "You're receiving a call from $fUser.",
          'toUser' => $toUser,
          'message' => $message,
          'caller_name' => $fUser ?? "",
          'receiver_name' => $tUser ?? "",
          'caller_profile_pic' => $fImage ? url('public/images/user/' . $fImage) : "",
          'receiver_profile_pic' => $tImage ? url('public/images/user/' . $tImage) : "",
          'mobile' => $mobile,
          // 'channel' => $channel,
          'call' => '1',
          'my_id' => $toUser,
          'my_secondid' => $fromUser,
        //   'isType' => 'video_call',
        'isType' => $isType,
          "Message" => 'calling'
        ]
      ];
      $this->sendNotification($data);

      // print_r($data);
      // die;

      $result = [
        "response_code" => "1",
        "message" => "Video Call Connected",
        // "token" => $token,
        "call_id" => $callId,
        "status" => "success",
      ];

      return response()->json($result);
    }
  }
  
  
    
     public function get_call_list(Request $request)
    {
        // if (!$request->has('user_id') || $request->input('user_id') == '') {
        //     $result["response_code"] = "0";
        //     $result["message"] = "Enter Data";
        //     $result["status"] = "failure";
        //     return response()->json($result);
        // }
        
        
         $user_id = Auth::user()->token()->user_id;

        // $user_id = $request->input('user_id');

        $convo_qry = DB::select("SELECT id, fullname FROM users WHERE id IN (SELECT to_user FROM notifications WHERE from_user = ?) OR id IN (SELECT from_user FROM notifications WHERE to_user = ?)", [$user_id, $user_id]);

        $convo_list_arr = [];

        foreach ($convo_qry as $key => $row) {

            // $last_message_qry = DB::select("SELECT * FROM chats WHERE (chats.from_user = ? AND chats.to_user = ?) OR (chats.to_user = ? AND chats.from_user = ?) ORDER BY UNIX_TIMESTAMP(`created_at`) DESC LIMIT 1", [$user_id, $row->id, $user_id, $row->id]);

            $last_message_qry = DB::select("SELECT * FROM notifications WHERE (notifications.from_user = ? AND notifications.to_user = ?) OR (notifications.to_user = ? AND notifications.from_user = ?) ORDER BY UNIX_TIMESTAMP(`created_at`) DESC", [$user_id, $row->id, $user_id, $row->id]);


            $last_message_qr = $last_message_qry;

            foreach ($last_message_qr as $key => $ro) {
                $user_list = [];
                $user_list['id'] = (string)$ro->not_id;
                $user_list['from_user'] = (string)$ro->from_user;
                $user_list['to_user'] = (string)$ro->to_user;
                $user_list['call_type'] = (string)$ro->call_type;
                $user_list['my_id'] = (string)$user_id;

                if ($ro->from_user == $user_id) {
                    $user_list['second_user_id'] = (string)$ro->to_user;
                } else {
                    $user_list['second_user_id'] = (string)$ro->from_user;
                }


                // $user_list['last_message_created_date'] = $ro->date;
                // $user_list['last_message_created_time'] = date('H:i', strtotime($ro->time));
                // $user_list['last_message_created'] = $ro->created_at;
                // $user_list['last_message_created'] = date('H:i', strtotime($ro->created_at));
                $user_list['message'] = (string)$ro->message;
                  if ($ro->from_user == $user_id) {
                    $user_list['is_comeing'] = "Outgoing";
                } else {
                    $user_list['is_comeing'] = "Incoming";
                }

                $createdDate = Carbon::createFromFormat('Y-m-d H:i:s', $ro->created_at)
                    ->format('Y-m-d\TH:i:s.u\Z');

                $user_list['timestamp'] = $createdDate;


                if ($ro->from_user == $request->input('user_id')) {
                    $user = DB::table('users')->where('id', $ro->to_user)->first();
                } else {
                    $user = DB::table('users')->where('id', $ro->from_user)->first();
                }

                if (!empty($user->profile_pic)) {
                    $url = explode(":", $user->profile_pic);

                    if ($url[0] == "https" || $url[0] == "http") {
                        $user_list['profile_pic'] = $user->profile_pic;
                    } else {
                        $user_list['profile_pic'] = url('public/images/user/' . $user->profile_pic);
                    }
                } else {
                    $user_list['profile_pic'] = "";
                }

                $user_list['username'] = !empty($user->fullname) ? $user->fullname : "";
                // $user_list['firstname'] = !empty($user->firstname) ? $user->firstname : "";


                $convo_list_arr[] = $user_list;
            }
        }
        $chat = array();
        foreach ($convo_list_arr as $key => $row) {
            $chat[$key] = $row['id'];
        }
        array_multisort($chat, SORT_DESC, $convo_list_arr);


        if (!empty($convo_list_arr)) {
            $result['response_code'] = "1";
            $result['message'] = "Call List Found";
            $result['messages list'] = $convo_list_arr;
            $result["status"] = "success";
            return response()->json($result);
        } else {
            $result['response_code'] = "0";
            $result['message'] = "Call List Not Found";
            $result['messages list'] = $convo_list_arr;
            $result["status"] = "success";
            return response()->json($result);
        }
    }
    public function total_unread_messages(Request $request)
{
    $user_id = Auth::user()->token()->user_id;

    $total_unread_messages = Chat::where('to_user', $user_id)->where('read_message', 0)->count();

    $response = [
        'success' => true,
        'total_unread_messages' => (string)$total_unread_messages
    ];

    return response()->json($response, 200);
}

}
