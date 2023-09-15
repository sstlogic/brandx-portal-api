<?php

use App\Http\Controllers\CancelSubscriptionController;
use App\Http\Controllers\CheckoutPaymentController;
use App\Http\Controllers\ContactFormController;
use App\Http\Controllers\EmptyCartController;
use App\Http\Controllers\GetCheckoutDetailsController;
use App\Http\Controllers\GetPendingReservationsController;
use App\Http\Controllers\PaymentMethodsController;
use App\Http\Controllers\PendingReservationsController;
use App\Http\Controllers\RepeatingReservationDatesController;
use App\Http\Controllers\RepeatingReservationQuoteController;
use App\Http\Controllers\RepeatingScheduleSlotsController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReservationQuoteController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ResumeSubscriptionController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UpdateReservationRequestController;
use App\Http\Controllers\UpdateUserPasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserExistsController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Routing\PendingResourceRegistration;
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

if (!function_exists('uuidResourceRoute')) {
    /**
     * @param  string  $resources
     * @param  string  $controller
     *
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    function uuidResourceRoute(string $resources, string $controller): PendingResourceRegistration
    {
        $array = collect(explode('.', $resources));

        $params = $array->mapWithKeys(function ($resource) {
            return [$resource => Str::singular($resource) . ':uuid'];
        })->all();

        return Route::apiResource($resources, $controller)->parameters($params)->names(
            $array->mapWithKeys(function ($resource) {
                return [$resource => $resource . '.uuid'];
            })->all()
        );
    }
}

Route::put('/users/{user:uuid}', [UserController::class, 'update']);
// Route::middleware('auth:sanctum')->group(function () {
Route::get('/user', fn (Request $request) => new UserResource($request->user()));
Route::apiResource('users', UserController::class)->only(['show']);

Route::get('/user/booked', [UserController::class, 'asBookedUser']);

Route::get('/payment-methods/setup', [PaymentMethodsController::class, 'setup']);
Route::get('/payment-methods/default', [PaymentMethodsController::class, 'default']);
Route::post('/payment-methods/default', [PaymentMethodsController::class, 'setDefault']);
Route::apiResource('payment-methods', PaymentMethodsController::class);

Route::resource('subscriptions', SubscriptionController::class);

Route::delete('/subscription', CancelSubscriptionController::class);
Route::post('/subscription/resume', ResumeSubscriptionController::class);

Route::get('/users/{user:uuid}/subscription-price', [SubscriptionController::class, 'price']);

Route::apiResource('/booked/reservations', ReservationController::class)->names(
    [
        'index' => 'booked.reservations.index',
        'store' => 'booked.reservations.store',
        'show' => 'booked.reservations.show',
        'update' => 'booked.reservations.update',
        'destroy' => 'booked.reservations.destroy',
    ]
);
Route::post('/reservations/update', UpdateReservationRequestController::class);

uuidResourceRoute('reservations', PendingReservationsController::class);

Route::get('/users/{user:uuid}/pending-reservations', GetPendingReservationsController::class)
    ->name('users.pending-reservations');

Route::get('users/{user:uuid}/checkout/details', GetCheckoutDetailsController::class)->name('checkout.details');
Route::post('users/{user:uuid}/checkout/payment', CheckoutPaymentController::class)->name(
    'checkout.payment'
);

Route::post('empty-cart', EmptyCartController::class);
// });

Route::post('/users', [UserController::class, 'store']);

Route::apiResource('schedules', ScheduleController::class)
    ->only(['index', 'show']);

Route::get('/schedules/{schedule}/slots', [ScheduleController::class, 'slots'])
    ->name('schedule.slots');

Route::get('/schedules/{schedule}/slots/repeating', RepeatingScheduleSlotsController::class)
    ->name('schedule.slots.repeating');

Route::apiResource('resources', ResourceController::class)
    ->only(['index', 'show']);

Route::get('/resources/{resource}/slots', [ResourceController::class, 'slots'])
    ->name('resources.slots');

Route::get('/resources/{resource}/slots/recurring', [ResourceController::class, 'recurringSlots'])
    ->name('resources.slots.recurring');

Route::post('users/exists', UserExistsController::class)
    ->name('users.exists');

Route::post('/reservations/quote', ReservationQuoteController::class)->name('reservations.quote');
Route::post('/reservations/repeating/quote', RepeatingReservationQuoteController::class)
    ->name('reservations.quote.repeating');

Route::post('/reservations/repeating/dates', RepeatingReservationDatesController::class)
    ->name('reservations.dates.repeating');

Route::post('update-password', UpdateUserPasswordController::class);
Route::post('/contact', ContactFormController::class);
