<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\Request;

class StoriesController extends Controller
{
  public function index()
  {
    $stories = Story::all();
    return view('content.apps.stories-list', compact('stories'));
  }


  // Datatables
  public function getStoriesData(Request $request)
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
    $totalRecords = Story::select('count(*) as allcount')->count();
    $totalRecordswithFilter = Story::select('count(*) as allcount')->where('user_id', 'like', '%' . $searchValue . '%')->count();
    // Inside the getStoriesData method
    $records = Story::join('users', 'story.user_id', '=', 'users.id')
      ->select('story.story_id', 'users.username', 'users.email', 'users.profile_pic', 'story.create_date', 'story.url')
      ->orderBy($columnName, $columnSortOrder)
      ->where(function ($query) use ($searchValue) {
        $query->where('users.username', 'like', '%' . $searchValue . '%');
      })
      ->skip($start)
      ->take($rowperpage)
      ->get();

    $data_arr = array();
    foreach ($records as $record) {
      $data_arr[] = array(
        "story_id" => $record->story_id,
        "username" => $record->username,
        "email" => $record->email,
        "profile_pic" => $record->profile_pic,
        "create_date" => date('m-d-Y', strtotime($record->create_date)),
        "url" => $record->url,
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

  // Delete Story
  public function deleteStories($id)
  {
    Story::find($id)->delete();
    return response()->json(['message' => 'Story deleted successfully', 'id' => $id]);
  }
}
