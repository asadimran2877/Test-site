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
Route::group(config('addons.route_group.authenticated.admin'), function() {
    Route::namespace('Admin')->group(function() {

        Route::get('remittances', 'RemittanceController@index')->middleware(['permission:view_remittance']);
        Route::get('remittances/edit/{id}', 'RemittanceController@edit');
        Route::post('remittances/update', 'RemittanceController@update');
        Route::get('remittances/user_search', 'RemittanceController@remittancesUserSearch');
        Route::get('remittances/csv', 'RemittanceController@remittanceCsv');
        Route::get('remittances/pdf', 'RemittanceController@remittancePdf');
    
    });
});

Route::group(config('addons.route_group.authenticated.user'), function() {

    // Remittance - Without Suspend Middleware
    Route::group(['namespace' => 'Users', 'prefix' => 'remittance', 'middleware' => ['permission:manage_remittance', 'check-user-suspended']], function () {
        Route::get('/index', 'RemittanceController@remittance');
        Route::match(array('GET', 'POST'), 'recepient-details', 'RemittanceController@remittanceDetails')->name('recepient.details');
        Route::match(array('GET', 'POST'), 'delivered/details', 'RemittanceController@deliveredDetails')->name('delivered.details');
        Route::match(array('GET', 'POST'), 'transfer-summery', 'RemittanceController@transferSummery')->name('transfer.summery');

        Route::get('remittanceRedirectTo', 'RemittanceController@remittanceRedirectTo');
        

        //Stripe
        Route::get('stripe_payment', 'RemittanceController@stripePayment');
        Route::post('stripe-make-payment', 'RemittanceController@stripeMakePayment');
        Route::post('stripe-confirm-payment', 'RemittanceController@stripeConfirm');
        Route::get('stripe-payment/success', 'RemittanceController@stripePaymentSuccess')->name('remittance.stripe.success');

        //PayPal
        Route::get('payment_success', 'RemittanceController@paypalRemittancePaymentConfirm');
        Route::get('payment_cancel', 'RemittanceController@paymentCancel');
        Route::get('paypal-payment/success/{amount}', 'RemittanceController@paypalRemittancePaymentSuccess')->name('remittance.paypal.success');




        Route::get('get-currency-related-data', 'RemittanceController@getCurrencyRelatedData');
        Route::get('get-calculated-values', 'RemittanceController@getCalculatedValues');
        Route::get('get-send-currency-related-data', 'RemittanceController@getSendCurrencyRelatedData');
        Route::get('get-received-currency-related-data', 'RemittanceController@getReceivedCurrencyRelatedData');
        Route::get('get-send-min-max-amount', 'RemittanceController@getSendMinMaxAmount');
        Route::get('get-calculated-values', 'RemittanceController@getCalculatedValues');
        Route::post('recepient-email-validation-check', 'RemittanceController@recepientEmailCheck');

    
    
    });
    Route::group(['namespace' => 'Users', 'prefix' => 'remittance'], function() {

        Route::get('remittance-money/print/{id}', 'RemittanceController@remittancePrintPdf');
    });
});



//Submit remittance form from home page
Route::post('frontend/remittance', 'FrontendRemittanceController@homePageRemittance');
Route::get('frontend/remittance', 'FrontendRemittanceController@homePageRemittance');

Route::prefix('remittance')->group(function() {

    // Unauthenticated User Remittance//
    Route::get('get-currency-related-data', 'Users\RemittanceController@getCurrencyRelatedData');
    Route::get('get-send-currency-related-data', 'Users\RemittanceController@getSendCurrencyRelatedData');
    Route::get('get-received-currency-related-data', 'Users\RemittanceController@getReceivedCurrencyRelatedData');
    Route::get('get-send-min-max-amount', 'Users\RemittanceController@getSendMinMaxAmount');
    Route::get('get-calculated-values', 'Users\RemittanceController@getCalculatedValues');
});
