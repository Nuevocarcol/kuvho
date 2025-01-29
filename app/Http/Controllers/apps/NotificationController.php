<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\user_notification;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\MulticastSendReport;

class NotificationController extends Controller
{
  public function index()
  {
    $notifications = user_notification::all();
    return view('content.apps.notifications-list', compact('notifications'));
  }

  // Datatables
  public function getNotificationData(Request $request)
  {

    $draw = $request->get('draw');
    $start = $request->get("start");
    $rowperpage = $request->get("length");

    $columnIndex_arr = $request->get('order');
    $columnName_arr = $request->get('columns');
    $order_arr = $request->get('order');
    $search_arr = $request->get('search');

    $columnIndex = $columnIndex_arr[0]['column'];
    $columnName = $columnName_arr[$columnIndex]['data'];
    $columnSortOrder = $order_arr[0]['dir'];

    $searchValue = $search_arr['value'];
    $totalRecords = user_notification::select('count(*) as allcount')->count();
    $totalRecordswithFilter = user_notification::select('count(*) as allcount')->where('title', 'like', '%' . $searchValue . '%')->count();

    $records = user_notification::orderBy($columnName, $columnSortOrder)
      ->where(function ($query) use ($searchValue) {
        $query->where('title', 'like', '%' . $searchValue . '%');
      })
      ->skip($start)
      ->take($rowperpage)
      ->get();


    $data_arr = array();
    foreach ($records as $record) {
      $data_arr[] = array(
        "not_id" => $record->not_id,
        "title" => $record->title,
        "message" => $record->message,
        "created_at" => $record->created_at,
        "action" => ''
      );
    }

    $response = array(
      "draw" => intval($draw),
      "iTotalRecords" => $totalRecords,
      "iTotalDisplayRecords" => $totalRecordswithFilter,
      "aaData" => $data_arr
    );

    echo json_encode($response);
    exit;
  }


  // Add Notification
  public function addNotification()
  {
    return view('content.apps.notifications-add');
  }

  public function saveNotification(Request $request)
  {
    $rules = [
      'title' => 'required',
      'message' => 'required'
    ];

    $customMessages = [
      'title.required' => 'Please enter title.',
      'message.required' => 'Please enter message.'
    ];

    $this->validate($request, $rules, $customMessages);
    $this->sendNotification($request);
    $notification = new user_notification([
      'title' => $request->title,
      'message' => $request->message,
    ]);
    $notification->save();

    return redirect()->route('notifications-list')->with('message', 'Notification added successfully');
  }


  public function sendNotification($request)
  {
    $firebase = (new Factory)
      ->withServiceAccount(config_path('firebase_credentials.json'));

    // Get the Firebase Messaging instance
    $messaging = $firebase->createMessaging();

    $users = User::all();
    $allDeviceTokens = [];
    foreach ($users as $user) {
      $deviceToken = $user->device_token;
      if ($deviceToken) {
        $allDeviceTokens[] = $deviceToken;
      }
    }

    $message = CloudMessage::fromArray([
      'notification' => [
        'title' => $request->title,
        'body' => $request->message,
      ],
      'data' => [
        'key' => 'value',
      ]
    ]);

    // Send the message to all device tokens in bulk
    $report = $messaging->sendMulticast($message, $allDeviceTokens);
  }

  // Delete Notification
  public function deleteNotification($id)
  {
    $notification = user_notification::find($id);

    if (!$notification) {
      return response()->json(['success' => false]);
    }

    $notification->delete();
    return response()->json(['success' => true]);
  }
}
