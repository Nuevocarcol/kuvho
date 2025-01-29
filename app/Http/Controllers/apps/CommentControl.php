<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class CommentControl extends Controller
{
  public function index()
  {
    $comments = Comment::all();
    return view('content.apps.comments-list', compact('comments'));
  }

  // Datatables
  public function getCommentData(Request $request)
  {
    $draw = $request->get('draw');
    $start = $request->get("start");
    $rowperpage = $request->get("length");

    $searchValue = $request->get('search')['value'];

    $query = Comment::query();

    // Apply search filter
    if (!empty($searchValue)) {
      $query->where('text', 'like', '%' . $searchValue . '%');
    }

    $totalRecords = $query->count();

    $records = $query->orderBy('date', 'desc')
      ->skip($start)
      ->take($rowperpage)
      ->get();

    $data_arr = [];
    foreach ($records as $record) {
      $commentByuser = User::find($record->user_id);
      $blockedPost = Post::find($record->post_id);

      $commentUserName = $commentByuser ? $commentByuser->username : '';
      $commentUserEmail = $commentByuser ? $commentByuser->email : '';
      $commentUserPic = $commentByuser ? $commentByuser->profile_pic : '';

      $postUser = '';
      $postUserName = '';
      $postUserEmail = '';
      $postUserPic = '';
      if ($blockedPost) {
        $postUser = User::find($blockedPost->user_id);
        $postUserName = $postUser ? $postUser->username : '';
        $postUserEmail = $postUser ? $postUser->email : '';
        $postUserPic = $postUser ? $postUser->profile_pic : '';
      }

      $date = date('m-d-Y', $record->date / 1000);

      $data_arr[] = [
        "comment_id" => $record->comment_id,
        "commentUserName" => $commentUserName,
        "commentUserEmail" => $commentUserEmail,
        "commentUserPic" => $commentUserPic,
        "postUserName" => $postUserName,
        "postUserEmail" => $postUserEmail,
        "postUserPic" => $postUserPic,
        "text" => $record->text,
        "date" => $date,
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

  // Delete Comment
  public function deleteComment($id)
  {
    Comment::find($id)->delete();
    return response()->json(['message' => 'Comment deleted successfully', 'id' => $id]);
  }
}
