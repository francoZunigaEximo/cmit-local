<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Route::get('/password', [AuthController::class, 'testCreate'])->name('password');

Route::group(['middleware' => 'guest'], function(){
    Route::view('/', 'layouts.login')->name("login");
    Route::post('/validate-login', [AuthController::class, 'login'])->name('validate-login');
});

Route::group(['middleware' => 'auth'], function(){
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/changePassword', [AuthController::class, 'cambiarPass'])->name('changePassword');
    Route::post('/changePassword', [AuthController::class, 'cambiarPostPass'])->name('changePassword');
    
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    
});