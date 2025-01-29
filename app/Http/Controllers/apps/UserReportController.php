<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User_report;
use Illuminate\Http\Request;

class UserReportController extends Controller
{
  public function index()
  {
    return view('content.apps.userreport-list');
  }

  // Datatables
  public function getUserReportData(Request $request)
  {
    $draw = $request->get('draw');
    $start = $request->get("start");
    $rowperpage = $request->get("length");

    $searchValue = $request->get('search')['value'];

    $query = User_report::query();

    // Apply search filter
    if (!empty($searchValue)) {
      $query->where('report_text', 'like', '%' . $searchValue . '%');
    }

    $totalRecords = $query->count();

    $records = $query->orderBy('created_date', 'desc')
      ->skip($start)
      ->take($rowperpage)
      ->get();

    $data_arr = [];
    foreach ($records as $record) {
      $reportByUser = User::find($record->reportByUserId);
      $reportedUser = User::find($record->reportedUserId);

      // Get usernames and images if users exist
      $reportByUserName = $reportByUser ? $reportByUser->username : '';
      $reportByUserEmail = $reportByUser ? $reportByUser->email : '';
      $reportedUserName = $reportedUser ? $reportedUser->username : '';
      $reportedUserEmail = $reportedUser ? $reportedUser->email : '';
      $reportByUserImage = $reportByUser ? $reportByUser->profile_pic : '';
      $reportedUserImage = $reportedUser ? $reportedUser->profile_pic : '';

      $data_arr[] = [
        "id" => $record->id,
        "reportByUserName" => $reportByUserName,
        "reportByUserEmail" => $reportByUserEmail,
        "reportedUserName" => $reportedUserName,
        "reportByUserImage" => $reportByUserImage,
        "reportedUserEmail" => $reportedUserEmail,
        "reportedUserImage" => $reportedUserImage,
        "report_text" => $record->report_text,
        "created_date" => date_format(date_create($record->created_date), 'm-d-Y'),
        "action" => ''
      ];
    }

    $response = [
      "draw" => intval($draw),
      "recordsTotal" => $totalRecords,
      "recordsFiltered" => $totalRecords,
      "data" => $data_arr
    ];

    return response()->json($response);
  }

  // Delete Report
  public function deleteUserReport($id)
  {
    User_report::find($id)->delete();
    return response()->json(['message' => 'User Report deleted successfully', 'id' => $id]);
  }
}
