<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\PrestacionesController;

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
    
    Route::get('/clientes', [ClientesController::class, 'clientes'])->name('clientes');
    Route::get('/pacientes', [ClientesController::class, 'pacientes'])->name('pacientes');
    Route::get('/grupos', [ClientesController::class, 'grupos'])->name('grupos');

    Route::get('/examenes', [ProveedoresController::class, 'examenes'])->name('examenes');
    Route::get('/proveedores', [ProveedoresController::class, 'proveedores'])->name('proveedores');
    Route::get('/profesionales', [ProveedoresController::class, 'profesionales'])->name('profesionales');

    Route::get('/prestaciones', [PrestacionesController::class, 'prestaciones'])->name('prestaciones');
    Route::get('/carnet', [PrestacionesController::class, 'carnet'])->name('carnet');
    Route::get('/pcr', [PrestacionesController::class, 'pcr'])->name('pcr');
    Route::get('/constancias', [PrestacionesController::class, 'constancias'])->name('constancias');
    Route::get('/placas', [PrestacionesController::class, 'placas'])->name('placas');
    Route::get('/entregadas', [PrestacionesController::class, 'entregadas'])->name('entregadas');
    
});