<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeputadoController;
use App\Http\Controllers\DespesaController;

Route::get('/', function () {
    return redirect()->route('deputados.index');
});

Route::resource('deputados', DeputadoController::class)->only(['index', 'show']);
Route::resource('despesas', DespesaController::class)->only(['index', 'show']);
