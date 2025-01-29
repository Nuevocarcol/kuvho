<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\StoryController;
use App\Http\Controllers\Api\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

 Route::post('post_by_user', [UserController::class, 'post_by_user']);
 Route::post('reels_by_user', [UserController::class, 'reels_by_user']);
 Route::post('tags_by_user', [UserController::class, 'tags_by_user']);
 Route::post('user_data', [UserController::class, 'user_data']);
 Route::post('social_login', [AuthController::class, 'social_login']);
 Route::get('get_all_settings', [UserController::class, 'get_all_settings']);

 
 Route::post('my_followers', [FollowController::class, 'my_followers']);
 Route::post('my_following', [FollowController::class, 'my_following']);
 Route::get('delete_story', [StoryController::class, 'delete_story']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




Route::post('login', [AuthController::class, 'login']);
Route::post('username_email_check', [AuthController::class, 'username_email_check']);
Route::post('forget_pass', [UserController::class, 'forget_pass']);

Route::middleware('auth:api')->group(function () {
    Route::post('register_new', [AuthController::class, 'register_new']);
    Route::post('user_date', [AuthController::class, 'user_date']);

    Route::post('all_post_by_user', [PostController::class, 'index']);
    Route::post('add_post', [PostController::class, 'store']);
    Route::post('edit_post', [PostController::class, 'update']);
    Route::post('post_like', [PostController::class, 'post_like']);
    Route::post('likes_by_post', [PostController::class, 'likes_by_post']);
    Route::post('add_comment', [PostController::class, 'add_comment']);
    Route::post('delete_comment', [PostController::class, 'delete_comment']);
    Route::post('comments_by_post', [PostController::class, 'comments_by_post']);
    Route::get('get_all_latest_post', [PostController::class, 'get_all_latest_post']);
    Route::post('bookmarkPost', [PostController::class, 'bookmarkPost']);
    Route::post('delete_bookmarkpost', [PostController::class, 'delete_bookmarkpost']);
    Route::get('trending_post', [PostController::class, 'trending_post']);
    Route::post('get_post_details', [PostController::class, 'get_post_details']);
    Route::post('search_post', [PostController::class, 'search_post']);
    Route::post('get_user_bookmark_post', [PostController::class, 'get_user_bookmark_post']);
    Route::post('notification_list', [PostController::class, 'notification_list']);
    Route::post('add_reel', [PostController::class, 'add_reel']);
    Route::post('get_all_reels', [PostController::class, 'get_all_reels']);
    Route::post('add_reel_comment', [PostController::class, 'add_reel_comment']);
    
     Route::post('like_post', [PostController::class, 'like_post']);
     Route::post('unlike_post', [PostController::class, 'unlike_post']);
     
     Route::post('like_reels', [PostController::class, 'like_reels']);
     Route::post('unlike_reels', [PostController::class, 'unlike_reels']);
     Route::post('get_reel_details', [PostController::class, 'get_reel_details']);
     Route::post('comments_by_reel', [PostController::class, 'comments_by_reel']);


    Route::get('get_all_user', [UserController::class, 'show']);
   
    Route::post('search_users', [UserController::class, 'search_users']);
    Route::post('user_delete', [UserController::class, 'user_delete']);
    Route::post('change_password', [UserController::class, 'change_password']);
   
    Route::post('users_filter', [UserController::class, 'users_filter']);
    Route::post('add_comment_report', [UserController::class, 'add_comment_report']);
    Route::post('filter', [UserController::class, 'filter']);
    Route::post('all_post_by_user_pagination', [UserController::class, 'all_post_by_user_pagination']);
    Route::post('profile_block', [UserController::class, 'profile_block']);
    Route::post('profile_unblock', [UserController::class, 'profile_unblock']);
    Route::post('posts_report', [UserController::class, 'posts_report']);
    Route::post('posts_unblock', [UserController::class, 'posts_unblock']);
    Route::post('user_report', [UserController::class, 'user_report']);
    Route::post('get_setting', [UserController::class, 'get_setting']);
    Route::post('user_update_devicetoken', [UserController::class, 'user_update_devicetoken']);
   

    Route::post('follow', [FollowController::class, 'follow']);
    // Route::get('my_followers', [FollowController::class, 'my_followers']);
    // Route::get('my_following', [FollowController::class, 'my_following']);
    Route::post('follow_user', [FollowController::class, 'follow_user']);
    Route::post('unfollow_user', [FollowController::class, 'unfollow_user']);


    Route::post('add_story', [StoryController::class, 'add_story']);
    Route::post('get_story_by_user', [StoryController::class, 'get_story_by_user']);
    
    Route::post('chat_api', [ChatController::class, 'chat_api']);
    Route::post('message_list', [ChatController::class, 'message_list']);
    Route::post('user_chat_list', [ChatController::class, 'user_chat_list']);
    Route::post('videoCall', [ChatController::class, 'videoCall']);
    Route::post('audioCall', [ChatController::class, 'audioCall']);
    Route::post('get_call_list', [ChatController::class, 'get_call_list']);
    Route::post('call_cut_from_user', [ChatController::class, 'call_cut_from_user']);
    Route::post('cut_call', [ChatController::class, 'cut_call']);
    
    Route::post('user_online', [UserController::class, 'user_online']);
    Route::post('total_unread_messages', [ChatController::class, 'total_unread_messages']);
   


});


