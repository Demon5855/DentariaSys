<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PacienteController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('pacientes', PacienteController::class)->middleware(['auth', 'verified']);
