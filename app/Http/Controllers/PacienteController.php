<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    /**
     * Muestra una lista de pacientes, filtrados por estado (activos/inactivos) y búsqueda.
     */
    public function index(Request $request)
    {
        $estado = $request->get('estado', 'activos');
        $buscar = $request->get('buscar');

        $query = Paciente::query();

        if ($estado === 'activos') {
            $query->where('activo', true);
        } elseif ($estado === 'inactivos') {
            $query->where('activo', false);
        }

        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('primer_nombre', 'ILIKE', "%{$buscar}%")
                    ->orWhere('segundo_nombre', 'ILIKE', "%{$buscar}%")
                    ->orWhere('primer_apellido', 'ILIKE', "%{$buscar}%")
                    ->orWhere('segundo_apellido', 'ILIKE', "%{$buscar}%")
                    ->orWhere('email', 'ILIKE', "%{$buscar}%");
            });
        }

        $pacientes = $query->orderBy('id', 'desc')->paginate(10);
        
        $totalActivos = Paciente::where('activo', true)->count();
        $totalInactivos = Paciente::where('activo', false)->count();

        return view('pacientes.index', compact('pacientes', 'estado', 'totalActivos', 'totalInactivos'));
    }

    /**
     * Muestra el formulario para crear un nuevo paciente.
     */
    public function create()
    {
        return view('pacientes.create');
    }

    /**
     * Almacena un nuevo paciente en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'primer_nombre' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/'],
            'segundo_nombre' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/'],
            'primer_apellido' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/'],
            'segundo_apellido' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'telefono' => ['nullable', 'string', 'max:15'],
            'direccion' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:100', 'unique:pacientes,email'],
        ]);

        Paciente::create($request->all());

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un paciente existente.
     */
    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    /**
     * Actualiza un paciente en la base de datos.
     */
    public function update(Request $request, Paciente $paciente)
    {
        $request->validate([
            'primer_nombre' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/'],
            'segundo_nombre' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/'],
            'primer_apellido' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/'],
            'segundo_apellido' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'telefono' => ['nullable', 'string', 'max:15'],
            'direccion' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:100', 'unique:pacientes,email,' . $paciente->id],
        ]);

        $paciente->update($request->all());

        return redirect()->route('pacientes.index')
            ->with('status', 'Paciente actualizado exitosamente.');
    }

    /**
     * Desactiva un paciente (soft delete).
     */
    public function destroy(Paciente $paciente)
    {
        $paciente->update(['activo' => false]);

        return redirect()->route('pacientes.index')
            ->with('status', 'Paciente desactivado exitosamente.');
    }

    /**
     * Reactiva un paciente.
     */
    public function restore(Paciente $paciente)
    {
        $paciente->update(['activo' => true]);

        return redirect()->route('pacientes.index', ['estado' => 'inactivos'])
            ->with('status', 'Paciente reactivado exitosamente.');
    }
}