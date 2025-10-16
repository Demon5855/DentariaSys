<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\HistoriaClinicaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Rutas del Perfil (instaladas por Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- HISTORIAS CLÍNICAS ---
    Route::get('pacientes/{paciente}/historia/crear', [HistoriaClinicaController::class, 'create'])->name('historias.create');
    Route::post('pacientes/{paciente}/historia', [HistoriaClinicaController::class, 'store'])->name('historias.store');
    Route::get('historias/{historiaClinica}', [HistoriaClinicaController::class, 'show'])->name('historias.show');
    // --------------------------------------------------

    // --- PACIENTES ---
    Route::patch('pacientes/{paciente}/restore', [PacienteController::class, 'restore'])->name('pacientes.restore');
    Route::resource('pacientes', PacienteController::class);
    // --------------------------------------------------



});

// Esta línea la añade Breeze y AHORA SÍ FUNCIONARÁ
require __DIR__.'/auth.php';