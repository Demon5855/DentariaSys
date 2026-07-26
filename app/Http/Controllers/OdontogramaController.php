<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOdontogramaRequest;
use App\Models\Condicion;
use App\Models\HistoriaClinica;
use App\Models\Odontograma;
use App\Models\OdontogramaPieza;
use Illuminate\Support\Facades\DB;

class OdontogramaController extends Controller
{
    public function create(HistoriaClinica $historiaClinica)
    {
        $this->authorize('create', Odontograma::class);

        if ($historiaClinica->esta_vencida) {
            return redirect()
                ->route('historias.show', $historiaClinica)
                ->with('info', 'Esta historia clínica venció. Abre una nueva desde el perfil del paciente.');
        }

        $historiaClinica->load('paciente');

        $condiciones = Condicion::orderBy('orden')->get();
        $tipoSugerido = $historiaClinica->odontogramas()->where('tipo', 'inicial')->exists()
            ? 'evolutivo'
            : 'inicial';

        return view('odontogramas.create', compact('historiaClinica', 'condiciones', 'tipoSugerido'));
    }

    public function store(StoreOdontogramaRequest $request, HistoriaClinica $historiaClinica)
    {
        $this->authorize('create', Odontograma::class);

        if ($historiaClinica->esta_vencida) {
            return redirect()
                ->route('historias.show', $historiaClinica)
                ->with('info', 'Esta historia clínica venció. Abre una nueva desde el perfil del paciente.');
        }

        $datos = $request->validated();

        $odontograma = DB::transaction(function () use ($datos, $historiaClinica, $request) {
            $odontograma = $historiaClinica->odontogramas()->create([
                'odontologo_id' => $request->user()->id,
                'tipo' => $datos['tipo'],
                'denticion' => $datos['denticion'],
                'fecha' => $datos['fecha'],
                'firmado_at' => now(),
                // Los índices se calculan más abajo, una vez insertadas
                // las piezas y hallazgos; se actualizan al final.
            ]);

            $piezasCreadas = [];

            // Movilidad/recesión primero, para tener la fila de la pieza
            // lista antes de colgarle hallazgos.
            foreach ($datos['periodontal'] ?? [] as $entrada) {
                $piezasCreadas[$entrada['pieza']] = OdontogramaPieza::create([
                    'odontograma_id' => $odontograma->id,
                    'pieza' => $entrada['pieza'],
                    'movilidad' => $entrada['movilidad'] ?? null,
                    'recesion' => $entrada['recesion'] ?? null,
                ]);
            }

            foreach ($datos['hallazgos'] ?? [] as $hallazgo) {
                $pieza = $piezasCreadas[$hallazgo['pieza']]
                    ??= OdontogramaPieza::create([
                        'odontograma_id' => $odontograma->id,
                        'pieza' => $hallazgo['pieza'],
                    ]);

                $pieza->hallazgos()->create([
                    'condicion_id' => $hallazgo['condicion_id'],
                    'superficie' => $hallazgo['superficie'] ?? null,
                ]);
            }

            $piezasConHallazgos = $odontograma->piezas()->with('hallazgos.condicion')->get();
            $indices = Odontograma::calcularIndices($piezasConHallazgos);

            $odontograma->update([
                'cpod_c' => $indices['cpod']['c'], 'cpod_p' => $indices['cpod']['p'], 'cpod_o' => $indices['cpod']['o'],
                'ceod_c' => $indices['ceod']['c'], 'ceod_e' => $indices['ceod']['e'], 'ceod_o' => $indices['ceod']['o'],
            ]);

            return $odontograma;
        });

        return redirect()
            ->route('odontogramas.show', $odontograma)
            ->with('success', 'Odontograma firmado. Este registro ya no se puede modificar.');
    }

    public function show(Odontograma $odontograma)
    {
        $this->authorize('view', $odontograma);

        $odontograma->load(
            'historiaClinica.paciente',
            'odontologo',
            'piezas.hallazgos.condicion',
        );

        $condicionesIndexadas = Condicion::all()->keyBy('id')
            ->map(fn (Condicion $c) => ['color' => $c->color, 'simbolo' => $c->simbolo]);

        $hallazgosPorPieza = $odontograma->piezas->mapWithKeys(function ($pieza) {
            $superficies = [];
            $piezaCompleta = [];

            foreach ($pieza->hallazgos as $hallazgo) {
                if ($hallazgo->superficie) {
                    $superficies[$hallazgo->superficie] = $hallazgo->condicion_id;
                } else {
                    $piezaCompleta[] = $hallazgo->condicion_id;
                }
            }

            return [$pieza->pieza => ['superficies' => $superficies, 'piezas' => $piezaCompleta]];
        });

        return view('odontogramas.show', compact('odontograma', 'condicionesIndexadas', 'hallazgosPorPieza'));
    }
}
