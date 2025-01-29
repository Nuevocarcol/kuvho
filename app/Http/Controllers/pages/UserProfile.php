<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\AdminProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserProfile extends Controller
{
  public function index()
  {
    $adminProfile = AdminProfile::first();
    return view('content.pages.pages-profile-user', compact('adminProfile'));
  }

  public function editProfile($id)
  {
    $adminProfile = AdminProfile::find($id);

    if (!$adminProfile) {
      return redirect()->route('pages-profile-user')->with('error', 'User not found.');
    }

    return view('content.pages.pages-profile-user', compact('adminProfile'));
  }

  public function updateProfile($id, Request $request)
  {
    $adminProfile = AdminProfile::find($id);

    if (!$adminProfile) {
      return response()->json(['success' => false, 'message' => 'User not found']);
    }

    $validator = Validator::make($request->all(), [
      'first_name' => 'required',
      'last_name' => 'required',
      'email' => 'required|email',
      'phone' => 'required|numeric',
      'image' => 'image',
    ]);

    if ($validator->fails()) {
      return response()->json(['success' => false, 'errors' => $validator->errors()]);
    }

    $adminProfile->first_name = $request->input('first_name');
    $adminProfile->last_name = $request->input('last_name');
    $adminProfile->email = $request->input('email');
    $adminProfile->phone = $request->input('phone');

    if ($request->hasFile('image')) {
      $image = $request->file('image');
      $imageName = $image->getClientOriginalName();
      $image->move(public_path('assets/images'), $imageName);
      $adminProfile->image = $imageName;
    }
    $adminProfile->save();

    return redirect()->route('pages-profile-user')->with('message', 'User profile updated successfully');
  }
}
