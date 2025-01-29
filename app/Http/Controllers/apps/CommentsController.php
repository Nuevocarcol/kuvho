<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
  public function index($postId)
  {
    // Fetch comments along with associated user details
    $comments = Comment::where('post_id', $postId)
      ->with('user:id,username,profile_pic,created_at')
      ->get();

    foreach ($comments as $comment) {
      $timestamp = $comment->date / 1000;
      $comment->formatted_created_at = date('d, F Y', $timestamp);
    }

    return response()->json($comments);
  }
}
