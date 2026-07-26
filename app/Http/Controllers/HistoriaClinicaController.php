<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHistoriaClinicaRequest;
use App\Models\HistoriaClinica;
use App\Models\Paciente;

class HistoriaClinicaController extends Controller
{
    public function create(Paciente $paciente)
    {
        if ($paciente->historiaClinica) {
            return redirect()
                ->route('historias.show', $paciente->historiaClinica)
                ->with('info', 'Este paciente ya tiene una historia clínica.');
        }

        return view('historias.create', compact('paciente'));
    }

    public function store(StoreHistoriaClinicaRequest $request, Paciente $paciente)
    {
        if ($paciente->historiaClinica) {
            return redirect()
                ->route('historias.show', $paciente->historiaClinica)
                ->with('info', 'Este paciente ya tiene una historia clínica.');
        }

        $historiaClinica = $paciente->historiaClinica()->create($request->validated());

        return redirect()
            ->route('consultas.create', $historiaClinica)
            ->with('success', 'Historia clínica abierta. Registra la primera consulta.');
    }

    public function show(HistoriaClinica $historiaClinica)
    {
        $historiaClinica->load('paciente', 'consultas.profesional');

        return view('historias.show', compact('historiaClinica'));
    }
}
