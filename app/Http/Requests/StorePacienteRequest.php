<?php

namespace App\Http\Requests;

use App\Rules\CedulaEcuatorianaValida;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StorePacienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $numeroDocumento = [
            $this->input('tipo_documento') === 'temporal' ? 'nullable' : 'required',
            'string', 'max:20', 'unique:pacientes,numero_documento',
        ];

        if ($this->input('tipo_documento') === 'cedula') {
            $numeroDocumento[] = new CedulaEcuatorianaValida();
        }

        $reglas = [
            'tipo_documento' => ['required', 'in:cedula,pasaporte,carnet_refugiado,temporal'],
            'numero_documento' => $numeroDocumento,
            'primer_nombre' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/u'],
            'segundo_nombre' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/u'],
            'primer_apellido' => ['required', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/u'],
            'segundo_apellido' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-ZáéíóúñÑ\s]+$/u'],
            'sexo' => ['required', 'in:H,M'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'telefono' => ['nullable', 'string', 'max:15'],
            'direccion' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:100', 'unique:pacientes,email'],
        ];

        // El representante legal es obligatorio solo si el paciente es menor
        // de edad. Se valida contra la fecha_nacimiento del propio request,
        // no contra un modelo ya guardado (todavía no existe).
        $esMenor = $this->filled('fecha_nacimiento')
            && Carbon::parse($this->input('fecha_nacimiento'))->age < 18;

        $reglas['representante_nombre'] = [$esMenor ? 'required' : 'nullable', 'string', 'max:150'];
        $reglas['representante_documento'] = [$esMenor ? 'required' : 'nullable', 'string', 'max:20'];
        $reglas['representante_parentesco'] = [$esMenor ? 'required' : 'nullable', 'string', 'max:50'];
        $reglas['representante_telefono'] = [$esMenor ? 'required' : 'nullable', 'string', 'max:15'];

        return $reglas;
    }

    public function messages(): array
    {
        return [
            'representante_nombre.required' => 'El paciente es menor de edad: se requiere el nombre del representante legal.',
            'representante_documento.required' => 'El paciente es menor de edad: se requiere el documento del representante legal.',
            'representante_parentesco.required' => 'El paciente es menor de edad: se requiere el parentesco del representante legal.',
            'representante_telefono.required' => 'El paciente es menor de edad: se requiere un teléfono de contacto del representante legal.',
        ];
    }
}
