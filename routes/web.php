<?php

use Illuminate\Support\Facades\Route;

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


Route::as('principal')->middleware('guest')->get('/', function () {
    return redirect('login');
});

require __DIR__.'/auth.php';
require __DIR__.'/ccleh.php';
require __DIR__.'/archivo/admin.php';
require __DIR__.'/archivo/inventario.php';
require __DIR__.'/archivo/transferencia.php';
require __DIR__.'/archivo/cadido.php';
require __DIR__.'/archivo/firmas.php';


Route::fallback(function ($request) {
    if ( request()->expectsJson() ) {
        return response()->json(['success' => false, 'message' => 'Not found'], 404);
    }
    return view('errors.404');

});
