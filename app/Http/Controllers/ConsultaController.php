<?php

namespace App\Http\Controllers;

use App\Exceptions\StockInsuficienteException;
use App\Http\Requests\StoreConsultaRequest;
use App\Models\Antecedente;
use App\Models\Consulta;
use App\Models\DiagnosticoCie10;
use App\Models\HistoriaClinica;
use App\Models\Producto;
use App\Models\RegionEstomatognatica;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

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
        $productos = Producto::activos()->orderBy('nombre')->get();

        return view('consultas.create', compact('historiaClinica', 'antecedentes', 'regiones', 'codigosCie10', 'productos'));
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

        // Las casillas marcadas, los diagnósticos y los tratamientos
        // viajan en el mismo request, pero se guardan en tablas aparte —
        // no son columnas de `consultas`.
        $antecedentesPersonales = Arr::pull($datos, 'antecedentes_personales_marcados', []);
        $antecedentesFamiliares = Arr::pull($datos, 'antecedentes_familiares_marcados', []);
        $regionesAfectadas = Arr::pull($datos, 'regiones_afectadas', []);
        $diagnosticos = Arr::pull($datos, 'diagnosticos', []);
        $tratamientos = Arr::pull($datos, 'tratamientos', []);

        try {
            $consulta = DB::transaction(function () use (
                $datos, $historiaClinica, $request,
                $antecedentesPersonales, $antecedentesFamiliares, $regionesAfectadas,
                $diagnosticos, $tratamientos,
            ) {
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

                foreach (array_values($tratamientos) as $posicion => $tratamiento) {
                    $registroTratamiento = $consulta->tratamientos()->create([
                        'profesional_id' => $request->user()?->id,
                        'fecha' => $tratamiento['fecha'],
                        'diagnostico_complicaciones' => $tratamiento['diagnostico_complicaciones'] ?? null,
                        'procedimiento' => $tratamiento['procedimiento'],
                        'prescripciones' => $tratamiento['prescripciones'] ?? null,
                        'proxima_cita' => $tratamiento['proxima_cita'] ?? null,
                        'estado' => $tratamiento['estado'],
                        'orden' => $posicion,
                    ]);

                    // Cada insumo listado se descuenta con FIFO/FEFO
                    // (primero el lote que vence antes) y queda registrado
                    // en el pivote para poder consultar qué se usó.
                    foreach ($tratamiento['productos'] ?? [] as $insumo) {
                        $producto = Producto::findOrFail($insumo['producto_id']);

                        $producto->descontarStock((int) $insumo['cantidad'], [
                            'usuario_id' => $request->user()?->id,
                            'tratamiento_id' => $registroTratamiento->id,
                            'motivo' => "Consumido en tratamiento de la consulta del {$consulta->fecha->format('d/m/Y')}",
                        ]);

                        $registroTratamiento->productos()->attach($producto->id, ['cantidad' => $insumo['cantidad']]);
                    }
                }

                return $consulta;
            });
        } catch (StockInsuficienteException $e) {
            return back()->withErrors(['tratamientos' => $e->getMessage()])->withInput();
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
            'tratamientos.profesional',
            'tratamientos.productos',
        );

        return view('consultas.show', compact('consulta'));
    }
}
