<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AgoraKey extends Controller
{
  public function index()
  {
    $agorakey = Setting::first();
    return view('content.apps.agorakey-add', compact('agorakey'));
  }

  public function saveAgoraKey(Request $request)
  {
    $rules = [
      'agora_key' => 'required',
    ];

    $customMessages = [
      'agora_key.required' => 'Please enter agora key.',
    ];

    $this->validate($request, $rules, $customMessages);

    // Fetch existing settings
    $settings = Setting::first();

    $settings->agora_key = $request->input('agora_key');
    $settings->save();

    return redirect()->route('agorakey-add')->with('message', 'Agora Key updated successfully');
  }
}
