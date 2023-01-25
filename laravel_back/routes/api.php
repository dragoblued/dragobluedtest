<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * AuthComponent ->
 * {path: '', redirectTo: 'login', pathMatch: 'full'},
 * {path: 'login', component: LoginComponent},
 * {path: 'register', component: RegisterComponent},
 * {path: 'verify', component: VerifyComponent}, -> verify/{token} ->logout
 * {path: 'email', component: EmailComponent},
 * {path: 'reset', component: ResetComponent}, -> reset/{token} ->logout
 */

//Route::group([
//   'middleware' => ['cors-all']
//], function ()
//{
//   Route::group([
//      'namespace'  => 'Api'
//   ], function () {
//      Route::any('stream/on_publish', 'StreamController@onPublish');
//   });
//});

Route::group([
    'middleware' => ['cors']
], function ()
{
    Route::group([
        'namespace'  => 'Http\Controllers\Api'
    ], function () {
        Route::get('page/{uniqueField}', 'PageController@show');
        Route::get('live-courses', 'EventController@index');
        Route::get('courses', 'CourseController@index');
        Route::get('get-courses-navs', 'CourseController@getAllNavs');
        Route::get('courses/{uniqueField}', 'CourseController@show');
        Route::get('visit-live-course/{id}', 'EventController@visitCourse');
        Route::get('visit-course/{id}', 'CourseController@visitCourse');
        Route::get('visit-topic/{id}', 'CourseController@visitTopic');
        Route::get('lessons/{uniqueField}', 'LessonController@show');
        Route::get('settings', 'SettingsController@index');
        Route::get('is-payment-enabled', 'SettingsController@isPaymentEnabled');
        Route::get('template', 'Admin\CertificateController@getTemplate');
        Route::post('feedback', 'FeedbackController@send')->middleware(['xss-sanitizer']);
        Route::get('promocode/{code}', 'PromocodeController@show');

//      Route::get('paypal/{userId?}/{product?}/{productId?}', 'PaymentController@paypal')->name('api.paypal');
//      Route::get('cancel', 'PaymentController@cancel')->name('api.paypal.cancel');
//      Route::get('paypal/success', 'PaymentController@success')->name('api.paypal.success');

        // archive course
//      Route::get('archive/courses/{courseId}', 'CourseController@archiveCourseMaterials')->name('archive.course');
    });

    Route::group([
        'prefix' => 'auth',
        'namespace' => 'Http\Controllers\Auth',
    ], function ()
    {
        Route::post('register','ApiRegisterController@register');
        Route::get('verification/resend/{email}','ApiRegisterController@sendVerifyEmail');
        Route::get('verify/{token}','ApiVerificationController@verify');
        Route::post('login', 'ApiLoginController@login');
        Route::post('social_login', 'SocialAuthController@login');
        Route::group([
            'middleware' => ['auth:api']
        ], function ()
        {
            Route::get('logout', 'ApiLoginController@logout');
            Route::get('check-auth', function () {
                return response()->json('Access allowed');
            });
        });
    });
    Route::group([
        'namespace'  => 'Http\Controllers\Api'
    ], function () {
    Route::get('orders/{invoiceId}/refresh-invoice-files','OrderController@refreshInvoiceFiles');
    });
    Route::group([
        'prefix' => 'password',
        'namespace' => 'Http\Controllers\Auth',
    ], function ()
    {
        Route::post('email', 'ApiForgotPasswordController@email');
        Route::get('reset/{token}', 'ApiResetPasswordController@reset');
        Route::post('reset', 'ApiResetPasswordController@update');
    });

    Route::get('hls/{token}/{folder}/{fileName}', function (string $token, string $folder, string $fileName, Request $request) {
        Log::debug($request->headers->get('User-Agent'));
        if ($token && $request->headers->get('User-Agent')) {
            if (strpos($request->headers->get('User-Agent'), 'Mac OS') !== false) {
                $request->headers->set('Authorization', 'Bearer '.$token);
            } else {
                return response()->json('Access denied', 401);
            }
        }
        if (Auth::guard('api')->check()) {
            return response()->file(public_path("media/hls/{$folder}/{$fileName}"));
        } else {
            return response()->json('Access denied', 401);
        }
    });

    Route::group([
        'namespace'  => 'Http\Controllers\Api',
        'middleware' => 'auth:api'
    ], function () {
        Route::get('users/{uniqueField}', 'Admin\UserController@show');
        Route::put('users/{uniqueField}', 'Admin\UserController@update')->middleware(['xss-sanitizer']);
        Route::get('get-user-progress/{id}', 'Admin\UserController@getProgress');
        Route::get('has-permissions/{id}', 'Admin\UserController@hasPermissions');
        Route::post('store-user-progress/{uniqueField}', 'Admin\UserController@storeProgress');
        Route::post('increment-lesson-view-count/{userId}/{lessonId}', 'Admin\UserController@incrementViewCountForUser');
        Route::get('notification-outdated/{id}', 'Admin\NotificationController@makeOutdated');
        Route::post('booking', 'PaymentController@handleBooking');
        Route::post('set-booking-item-count', 'PaymentController@setBookingItemCount');
        Route::post('check-basket-items', 'PaymentController@checkBasket');
        Route::post('payment/stripe/create-checkout-session', 'PaymentController@stripeCreateSession');
        Route::post('payment/paypal/create-checkout-session/{sessionId}', 'PaymentController@paypalCreateSession');
        Route::get('payment/{paymentPlatform}/confirm', 'PaymentController@confirmPayment');
        Route::post('payment/get-items-free', 'PaymentController@getItemsFree');
        Route::get('test/{courseId}', 'Admin\TestController@findByCourseId');
        Route::resource('test-result', 'Admin\TestResultController');
        Route::get('finish-test/{id}', 'Admin\TestResultController@finish');

        Route::get('admin/messages', 'Admin\MessageController@index');
        Route::get('admin/marked-messages', 'Admin\MessageController@indexMarked');
        Route::post('admin/messages', 'Admin\MessageController@store')->middleware(['xss-sanitizer']);
        Route::post('admin/messages/{shouldBroadcastToCurrentUser}/{shouldNotify}', 'Admin\MessageController@store')->middleware(['xss-sanitizer']);
        Route::put('admin/messages/{id}', 'Admin\MessageController@update')->middleware(['xss-sanitizer']);
        Route::delete('admin/messages/{id}', 'Admin\MessageController@destroy');
        Route::delete('admin/messages/{id}/{shouldBroadcastToCurrentUser}', 'Admin\MessageController@destroy');

        Route::get('admin/courses', 'Admin\CourseController@index');
        Route::get('admin/promocode/{code}', 'Admin\PromocodeController@show');
        Route::get('stream/{name}', 'Admin\StreamController@findByNameAndKey');
        Route::get('hls/{folder}/{fileName}', function (string $folder, string $fileName) {
            return response()->file(public_path("media/hls/{$folder}/{$fileName}"));
        });
//      Route::post('/broadcasting/auth', function (Request $request) {
//         Log::debug('BROADCAST API REQ');
//         Log::debug($request->get('socket_id'));
//         return Broadcast::auth($request);
//         return response()->json('Access allowed');
//      });
    });
});
