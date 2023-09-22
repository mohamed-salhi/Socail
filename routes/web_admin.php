<?php

use App\Http\Controllers\Admin\PaymentGateway\ProcessPaymentController;
use App\Http\Controllers\Admin\Places\CityController;
use App\Http\Controllers\Admin\Places\CountryController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

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
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {


        Route::middleware('auth')->prefix('admin')->group(function () {

            Route::get('/', function () {
                return redirect(route('countries.index'));
            })->name('admin.index');
            Route::controller(CityController::class)->prefix('cities')->name('cities.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{uuid}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');
            });
            Route::controller(App\Http\Controllers\Admin\Mosque\MosqueController::class)->prefix('mosques')->name('mosques.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{uuid}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');
            });
            Route::controller(CountryController::class)->prefix('countries')->name('countries.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{uuid}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');
            });
            Route::controller(\App\Http\Controllers\Admin\Intro\IntroController::class)->prefix('intros')->name('intros.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{uuid}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');

            });
//
//            Route::controller(\App\Http\Controllers\Admin\Notifications\NotificationController::class)->name('notifications.')->prefix('notifications')->group(function () {
//                Route::get('/', 'index')->name('index');
//                Route::post('/store', 'store')->name('store');
//                Route::delete('/{id}', 'destroy')->name('delete');
//                Route::get('/indexTable', 'indexTable')->name('indexTable');
//
//            });
//            Route::controller(\App\Http\Controllers\Admin\Ads\AdsController::class)->name('ads.')->prefix('ads')->group(function () {
//                Route::get('/', 'index')->name('index');
//                Route::post('/store', 'store')->name('store');
//                Route::post('/update', 'update')->name('update');
//                Route::delete('/{id}', 'destroy')->name('delete');
//                Route::get('/indexTable', 'indexTable')->name('indexTable');
//                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');
//
//            });
            Route::controller(\App\Http\Controllers\Admin\Live\LiveController::class)->name('lives.')->prefix('lives')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');

            });


            Route::controller(\App\Http\Controllers\Admin\Shahid\ShahidController::class)->name('shahids.')->prefix('shahids')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{id}', 'updateStatus')->name('updateStatus');

                Route::delete('/{uuid}', 'destroy')->name('delete');

            });
            Route::controller(\App\Http\Controllers\Admin\Setting\SettingController::class)->prefix('settings')->name('settings.')->group(function () {
                Route::get('/policies_privacy', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'policies_privacy'])->name('policies_privacy');
                Route::post('/policies_privacy', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'policies_privacy_post'])->name('policies_privacy');
                Route::get('/about_application', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'about_application'])->name('about_application');
                Route::post('/about_application', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'about_application_post'])->name('about_application');
                Route::get('/terms_conditions', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'terms_conditions'])->name('terms_conditions');
                Route::post('/terms_conditions', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'terms_conditions_post'])->name('terms_conditions');
                Route::get('/delete_my_account', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'delete_my_account'])->name('delete_my_account');
                Route::post('/delete_my_account', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'delete_my_account_post'])->name('delete_my_account');
                Route::post('/', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'post'])->name('index');
                Route::get('/', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'index'])->name('index');

            });


                 Route::controller(\App\Http\Controllers\Admin\AdminController::class)->name('managers.')->prefix('managers')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{id}', 'updateStatus')->name('updateStatus');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });

//            Route::get('/support/index/{uuid?}', [\App\Http\Controllers\Admin\Support\SupportController::class,'index'])->name('index');
//            Route::post('/support/message/send', [\App\Http\Controllers\Admin\Support\SupportController::class,'message'])->name('send_msg');
//            Route::get('/support/readMore/{uuid}', [\App\Http\Controllers\Admin\Support\SupportController::class,'readMore'])->name('admin.support.read_more');

            Route::controller(\App\Http\Controllers\Admin\Category\CategoryController::class)->prefix('categories')->name('categories.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{id}', 'updateStatus')->name('updateStatus');
                Route::delete('/{id}', 'destroy')->name('delete');
            });
            Route::controller(\App\Http\Controllers\Admin\User\UserController::class)->prefix('users')->name('users.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{id}', 'updateStatus')->name('updateStatus');
                Route::get('/country/{uuid}', 'country')->name('country');

            });




        });

    });
