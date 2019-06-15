<?php

use Illuminate\Http\Request;

// // // // // // // // // // // // // // // // // // // // // // // // // // //

Route::prefix('page')->group(function(){
    Route::post('register', 'Api\Page\AuthController@register');
    Route::post('update', 'Api\Page\AuthController@update');
    Route::post('login', 'Api\Page\AuthController@login');

    Route::post('send', 'Api\Page\ContactController@send');
});

// // // // // // // // // // // // // // // // // // // // // // // // // // //

Route::prefix('admin')->group(function(){
    Route::post('login', 'Api\Admin\AuthController@login');

    Route::middleware('auth:admin')->group(function(){
        Route::get('check', 'Api\Admin\AuthController@check');
        Route::prefix('users')->group(function(){
            Route::get('', 'Api\Admin\UsersController@users');
            Route::post('', 'Api\Admin\UsersController@save');
            Route::post('/{id}', 'Api\Admin\UsersController@update');
            Route::delete('/{id}', 'Api\Admin\UsersController@delete');
        });
    });
});

// // // // // // // // // // // // // // // // // // // // // // // // // // //

Route::get('login', function(){
    return response()->json([
        'status' => 'error',
        'message' => 'Unauthorized',
    ]);
})->name('login');
