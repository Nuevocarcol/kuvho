<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginBasic extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs]);
  }

  // Login
  public function login(Request $request)
  {
    $request->validate([
      'email' => 'required',
      'password' => 'required',
    ]);

    if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
      return redirect()->route('dashboard');
    } else {
      return redirect()->route('auth-login-basic')->withErrors(['error' => 'Not authorized. Please check your credentials and try again.']);
    }
  }

  // Logout
  public function logout(Request $request)
  {
    Auth::guard('admin')->logout();
    return redirect()->route('auth-login-basic');
  }
}
