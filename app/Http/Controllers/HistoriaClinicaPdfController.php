<?php

namespace App\Http\Controllers;

use App\Models\HistoriaClinica;
use Barryvdh\DomPDF\Facade\Pdf;

class HistoriaClinicaPdfController extends Controller
{
    /**
     * El 033 impreso mezcla dos tipos de contenido:
     *   - "instantánea" (secciones A-G): datos de la visita más reciente,
     *     como en la hoja física, donde solo hay espacio para una.
     *   - "historial acumulado" (secciones M y O): diagnósticos y
     *     tratamientos de TODAS las consultas de esta historia, tal como
     *     el formulario real acumula filas de evolución en la misma hoja
     *     a lo largo del tiempo.
     *   - El odontograma (H) y sus índices (I, J) usan el más reciente
     *     de esta historia, sea inicial o evolutivo.
     */
    public function exportar(HistoriaClinica $historiaClinica)
    {
        $this->authorize('view', $historiaClinica);

        $historiaClinica->load([
            'paciente',
            'consultas' => fn ($query) => $query->with([
                'antecedentesPersonalesMarcados',
                'antecedentesFamiliaresMarcados',
                'regionesAfectadas',
                'diagnosticos.cie10',
                'tratamientos.profesional',
            ]),
            'odontogramas' => fn ($query) => $query->with([
                'piezas.hallazgos.condicion',
                'ihosRegistros.sextante',
                'odontologo',
            ]),
        ]);

        $consultaReciente = $historiaClinica->consultas->first(); // ya viene ordenado desc por fecha
        $odontogramaReciente = $historiaClinica->odontogramas->first();

        $diagnosticos = $historiaClinica->consultas
            ->flatMap(fn ($consulta) => $consulta->diagnosticos->map(fn ($d) => [
                'fecha' => $consulta->fecha,
                'diagnostico' => $d,
            ]))
            ->sortBy('fecha');

        $tratamientos = $historiaClinica->consultas
            ->flatMap(fn ($consulta) => $consulta->tratamientos->map(fn ($t) => [
                'fecha' => $t->fecha, // fecha propia de la sesión (Sección O), no la de la consulta
                'tratamiento' => $t,
            ]))
            ->sortBy('fecha');

        $pdf = Pdf::loadView('pdf.historia-clinica', [
            'historiaClinica' => $historiaClinica,
            'paciente' => $historiaClinica->paciente,
            'consultaReciente' => $consultaReciente,
            'odontogramaReciente' => $odontogramaReciente,
            'diagnosticos' => $diagnosticos,
            'tratamientos' => $tratamientos,
        ])->setPaper('a4', 'portrait');

        $nombreArchivo = 'historia-clinica-' . $historiaClinica->paciente->numero_documento . '.pdf';

        return $pdf->stream($nombreArchivo);
    }
}
