<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsultaRequest;
use App\Models\Antecedente;
use App\Models\Consulta;
use App\Models\HistoriaClinica;
use App\Models\RegionEstomatognatica;
use Illuminate\Support\Arr;

class ConsultaController extends Controller
{
    public function create(HistoriaClinica $historiaClinica)
    {
        $this->authorize('create', Consulta::class);

        if ($historiaClinica->esta_vencida) {
            return redirect()
                ->route('historias.show', $historiaClinica)
                ->with('info', 'Esta historia clínica venció. Abre una nueva desde el perfil del paciente.');
        }

        $historiaClinica->load('paciente');

        $antecedentes = Antecedente::orderBy('codigo')->get();
        $regiones = RegionEstomatognatica::orderBy('numero')->get();

        return view('consultas.create', compact('historiaClinica', 'antecedentes', 'regiones'));
    }

    public function store(StoreConsultaRequest $request, HistoriaClinica $historiaClinica)
    {
        $this->authorize('create', Consulta::class);

        if ($historiaClinica->esta_vencida) {
            return redirect()
                ->route('historias.show', $historiaClinica)
                ->with('info', 'Esta historia clínica venció. Abre una nueva desde el perfil del paciente.');
        }

        $datos = $request->validated();

        // Las casillas marcadas viajan en el mismo request, pero se guardan
        // en tablas pivote aparte — no son columnas de `consultas`.
        $antecedentesPersonales = Arr::pull($datos, 'antecedentes_personales_marcados', []);
        $antecedentesFamiliares = Arr::pull($datos, 'antecedentes_familiares_marcados', []);
        $regionesAfectadas = Arr::pull($datos, 'regiones_afectadas', []);

        $consulta = $historiaClinica->consultas()->create(
            $datos + ['profesional_id' => $request->user()?->id]
        );

        if (! empty($antecedentesPersonales)) {
            $consulta->antecedentesPersonalesMarcados()->attach($antecedentesPersonales, ['tipo' => 'personal']);
        }

        if (! empty($antecedentesFamiliares)) {
            $consulta->antecedentesFamiliaresMarcados()->attach($antecedentesFamiliares, ['tipo' => 'familiar']);
        }

        if (! empty($regionesAfectadas)) {
            $consulta->regionesAfectadas()->attach($regionesAfectadas);
        }

        return redirect()
            ->route('historias.show', $historiaClinica)
            ->with('success', 'Consulta registrada exitosamente.');
    }

    public function show(Consulta $consulta)
    {
        $this->authorize('view', $consulta);

        $consulta->load(
            'historiaClinica.paciente',
            'profesional',
            'antecedentesPersonalesMarcados',
            'antecedentesFamiliaresMarcados',
            'regionesAfectadas',
        );

        return view('consultas.show', compact('consulta'));
    }
}
