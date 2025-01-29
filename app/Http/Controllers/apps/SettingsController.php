<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
  // Add Notifcation Key
  public function addNotificationKey()
  {
    $keys = Setting::first();
    return view('content.apps.notificationkey-add', compact('keys'));
  }

  public function saveNotificationkey(Request $request)
  {
    $rules = [
      'notify_key' => 'required',
    ];

    $customMessages = [
      'notify_key.required' => 'Please enter notification key.',
    ];

    $this->validate($request, $rules, $customMessages);

    // Fetch existing settings
    $settings = Setting::first();

    $settings->notify_key = $request->input('notify_key');
    $settings->save();

    return redirect()->route('notificationkey-add')->with('message', 'Notification Key updated successfully');
  }


  // Add PrivacyPolicy
  public function addPrivacyPolicy()
  {
    $privacypolicy = Setting::first();
    return view('content.apps.privacypolicy-add', compact('privacypolicy'));
  }

  public function savePrivacyPolicy(Request $request)
  {
    $rules = [
      'prv_pol_url' => 'required',
    ];

    $customMessages = [
      'prv_pol_url.required' => 'Please enter privacy policy.',
    ];

    $this->validate($request, $rules, $customMessages);

    // Fetch existing settings
    $settings = Setting::first();

    $settings->prv_pol_url = $request->input('prv_pol_url');
    $settings->save();

    return redirect()->route('privacypolicy-add')->with('message', 'Privacy Policy updated successfully');
  }




  // Add Terms & Conditions
  public function addTermCondition()
  {
    $termcondition = Setting::first();
    return view('content.apps.termcondition-add', compact('termcondition'));
  }

  public function saveTermCondition(Request $request)
  {
    $rules = [
      'tnc_url' => 'required',
    ];

    $customMessages = [
      'tnc_url.required' => 'Please enter terms & conditions.',
    ];

    $this->validate($request, $rules, $customMessages);

    // Fetch existing settings
    $settings = Setting::first();

    $settings->tnc_url = $request->input('tnc_url');
    $settings->save();

    return redirect()->route('termcondition-add')->with('message', 'Terms & Conditions updated successfully');
  }
}
