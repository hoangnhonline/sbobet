<?php

Route::group(['middleware' => ['web']], function () {

	// Home
	Route::get('/', [
		'uses' => 'HomeController@index', 
		'as' => 'home'
	]);

	// Admin
	Route::get('admin', [
		'uses' => 'AdminController@admin',
		'as' => 'admin',
		'middleware' => 'admin'
	]);





	// User
	Route::get('user/sort/{role}', 'UserController@indexSort');

	Route::get('user/roles', 'UserController@getRoles');
	Route::post('user/roles', 'UserController@postRoles');

	Route::put('userseen/{user}', 'UserController@updateSeen');

	Route::resource('user', 'UserController');

	// Authentication routes...
	Route::get('auth/login', 'Auth\AuthController@getLogin');
	Route::post('auth/login', 'Auth\AuthController@postLogin');
	Route::get('auth/logout', 'Auth\AuthController@getLogout');
	Route::get('auth/confirm/{token}', 'Auth\AuthController@getConfirm');

	// Resend routes...
	Route::get('auth/resend', 'Auth\AuthController@getResend');

	// Registration routes...
	Route::get('auth/register', 'Auth\AuthController@getRegister');
	Route::post('auth/register', 'Auth\AuthController@postRegister');

	// Password reset link request routes...
	Route::get('password/email', 'Auth\PasswordController@getEmail');
	Route::post('password/email', 'Auth\PasswordController@postEmail');

	// Password reset routes...
	Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
	Route::post('password/reset', 'Auth\PasswordController@postReset');

	Route::get('match/', ['uses' => 'MatchController@index', 'as' => 'match.index', 'middleware' => 'isChoiceAccount']);

	Route::get('match/list-account', ['uses' => 'MatchController@listAccount', 'as' => 'match.list-account']);
	Route::get('manage-account', ['uses' => 'AccountController@manage', 'as' => 'manage-account']);
	Route::get('manage-account/create', ['uses' => 'AccountController@create', 'as' => 'account.create']);

	Route::get('manage-account/destroy/{id}', ['uses' => 'AccountController@destroy', 'as' => 'account.destroy']);
	Route::get('manage-account/deactive/{id}', ['uses' => 'AccountController@deactive', 'as' => 'account.deactive']);
	Route::get('manage-account/active/{id}', ['uses' => 'AccountController@active', 'as' => 'account.active']);

	Route::post('change-account', ['uses' => 'AccountController@change', 'as' => 'change-account']);
	Route::post('store-account', ['uses' => 'AccountController@store', 'as' => 'store-account']);


	Route::get('report/statement', ['uses' => 'ReportController@statement', 'as' => 'statement']);
	Route::get('report/schedule', ['uses' => 'ReportController@reportSchedule', 'as' => 'schedule']);

	Route::get('report/view-log', ['uses' => 'ReportController@viewLog', 'as' => 'view-log']);
	Route::get('report/detail-log', ['uses' => 'ReportController@detailLog', 'as' => 'detail-log']);
	Route::get('report/update-run', ['uses' => 'ReportController@updateRun', 'as' => 'update-run']);
	Route::get('crawler', ['uses' => 'ReportController@crawler', 'as' => 'crawler']);
	
	Route::get('push-gearman', ['uses' => 'ReportController@pushGearman', 'as' => 'push-gearman']);

	Route::get('match/ajax-load-bet', ['uses' => 'MatchController@ajaxLoadBet', 'as' => 'ajax-load-bet']);
	Route::get('match/set-provider', ['uses' => 'MatchController@setProvider', 'as' => 'set-provider']);
	Route::get('match/set-account', ['uses' => 'MatchController@setAccount', 'as' => 'set-account']);
	Route::get('match/bet/{match_id}', ['uses' => 'MatchController@bet', 'as' => 'match.bet']);
	Route::get('league/', ['uses' => 'MatchController@league', 'as' => 'league']);
	Route::get('remove-league/{league_id}', ['uses' => 'MatchController@removeLeague', 'as' => 'remove-league']);
	Route::get('add-league/{league_id}', ['uses' => 'MatchController@addLeague', 'as' => 'add-league']);
	Route::post('match/store-bet', ['uses' => 'MatchController@storeBet', 'as' => 'match.store-bet']);
	Route::post('ajax-match-modal', ['uses' => 'MatchController@ajaxMatchModal', 'as' => 'ajax-match-modal']);
	Route::post('ajax-copy-schedule', ['uses' => 'MatchController@ajaxCopySchedule', 'as' => 'ajax-copy-schedule']);
	Route::get('match/destroy-bet/{match_id}/{bet_id}', ['uses' => 'MatchController@destroyBet', 'as' => 'match.destroy-bet']);
});
Route::get('/', ['uses' => 'AdminController@index']);
