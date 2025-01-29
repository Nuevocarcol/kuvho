<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Posts_report;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Post;

class PostReportController extends Controller
{
  public function index()
  {
    $postreport = Posts_report::all();
    return view('content.apps.postreport-list', compact('postreport'));
  }

  // Datatables
  public function getPostReportData(Request $request)
  {
    $draw = $request->get('draw');
    $start = $request->get("start");
    $rowperpage = $request->get("length");

    $searchValue = $request->get('search')['value'];

    $query = Posts_report::query();

    // Apply search filter
    if (!empty($searchValue)) {
      $query->where('report_text', 'like', '%' . $searchValue . '%');
    }

    $totalRecords = $query->count();

    $records = $query->orderBy('created_at', 'desc')
      ->skip($start)
      ->take($rowperpage)
      ->get();

    $data_arr = [];
    foreach ($records as $record) {
      $blockedByUser = User::find($record->blockedByUserId);
      $blockedPost = Post::find($record->blockedPostsId);

      $blockedByUserName = $blockedByUser ? $blockedByUser->username : '';
      $blockedByUserEmail = $blockedByUser ? $blockedByUser->email : '';
      $blockedByUserPic = $blockedByUser ? $blockedByUser->profile_pic : '';
      $blockedPostPic = $blockedPost ? $blockedPost->image : '';

      // If blocked post is not found, set post user details as ''
      if (!$blockedPost) {
        $postUserName = '';
        $postUserEmail = '';
        $postUserPic = '';
      } else {
        $postUser = User::find($blockedPost->user_id);
        // If post user is not found, set post user details as ''
        if (!$postUser) {
          $postUserName = '';
          $postUserEmail = '';
          $postUserPic = '';
        } else {
          $postUserName = $postUser->username;
          $postUserEmail = $postUser->email;
          $postUserPic = $postUser->profile_pic;
        }
      }

      $data_arr[] = [
        "id" => $record->id,
        "blockedByUserName" => $blockedByUserName,
        "blockedByUserEmail" => $blockedByUserEmail,
        "blockedByUserPic" => $blockedByUserPic,
        "blockedPostPic" => $blockedPostPic,
        "postUserName" => $postUserName,
        "postUserEmail" => $postUserEmail,
        "postUserPic" => $postUserPic,
        "report_text" => $record->report_text,
        "created_at" => $record->created_at->format('m-d-Y'),
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
  public function deletePostReport($id)
  {
    Posts_report::find($id)->delete();
    return response()->json(['message' => 'Post Report deleted successfully', 'id' => $id]);
  }
}
