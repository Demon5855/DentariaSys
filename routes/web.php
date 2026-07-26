<?php

use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\HistoriaClinicaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\ProfileController;
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
    // Rutas de Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de Pacientes
    Route::patch('pacientes/{paciente}/restore', [PacienteController::class, 'restore'])->name('pacientes.restore');
    Route::resource('pacientes', PacienteController::class);

    // Rutas de Historias Clínicas (la carpeta: se abre una sola vez por paciente)
    Route::get('pacientes/{paciente}/historia/crear', [HistoriaClinicaController::class, 'create'])->name('historias.create');
    Route::post('pacientes/{paciente}/historia', [HistoriaClinicaController::class, 'store'])->name('historias.store');
    Route::get('historias/{historiaClinica}', [HistoriaClinicaController::class, 'show'])->name('historias.show');

    // Rutas de Consultas (cada visita, cuelga de la historia clínica)
    Route::get('historias/{historiaClinica}/consultas/crear', [ConsultaController::class, 'create'])->name('consultas.create');
    Route::post('historias/{historiaClinica}/consultas', [ConsultaController::class, 'store'])->name('consultas.store');
    Route::get('consultas/{consulta}', [ConsultaController::class, 'show'])->name('consultas.show');
});

require __DIR__.'/auth.php';
