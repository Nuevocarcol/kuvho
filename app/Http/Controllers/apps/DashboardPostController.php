<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class DashboardPostController extends Controller
{
  public function index()
  {
    $dashboardpost = Post::all();
    return view('content.apps.dashboard', compact('dashboardpost'));
  }

  // Datatables
  public function getDashboardPostData(Request $request)
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
    $totalRecords = Post::select('count(*) as allcount')->count();
    $totalRecordswithFilter = Post::select('count(*) as allcount')->where('user_id', 'like', '%' . $searchValue . '%')->count();
    // Inside the getStoriesData method
    $records = Post::join('users', 'posts.user_id', '=', 'users.id')
      ->select('posts.post_id', 'users.username', 'users.email', 'users.profile_pic', 'posts.create_date', 'posts.image', 'posts.text')
      ->orderBy($columnName, $columnSortOrder)
      ->where(function ($query) use ($searchValue) {
        $query->where('users.username', 'like', '%' . $searchValue . '%');
      })
      ->skip($start)
      ->take($rowperpage)
      ->get();

    $data_arr = array();
    foreach ($records as $record) {
      $create_date = date('m-d-Y', $record->create_date / 1000);
      $data_arr[] = array(
        "post_id" => $record->post_id,
        "username" => $record->username,
        "email" => $record->email,
        "profile_pic" => $record->profile_pic,
        "create_date" => $create_date,
        "text" => $record->text,
        "image" => $record->image,
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

  // Delete Dashboard Post
  public function DeleteDashboardPost($id)
  {
    Post::find($id)->delete();
    return response()->json(['message' => 'Post deleted successfully', 'id' => $id]);
  }
}
