<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\ReelResource;
use App\Http\Resources\TagResource;
use App\Models\Bookmark;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Models\Follow;
use App\Models\Profile_blocklist;
use App\Models\Comment_report;
use App\Models\Reel;
use App\Models\Reel_Comment;
use App\Models\Reel_Like;
use App\Models\post_user_tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Mail\ForgetPass;
use App\Models\Posts_report;
use App\Models\Setting;
use App\Models\User_report;

class UserController extends BaseController
{
    public function show(Request $request)
    {

        $user_id = Auth::user()->token()->user_id;
        // $user_data = User::all();
        return response(['response_code' => 1, 'message' => "user found", 'status' => "success", 'user' => UserResource::collection($user_data)]);
    }

    public function post_by_user(Request $request)
    {
        // $user_id = Auth::user()->token()->user_id;
        
        $user_id = $request->input('user_id');
        $to_user_id = $request->input('to_user_id');

        // if ($request->to_user == "") {
        //     return response(['response_code' => 1, 'message' => "Enter data", 'status' => "failed"]);
        // }

        $posts = Post::select('posts.*')->where('posts.user_id', $user_id)

            //     // ->join('follow', 'follow.to_user', '=', 'posts.user_id')
            // ->leftjoin('follow', function($join) use($user_id){
            //     $join->on('follow.to_user','=','posts.user_id');

            // })
            // ->where('follow.from_user', '=', $user_id)
            // ->orwhere('posts.user_id', Auth::id()) 
            ->with('comment', 'user')->orderBy('posts.post_id', 'DESC')->groupBy('posts.post_id')->get()->transform(function ($ts) use ($user_id) {

                $ts['is_likes'] = (Like::where('post_id', '=', $ts['post_id'])->where('user_id', '=', $user_id)->count()) ? "true" : "false";
                $ts['total_like'] = Like::where('post_id', '=', $ts['post_id'])->count();
                $ts['total_comment'] = Comment::where('post_id', '=', $ts['post_id'])->count();
                $ts['bookmark'] = (Bookmark::where('post_id', '=', $ts['post_id'])->where('user_id', '=', $user_id)->count()) ? "true" : "false";
                
                return $ts;
            });
            
        return response(['status' => 1, 'msg' => "Post Found", 'follower' => PostResource::collection($posts)]);
    }
    
    
    public function reels_by_user(Request $request)
    {
        // $user_id = Auth::user()->token()->user_id;
        
        $user_id = $request->input('user_id');
        $to_user_id = $request->input('to_user_id');

        // if ($request->to_user == "") {
        //     return response(['response_code' => 1, 'message' => "Enter data", 'status' => "failed"]);
        // }

        $posts = Reel::select('reels.*')->where('reels.user_id', $user_id)

            //     // ->join('follow', 'follow.to_user', '=', 'posts.user_id')
            // ->leftjoin('follow', function($join) use($user_id){
            //     $join->on('follow.to_user','=','posts.user_id');

            // })
            // ->where('follow.from_user', '=', $user_id)
            // ->orwhere('posts.user_id', Auth::id()) 
            ->with('reels_comment', 'user')->orderBy('reels.id', 'DESC')->groupBy('reels.id')->get()->transform(function ($ts) use ($user_id) {

                $ts['is_likes'] = (Reel_Like::where('reel_id', '=', $ts['id'])->where('user_id', '=', $user_id)->count()) ? "true" : "false";
                $ts['total_like'] = Reel_Like::where('reel_id', '=', $ts['id'])->count();
                $ts['total_comment'] = Reel_Comment::where('reel_id', '=', $ts['id'])->count();
                // $ts['bookmark'] = (Bookmark::where('post_id', '=', $ts['post_id'])->where('user_id', '=', $user_id)->count()) ? "true" : "false";
                
                return $ts;
            });
            
        return response(['status' => 1, 'msg' => "Reels Found", 'reels' => ReelResource::collection($posts)]);
    }
    
    public function tags_by_user(Request $request)
    {
        // $user_id = Auth::user()->token()->user_id;
        
        $user_id = $request->input('user_id');
        $to_user_id = $request->input('to_user_id');

        // if ($request->to_user == "") {
        //     return response(['response_code' => 1, 'message' => "Enter data", 'status' => "failed"]);
        // }

        // $posts = post_user_tag::select('post_user_tags.*')->where('post_user_tags.tag_users', $user_id);
        
         // Retrieve posts with specific tags
         
         
    $posts = post_user_tag::select('posts.*','post_user_tags.*')
        ->join('posts', 'post_user_tags.post_id', '=', 'posts.post_id') // Corrected table name
        // ->join('reels', 'post_user_tags.reel_id', '=', 'reels.id') // Corrected table name
        ->where('post_user_tags.tag_users', $user_id) // Corrected table name
        ->orderBy('post_user_tags.tag_id', 'DESC')
        ->get(); 
        
        
    $reels = post_user_tag::select('reels.*','post_user_tags.*')
        ->join('reels', 'post_user_tags.reel_id', '=', 'reels.id') // Corrected table name
        // ->join('reels', 'post_user_tags.reel_id', '=', 'reels.id') // Corrected table name
        ->where('post_user_tags.tag_users', $user_id) // Corrected table name
        ->orderBy('post_user_tags.tag_id', 'DESC')
        ->get(); 
        
        // $mergedResults = $posts->merge($reels);
        
        $postsCollection = collect($posts);
$reelsCollection = collect($reels);

$mergedResults = $postsCollection->merge($reelsCollection)->sortByDesc('created_at');

            //     // ->join('follow', 'follow.to_user', '=', 'posts.user_id')
            // ->leftjoin('follow', function($join) use($user_id){
            //     $join->on('follow.to_user','=','posts.user_id');

            // })
            // ->where('follow.from_user', '=', $user_id)
            // // ->orwhere('posts.user_id', Auth::id()) 
            // ->with('reels_comment', 'user')->orderBy('reels.id', 'DESC')->groupBy('reels.id')->get()->transform(function ($ts) use ($user_id) {

            //     $ts['is_likes'] = (Reel_Like::where('reel_id', '=', $ts['id'])->where('user_id', '=', $user_id)->count()) ? "true" : "false";
            //     $ts['total_like'] = Reel_Like::where('reel_id', '=', $ts['id'])->count();
            //     $ts['total_comment'] = Reel_Comment::where('reel_id', '=', $ts['id'])->count();
            //     // $ts['bookmark'] = (Bookmark::where('post_id', '=', $ts['post_id'])->where('user_id', '=', $user_id)->count()) ? "true" : "false";
                
            //     return $ts;
            // });
            
        return response(['status' => 1, 'msg' => "Reels Found", 'tags' => TagResource::collection($mergedResults)]);
    }


