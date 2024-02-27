<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Route::group(
    [
        'prefix' => \Mcamara\LaravelLocalization\Facades\LaravelLocalization::setLocale(),
        'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]
    ], function(){

    Route::get('/' , function (){
        return view('admin.index');
    })->name('ahmed');
    Route::prefix('admin')->name('admin.')->group(function (){

        Route::prefix('users')->name('user.')->controller(\App\Http\Controllers\Admin\User\UserController::class)->group(function (){
            Route::get('/' , 'index')->name('index');
            Route::get('/index_ban' , 'index_ban')->name('index_ban');
            Route::get('/index_verified' , 'index_verified')->name('index_verified');

            Route::get('/getdata' , 'getdata')->name('getdata');
            Route::get('/getbanaccounts' , 'getbanaccounts')->name('getbanaccounts');
            Route::get('/getverifiedaccounts' , 'getverifiedaccounts')->name('getverifiedaccounts');

            Route::post('/store' , 'store')->name('store');
            Route::post('/update' , 'update')->name('update');
            Route::post('/account_verification' , 'account_verification')->name('account_verification');
            Route::post('/ban' , 'ban')->name('ban');
            Route::post('/temporary_ban' , 'temporary_ban')->name('temporary_ban');
            Route::get('/getfollowers' , 'getfollowers')->name('getfollowers');
            Route::get('/getposts' , 'getposts')->name('getposts');
            Route::get('/details/{id}' , 'details')->name('details');
        });

        Route::prefix('posts')->name('post.')->controller(\App\Http\Controllers\Admin\Post\PostController::class)->group(function (){
            Route::get('/' , 'index')->name('index');
            Route::get('/getdata' , 'getdata')->name('getdata');
            Route::get('/getcomments' , 'getcomments')->name('getcomments');
            Route::post('/deletecomment' , 'deletecomment')->name('deletecomment');
            Route::get('/getlikes' , 'getlikes')->name('getlikes');
            Route::post('/update' , 'update')->name('update');
            Route::post('/delete' , 'delete')->name('delete');
        });

        Route::prefix('reels')->name('reels.')->controller(\App\Http\Controllers\Admin\Reels\ReelsController::class)->group(function (){
            Route::get('/' , 'index')->name('index');
            Route::get('/getdata' , 'getdata')->name('getdata');
            Route::get('/getcomments' , 'getcomments')->name('getcomments');
            Route::post('/deletecomment' , 'deletecomment')->name('deletecomment');
            Route::get('/getlikes' , 'getlikes')->name('getlikes');
            Route::post('/update' , 'update')->name('update');
            Route::post('/delete' , 'delete')->name('delete');
        });

        Route::prefix('admins')->name('admin.')->controller(\App\Http\Controllers\Admin\Admin\AdminController::class)->group(function (){

        });

        Route::prefix('comments')->name('comment.')->controller(\App\Http\Controllers\Admin\Comment\CommentController::class)->group(function (){
            Route::get('/' , 'index')->name('index');
            Route::get('/getdata' , 'getdata')->name('getdata');
            Route::get('/getcomments' , 'getcomments')->name('getcomments');
            Route::get('/details/{id}' , 'details')->name('details');
            Route::post('/update' , 'update')->name('update');
            Route::post('/delete' , 'delete')->name('delete');
        });

    });


    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    require __DIR__.'/auth.php';
});






















