<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeputadosController;

Route::get('/', [DeputadosController::class, 'index']);
Route::get('/deputados/load', [DeputadosController::class, 'loadMore']);