    public function search_users(Request $request)
    {
        $result = [];
        $users = [];

        $user_id = Auth::user()->token()->user_id;
        $text = $request->input('text');

        if (empty($text)) {
            $result["response_code"] = "0";
            $result["message"] = "Users Not Found";
            $result['users'] = $users;
            $result["status"] = "failure";
            return response()->json($result);
        }

        $users = User::where('username', 'like', "%$text%")
            ->orderByDesc('id')
            ->get();

        foreach ($users as $user) {
            if (!empty($user->profile_pic)) {
                $url = explode(":", $user->profile_pic);

                if ($url[0] == "https" || $url[0] == "http") {
                    $user->profile_pic = $user->profile_pic;
                } else {
                    $user->profile_pic =  url('public/images/user/'. $user->profile_pic);
                    
                     
                }
            } else {
                $user->profile_pic = "";
            }
        }

        if (!empty($users)) {
            $result['response_code'] = "1";
            $result['message'] = "Users Found";
            $result['users'] = $users;
            $result["status"] = "success";
        } else {
            $result["response_code"] = "0";
            $result["message"] = "Users Not Found";
            $result['users'] = $users;
            $result["status"] = "failure";
        }

        return response()->json($result);
    }

    public function user_delete(Request $request)
    {
        $user_id = Auth::user()->token()->user_id;



        $user = User::where('id', $user_id)->delete();

        if ($user) {
            // $user->delete();
            
            $result["response_code"] = "1";
            $result["message"] = "Users all data deleted sucess..!";
            $result["status"] = "sucess";
            // return json_encode($result);
            return response()->json($result);

            // return response()->json(['message' => 'User account deleted successfully']);
        } else {
            // return response()->json(['message' => 'User already deleted']);
            $result["response_code"] = "0";
            $result["message"] = "Data base error.. User not deleted..!";
            $result["status"] = "fail";
            
            return response()->json($result);
        }
    }


