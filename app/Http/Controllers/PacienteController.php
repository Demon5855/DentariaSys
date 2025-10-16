<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->get('estado', 'activos');
        $buscar = $request->get('buscar');

        $query = Paciente::query();

        if ($estado === 'activos') {
            $query->where('pac_activo', true);
        } elseif ($estado === 'inactivos') {
            $query->where('pac_activo', false);
        }

        if ($buscar) {
            $query->where(function ($q) use ($buscar) {
                $q->where('pac_primer_nombre', 'ILIKE', "%{$buscar}%")
                    ->orWhere('pac_segundo_nombre', 'ILIKE', "%{$buscar}%")
                    ->orWhere('pac_primer_apellido', 'ILIKE', "%{$buscar}%")
                    ->orWhere('pac_segundo_apellido', 'ILIKE', "%{$buscar}%")
                    ->orWhere('pac_email', 'ILIKE', "%{$buscar}%");
            });
        }

        $pacientes = $query->orderBy('pac_id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $pacientes->load('historiaClinica');

        $totalActivos = Paciente::where('pac_activo', true)->count();
        $totalInactivos = Paciente::where('pac_activo', false)->count();

        return view('pacientes.index', compact('pacientes', 'estado', 'totalActivos', 'totalInactivos'));
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pac_primer_nombre' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/'],
            'pac_segundo_nombre' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/'],
            'pac_primer_apellido' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/'],
            'pac_segundo_apellido' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/'],
            'pac_fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'pac_telefono' => ['nullable', 'string', 'max:15'],
            'pac_direccion' => ['nullable', 'string', 'max:150'],
            'pac_email' => ['nullable', 'email', 'max:100', 'unique:pacientes,pac_email'],
        ]);

        Paciente::create($request->all());

        return redirect()->route('pacientes.index')->with('success', 'Paciente creado exitosamente.');
    }

    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(Request $request, Paciente $paciente)
    {
        $request->validate([
            'pac_primer_nombre' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/'],
            'pac_segundo_nombre' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/'],
            'pac_primer_apellido' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/'],
            'pac_segundo_apellido' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/'],
            'pac_fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'pac_telefono' => ['nullable', 'string', 'max:15'],
            'pac_direccion' => ['nullable', 'string', 'max:150'],
            'pac_email' => ['nullable', 'email', 'max:100', 'unique:pacientes,pac_email,' . $paciente->pac_id . ',pac_id'],
        ]);

        $paciente->update($request->all());

        return redirect()->route('pacientes.index')->with('status', 'Paciente actualizado exitosamente.');
    }

    public function destroy(Paciente $paciente)
    {
        $paciente->update(['pac_activo' => false]);
        return redirect()->route('pacientes.index')->with('status', 'Paciente desactivado exitosamente.');
    }

    public function restore(Paciente $paciente)
    {
        $paciente->update(['pac_activo' => true]);
        return redirect()->route('pacientes.index', ['estado' => 'inactivos'])->with('status', 'Paciente reactivado exitosamente.');
    }
}