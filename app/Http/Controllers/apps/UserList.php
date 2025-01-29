<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserList extends Controller
{
  public function index()
  {
    $users = User::all();
    return view('content.apps.app-user-list', compact('users'));
  }


  // Datatables
  public function getUsersData(Request $request)
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
    $totalRecords = User::select('count(*) as allcount')->count();
    $totalRecordswithFilter = User::select('count(*) as allcount')->where('username', 'like', '%' . $searchValue . '%')->count();

    $records = User::orderBy($columnName, $columnSortOrder)
      ->where(function ($query) use ($searchValue) {
        $query->where('username', 'like', '%' . $searchValue . '%');
      })
      ->skip($start)
      ->take($rowperpage)
      ->get();

    $data_arr = array();
    foreach ($records as $record) {
      $data_arr[] = array(
        "id" => $record->id,
        "username" => $record->username,
        "email" => $record->email,
        "phone" => $record->phone,
        "profile_pic" => $record->profile_pic,
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


  // Delete User
  public function deleteUser($id)
  {
    User::find($id)->delete();
    return response()->json(['message' => 'User deleted successfully', 'id' => $id]);
  }
}
