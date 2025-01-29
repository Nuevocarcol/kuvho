<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserProfileResource;
use App\Http\Resources\LoginResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
// Use Defuse\Crypto\File;
use Validator;
use App\Models\User;
use File;

class AuthController extends BaseController
{
    public function login2(Request $request)
    {

        if ($request->email == "" || $request->password == "") {
            return response()->json([
                "response_code" => "0",
                "message" => "email id Not Found",
                "status" => "failure"
            ]);
        }

        $data = array(
            'email' => $request->email,
            'password' => $request->password,
        );
        if (!auth()->attempt($data)) {
            return response()->json([
                "response_code" => "0",
                "message" => "email id Not Found",
                "status" => "failure"
            ]);
        }

        $token = auth()->user()->createToken('API Token')->accessToken;
        return response(['response_code' => 1, 'message' => "user login success", 'status' => "success", 'user' => new UserResource(auth()->user()), 'token' => $token]);
    }

    public function login4(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = Auth::user();
            // print_r($user->id);
            // exit;
            if ($user->email_verified_at == null) {
                return $this->sendError("Email not verified.");
            }
            if (!empty($request->device_token)) {
                User::where('id', $user->id)->update(['device_token' => $request->device_token]);
            }
            // if (strpos($user->profile_pic, 'profile_pic/') !== false) {
            //     $profile = new BaseController;
            //     $img = $profile->s3FetchFile($user->profile_pic);
            // } else {
            //     if (strpos($user->profile_pic, 'https://') !== false) {
            //         $img = $user->profile_pic;
            //     } else {
            //         $img = $user->profile_pic ? url('/profile_pic/' . $user->profile_pic) : url('/profile_pic/1.png');
            //     }
            // }
            $res = array(
                "token" => $user->createToken('Sellapy')->accessToken,
                "email" => $user->email,
                "name " => (string)$user->username,
                // 'profile_pic' => $img,
                // "user_type" => $user->user_type,
                // "country" => $user->country
            );

            return $this->sendResponse($res, "Login success.");
        } else {

            $userdtl = User::where('email', $request->email)->first();
            if (!empty($userdtl)) {

                if ($userdtl->password != bcrypt($request->password)) {
                    return $this->sendError("Invalid email or Password.");
                }
                $userdtl->update(['password' => bcrypt($request->password), 'email_verified_at' => now()]);
                if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                    $user = Auth::user();
                    // print_r($user->id);
                    // exit;
                    if ($user->email_verified_at == null) {
                        return $this->sendError("Email not verified.");
                    }
                    if (!empty($request->device_token)) {
                        User::where('id', $user->id)->update(['device_token' => $request->device_token]);
                    }
                    // if (strpos($user->profile_pic, 'profile_pic/') !== false) {
                    //     $profile = new BaseController;
                    //     $img = $profile->s3FetchFile($user->profile_pic);
                    // } else {
                    //     if (strpos($user->profile_pic, 'https://') !== false) {
                    //         $img = $user->profile_pic;
                    //     } else {
                    //         $img = $user->profile_pic ? url('/profile_pic/' . $user->profile_pic) : url('/profile_pic/1.png');
                    //     }
                    // }
                    $res = array(
                        "token" => $user->createToken('Sellapy')->accessToken,
                        "email" => $user->email,
                        "name " => (string)$user->username,
                        // 'profile_pic' => $img,
                        // "user_type" => $user->user_type,
                        // "country" => $user->country
                    );
                    return $this->sendResponse($res, "Login success.");
                }
                return $this->sendError('Unauthorised.');
            }
            return $this->sendError("Invalid email or Password.");
            // return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function login3(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'response_code' => 0,
                'message' => 'Validation failed',
                'status' => 'failure',
                'errors' => $validator->errors(),
            ]);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            if ($user->email_verified_at == null) {
                return response()->json([
                    'response_code' => 0,
                    'message' => 'Email not verified.',
                    'status' => 'failure',
                ]);
            }

            if (!empty($request->device_token)) {
                $user->update(['device_token' => $request->device_token]);
            }

            $token = $user->createToken('MyAuthApp')->accessToken;

            return response()->json([
                'response_code' => 1,
                'message' => 'Login success.',
                'status' => 'success',
                'token' => $token,
                'user' => new UserResource($user),
            ]);
        } else {
            return response()->json([
                'response_code' => 0,
                'message' => 'Invalid email or password.',
                'status' => 'failure',
            ]);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'response_code' => 0,
                'message' => 'Validation failed',
                'status' => 'failure',
                'errors' => $validator->errors(),
            ]);
        }

        $user = User::where('email', $request->email)->first();
        
        //  dd("$user");
        // dd($request->all());
        
         if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
             
             
            //   dd("$user");
             
        //     $authUser = Auth::user();
            // $token =  $authUser->createToken('MyAuthApp')->accessToken;

        // if ($user && Hash::check($request->password, $user->password)) {
            
            
           
            // Password is correct
            // if ($user->email_verified_at == null) {
            //     return response()->json([
            //         'response_code' => 0,
            //         'message' => 'Email not verified',
            //         'status' => 'failure',
            //     ]);
            // }

            // Your other login logic here...

            return response()->json([
                'response_code' => "1",
                'message' => 'Login successful',
                'status' => 'success',
                'user' => new LoginResource($user),
                'user_token' => $user->createToken('MyAuthApp')->accessToken,
            ]);
        }

        return response()->json([
            'response_code' => "0",
            'message' => 'Invalid email or password',
            'status' => 'failure',
        ]);
    }



    public function username_email_check2(Request $request)
    {

        if ($request->email == "" || $request->username == "" || $request->password == "") {
            return response()->json([
                "response_code" => "0",
                "message" => "Please Enter Data and not be empty..!",
                "status" => "failure"
            ]);
        }

        $user_check = User::where(function ($query) use ($request) {
            $query->where('username', '=', $request->username)
                ->orWhere('email', '=', $request->email);
        })->count();

        if ($user_check != 0) {
            $temp["response_code"] = "0";
            $temp["message"] = "username & Email Id Already Registered";
            $temp["status"] = "failure";
            return response($temp);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        $token = $user->createToken('MyAuthApp')->accessToken;

        return response(['response_code' => 1, 'message' => "user register success", 'status' => "success", 'user' => new UserResource($user), 'token' => $token]);
    }
    public function username_email_check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'username' => 'required',
            'password' => 'required|min:6', // Adjust the minimum length as needed
            'firstname' => 'required',
            'lastname' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'response_code' => 0,
                'message' => 'Validation failed',
                'status' => 'failure',
                'errors' => $validator->errors(),
            ]);
        }

        $user_check = User::where(function ($query) use ($request) {
            $query->where('username', '=', $request->username)
                ->orWhere('email', '=', $request->email);
        })->count();

        if ($user_check != 0) {
            return response()->json([
                'response_code' => 0,
                'message' => 'Username or Email ID already registered',
                'status' => 'failure',
            ]);
        }
        
         $fullname = $request->firstname . ' ' . $request->lastname;

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'fullname' => $fullname,
        ]);

        $token = $user->createToken('MyAuthApp')->accessToken;

        return response()->json([
            'response_code' => "1",
            'message' => 'user register success',
            'status' => 'success',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }


    public function register_new(Request $request)
    {

        if ($request->email == "" || $request->username == "" || $request->gender == "" || $request->age == "" ) {
            return response()->json([
                "response_code" => "0",
                "message" => "Enter Data",
                "status" => "failure"
            ]);
        }

        $user_id = Auth::user()->token()->user_id;
        
        // $fullname = $request->firstname . ' ' . $request->lastname;

        $user = array(
            'email' => $request->email,
            'username' => $request->username,
            // 'fullname' => $fullname,
            'age' => $request->age,
            'gender' => $request->gender,
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'bio' => $request->bio,
            'device_token' => $request->device_token,
            // 'create_date' => round(microtime(true) * 1000)
        );


        User::where('id', $user_id)->update($user);

        $user = User::find($user_id);
        
        $token = $user->createToken('MyAuthApp')->accessToken;

        return response(['response_code' => "1", 'message' => "user register success", 'status' => "success", 'user' => new LoginResource($user), 'token' => $token]);
    }
    
    public function social_login(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'login_type' => 'required',
      'email' => 'required',
      'device_token' => 'required',
    ]);
    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'data' => $validator->errors(),
      ]);
    }
    $input = $request->all();
    // dd($input);
    if (User::where('email', $request->email)->exists()) {
      $user = User::where('email', $request->email)->first();
      $user->update($input);
      // return response()->json([
      //   'success' => false,
      //   'data' => array("token" => $user->createToken('MyApp')->accessToken, "login_type" => (string)$user->login_type), "Login success.",
      // ]);
      return response([
        'user_id' => (string) $user->id,
        'token' => $user->createToken('MyAuthApp')->accessToken,
        'login_type' => (string)$user->login_type,
        'message' => "Login success.",
      ]);
    }
    User::create($input);
    $user = User::where('email', $request->email)->first();
    // return response()->json([
    //   'success' => false,
    //   'data' => array("token" => $user->createToken('MyApp')->accessToken, "login_type" => (string)$user->login_type), "Signup success.",
    // ]);
    return response([
      'user_id' => (string) $user->id,
      'token' => $user->createToken('MyAuthApp')->accessToken,
      'login_type' => (string)$user->login_type,
      'message' => "Signup success.",
    ]);
  }

    public function user_date(Request $request)
    {

        $user_id = Auth::user()->token()->user_id;
        $user = User::find($user_id);
        if ($request->file('profile_pic')) {

            $file = $request->file('profile_pic');
            $filename = "user". date('YmdHis') . $file->getClientOriginalName();
            // $filename = str_replace(" ", "", $filename);
            $file->move(public_path('/images/user/'), $filename);
            $update['profile_pic'] = $filename;
            // if (File::exists('images/user/' . $user->image)) {
            //     File::delete('images/profile_pic/' . $user->image);
            // }
        }

        // $update['fullname'] = ($request->fullname) ? $request->fullname : $user->fullname;
        $update['fullname'] = ($request->firstname && $request->lastname) ? $request->firstname . ' ' . $request->lastname : $user->fullname;

        $update['username'] = ($request->username) ? $request->username : $user->username;
        $update['email'] = ($request->email) ? $request->email : $user->email;
        $update['phone'] = ($request->phone) ? $request->phone : $user->phone;
        $update['age'] = ($request->age) ? $request->age : $user->age;
        $update['gender'] = ($request->gender) ? $request->gender : $user->gender;
        $update['country'] = ($request->country) ? $request->country : $user->country;
        $update['state'] = ($request->state) ? $request->state : $user->state;
        $update['city'] = ($request->city) ? $request->city : $user->city;
        $update['bio'] = ($request->bio) ? $request->bio : $user->bio;

        User::where('id', $user_id)->update($update);

        $user = User::find($user_id);
        return response(['response_code' => 1, 'message' => "Update success", 'user' => new UserProfileResource($user), 'status' => "success"]);
    }
}
