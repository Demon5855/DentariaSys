<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
use App\Models\Paciente;
use Illuminate\Http\Request;

class HistoriaClinicaController extends Controller
{
    public function create(Paciente $paciente)
    {
        if ($paciente->historiaClinica) {
            return redirect()->route('historias.show', $paciente->historiaClinica)->with('info', 'Este paciente ya tiene una historia clínica.');
        }
        return view('historias.create', compact('paciente'));
    }

    public function store(Request $request, Paciente $paciente)
    {
        $request->validate([
            'hcl_fecha_apertura' => ['required', 'date'],
            'hcl_antecedentes_personales' => ['nullable', 'string'],
            'hcl_antecedentes_familiares' => ['nullable', 'string'],
            'hcl_examen_clinico_general' => ['nullable', 'string'],
        ]);

        $datos = $request->all();
        $datos['hcl_pac_id'] = $paciente->pac_id; // Añadimos el ID del paciente manualmente
        HistoriaClinica::create($datos);

        return redirect()->route('pacientes.index')->with('success', 'Historia Clínica creada exitosamente.');
    }

    public function show(HistoriaClinica $historiaClinica)
    {
        $historiaClinica->load('paciente');
        return view('historias.show', compact('historiaClinica'));
    }
}