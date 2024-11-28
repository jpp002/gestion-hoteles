<?php

use App\Http\Controllers\Api\HabitacionController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\HuespedController;
use App\Http\Controllers\Api\ServicioController;
use App\Models\Habitacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




Route::get('hotel/all', [HotelController::class, 'all']);
Route::get('habitacion/all', [HabitacionController::class, 'all']);
Route::get('huesped/all', [HuespedController::class, 'all']);
Route::get('servicio/all', [ServicioController::class, 'all']);

Route::resource('servicio', ServicioController::class)->except(["create", "edit"]);
Route::resource('hotel', HotelController::class)->except(["create", "edit"]);
Route::resource('habitacion', HabitacionController::class)->except(["create", "edit"]);
Route::resource('huesped', HuespedController::class)->except(["create", "edit"]);




Route::get('hotel/{hotel}/habitaciones', [HotelController::class, 'habitaciones']);
Route::get('hotel/{hotel}/servicios', [HotelController::class, 'servicios']);
Route::get('servicio/{servicio}/hoteles', [ServicioController::class, 'hoteles']);
Route::get('habitacion/{habitacion}/hotel', [HabitacionController::class, 'hotel']);
Route::get('habitacion/{habitacion}/huespedes', [HabitacionController::class, 'huespedes']);
Route::post('hotel/{hotel}/servicio/{servicio}', [HotelController::class, 'addServicio']);
Route::post('huesped/{huesped}/reservar/{habitacion}', [HuespedController::class, 'reservarHabitacion']);
Route::post('/huesped/{huesped}/checkout', [HuespedController::class, 'checkoutHabitacion']);
Route::delete('hotel/{hotel}/servicio/{servicio}', [HotelController::class, 'removeServicio']);


