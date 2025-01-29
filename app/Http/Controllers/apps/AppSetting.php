<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AppSetting extends Controller
{
  public function index()
  {
    $settings = Setting::first();
    return view('content.apps.appsetting-add', compact('settings'));
  }

  public function saveAppSetting(Request $request)
  {
    $rules = [
      'name' => 'required',
      'email' => 'required',
      'text' => 'required',
      'color' => 'required',
      'logo' => 'nullable|image',
    ];

    $customMessages = [
      'name.required' => 'Please enter app name.',
      'email.required' => 'Please enter app email.',
      'text.required' => 'Please enter app text.',
    ];

    $this->validate($request, $rules, $customMessages);

    // Function to convert hex color to RGB
    function hexToRgb($hex)
    {
      // Remove '#' if it exists
      $hex = str_replace('#', '', $hex);

      // Get individual color components
      $r = hexdec(substr($hex, 0, 2));
      $g = hexdec(substr($hex, 2, 2));
      $b = hexdec(substr($hex, 4, 2));

      // Return RGB format
      return "$r, $g, $b";
    }

    // Fetch existing settings or create new if not exists
    $settings = Setting::firstOrNew();

    $settings->name = $request->input('name');
    $settings->email = $request->input('email');
    $settings->text = $request->input('text');

    $settings->color = hexToRgb($request->input('color'));

    // If a new logo is uploaded, update the logo field
    if ($request->hasFile('logo')) {
      $image = $request->file('logo');
      $imageName = time() . '.' . $image->getClientOriginalExtension();
      $image->move(public_path('assets/images'), $imageName);
      // Store only the image name in the database
      $settings->logo = $imageName;
    }

    $settings->save();

    return redirect()->route('appsetting-add')->with('message', 'App Details updated successfully');
  }
}
