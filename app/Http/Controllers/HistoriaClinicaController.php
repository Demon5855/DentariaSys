<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHistoriaClinicaRequest;
use App\Models\HistoriaClinica;
use App\Models\Paciente;
use Carbon\Carbon;

class HistoriaClinicaController extends Controller
{
    public function create(Paciente $paciente)
    {
        $this->authorize('create', HistoriaClinica::class);

        if ($vigente = $paciente->historiaClinicaVigente) {
            return redirect()
                ->route('historias.show', $vigente)
                ->with('info', 'Este paciente ya tiene una historia clínica vigente.');
        }

        $anterior = $paciente->historiaClinicaMasReciente;

        return view('historias.create', compact('paciente', 'anterior'));
    }

    public function store(StoreHistoriaClinicaRequest $request, Paciente $paciente)
    {
        $this->authorize('create', HistoriaClinica::class);

        if ($vigente = $paciente->historiaClinicaVigente) {
            return redirect()
                ->route('historias.show', $vigente)
                ->with('info', 'Este paciente ya tiene una historia clínica vigente.');
        }

        $datos = $request->validated();

        $datos['fecha_vencimiento'] = HistoriaClinica::calcularFechaVencimiento(
            $datos['tipo_vigencia'],
            Carbon::parse($datos['fecha_apertura']),
            isset($datos['fecha_probable_parto']) ? Carbon::parse($datos['fecha_probable_parto']) : null,
            isset($datos['fecha_fin_periodo_lectivo']) ? Carbon::parse($datos['fecha_fin_periodo_lectivo']) : null,
        );

        $historiaClinica = $paciente->historiasClinicas()->create($datos);

        return redirect()
            ->route('consultas.create', $historiaClinica)
            ->with('success', 'Historia clínica abierta. Registra la primera consulta.');
    }

    public function show(HistoriaClinica $historiaClinica)
    {
        $this->authorize('view', $historiaClinica);

        $historiaClinica->load('paciente', 'consultas.profesional');

        return view('historias.show', compact('historiaClinica'));
    }
}
