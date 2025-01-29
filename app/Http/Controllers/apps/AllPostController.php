<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AllPostController extends Controller
{
  public function index(Request $request)
  {
    $search = $request->input('search');

    $postsQuery = Post::query();

    if ($search) {
      // Search by post content or any other criteria
      $postsQuery->where('username', 'LIKE', '%' . $search . '%');
    }

    // Join posts table with users table to get user details
    $postsQuery->join('users', 'posts.user_id', '=', 'users.id')
      ->select('posts.*', 'users.username', 'users.profile_pic', 'users.email', 'users.created_at as user_created_at');

    // Left join with likes table to get total likes for each post
    $postsQuery->leftJoin(DB::raw('(SELECT post_id, COUNT(like_id) AS total_likes FROM likes GROUP BY post_id) AS post_likes'), 'posts.post_id', '=', 'post_likes.post_id')
      ->selectRaw('posts.*, users.username, users.profile_pic, users.email, users.created_at as user_created_at, COALESCE(post_likes.total_likes, 0) as total_likes');

    // Left join with comments table to get total comments for each post
    $postsQuery->leftJoin(DB::raw('(SELECT post_id, COUNT(comment_id) AS total_comments FROM comments GROUP BY post_id) AS post_comments'), 'posts.post_id', '=', 'post_comments.post_id')
      ->selectRaw('posts.*, users.username, users.profile_pic, users.email, users.created_at as user_created_at, COALESCE(post_likes.total_likes, 0) as total_likes, COALESCE(post_comments.total_comments, 0) as total_comments');

    $posts = $postsQuery->latest()->paginate(12);
    $postsQuery->addSelect('text');

    // Convert the create_date to a timestamp
    foreach ($posts as $post) {
      $timestamp = $post->create_date / 1000;
      $post->formatted_created_at = date('Y-m-d', $timestamp);
    }

    return view('content.apps.allpost-list', compact('posts', 'search'));
  }



  // Delete All Post
  public function deleteAllPost($id)
  {
    $post = Post::find($id);

    if (!$post) {
      return response()->json(['success' => false]);
    }

    $post->delete();
    return response()->json(['success' => true]);
  }
}
