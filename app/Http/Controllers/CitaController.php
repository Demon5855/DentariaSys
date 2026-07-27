<?php

namespace App\Http\Controllers;

use App\Http\Requests\CambiarEstadoCitaRequest;
use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaRequest;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Cita::class);

        $fecha = $request->filled('fecha') ? Carbon::parse($request->get('fecha')) : now();

        $citas = Cita::with(['paciente', 'profesional'])
            ->whereDate('fecha_hora', $fecha)
            ->orderBy('fecha_hora')
            ->get();

        $profesionales = User::role('odontologo')->orderBy('name')->get();

        return view('citas.index', compact('citas', 'fecha', 'profesionales'));
    }

    public function create()
    {
        $this->authorize('create', Cita::class);

        $profesionales = User::role('odontologo')->orderBy('name')->get();

        return view('citas.create', compact('profesionales'));
    }

    public function store(StoreCitaRequest $request)
    {
        $this->authorize('create', Cita::class);

        $cita = Cita::create($request->validated());

        return redirect()
            ->route('citas.index', ['fecha' => $cita->fecha_hora->toDateString()])
            ->with('success', 'Cita agendada exitosamente.');
    }

    public function edit(Cita $cita)
    {
        $this->authorize('gestionar', $cita);

        $cita->load('paciente');
        $profesionales = User::role('odontologo')->orderBy('name')->get();

        return view('citas.edit', compact('cita', 'profesionales'));
    }

    public function update(UpdateCitaRequest $request, Cita $cita)
    {
        $this->authorize('gestionar', $cita);

        $cita->update($request->validated());

        return redirect()
            ->route('citas.index', ['fecha' => $cita->fecha_hora->toDateString()])
            ->with('success', 'Cita actualizada exitosamente.');
    }

    public function cambiarEstado(CambiarEstadoCitaRequest $request, Cita $cita)
    {
        $this->authorize('gestionar', $cita);

        $cita->update($request->validated());

        return back()->with('success', 'Estado de la cita actualizado.');
    }

    /**
     * Búsqueda de pacientes para el selector del formulario de citas
     * (evita cargar todos los pacientes en un <select> gigante).
     */
    public function buscarPacientes(Request $request)
    {
        $this->authorize('create', Cita::class);

        $termino = $request->get('q', '');

        $pacientes = Paciente::activos()
            ->buscar($termino)
            ->limit(10)
            ->get(['id', 'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'numero_documento']);

        return response()->json(
            $pacientes->map(fn (Paciente $p) => [
                'id' => $p->id,
                'texto' => "{$p->nombre_completo} — {$p->numero_documento}",
            ])
        );
    }
}
