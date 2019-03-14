<?php

/*
|--------------------------------------------------------------------------
| FrontEnd Routes
| Untuk Controller berada di 'LYTO/App/Controllers/FrontEnd'
|-------------------------------------------------------------------------- */

Route::group(['middleware' => ['isOurServer']], function () {
    Route::get('', 'indexController@index')->name('index');
    Route::get('register', 'indexController@register')->name('register');
    Route::post('register', 'indexController@registerPost')->name('register.post');
});
