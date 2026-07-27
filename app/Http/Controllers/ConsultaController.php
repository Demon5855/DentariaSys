<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsultaRequest;
use App\Models\Antecedente;
use App\Models\Consulta;
use App\Models\DiagnosticoCie10;
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
        $codigosCie10 = DiagnosticoCie10::orderBy('codigo')->get();

        return view('consultas.create', compact('historiaClinica', 'antecedentes', 'regiones', 'codigosCie10'));
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

        // Las casillas marcadas y los diagnósticos viajan en el mismo
        // request, pero se guardan en tablas aparte — no son columnas de
        // `consultas`.
        $antecedentesPersonales = Arr::pull($datos, 'antecedentes_personales_marcados', []);
        $antecedentesFamiliares = Arr::pull($datos, 'antecedentes_familiares_marcados', []);
        $regionesAfectadas = Arr::pull($datos, 'regiones_afectadas', []);
        $diagnosticos = Arr::pull($datos, 'diagnosticos', []);

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

        // El orden en que llegan refleja el criterio del profesional
        // (complejidad/urgencia), tal como pide el instructivo — se
        // conserva tal cual en la columna 'orden', no se reordena.
        foreach (array_values($diagnosticos) as $posicion => $diagnostico) {
            $consulta->diagnosticos()->create([
                'diagnostico_cie10_id' => $diagnostico['diagnostico_cie10_id'],
                'descripcion' => $diagnostico['descripcion'],
                'estado' => $diagnostico['estado'],
                'orden' => $posicion,
            ]);
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
            'diagnosticos.cie10',
        );

        return view('consultas.show', compact('consulta'));
    }
}
