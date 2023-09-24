<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Post\PostController;

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
Route::middleware(['guest:sanctum'])->prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('profile/favorite/{uuid?}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getPostFavorite']);

    Route::get('profile/edit', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'editProfile']);
    Route::post('profile/update', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'updateProfile']);
    Route::get('profile/followers', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getMyFollowers']);
    Route::get('profile/following', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getMyFollowing']);
    Route::post('followers', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'followersPost']);
    Route::get('block', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'blockUser']);

    Route::post('post/store', [PostController::class, 'addPost']);
    Route::post('post/comment/store', [\App\Http\Controllers\Api\Post\PostController::class, 'addComment']);
    Route::post('post/reply/store', [\App\Http\Controllers\Api\Post\PostController::class, 'replyComment']);
    Route::post('post/{uuid}/favorite', [PostController::class, 'favoritePost']);

    Route::post('post/like', [\App\Http\Controllers\Api\Post\PostController::class, 'addLike']);


    Route::post('post/comment/{uuid}/favorite', [PostController::class, 'favoriteComment']);
    Route::delete('post/comment/{uuid}/delete', [\App\Http\Controllers\Api\Post\PostController::class, 'deleteComment']);
    Route::post('post/comment/{uuid}/report', [\App\Http\Controllers\Api\Post\PostController::class, 'reportComment']);
    Route::post('post/comment/{uuid}/hidden', [\App\Http\Controllers\Api\Post\PostController::class, 'hiddenComment']);
    Route::get('post/{uuid}', [\App\Http\Controllers\Api\Post\PostController::class, 'getComment']);

    Route::post('story/store', [\App\Http\Controllers\Api\Home\HomeController::class, 'addStory']);
    Route::post('story/like', [\App\Http\Controllers\Api\Home\HomeController::class, 'addLike']);
    Route::delete('story/{uuid}/delete', [\App\Http\Controllers\Api\Home\HomeController::class, 'deleteStory']);

    Route::get('story/{uuid}/{story_uuid?}', [\App\Http\Controllers\Api\Home\HomeController::class, 'getStory']);

    Route::get('home', [\App\Http\Controllers\Api\Home\HomeController::class, 'home']);

    Route::get('profile/rails/{uuid?}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getRails']);
    Route::get('profile/{uuid?}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'profile']);

    Route::get('rails', [\App\Http\Controllers\Api\Home\HomeController::class, 'rails']);

});
