<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePacienteRequest;
use App\Http\Requests\UpdatePacienteRequest;
use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Paciente::class, 'paciente');
    }

    public function index(Request $request)
    {
        $estado = $request->get('estado', 'activos');
        $buscar = $request->get('buscar');

        $query = Paciente::query();

        if ($estado === 'activos') {
            $query->activos();
        } elseif ($estado === 'inactivos') {
            $query->inactivos();
        }

        $query->buscar($buscar);

        $pacientes = $query->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $pacientes->load('historiaClinicaVigente');

        $totalActivos = Paciente::activos()->count();
        $totalInactivos = Paciente::inactivos()->count();

        return view('pacientes.index', compact('pacientes', 'estado', 'totalActivos', 'totalInactivos'));
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(StorePacienteRequest $request)
    {
        $datos = $request->validated();

        // El instructivo del 033 dice que, sin cédula/pasaporte/carné, el
        // número de historia clínica es "un código de 17 dígitos temporales
        // que será emitido por el servicio de estadística". Como no hay un
        // módulo de estadística todavía, lo generamos aquí si no vino uno.
        if ($datos['tipo_documento'] === 'temporal' && empty($datos['numero_documento'])) {
            $datos['numero_documento'] = Paciente::generarDocumentoTemporal();
        }

        Paciente::create($datos);

        return redirect()->route('pacientes.index')->with('success', 'Paciente creado exitosamente.');
    }

    public function show(Paciente $paciente)
    {
        $paciente->load('historiaClinicaVigente', 'historiasClinicas.consultas');

        return view('pacientes.show', compact('paciente'));
    }

    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(UpdatePacienteRequest $request, Paciente $paciente)
    {
        $paciente->update($request->validated());

        return redirect()->route('pacientes.index')->with('status', 'Paciente actualizado exitosamente.');
    }

    public function destroy(Paciente $paciente)
    {
        $paciente->update(['activo' => false]);

        return redirect()->route('pacientes.index')->with('status', 'Paciente desactivado exitosamente.');
    }

    public function restore(Paciente $paciente)
    {
        $this->authorize('restore', $paciente);

        $paciente->update(['activo' => true]);

        return redirect()->route('pacientes.index', ['estado' => 'inactivos'])->with('status', 'Paciente reactivado exitosamente.');
    }
}
