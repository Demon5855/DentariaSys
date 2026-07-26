<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsultaRequest;
use App\Models\Consulta;
use App\Models\HistoriaClinica;

class ConsultaController extends Controller
{
    public function create(HistoriaClinica $historiaClinica)
    {
        $historiaClinica->load('paciente');

        return view('consultas.create', compact('historiaClinica'));
    }

    public function store(StoreConsultaRequest $request, HistoriaClinica $historiaClinica)
    {
        $historiaClinica->consultas()->create(
            $request->validated() + ['profesional_id' => $request->user()?->id]
        );

        return redirect()
            ->route('historias.show', $historiaClinica)
            ->with('success', 'Consulta registrada exitosamente.');
    }

    public function show(Consulta $consulta)
    {
        $consulta->load('historiaClinica.paciente', 'profesional');

        return view('consultas.show', compact('consulta'));
    }
}
