<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SocialMates extends Controller
{
  public function index(Request $request)
  {
    $search = $request->input('search');

    $usersQuery = User::query();
    if ($search) {
      $usersQuery->where('username', 'LIKE', '%' . $search . '%');
    }
    $users = $usersQuery->latest()->paginate(12);

    // Fetch social mates data with user details and their follower/following counts
    $followersData = Follow::select(
      'from_user',
      DB::raw('COUNT(case when status = 0 then 1 else null end) as totalfollowers')
    )
      ->groupBy('from_user')
      ->get();

    $followingData = Follow::select(
      'to_user',
      DB::raw('COUNT(case when status = 0 then 1 else null end) as totalfollowing')
    )
      ->groupBy('to_user')
      ->get();

    $socialmates = [];
    foreach ($followersData as $follower) {
      $socialmates[$follower->from_user]['totalfollowers'] = $follower->totalfollowers;
    }
    foreach ($followingData as $following) {
      $socialmates[$following->to_user]['totalfollowing'] = $following->totalfollowing;
    }

    return view('content.apps.socialmates-list', compact('users', 'socialmates', 'search'));
  }
}
