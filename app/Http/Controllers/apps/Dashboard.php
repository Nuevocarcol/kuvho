<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\User;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Dashboard extends Controller
{
  public function index()
  {
    $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $totalPostsbyMonth = array_fill_keys($monthNames, 0);
    $totalStorybyMonth = array_fill_keys($monthNames, 0);

    $data = User::select('id', 'created_at')->get()->groupBy(function ($data) {
      return Carbon::parse($data->created_at)->format('M');
    });

    // Assuming you have 'posts' and 'story' tables with 'date' and 'time' columns respectively
    $posts = DB::table('posts')->get();
    $stories = DB::table('story')->get();

    // Process posts
    foreach ($posts as $post) {
      if (isset($post->create_date)) {
        // Assuming create_date is in milliseconds since Unix epoch
        $date = Carbon::createFromTimestamp($post->create_date / 1000);
        $monthName = $date->format('M');
        $totalPostsbyMonth[$monthName]++;
      }
    }

    // Process stories
    foreach ($stories as $story) {
      if (isset($story->create_date)) {
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $story->create_date);
        $monthName = $date->format('M');
        $totalStorybyMonth[$monthName]++;
      }
    }

    $monthCountData = [];
    foreach ($monthNames as $month) {
      $monthCountData[] = [
        'x' => $month,
        'Posts' => $totalPostsbyMonth[$month],
        'Story' => $totalStorybyMonth[$month],
      ];
    }

    // Total Posts
    $posts = Post::all();
    $totalposts = count($posts);

    // Total Likes
    $likes = Like::all();
    $totallikes = count($likes);

    // Total Comments
    $comments = Comment::all();
    $totalcomments = count($comments);

    // Total Stories
    $stories = Story::all();
    $totalstories = count($stories);

    // Total Bookmarks
    $bookmarks = Bookmark::all();
    $totalbookmarks = count($bookmarks);

    $months = [];
    $monthCount = [];

    foreach ($monthNames as $monthName) {
      $months[] = $monthName;
      $monthCount[] = isset($data[$monthName]) ? count($data[$monthName]) : 0;
    }

    // Total Users
    $users = User::all();
    $totalUsers = count($users);
    return view('content.apps.dashboard', [
      'data' => $data,
      'months' => $months,
      'monthCount' => $monthCount,
      'monthCountData' => $monthCountData,
      'users' => $users,
      'totalUsers' => $totalUsers,
      'totalposts' => $totalposts,
      'totallikes' => $totallikes,
      'totalcomments' => $totalcomments,
      'totalstories' => $totalstories,
      'totalbookmarks' => $totalbookmarks,
    ]);
  }
}
