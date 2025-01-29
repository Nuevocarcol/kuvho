<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Like;
use Illuminate\Http\Request;

class LikesController extends Controller
{
  public function index($postId)
  {
    // Fetch comments along with associated user details
    $likes = Like::where('post_id', $postId)
      ->with('user:id,username,profile_pic,created_at')
      ->get();

    foreach ($likes as $like) {
      $timestamp = $like->date / 1000;
      $like->formatted_created_at = date('d, F Y', $timestamp);
    }

    return response()->json($likes);
  }
}
