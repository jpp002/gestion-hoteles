<?php

use App\Http\Controllers\Api\HabitacionController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\HuespedController;
use App\Http\Controllers\Api\ServicioController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|----------------------------------------------------------------------
| API Routes
|----------------------------------------------------------------------
| Aquí puedes registrar las rutas de tu API para la aplicación.
| Estas rutas son cargadas por el RouteServiceProvider y todas estarán
| asignadas al grupo de middleware "api".
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('user/login', [UserController::class, 'login']);


// Rutas para Servicios
Route::group(['prefix' => 'servicio', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/all', [ServicioController::class, 'all']);
    Route::get('/{servicio}/hoteles', [ServicioController::class, 'hoteles']);
});
Route::resource('/servicio', ServicioController::class)->except(['create', 'edit']);

// Rutas para Hoteles
Route::group(['prefix' => 'hotel'], function () {
    Route::get('/all', [HotelController::class, 'all']);
    Route::post('/cascada', [HotelController::class, 'cascada']);
    Route::get('/{hotel}/habitaciones', [HotelController::class, 'habitaciones']);
    Route::get('/{hotel}/servicios', [HotelController::class, 'servicios']);
    Route::post('/{hotel}/servicio/{servicio}', [HotelController::class, 'addServicio']);
    Route::delete('/{hotel}/servicio/{servicio}', [HotelController::class, 'removeServicio']);
});
Route::resource('/hotel', HotelController::class)->except(['create', 'edit']);

// Rutas para Habitaciones


Route::group(['prefix' => 'habitacion'], function () {
    Route::post('/bulk', [HabitacionController::class, 'bulkStore']);
    Route::get('/all', [HabitacionController::class, 'all']);
    Route::get('/{habitacion}/hotel', [HabitacionController::class, 'hotel']);
    Route::get('/{habitacion}/huespedes', [HabitacionController::class, 'huespedes']);
});
Route::resource('/habitacion', HabitacionController::class)->except(['create', 'edit']);



// Rutas para Huespedes
Route::group(['prefix' => 'huesped'], function () {
    Route::get('/all', [HuespedController::class, 'all']);
    Route::get('/{huesped}/habitacion', [HuespedController::class, 'habitacion']);
    Route::post('/{huesped}/reservar/{habitacion}', [HuespedController::class, 'reservarHabitacion']);
    Route::post('/{huesped}/checkout', [HuespedController::class, 'checkoutHabitacion']);
});
Route::resource('/huesped', HuespedController::class)->except(['create', 'edit']);

// Rutas para Usuarios




