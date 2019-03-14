<?php

/*
|--------------------------------------------------------------------------
| BackEnd Routes
| Untuk Controller berada di 'LYTO/App/Controllers/BackEnd'
|-------------------------------------------------------------------------- */


Route::prefix('ototools')->group(function() {
    Route::group(['middleware' => ['isOurServer']], function () {
    Route::get('', 'indexController@index')->name('admin-index')->middleware('auth');
    Route::get('login', 'Auth\LoginController@index')->name('login');
    Route::post('login', 'Auth\LoginController@login');
    Route::post('logout', 'Auth\LoginController@logout')->name('logout');
    Route::group(['middleware' => ['auth','role']], function () {
        Route::get('pelanggan/listcust.json', 'Pelanggan\indexPelangganController@listCust')->name('listcust.json');
        Route::get('events/events-register/listregister.json', 'Events\EventsRegister\indexEventsRegisterController@listRegister')->name('listregister.json');
        Route::get('unit/listunit.json', 'Unit\indexUnitController@listUnit')->name('listunit.json');
        Route::post('events/events-register/setpemenang', 'Events\EventsRegister\indexEventsRegisterController@setPemenang')->name('set-pemenang');
        

        Route::get('file-manager/download/{file}', 'FileManager\indexFileManagerController@download')->name('file-manager.download');
        Route::get('events/register/{id}', 'Events\EventsRegister\CustController@index')->name('cust-register');
        Route::post('events/register', 'Events\EventsRegister\CustController@post')->name('cust-register.post');
        Route::get('events/print/nipl', 'printController@nipl')->name('print.nipl');
        Route::get('events/print/bap', 'printController@bap')->name('print.bap');
        Route::get('events/print/lot', 'printController@lot')->name('print.lot');
        Route::get('events/print/kwitansi-pembayaran', 'printController@kwitansiPembayaran')->name('print.kwi.pem');
        Route::get('events/print/kwitansi-pelunasan', 'printController@kwitansiPelunasan')->name('print.kwi.pel');
        Route::post('profile', 'Profile\indexProfileController@edit')->name('profile.post');
        Route::post('profile/ganti-password', 'Profile\indexProfileController@password')->name('profile.password');
        Route::get('transaksi/pembayaran/bukti/{file}', 'Transaksi\editTransaksiController@bukti')->name('bukti.pembayaran');
        Route::post('transaksi/pembayaran', 'Transaksi\editTransaksiController@pembayaran')->name('transaksi.pembayaran');


        $Menu = Mhpix\App\Model\t_Routes::with('children')
                ->where('tampil',1)
                ->where('id_parent',0)
                ->get();
        /* ========= LOOPING ==================*/
        foreach($Menu as $Route){
            /* ===== ROUTING ===================*/
            if($Route->children->count() > 0){
                \App\Providers\RouteServiceProvider::BREAD(
                    $Route->alias_route,
                    Mhpix::remSpace($Route->nm_route),
                    Mhpix::remSpace($Route->nm_route),
                    $Route->bread,
                    $Route->id_parent
                );
                /* === PARENT ROUTING ============*/
                foreach($Route->children as $subRoute){
                    \App\Providers\RouteServiceProvider::BREAD(
                        $subRoute->alias_route,
                        Mhpix::remSpace($Route->nm_route).'\\'.Mhpix::remSpace($subRoute->nm_route),
                        Mhpix::remSpace($subRoute->nm_route),
                        $subRoute->bread,
                        $subRoute->id_parent
                    );
                }
                /* === PARENT ROUTING ============*/
            }else{
                \App\Providers\RouteServiceProvider::BREAD(
                    $Route->alias_route,
                    Mhpix::remSpace($Route->nm_route),
                    Mhpix::remSpace($Route->nm_route),
                    $Route->bread,
                    $Route->id_parent
                );
            }
            /* ===== ROUTING ===================*/
        }
        /* ========= LOOPING ==================*/
    });
    /* ========= ROUTE:LIST ==============*/
    Route::group(['middleware' => ['auth']], function () {
        Route::get('routes:list', function() {
            $routeCollection = Route::getRoutes();
            $array_routes = [];
            foreach ($routeCollection as $value) {
                $array_routes[] = [
                    'method' => implode('|',$value->methods()),
                    'prefix' => $value->getPrefix(),
                    'uri'=> $value->uri(),
                    'name' => $value->getName(),
                    'action' => $value->getActionName(),
                    'middleware' => implode(',',$value->middleware())
                ];
            }
            return $array_routes;
        });
    });
    /* ========= ROUTE:LIST ==============*/
    // Route::get('test','temp\TempController@test');
    // Route::get('test/edit/{id}','temp\TempController@edit');

  });
});
