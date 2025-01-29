<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\AdminProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;


class AccountSettingsSecurity extends Controller
{
  public function index()
  {
    $adminProfile = AdminProfile::first();
    return view('content.pages.pages-profile-security', compact('adminProfile'));
  }

  public function changePassword(Request $request, $id)
  {
    $adminProfile = AdminProfile::find($id);

    // Validate the request data
    $validator = Validator::make($request->all(), [
      'currentPassword' => [
        'required',
        function ($attribute, $value, $fail) use ($adminProfile) {
          // Check if the current password matches the one in the database
          if (!Hash::check($value, $adminProfile->password)) {
            $fail('The current password is incorrect.');
          }
        }
      ],
      'newPassword' => 'required|min:8|different:currentPassword',
      'password' => 'required|same:newPassword',
    ]);

    // If validation fails, return the errors
    if ($validator->fails()) {
      return redirect()->back()->withErrors($validator)->withInput();
    }

    // Update the password in the database with the hashed value
    $adminProfile->password = Hash::make($request->newPassword);
    $adminProfile->save();

    // Redirect to app/ecommerce/dashboard after successful password change
    return redirect()->route('pages-profile-security')->with('message', 'Password changed successfully.');
  }
}
