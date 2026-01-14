<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SessionAuthController;


Route::view('/', 'welcome');

//  Ruta de estado opcional para pruebas rápidas (puedes quitarla si no la usas)
Route::get('/status', fn() => ['status' => 'ok']);;
