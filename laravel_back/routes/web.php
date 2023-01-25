<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// AUTH
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Auth::routes(['register' => false]);
Route::get('email/verify', 'Http\Controllers\Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}', 'Http\Controllers\Auth\VerificationController@verify')->name('verification.verify');
Route::get('email/resend', 'Http\Controllers\Auth\VerificationController@resend')->name('verification.resend');

// ADMIN
Route::group([
    'prefix'     => 'admin',
    'namespace'  => 'Http\Controllers\Admin',
    'as'         => 'admin.',
    'middleware' => 'auth',
], function () {
    Route::get('/', 'IndexController@index')->name('index');
    // обратная связь
    // Route::resource('feedbacks', 'FeedbackController');
    // страницы
    Route::resource('pages', 'PageController');
    Route::resource('page-home','Pages\PageHomeController');
    Route::resource('page-events','Pages\PageEventsController');
    Route::resource('page-about-edit','Pages\PageAboutController')->only('index', 'edit', 'update');

    Route::resource('contacts', 'ContactController');
    Route::resource('groupes', 'GroupController');
    // пользователи
    Route::resource('users', 'UserController');
    Route::get('user-list', 'UserController@showUserList');
    Route::get('users/remove/all-devices', 'UserController@removeAllDevices');
    Route::get('users/{id}/remove-device/{deviceIdx}', 'UserController@removeDevice');
    Route::get('users/{id}/info/{simplified}', 'UserController@showUserInfo');
    Route::resource('statistic', 'UserStatisticController');
    Route::resource('roles', 'RoleController');
    Route::resource('permissions', 'PermissionController');
    // courses
    Route::resource('courses', 'CourseController');
    Route::put('courses-fast-update/{id}', 'CourseController@fastUpdate');
    // topics
    Route::resource('topics', 'TopicController');
//   Route::get('topics-reconvert-promo/{id}', 'TopicController@reconvertPromoToSec');
    // lessons
    Route::resource('lessons', 'LessonController');
    Route::get('lessons-reconvert-mp4-to-hls/{id}', 'LessonController@reconvertMp4ToHls');
    Route::get('lessons-types-to-hls/{id}', 'LessonController@typesToHls');
    // gallery
    Route::resource('gallery', 'GalleryController');
    Route::get('gallery-reconvert-mp4-to-hls/{id}', 'GalleryController@reconvertMp4ToHls');
    // events
    Route::resource('events', 'EventController');
    Route::put('events-fast-update/{id}', 'EventController@fastUpdate');
    //dates
    Route::resource('dates', 'DateController');
    Route::get('dates/{id}/get-customers-list/{type}', 'DateController@getCustomersList');
    Route::get('dates-check-seats', 'DateController@checkSeats');
    // tests
    Route::resource('tests','TestsController');
    // test questions
    Route::resource('test_questions','TestQuestionsController');
    // chat messages
    Route::resource('chat','ChatController');
    Route::get('chat-index','ChatController@indexJSON');
    Route::get('chat/{id}/rooms','ChatController@showRooms');
    // chat messages
    Route::post('messages', 'MessageController@store');
    Route::post('messages/{shouldBroadcastToCurrentUser}/{shouldNotify}', 'MessageController@store');
    Route::put('messages/{id}', 'MessageController@update');
    Route::delete('messages/{id}', 'MessageController@destroy');
    Route::delete('messages/{id}/{shouldBroadcastToCurrentUser}', 'MessageController@destroy');

    // streams
    Route::resource('streams','StreamController');
    Route::put('streams-fast-update/{id}', 'StreamController@fastUpdate');
    // feedback
    Route::resource('feedback','FeedbackController');
    // promocodes
    Route::resource('promocodes','PromocodeController');
    Route::get('generate-promocode','PromocodeController@generate');
    Route::get('promocodes-get-items/App/{type}','PromocodeController@getItems');
    // orders
    Route::resource('orders','OrderController');
    Route::get('orders/{invoiceId}/refresh-invoice-files','OrderController@refreshInvoiceFiles');
    Route::resource('video-orders','VideoOrderController');
    Route::get('video-orders/{invoiceId}/refresh-invoice-files','VideoOrderController@refreshInvoiceFiles');
    Route::get('video-orders-fresh-invoices','VideoOrderController@freshInvoices');

    // settings
    Route::resource('/settings','SettingController');
    Route::resource('/social_links','SocialLinksController');
    Route::get('/currency','SettingController@currency')->name('currency.index');
    Route::put('/currency','SettingController@updateCurrency')->name('currency.update');
    Route::get('/address','SettingController@address')->name('address.index');
    Route::put('/address','SettingController@addressUpdate')->name('address.update');

    // медиа-файлы
    Route::post('uploader', 'UploadController@uploader')->name('uploader');
    Route::post('browser',  'UploadController@browser')->name('browser');
});

// Utilities
Route::group([
    'prefix'     => 'admin/utility',
    'namespace'  => 'Services',
    'as'         => 'admin.utility',
    'middleware' => 'auth'
], function () {
    Route::get('slug/{input}', 'Utilities@slug');
});

Route::group([
    'middleware' => ['web', 'xss-sanitizer'],
], function () {
    // PAGES
    Route::get('/', 'Http\Controllers\PageController@index')->name('index');
    //LOGOUT
    Route::get('/logout', 'Http\Controllers\Auth\LoginController@logout')->name('logout');
});

Route::group([
    'prefix'     => 'media',
    'middleware' => 'auth'
], function () {
    Route::get('company_invoices/{fileName}', function (string $fileName) {
        if (Auth::user()) {
            if (Auth::user()->role_id === 1) {
                return response()->file(storage_path("media/invoices_company/{$fileName}"));
            } else {
                return response()->json('Access denied. Your account has not admin priviligies', 403);
            }
        } else {
            return response()->json('Access denied', 401);
        }
    });
});

//REDIRECTS
Route::get('home', function () {
    return redirect('/');
})->name('home');

//Route::post('/broadcasting/auth', function (Request $request) {
//   Log::debug('BROADCAST AUTH');
//   Log::debug($request->all());
////   if ($request->channel_name == 'users') {
//      return Broadcast::auth($request);
////   }
//})->middleware('auth');
