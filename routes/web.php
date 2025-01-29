<?php

use App\Http\Controllers\apps\AgoraKey;
use App\Http\Controllers\apps\AllPostController;
use App\Http\Controllers\apps\AppSetting;
use App\Http\Controllers\apps\CommentControl;
use App\Http\Controllers\apps\CommentsController;
use App\Http\Controllers\apps\Dashboard;
use App\Http\Controllers\apps\DashboardPostController;
use App\Http\Controllers\apps\LikesController;
use App\Http\Controllers\apps\NotificationController;
use App\Http\Controllers\apps\PostReportController;
use App\Http\Controllers\apps\SettingsController;
use App\Http\Controllers\apps\SocialMates;
use App\Http\Controllers\apps\StoriesController;
use App\Http\Controllers\apps\UserReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\laravel_example\UserManagement;
use Illuminate\Notifications\Notification;
use App\Http\Controllers\Api\AppSettingController;

$controller_path = 'App\Http\Controllers';


// Main Page Route
Route::get('/auth/login-basic', $controller_path . '\authentications\LoginBasic@index')->name('auth-login-basic');
Route::get('/test', $controller_path . '\pruebadb\prueba@index')->name('prueba');
Route::post('/auth/login', $controller_path . '\authentications\LoginBasic@login')->name('auth-login');

Route::group(
  ['middleware' => ['admin']],
  function () {
    $controller_path = 'App\Http\Controllers';

    // Logout
    Route::get('/auth/logout', $controller_path . '\authentications\LoginBasic@logout')->name('auth-logout');


    // Profile
    Route::get('/pages/profile-user', $controller_path . '\pages\UserProfile@index')->name('pages-profile-user');
    Route::get('/pages/profile-edit/{id}', $controller_path . '\pages\UserProfile@editProfile')->name('pages-profile-edit');
    Route::post('/pages/profile-update/{id}', $controller_path . '\pages\UserProfile@updateProfile')->name('pages-profile-update');
    Route::get('/pages/profile-security', $controller_path . '\pages\AccountSettingsSecurity@index')->name('pages-profile-security');
    Route::get('/pages/profile-editsecurity/{id}', $controller_path . '\pages\AccountSettingsSecurity@editSecurity')->name('pages-profile-editsecurity');
    Route::post('/pages/profile-updatesecurity/{id}', $controller_path . '\pages\AccountSettingsSecurity@changePassword')->name('pages-profile-updatesecurity');



    // Dashboard
    Route::get('/', [Dashboard::class, 'index'])->name('dashboard');
    Route::get('dashboard', [Dashboard::class, 'index'])->name('dashboard');


    // Dashboard Post
    Route::get('dashboardpost-list', [DashboardPostController::class, 'index'])->name('dashboardpost-list');
    Route::get('getdashboardpostdata', [DashboardPostController::class, 'getDashboardPostData'])->name('getdashboardpostdata');
    Route::post('dashboardpost-delete/{id}', [DashboardPostController::class, 'DeleteDashboardPost'])->name('dashboardpost-delete');



    // Users
    Route::get('/app/user/list', $controller_path . '\apps\UserList@index')->name('app-user-list');
    Route::get('/app/user/getusersdata', $controller_path . '\apps\UserList@getUsersData')->name('app-user-getusersdata');
    Route::post('/app/user/userdelete/{id}', $controller_path . '\apps\UserList@deleteUser')->name('app-user-userdelete');



    // Posts
    Route::get('allpost-list', [AllPostController::class, 'index'])->name('allpost-list');
    Route::post('allpost-delete/{post_id}', [AllPostController::class, 'deleteAllPost'])->name('allpost-delete');
    Route::get('get-comments/{post_id}', [CommentsController::class, 'index'])->name('get-comments');
    Route::get('get-likes/{post_id}', [LikesController::class, 'index'])->name('get-likes');



    // Comments
    Route::get('comments-list', [CommentControl::class, 'index'])->name('comments-list');
    Route::get('getcommentdata', [CommentControl::class, 'getCommentData'])->name('getcommentdata');
    Route::post('comments-delete/{id}', [CommentControl::class, 'deleteComment'])->name('comments-delete');



    // Stories
    Route::get('stories-list', [StoriesController::class, 'index'])->name('stories-list');
    Route::get('getstoriesdata', [StoriesController::class, 'getStoriesData'])->name('getstoriesdata');
    Route::post('stories-delete/{id}', [StoriesController::class, 'deleteStories'])->name('stories-delete');



    // Followers-Following
    Route::get('socialmates-list', [SocialMates::class, 'index'])->name('socialmates-list');



    // Reports
    Route::get('postreport-list', [PostReportController::class, 'index'])->name('postreport-list');
    Route::get('getpostreportdata', [PostReportController::class, 'getPostReportData'])->name('getpostreportdata');
    Route::post('postreport-delete/{id}', [PostReportController::class, 'deletePostReport'])->name('postreport-delete');
    Route::get('userreport-list', [UserReportController::class, 'index'])->name('userreport-list');
    Route::get('getuserreportdata', [UserReportController::class, 'getUserReportData'])->name('getuserreportdata');
    Route::post('userreport-delete/{id}', [UserReportController::class, 'deleteUserReport'])->name('userreport-delete');



    // Notification
    Route::get('notifications-list', [NotificationController::class, 'index'])->name('notifications-list');
    Route::get('getnotificationdata', [NotificationController::class, 'getNotificationData'])->name('getnotificationdata');
    Route::get('notifications-add', [NotificationController::class, 'addNotification'])->name('notifications-add');
    Route::post('notifications-save', [NotificationController::class, 'saveNotification'])->name('notifications-save');
    Route::post('notifications-delete/{id}', [NotificationController::class, 'deleteNotification'])->name('notifications-delete');



    // Settings
    Route::get('appsetting-add', [AppSetting::class, 'index'])->name('appsetting-add');
    Route::post('appsetting-save', [AppSetting::class, 'saveAppSetting'])->name('appsetting-save');

    Route::get('agorakey-add', [AgoraKey::class, 'index'])->name('agorakey-add');
    Route::post('agorakey-save', [AgoraKey::class, 'saveAgoraKey'])->name('agorakey-save');

    Route::get('notificationkey-add', [SettingsController::class, 'addNotificationKey'])->name('notificationkey-add');
    Route::post('notificationkey-save', [SettingsController::class, 'saveNotificationkey'])->name('notificationkey-save');

    Route::get('privacypolicy-add', [SettingsController::class, 'addPrivacyPolicy'])->name('privacypolicy-add');
    Route::post('privacypolicy-save', [SettingsController::class, 'savePrivacyPolicy'])->name('privacypolicy-save');

    Route::get('termcondition-add', [SettingsController::class, 'addTermCondition'])->name('termcondition-add');
    Route::post('termcondition-save', [SettingsController::class, 'saveTermCondition'])->name('termcondition-save');
  });

  Route::group(['prefix' => 'dashboard/api'], function() {
    Route::get('get_all_settings', [AppSettingController::class, 'getAllSettings']);
    Route::post('update_setting', [AppSettingController::class, 'updateSetting']);
    Route::post('update_logo', [AppSettingController::class, 'updateLogo']);
});