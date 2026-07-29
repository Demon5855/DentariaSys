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

        // Mismo patrón que el nombre completo, admitiendo también guion y
        // apóstrofe (José-Luis, D'Angelo) — antes solo se permitían letras
        // y espacios, lo que rechazaba nombres compuestos legítimos.
        $nombrePattern = 'regex:/^[\p{L}\s\'-]+$/u';

        $reglas = [
            'tipo_documento' => ['required', 'in:cedula,pasaporte,carnet_refugiado,temporal'],
            'numero_documento' => $numeroDocumento,
            'primer_nombre' => ['required', 'string', 'max:50', $nombrePattern],
            'segundo_nombre' => ['nullable', 'string', 'max:50', $nombrePattern],
            'primer_apellido' => ['required', 'string', 'max:50', $nombrePattern],
            'segundo_apellido' => ['nullable', 'string', 'max:50', $nombrePattern],
            'sexo' => ['required', 'in:H,M'],
            'fecha_nacimiento' => ['required', 'date', 'before_or_equal:today'],
            'telefono' => ['nullable', 'string', 'max:15'],
            'direccion' => ['nullable', 'string', 'max:150'],
            'email' => ['nullable', 'lowercase', 'email', 'max:100', 'unique:pacientes,email'],
        ];

        // El representante legal es obligatorio solo si el paciente es menor
        // de edad. Se valida contra la fecha_nacimiento del propio request,
        // no contra un modelo ya guardado (todavía no existe).
        $esMenor = $this->filled('fecha_nacimiento')
            && Carbon::parse($this->input('fecha_nacimiento'))->age < 18;

        $reglas['representante_nombre'] = [$esMenor ? 'required' : 'nullable', 'string', 'max:150', $nombrePattern];
        $reglas['representante_tipo_documento'] = [$esMenor ? 'required' : 'nullable', 'in:cedula,pasaporte,carnet_refugiado'];
        $reglas['representante_parentesco'] = [$esMenor ? 'required' : 'nullable', 'string', 'max:50'];
        $reglas['representante_telefono'] = [$esMenor ? 'required' : 'nullable', 'string', 'max:15'];

        // Mismo esquema que numero_documento del paciente: solo se aplica
        // la validación de dígito verificador si el tipo declarado es
        // 'cedula'. Antes esta regla no existía para el representante en
        // absoluto — cualquier texto pasaba, sin importar el tipo.
        $representanteDocumento = [$esMenor ? 'required' : 'nullable', 'string', 'max:20'];
        if ($this->input('representante_tipo_documento') === 'cedula') {
            $representanteDocumento[] = new CedulaEcuatorianaValida();
        }
        $reglas['representante_documento'] = $representanteDocumento;

        return $reglas;
    }

    public function messages(): array
    {
        return [
            'representante_nombre.required' => 'El paciente es menor de edad: se requiere el nombre del representante legal.',
            'representante_tipo_documento.required' => 'El paciente es menor de edad: indica el tipo de documento del representante legal.',
            'representante_documento.required' => 'El paciente es menor de edad: se requiere el documento del representante legal.',
            'representante_parentesco.required' => 'El paciente es menor de edad: se requiere el parentesco del representante legal.',
            'representante_telefono.required' => 'El paciente es menor de edad: se requiere un teléfono de contacto del representante legal.',
        ];
    }
}