    public function change_password2(Request $request)
    {
        $id = Auth::user()->token()->user_id;

        // if (empty($id)) {
        //     abort(404);
        // }

        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'npassword' => 'required|min:6', // Adjust the minimum length as needed
            'cpassword' => 'required|same:npassword',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'response_code' => 0,
                'message' => 'Validation failed',
                'status' => 'failure',
                'errors' => $validator->errors(),
            ]);
        }

        $user = User::find($id);

        if (!$user) {
            abort(404);
        }

        $password = $request->input('password');
        $npassword = $request->input('npassword');
        $cpassword = $request->input('cpassword');

        if (Hash::check($password, $user->password)) {
            $user->update(['password' => Hash::make($npassword)]);

            return response()->json([
                'response_code' => 1,
                'message' => 'Successfully Changed',
                'status' => 'success',
            ]);
        } else {
            return response()->json([
                'response_code' => 0,
                'message' => 'Old Password Wrong',
                'status' => 'failure',
            ]);
        }
    }

    public function change_password_done(Request $request)
    {
        $id = Auth::user()->token()->user_id;
        // $validator = Validator::make($request->all(), [
        //     'email' => 'required|email',
        // ]);

        // if ($validator->fails()) {
        //     return $this->sendError($validator->errors());
        // }

        if (!User::where('id', $id)->exists()) {
            return response()->json(['error' => "Invalid Email id..!"]);
        }

        if (!empty($request->password)) {

            $validator = Validator::make($request->all(), [
                // 'id' => 'required|email',
                'password' => 'required',
                'npassword' => 'required',
                'cpassword' => 'required|same:npassword',
            ]);
            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }


            if (User::where('id', $id)->where('password', $request->password)->exists()) {
                User::where('id', $id)->where('password', $request->password)->update(['email_verified_at' => now(), 'password' => bcrypt($request->npassword)]);

                return $this->sendMessage('Password reset success.');
            } else {
                return $this->sendError('Invelid Password.');
            }
        }
    }
    public function change_password(Request $request)
    {
        $id = Auth::user()->id;

        if (!User::where('id', $id)->exists()) {
            return response()->json(['error' => "Invalid User..!"]);
        }

        if (!empty($request->password)) {
            $validator = Validator::make($request->all(), [
                'password' => 'required',
                'npassword' => 'required|min:6', // Adjust the minimum length as needed
                'cpassword' => 'required|same:npassword',
            ]);

            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }

            $user = User::find($id);

            // if ($user && $user->password == md5($request->password)) {
            if ($user && Hash::check($request->password, $user->password)) {

                $user->update(['email_verified_at' => now(), 'password' => bcrypt($request->npassword)]);

                return $this->sendMessage('Password reset success.');
            } else {
                return $this->sendError('Invalid Password.');
            }
        }

        return $this->sendError('Password field is required.');
    }

    public function forget_pass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if (!User::where('email', $request->email)->exists()) {
            return response()->json(['error' => "Invalid Email id..!"]);
        }

        if (!empty($request->email)) {

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                // 'otp' => 'required',
                // 'password' => 'required',
                // 'cnf_pass' => 'required|same:password',
            ]);
            if ($validator->fails()) {
                return $this->sendError($validator->errors());
            }
            
             $confirmationCode = rand(10000, 999999);
             
             print_r($confirmationCode);
             
            if (User::where('email', $request->email)->exists()) {
                User::where('email', $request->email)->update(['email_verified_at' => now(), 'password' => bcrypt($confirmationCode)]);

                return $this->sendMessage('Password reset success.');
            } else {
                return $this->sendError('Invelid otp.');
            }
        }

        //send mail
        $confirmationCode = rand(1000, 9999);
        $toEmail = $request->email;

        $mailData = array('code' => $confirmationCode);
        try {
            if (Mail::to($toEmail)->send(new ForgetPass($mailData))) {
                User::where('email', $request->email)->update(['password' => $confirmationCode]);
                return $this->sendResponse("", "Emails send successfully.");
            }
        } catch (Exception $e) {
            return $this->sendError("Email faild..", $e->getMessage());
        }
    }

    public function users_filter2(Request $request)
    {
        $result = [];
        header('Content-Type: application/json');

        $interestsId = $request->input('interests_id');
        $name = $request->input('name');
        $country = $request->input('country');
        $age = $request->input('age');
        $gender = $request->input('gender');
        $state = $request->input('state');

        $sql_query = '';

        // ... (rest of your logic)

        if (!empty($name) || !empty($country) || !empty($age) || !empty($gender) || !empty($state)) {

            if (!empty($age) && empty($name) && empty($country) && empty($gender) && empty($state)) {
                $age_a = explode(",", $age);
                $start_age = $age_a[0];
                $end_age = $age_a[1];

                $sql_query .= "age >= $start_age AND age <= $end_age ";
            }
            // else {
            //    // $age_a = explode(",", $age);
            //    // $start_age = 0;
            //    // $end_age = 0;

            //    $sql_query .= "age >= $start_age AND age <= $end_age AND ";
            // }

            // if (!empty($age)) {
            //    if (!empty($interests_id)) {
            //       $sql_query .= "interests_id like '%$interests_id%'";
            //    }
            // } else {
            //    if (!empty($interests_id)) {
            //       $sql_query .= "interests_id like '%$interests_id%'";
            //    }
            // }

            if (!empty($age)) {
                if (!empty($name)) {
                    $sql_query .= "username like '%$name%'";
                }
            } else {
                if (!empty($name)) {
                    $sql_query .= " username like '%$name%'";
                }
            }

            if (!empty($name)) {
                if (!empty($country)) {
                    $sql_query .= " or country like '%$country%'";
                }
            } else {
                if (!empty($country)) {
                    $sql_query .= " country like '%$country%'";
                }
            }

            if (!empty($country) || !empty($name)) {
                if (!empty($gender)) {
                    $sql_query .= " or gender like '%$gender%'";
                }
            } else {
                if (!empty($gender)) {
                    $sql_query .= " gender like '%$gender%'";
                }
            }

            if (!empty($country) || !empty($name) || !empty($gender)) {
                if (!empty($state)) {
                    $sql_query .= " or state like '%$state%'";
                }
            } else {
                if (!empty($state)) {
                    $sql_query .= " state like '%$state%'";
                }
            }

            // $sql = $this->db->query("SELECT * FROM users WHERE " . $sql_query . " ORDER BY id DESC ");
            $sql = DB::select("SELECT * FROM users ORDER BY id DESC");
            $users = $sql->get();
        } else {
            $sql = DB::select("SELECT * FROM users ORDER BY id DESC");
            $users = $sql->get();
        }

        for ($i = 0; $i < count($users); $i++) {

            if (!empty($users[$i]->profile_pic)) {

                $url = explode(":", $users[$i]->profile_pic);
                if ($url[0] == "https" || $url[0] == "http") {
                    $users[$i]->profile_pic = $users[$i]->profile_pic;
                } else {

                    $users[$i]->profile_pic = url() . 'assets/images/user/' . $users[$i]->profile_pic;
                }
            } else {
                $users[$i]->profile_pic = "";
            }
        }

        if (!empty($users)) {
            $result['response_code'] = "1";
            $result['message'] = "Users Found";
            $result['users'] = $users;
            $result["status"] = "success";
            echo json_encode($result);
        } else {
            $result["response_code"] = "0";
            $result["message"] = "Users Not Found";
            $result['users'] = $users;
            $result["status"] = "failure";
            echo json_encode($result);
        }



        // ... (rest of your logic)

        // return response()->json($result);
    }

    public function usersFilter(Request $request)
    {
        $result = [];

        $interestsId = $request->input('interests_id');
        $name = $request->input('name');
        $country = $request->input('country');
        $age = $request->input('age');
        $gender = $request->input('gender');
        $state = $request->input('state');

        $sql_query = '';

        if (!empty($age) && empty($name) && empty($country) && empty($gender) && empty($state)) {
            $age_a = explode(",", $age);
            $start_age = $age_a[0];
            $end_age = $age_a[1];

            $sql_query .= "age >= $start_age AND age <= $end_age ";
        }

        if (!empty($age) && !empty($name)) {
            $sql_query .= "username like '%$name%'";
        }

        if (!empty($name) && !empty($country)) {
            $sql_query .= ($sql_query ? ' OR ' : '') . "country like '%$country%'";
        }

        if ((!empty($country) || !empty($name)) && !empty($gender)) {
            $sql_query .= ($sql_query ? ' OR ' : '') . "gender like '%$gender%'";
        }

        if ((!empty($country) || !empty($name) || !empty($gender)) && !empty($state)) {
            $sql_query .= ($sql_query ? ' OR ' : '') . "state like '%$state%'";
        }

        if (!empty($sql_query)) {
            $sql_query = "WHERE " . $sql_query;
        }

        $users = DB::select("SELECT * FROM users $sql_query ORDER BY id DESC");

        foreach ($users as $user) {
            if (!empty($user->profile_pic)) {
                $url = explode(":", $user->profile_pic);
                if ($url[0] == "https" || $url[0] == "http") {
                    $user->profile_pic = $user->profile_pic;
                } else {
                    $user->profile_pic =   url('public/images/user/'. $user->profile_pic);
                   
                }
            } else {
                $user->profile_pic = "";
            }
        }

        if (!empty($users)) {
            $result['response_code'] = "1";
            $result['message'] = "Users Found";
            $result['users'] = $users;
            $result["status"] = "success";
            return response()->json($result);
        } else {
            $result["response_code"] = "0";
            $result["message"] = "Users Not Found";
            $result['users'] = $users;
            $result["status"] = "failure";
            return response()->json($result);
        }
    }

    public function users_filter(Request $request)
    {
        try {
            $interests_id = $request->input('interests_id');
            $username = $request->input('name');
            $country = $request->input('country');
            $age = $request->input('age');
            $gender = $request->input('gender');
            $state = $request->input('state');
            $user_id = Auth::user()->token()->user_id;


            $users = User::query();
            $users->where('id', '!=', $user_id);

            if ($username) {

                $input = $username; // Remove square brackets
                $username = explode(',', $input);
                if (count($username) > 0) {

                    $users->whereIn('username', $username);
                    $users->where('id', '!=', $user_id);

                    foreach ($username as $row) {
                        $users->where('username', $row);
                        $users->where('id', '!=', $user_id);
                    }
                }
            }


            if ($age) {
                $minPrice = (int)substr($age, 1, strpos($age, ',') - 1);
                $maxPrice = (int)substr($age, strpos($age, ',') + 1, -1);
                $users->whereBetween('age', [$minPrice, $maxPrice]);
                $users->where('id', '!=', $user_id);
            }

            if ($country) {

                $input = $country; // Remove square brackets
                $country = explode(',', $input);
                if (count($country) > 0) {

                    $users->whereIn('country', $country);
                    $users->where('id', '!=', $user_id);

                    foreach ($country as $row) {
                        $users->where('country', $row);
                        $users->where('id', '!=', $user_id);
                    }
                }
            }


            if ($state) {

                $input = $state; // Remove square brackets
                $state = explode(',', $input);
                if (count($state) > 0) {

                    $users->whereIn('state', $state);
                    $users->where('id', '!=', $user_id);

                    foreach ($state as $row) {
                        $users->where('state', $row);
                        $users->where('id', '!=', $user_id);
                    }
                }
            }


            if ($gender) {

                $input = $gender; // Remove square brackets
                $gender = explode(',', $input);
                if (count($gender) > 0) {

                    $users->whereIn('gender', $gender);
                    $users->where('id', '!=', $user_id);

                    foreach ($gender as $row) {
                        $users->where('gender', $row);
                        $users->where('id', '!=', $user_id);
                    }
                }
            }





            // if ($gender) {

            //     $input = str_replace(['[', ']'], '', $gender); // Remove square brackets
            //     $gender = explode(',', $input);

            //     // print_r ($gender);
            //     // die;
            //     if (count($gender) > 0) {

            //         $users->whereIn('gender', $gender);
            //         $users->where('id', '!=', $user_id);
            //         $users->whereNotIn('id', function ($query) use ($user_id) {
            //             $query->select('peer_id')
            //                 ->from('verified_user')
            //                 ->where('user_id', $user_id);
            //         });
            //         $users->whereNotIn('id', function ($query) use ($user_id) {
            //             $query->select('to_user')
            //                 ->from('user_like')
            //                 ->where('from_user', $user_id);
            //         });
            //         // foreach ($gender as $row) {
            //         //     $users->where('gender', $row);
            //         //     $users->where('id', '!=', $user_id);
            //         //     $users->whereNotIn('id', function ($query) use ($user_id) {
            //         //         $query->select('peer_id')
            //         //             ->from('verified_user')
            //         //             ->where('user_id', $user_id);
            //         //     });
            //         //     $users->whereNotIn('id', function ($query) use ($user_id) {
            //         //         $query->select('to_user')
            //         //             ->from('user_like')
            //         //             ->where('from_user', $user_id);
            //         //     });
            //         // }
            //     }
            // }




            $users->OrderByDesc('id');
            $items = $users->get();
            $array = array();

            foreach ($items as $row) {
                $restaurant = [];
                $restaurant['id'] = (string)$row->id;
                $restaurant['fullname'] = $row->fullname ?  $row->fullname : "";
                $restaurant['username'] = $row->username  ?  $row->username : "";
                $restaurant['email'] = $row->email ?  $row->email : "";
                $restaurant['phone'] = $row->phone ?  $row->phone : "";
                $restaurant['salt'] = $row->salt ?  $row->salt : "";
                $restaurant['login_type'] = $row->login_type ?  $row->login_type : "";
                $restaurant['google_id'] = $row->google_id ?  $row->google_id : "";


                if ($row->profile_pic) {
                    $restaurant['profile_pic'] =  url('public/images/user/'. $row->profile_pic);
                     
                } else {
                    $restaurant['profile_pic'] = "";
                }

                if ($row->cover_pic) {
                    $restaurant['cover_pic'] =url('public/images/user/'. $row->cover_pic);
                    
                    
                } else {
                    $restaurant['cover_pic'] = "";
                }

                $restaurant['age'] = $row->age ?  $row->age : "";
                $restaurant['gender'] = $row->gender ?  $row->gender : "";
                $restaurant['country'] = $row->country ?  $row->country : "";
                $restaurant['state'] = $row->state ?  $row->state : "";
                $restaurant['city'] = $row->city ?  $row->city : "";
                $restaurant['bio'] = $row->bio ?  $row->bio : "";
                $restaurant['interests_id'] = $row->interests_id ?  $row->interests_id : "";
                $restaurant['device_token'] = $row->device_token ?  $row->device_token : "";
                $restaurant['create_date'] = $row->create_date ?  $row->create_date : "";
                $restaurant['join_month'] = $row->join_month ?  $row->join_month : "";


                $array[] = $restaurant;
            }

            if (empty($array)) {

                $response = [
                    "response_code" => "0",
                    "message" => "User List Not Found..!",
                    "status" => "failure",
                    'Users' => [],
                ];

                return response()->json($response, 200);
            }

            $response = [
                "response_code" => "1",
                "message" => "User Found",
                "status" => "success",
                'users' => $array,

            ];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError("User not Found", $th->getMessage());
        }
    }

    public function add_comment_report(Request $request)
    {

        $user_id = Auth::user()->token()->user_id;

        $update['user_id'] = $user_id;
        $update['post_id'] = $request->post_id;
        $update['comment_id'] = $request->comment_id;
        $update['report_text'] = $request->report_text;

        $post = Comment_report::create($update);

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

    public function all_post_by_user_pagination(Request $request)
    {
        $result = [];
        header('Content-Type: application/json');

        $limit = $request->input('per_page');
        $page = $request->input('page');
        $user_id = Auth::user()->token()->user_id;

        $page = $page - 1;

        $fcount = DB::table('follow')->where('from_user', $user_id)->count();

        if ($fcount > 0) {
            $all_post = DB::table('posts')
                ->select('posts.*', 'follow.from_user as from_user', 'follow.to_user as to_user')
                ->join('follow', 'follow.to_user', '=', 'posts.user_id')
                ->where('follow.from_user', $user_id)
                ->orderBy('posts.post_id', 'desc')
                ->limit($limit)
                ->offset($page * $limit)
                ->get();

            $twenty_post = DB::table('posts')
                ->select('posts.*', 'posts.user_id as to_user')
                ->orderBy('post_id', 'desc')
                ->limit($limit)
                ->offset($page * $limit)
                ->get();

            $res = $twenty_post->merge($all_post)->unique('post_id')->values()->all();
        } else {
            $res = DB::table('posts')
                ->select('posts.*')
                ->orderBy('post_id', 'desc')
                ->limit($limit)
                ->offset($page * $limit)
                ->get();
        }

        if (!empty($res)) {
            foreach ($res as $key => $post) {
                
                $post->post_id = (string)$post->post_id;
                $post->user_id = (string)$post->user_id;
                $post->text = $post->text ? (string)$post->text : "";
                $post->height = $post->height ? (float)$post->height : 0.0;
                $post->created_at = $post->created_at ? (string)$post->created_at : "";
                $post->updated_at = $post->updated_at ? (string)$post->updated_at : "";
                $post->location = $post->location ? $post->location : "";
                  
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
                        $post->video = asset('public/assets/images/posts/' .$post->video);
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

                $user = User::where('id', $post->user_id)->first();

                if (!empty($user)) {
                    $post->username = $user->username;

                    if ($user->profile_pic == "") {
                        $post->profile_pic = "";
                    } else {
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
                
                 if ($user_id) {

                    if (Profile_blocklist::where('blockedByUserId', $user_id)->where('blockedUserId', $post->user_id)->exists()) {
                        $post->profile_block = "true";
                    } else {
                        $post->profile_block = "false";
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
                // Adapt the code to Laravel as needed
                // ...

                // Example:
                // $user = DB::table('users')->where('id', $post->user_id)->first();
                // $post->username = $user->username;

                // ...
            }if($res){
                $result['response_code'] = "1";
                $result['message'] = "Post Found";
                $result['post'] = $res;
                $result["status"] = "success";
                return response()->json($result);
            }
            
        } else {
            $result["response_code"] = "0";
            $result["message"] = "Post Not Found";
            $result['post'] = [];
            $result["status"] = "failure";
            return response()->json($result);
        }
    }

    public function profile_block(Request $request)
    {
        $result = [];
        $user_id = Auth::user()->token()->user_id;
        $blockedUserId = $request->input('blockedUserId');
        // $blockedByUserId = $user_id;


        if ($request->filled(['blockedUserId'])) {
            $data = [
                'blockedByUserId' => $user_id,
                'blockedUserId' => $request->input('blockedUserId'),
                'created_date' => round(microtime(true) * 1000),
            ];


            $Profile_blocklist =  Profile_blocklist::where('blockedByUserId', $user_id)->where('blockedUserId', $blockedUserId)->first();

            if ($Profile_blocklist) {
                $result["response_code"] = "0";
                $result["message"] = "Already Profile Block";
                $result["status"] = "fail";

                return response()->json($result);
            }

            try {
                // DB::beginTransaction();

                DB::table('profile_blocklist')->insert($data);

                DB::table('follow')
                    ->where('from_user', $data['blockedByUserId'])
                    ->where('to_user', $data['blockedUserId'])
                    ->delete();

                // DB::commit();

                $result["response_code"] = "1";
                $result["message"] = "Profile Block";
                $result["status"] = "success";

                return response()->json($result);
            } catch (\Exception $e) {
                // DB::rollBack();

                $result["response_code"] = "0";
                $result["message"] = "Database Error";
                $result["status"] = "failure";

                return response()->json($result);
            }
        } else {
            $result["response_code"] = "0";
            $result["message"] = "Missing Fields";
            $result["status"] = "failure";

            return response()->json($result);
        }
    }

    public function profile_unblock(Request $request)
    {
        $result = array();
        header('Content-Type: application/json');

        $user_id = Auth::user()->token()->user_id;
        $blockedUserId = $request->input('blockedUserId');

        if (isset($_POST['blockedUserId'])) {

            $blockedByUserId = $user_id;
            $blockedUserId = $request->input('blockedUserId');



            $done = Profile_blocklist::where('blockedByUserId', $user_id)->where('blockedUserId', $blockedUserId)->delete();

            if ($done) {
                $temp["response_code"] = "1";
                $temp["message"] = "Successfully Unblock";
                $temp["status"] = "success";

                return response()->json($temp);
                // echo json_encode($temp);
            } else {
                $temp["response_code"] = "0";
                $temp["message"] = "Database error";
                $temp["status"] = "failure";
                return response()->json($temp);
            }
        } else {

            $result["response_code"] = "0";
            $result["message"] = "Missing Fields";
            $result["status"] = "failure";
            return response()->json($result);
        }
    }

    public function posts_report(Request $request)
    {
        $result = [];
        $user_id = Auth::user()->token()->user_id;
        $blockedPostsId = $request->input('blockedPostsId');
        // $blockedByUserId = $user_id;


        if ($request->filled(['blockedPostsId'])) {
            $data = [
                'blockedByUserId' => $user_id,
                'blockedPostsId' => $request->input('blockedPostsId'),
                'status' => $request->input('status'),
                'report_text' => $request->input('report_text'),
                'created_date' => round(microtime(true) * 1000),
            ];


            $Profile_blocklist =  Posts_report::where('blockedByUserId', $user_id)->where('blockedPostsId', $blockedPostsId)->first();

            if ($Profile_blocklist) {
                $result["response_code"] = "0";
                $result["message"] = "Already Posts Report";
                $result["status"] = "fail";

                return response()->json($result);
            }

            try {
                // DB::beginTransaction();

                // DB::table('posts_report')->insert($data);

                $post = Posts_report::create($data);

                $result["response_code"] = "1";
                $result["message"] = "Posts Report";
                $result["status"] = "success";

                return response()->json($result);
            } catch (\Exception $e) {
                // DB::rollBack();

                $result["response_code"] = "0";
                $result["message"] = "Database Error";
                $result["status"] = "failure";

                return response()->json($result);
            }
        } else {
            $result["response_code"] = "0";
            $result["message"] = "Missing Fields";
            $result["status"] = "failure";

            return response()->json($result);
        }
    }

    public function posts_unblock(Request $request)
    {
        $result = array();
        header('Content-Type: application/json');

        $user_id = Auth::user()->token()->user_id;
        $blockedPostsId = $request->input('blockedPostsId');

        if (isset($_POST['blockedPostsId'])) {

            $blockedByUserId = $user_id;
            $blockedPostsId = $request->input('blockedPostsId');



            $done = Posts_report::where('blockedByUserId', $user_id)->where('blockedPostsId', $blockedPostsId)->delete();

            if ($done) {
                $temp["response_code"] = "1";
                $temp["message"] = "Successfully Unblock";
                $temp["status"] = "success";

                return response()->json($temp);
                // echo json_encode($temp);
            } else {
                $temp["response_code"] = "0";
                $temp["message"] = "Database error";
                $temp["status"] = "failure";
                return response()->json($temp);
            }
        } else {

            $result["response_code"] = "0";
            $result["message"] = "Missing Fields";
            $result["status"] = "failure";
            return response()->json($result);
        }
    }

    public function user_report(Request $request)
    {
        $result = [];
        $user_id = Auth::user()->token()->user_id;
        $reportedUserId = $request->input('reportedUserId');
        // $blockedByUserId = $user_id;


        if ($request->filled(['reportedUserId'])) {
            $data = [
                'reportByUserId' => $user_id,
                'reportedUserId' => $request->input('reportedUserId'),
                'status' => $request->input('status'),
                'report_text' => $request->input('report_text'),
                'created_date' => round(microtime(true) * 1000),
            ];


            $Profile_blocklist =  User_report::where('reportByUserId', $user_id)->where('reportedUserId', $reportedUserId)->first();

            if ($Profile_blocklist) {
                $result["response_code"] = "0";
                $result["message"] = "Already User Report";
                $result["status"] = "fail";

                return response()->json($result);
            }

            try {
                // DB::beginTransaction();

                DB::table('users_report')->insert($data);

                // $post = User_report::create($data);

                $result["response_code"] = "1";
                $result["message"] = "User Report";
                $result["status"] = "success";

                return response()->json($result);
            } catch (\Exception $e) {
                // DB::rollBack();

                $result["response_code"] = "0";
                $result["message"] = "Database Error";
                $result["status"] = "failure";

                return response()->json($result);
            }
        } else {
            $result["response_code"] = "0";
            $result["message"] = "Missing Fields";
            $result["status"] = "failure";

            return response()->json($result);
        }
    }

    public function get_setting()
    {

        $seting =  Setting::first();
        if (!$seting) {
            $result["response_code"] = "0";
            $result["message"] = "Setting Not Found";
            $result["status"] = "fail";
            return response()->json($result);
        }
        $result["response_code"] = "1";
        $result["message"] = "Settings Details";
        $result['settings'] = $seting;
        $result["status"] = "success";
        return response()->json($result);
    }
    
    
    public function user_data(Request $request)
    {
        // $user_id = Auth::user()->token()->user_id;
        //  $user_id = $request->input('user_id');
         
         $user_id = request('user_id');
        if (empty($user_id)) {
            $temp["response_code"] = "0";
            $temp["message"] = "Enter Data";
            $temp["status"] = "failure";
            echo json_encode($temp);
        } else {
            // $temp = array();
            // $profile = array();
            
            $profile = User::where('id', $user_id)->first();
            
            if (!$profile) {
                $response = [
                    'status' => 'failed',
                    'data' => 'user not found',
                ];
                return response()->json($response);
            }
            
            // $profile = User::find($user_id);
            // $profile = $this->front_model->get_user($id);
            
            $user_post = Post::where('user_id' , $user_id)->count();
            $user_followers = Follow::where('to_user' , $user_id)->count();
            $user_following = Follow::where('from_user' , $user_id)->count();
            // $user_post = $this->front_model->get_postby_userid($id);
            // $user_followers = $this->front_model->get_useridby_followers($id);
            // $user_following = $this->front_model->get_useridby_following($id);

            // $profile->profile_pic = base_url() . "uploads/profile_pics/" . $profile->profile_pic;
            
            //  print_r($profiles);
            //     die;

          
                
               
                if (!empty($profile->profile_pic)) {

                    $url = explode(":", $profile->profile_pic);

                    if ($url[0] == "https" || $url[0] == "http") {
                        $profile->profile_pic = $profile->profile_pic;
                    } elseif (!empty($profile->profile_pic)) {
                        $profile->profile_pic =  url('public/images/user/'. $profile->profile_pic);
                    } else {
                        $profile->profile_pic = $profile->profile_pic;
                    }
                } else {
                    $profile->profile_pic = "";
                }
                
                $profile->id = $profile->id ? (string)$profile->id : "";
                $profile->fullname = $profile->fullname ? $profile->fullname : "";
                $profile->username = $profile->username ? $profile->username : "";
                $profile->email = $profile->email ? $profile->email : "";
                $profile->phone = $profile->phone ? $profile->phone : "";
               
                $profile->salt = $profile->salt ? $profile->salt : "";
                $profile->login_type = $profile->login_type ? $profile->login_type : "";
                $profile->google_id = $profile->google_id ? $profile->google_id : "";
                $profile->age = $profile->age ? $profile->age : "";
                $profile->gender = $profile->gender ? $profile->gender : "";
                $profile->country = $profile->country ? $profile->country : "";      
                $profile->state = $profile->state ? $profile->state : "";      
                $profile->city = $profile->city ? $profile->city : "";
                $profile->bio = $profile->bio ? $profile->bio : "";   
                $profile->device_token = $profile->device_token ? $profile->device_token : "";   
                $profile->join_month = $profile->join_month ? $profile->join_month : "";
                $profile->dob = $profile->dob ? $profile->dob : "";   
                $profile->country_id = $profile->country_id ? $profile->country_id : ""; 
                $profile->state_id = $profile->state_id ? $profile->state_id : "";
                $profile->city_id = $profile->city_id ? $profile->city_id : "";
                $profile->is_Private = $profile->is_Private ? $profile->is_Private : "";
                $profile->is_admin = (string)$profile->is_admin ? (string)$profile->is_admin : "";
                if (!empty($profile->cover_pic)) {

                    $url = explode(":", $profile->cover_pic);

                    if ($url[0] == "https" || $url[0] == "http") {
                        $profile->cover_pic = $profile->cover_pic;
                    } elseif (!empty($profile->cover_pic)) {
                        $profile->cover_pic = base_url() . "assets/images/user/" . $profile->cover_pic;
                    } else {
                        $profile->cover_pic = $profile->cover_pic;
                    }
                } else {
                    $profile->cover_pic = "";
                }

                if (!empty($profile->interests_id)) {
                    $interests_id = explode(", ", $profile->interests_id);
                    $in_id = array();
                    $category_name = array();
                    foreach ($interests_id as $key => $id) {
                        array_push($in_id, $id);
                    }
                    $profile->interests_id = $in_id;
                } else {
                    $profile->interests_id = [];
                }

                // if($profile) {
                $temp["response_code"] = "1";
                $temp["message"] = "User Found";
                $temp['user'] = $profile;
                $temp['user_post'] = (string)$user_post;
                $temp['followers'] = (string)$user_followers;
                $temp['following'] = (string)$user_following;
                $temp["status"] = "success";
                // echo json_encode($temp);
                 return response()->json($temp);
            // } else {
            //     $temp["response_code"] = "0";
            //     $temp["message"] = "User Not Found";
            //     $temp['user'] = $profile;
            //     $temp['user_post'] = (string)$user_post;
            //     $temp['followers'] = (string)$user_followers;
            //     $temp['following'] = (string)$user_following;
            //     $temp["status"] = "failure";
            //     echo json_encode($temp);
            // }
        }
    }
    
    public function user_online(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'user_id' => 'required',
            'is_online' => 'required',
        ]);
        if ($validator->fails()) {

            return $this->sendError("Enter this field", $validator->errors(), 422);
        }
        
         $user_id = Auth::user()->token()->user_id;

        $is_online = $request->input('is_online');
        // $device_token = $request->input('device_token');

        try {
            // $phone = $request->input('phone');
            // $otp = $request->input('otp');
            // $where = 'mobile_no="' . $mob_no . '"';
            $data = array(
                "is_online" => $is_online,
                 "updated_at" => now(),
                //  "device_token" => $device_token,
            );
            User::where('id', $user_id)->update($data);

            // $approve = Chat::where('to_user', $request->user_id)->where('message_read', '0')->count();

            $temp = [
                "response_code" => "1",
                "message" => "User Online Update successfully",
                "status" => "success",
                // "unread_count" => $approve,
            ];

            return response()->json($temp);

            // return $this->sendMessage("User Online Update successfully");
            // print_r($user_data); 
            // echo $user_data['mobile_no'];
            // if (isset($user_data['mobile_no'])) {echo"yes";}else{echo "no";}

            // exit;
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError("User not Successfully", $th->getMessage());
            // return response()->json([
            //     'message' => $th->getMessage(),
            //     // 'access_token' => $accessToken,
            // ]);
        }
    }
    
    public function user_update_devicetoken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'user_id' => 'required',
            'device_token' => 'required',
        ]);
        if ($validator->fails()) {

            return $this->sendError("Enter this field", $validator->errors(), 422);
        }
        
         $user_id = Auth::user()->token()->user_id;

        $device_token = $request->input('device_token');
        // $device_token = $request->input('device_token');

        try {
            // $phone = $request->input('phone');
            // $otp = $request->input('otp');
            // $where = 'mobile_no="' . $mob_no . '"';
            $data = array(
                "device_token" => $device_token,
                //  "device_token" => $device_token,
            );
            User::where('id', $user_id)->update($data);

            // $approve = Chat::where('to_user', $request->user_id)->where('message_read', '0')->count();

            $temp = [
                "response_code" => "1",
                "message" => "User Devicetoken Update successfully",
                "status" => "success",
                // "unread_count" => $approve,
            ];

            return response()->json($temp);

            // return $this->sendMessage("User Online Update successfully");
            // print_r($user_data); 
            // echo $user_data['mobile_no'];
            // if (isset($user_data['mobile_no'])) {echo"yes";}else{echo "no";}

            // exit;
        } catch (\Throwable $th) {
            //throw $th;
            return $this->sendError("User not Successfully", $th->getMessage());
            // return response()->json([
            //     'message' => $th->getMessage(),
            //     // 'access_token' => $accessToken,
            // ]);
        }
    }
    
    public function get_all_settings(Request $request)
    {
        
        $private_key = $request->input('private_key');
        $account_sid = "Qs8UW8(xKjv3dIPRMC";

        $category = Setting::first();
            
        $result["name"] = (string)$category->name;
        
        $result["email"] = (string)$category->email;
        
        $result["text"] = (string)$category->text;
        
        $result["color"] = (string)$category->color;
        
        $result["logo"] = (string)$category->logo ? url('public/assets/images/'. $category->logo) : "";
        
        $result["agora_key"] = (string)$category->agora_key;
        
        $result["notify_key"] = (string)$category->notify_key;
         
        $result["prv_pol_url"] = (string)$category->prv_pol_url;

        $result["tnc_url"] = (string)$category->tnc_url;
        
        // url('public/assets/images/1711540762.jpg');

        

       return $this->sendResponse($result, "Privacy Policy Done");
             
        
        // $category = PrivacyModel::select('privacy_policy','term_conditions')->first();

        // $category = $query->row();

        // $result["response_code"] = "1";

        // $result["message"] = "Privacy Policy Done";

        
    }
}
