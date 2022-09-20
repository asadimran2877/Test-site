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

// Unauthenticated Agent
Route::group(['prefix' => 'agent', 'middleware' => ['no_auth:agent', 'locale']], function () {
    Route::group(['namespace' => 'Agent'], function () {
        Route::get('/login', function () {
            return view('agent::agent.auth.login');
        })->name('agent');

        Route::post('agentlog', 'AgentController@authenticate');
        Route::match(['GET', 'POST'], 'forget-password', 'AgentController@forgetPassword');
        Route::get('password/resets/{token}', 'AgentController@verifyToken');
        Route::post('confirm-password', 'AgentController@confirmNewPassword');
    });
    Route::get('/info', 'AgentController@infoDetails');
});

// // Authenticated Agent
Route::group(['prefix' => 'agent', 'middleware' => ['guest:agent', 'locale']], function () {
    
    // // Agent Status Check
    Route::get('check-agent-status', 'AgentController@checkAgentStatus');
    // Route::get('check-suspended-status', 'AgentController@checkSuspendedStatus');
    // Route::get('check-inactive-status', 'AgentController@checkInactiveStatus');

    Route::group(['namespace' => 'Agent'], function () {

        //Settings
        Route::get('profile', 'AgentController@profile');
        Route::get('wallet', 'AgentController@agentWallet');
        Route::post('profile/update', 'AgentController@updateProfileInfo');
        Route::post('profile/update_password', 'AgentController@updateProfilePassword');
        Route::match(['get', 'post'], 'profile-image-upload', 'AgentController@profileImage');
        Route::get('/logout', 'AgentController@logout');

        // user in Agent View
        Route::get('user', 'UserController@list');
        Route::get('user/add', 'UserController@add');
        Route::post('user/store', 'UserController@storeUser');
        Route::get('user/view/{id}', 'UserController@show');
        Route::get('user/edit/{id}', 'UserController@edit');
        Route::post('user/update', 'UserController@updateUser');
        Route::get('user/delete/{id}', 'UserController@destroyUser');
        
        // Deposit - With Suspend Middleware
        Route::group(['middleware' => ['check-agent-suspended']], function ()
        {
            // deposit frontend
            Route::match(array('GET', 'POST'), 'deposit', 'DepositController@create');
            Route::post('deposit/get-total-fees', 'DepositController@getTotalFeesAjax');
            Route::post('deposit/success', 'DepositController@success');
            
            
            // payout frontend
            Route::match(array('GET', 'POST'), 'payout', 'WithdrawalController@create');
            Route::post('payout/get-total-fees', 'WithdrawalController@getTotalFeesAjax');
            Route::post('payout/success', 'WithdrawalController@success');
            Route::post('payout/get-payout-currency-list', 'WithdrawalController@getCurrencyList');
            Route::post('payout/verification_code', 'WithdrawalController@verrificationCode');
        });
        
        Route::post('user/email_check', 'AgentController@postEmailCheckUserCreation');
        Route::post('user/phone-check', 'AgentController@postPhoneCheckUserCreation');
        Route::post('/get-fees-limit-check', 'AgentController@getFeesLimit');
        Route::post('/search-user', 'AgentController@searchUser');

        
        // transaction frontend
        Route::get('dashboard', 'TransactionController@dashboard')->name('agent.dashboard');
        Route::match(array('GET', 'POST'), 'transaction', 'TransactionController@index');
        Route::post('get_transaction', 'TransactionController@getTransaction');

        Route::get('deposit-money/print/{id}', 'DepositController@depositPrintPdf');
        Route::get('payout-money/print/{id}', 'WithdrawalController@payoutPrintPdf');
    });
});
// Agent Ends

# Agent Module Admin section
Route::group(config('addons.route_group.authenticated.admin'), function () {

    Route::group(['namespace' => 'Admin'], function () {

        // agents
        Route::get('agents', 'AgentController@index')->middleware(['permission:view_agent']);
        Route::get('agents/create', 'AgentController@create')->middleware(['permission:add_agent']);
        Route::post('agents/store', 'AgentController@store');
        Route::get('agents/details/{id}/{test?}', 'AgentController@show');
        Route::get('agents/edit/{id}', 'AgentController@edit')->middleware(['permission:edit_agent']);
        Route::post('agents/update', 'AgentController@update');
        Route::get('agents/delete/{id}', 'AgentController@destroy')->middleware(['permission:delete_role']);
        Route::post('agents/email_check', 'AgentController@postEmailCheck');
        Route::post('agents/duplicate-phone-number-check', 'AgentController@duplicatePhoneNumberCheck');
        
        // Agent Transaction list
        Route::get('agents/transactions/{id}', 'AgentTransactionController@eachAgentTransaction');
        Route::get('agents/wallets/{id}', 'AgentTransactionController@eachAgentWallet');

        // Deposit within agents
        Route::get('agents/deposit/{id}', 'AgentTransactionController@deposit')->middleware(['permission:view_deposit']);

        // Payout within agents
        Route::get('agents/payout/{id}', 'AgentTransactionController@payout')->middleware(['permission:view_withdrawal']);
        
        // Agent Revenues------------------
        Route::get('agents/revenues/list', 'AgentRevenueController@revenueList')->middleware(['permission:view_revenue']);
        Route::get('agents/revenues/user_search', 'AgentRevenueController@revenuesUserSearch');
        Route::get('agents/revenues/csv', 'AgentRevenueController@revenueCsv');
        Route::get('agents/revenues/pdf', 'AgentRevenueController@revenuePdf');

        // Users within agents
        Route::get('agents/user/{id}', 'AgentUserController@userList')->middleware(['permission:view_user']);
        Route::get('agents/user/delete/{id}', 'AgentUserController@deleteUser')->middleware(['permission:edit_user']);

        //Admin Can deposit for a Agent
        Route::match(array('GET', 'POST'), 'agents/deposit/create/{id}', 'AgentPayController@eachAgentDeposit');
        Route::post('agents/deposit/amount-fees-limit-check', 'AgentPayController@amountFeesLimitCheck');
        Route::post('agents/deposit/success', 'AgentPayController@eachAgentDepositSuccess');

    });
});
