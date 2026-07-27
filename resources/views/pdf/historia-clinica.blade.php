<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Historia Clínica — {{ $paciente->nombre_completo }}</title>
<style>
    /* dompdf: sin variables CSS, sin flexbox/grid — todo con tablas y bloques simples */
    @page { margin: 12mm 10mm; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5px; color: #16191d; }

    table { width: 100%; border-collapse: collapse; }
    td, th { border: 1px solid #9aa3ae; padding: 3px 5px; vertical-align: top; }
    .sin-borde, .sin-borde td, .sin-borde th { border: none; padding: 0; }

    .titulo-seccion {
        background: #e8edf3; font-weight: bold; font-size: 9px;
        text-transform: uppercase; padding: 3px 5px;
    }
    .etq {
        font-size: 7px; text-transform: uppercase; color: #5b6470;
        display: block; margin-bottom: 1px;
    }

    .membrete td { border: none; }
    .membrete .titulo { font-size: 13px; font-weight: bold; text-align: center; }
    .membrete .subtitulo { font-size: 10px; text-align: center; }
    .membrete .codigo { font-size: 8px; text-align: right; }

    .badge {
        display: inline-block; padding: 1px 6px; border-radius: 8px;
        font-size: 8px; font-weight: bold; color: #ffffff;
    }
    .badge-vigente { background: #16803c; }
    .badge-vencida { background: #b91c1c; }
    .badge-pre { background: #b45309; }
    .badge-def { background: #16803c; }
    .badge-alta { background: #16803c; }
    .badge-tratamiento { background: #1f52b8; }

    .pie-pagina { margin-top: 6px; font-size: 7.5px; color: #5b6470; text-align: center; }
    .bloque { margin-bottom: 8px; }
    .vacio { color: #9aa3ae; font-style: italic; }
</style>
</head>
<body>

<table class="membrete">
    <tr>
        <td style="width:70%">
            <div class="titulo">HISTORIA CLÍNICA ÚNICA — ODONTOLOGÍA</div>
            <div class="subtitulo">SNS-MSP/HCU-form.033/2021</div>
        </td>
        <td class="codigo">
            Generado: {{ now()->format('d/m/Y H:i') }}<br>
            Historia N.° {{ $historiaClinica->id }}
        </td>
    </tr>
</table>

<div class="bloque">
    <table>
        <tr><td colspan="4" class="titulo-seccion">A. Datos del paciente</td></tr>
        <tr>
            <td style="width:25%"><span class="etq">Nombre completo</span>{{ $paciente->nombre_completo }}</td>
            <td style="width:25%"><span class="etq">Documento</span>{{ $paciente->numero_documento }} ({{ ucfirst(str_replace('_',' ', $paciente->tipo_documento)) }})</td>
            <td style="width:15%"><span class="etq">Sexo</span>{{ $paciente->sexo === 'H' ? 'Hombre' : 'Mujer' }}</td>
            <td style="width:35%"><span class="etq">Fecha de nacimiento</span>{{ $paciente->fecha_nacimiento->format('d/m/Y') }} ({{ $paciente->edad_detallada }})</td>
        </tr>
        <tr>
            <td><span class="etq">Historia abierta</span>{{ $historiaClinica->fecha_apertura->format('d/m/Y') }}</td>
            <td><span class="etq">Vigencia</span>{{ ucfirst($historiaClinica->tipo_vigencia) }}</td>
            <td colspan="2">
                <span class="etq">Estado</span>
                <span class="badge {{ $historiaClinica->esta_vencida ? 'badge-vencida' : 'badge-vigente' }}">
                    {{ $historiaClinica->esta_vencida ? 'VENCIDA' : 'VIGENTE' }} — vence {{ $historiaClinica->fecha_vencimiento->format('d/m/Y') }}
                </span>
            </td>
        </tr>
        @if ($paciente->es_menor_de_edad)
        <tr>
            <td colspan="4">
                <span class="etq">Representante legal</span>
                {{ $paciente->representante_nombre }} ({{ $paciente->representante_parentesco }}) — {{ $paciente->representante_telefono }}
            </td>
        </tr>
        @endif
    </table>
</div>

@if ($consultaReciente)
<div class="bloque">
    <table>
        <tr><td colspan="2" class="titulo-seccion">B-C. Motivo de consulta y enfermedad actual (visita del {{ $consultaReciente->fecha->format('d/m/Y') }})</td></tr>
        <tr>
            <td style="width:50%"><span class="etq">Motivo de consulta</span>{{ $consultaReciente->motivo_consulta }}</td>
            <td><span class="etq">Enfermedad actual</span>{{ $consultaReciente->enfermedad_actual ?: '—' }}</td>
        </tr>
    </table>
</div>

<div class="bloque">
    <table>
        <tr><td colspan="2" class="titulo-seccion">D-E. Antecedentes patológicos</td></tr>
        <tr>
            <td style="width:50%">
                <span class="etq">Personales</span>
                @if ($consultaReciente->antecedentesPersonalesMarcados->isNotEmpty())
                    {{ $consultaReciente->antecedentesPersonalesMarcados->map(fn ($a) => $a->codigo.'. '.$a->nombre)->implode(' · ') }}<br>
                @endif
                {{ $consultaReciente->antecedentes_personales ?: 'No refiere antecedentes.' }}
            </td>
            <td>
                <span class="etq">Familiares</span>
                @if ($consultaReciente->antecedentesFamiliaresMarcados->isNotEmpty())
                    {{ $consultaReciente->antecedentesFamiliaresMarcados->map(fn ($a) => $a->codigo.'. '.$a->nombre)->implode(' · ') }}<br>
                @endif
                {{ $consultaReciente->antecedentes_familiares ?: 'No refiere antecedentes.' }}
            </td>
        </tr>
    </table>
</div>

<div class="bloque">
    <table>
        <tr><td colspan="4" class="titulo-seccion">F. Constantes vitales</td></tr>
        <tr>
            <td><span class="etq">Presión arterial</span>{{ $consultaReciente->presion_arterial ?: '—' }}</td>
            <td><span class="etq">Temperatura</span>{{ $consultaReciente->temperatura ? $consultaReciente->temperatura.' °C' : '—' }}</td>
            <td><span class="etq">Pulso</span>{{ $consultaReciente->pulso ?: '—' }}</td>
            <td><span class="etq">Frec. respiratoria</span>{{ $consultaReciente->frecuencia_respiratoria ?: '—' }}</td>
        </tr>
    </table>
</div>

<div class="bloque">
    <table>
        <tr><td class="titulo-seccion">G. Examen del sistema estomatognático</td></tr>
        <tr>
            <td>
                @if ($consultaReciente->regionesAfectadas->isNotEmpty())
                    {{ $consultaReciente->regionesAfectadas->map(fn ($r) => $r->numero.'. '.$r->nombre)->implode(' · ') }}<br>
                    {{ $consultaReciente->examen_estomatognatico }}
                @else
                    Sin patología aparente.
                @endif
            </td>
        </tr>
    </table>
</div>
@else
<div class="bloque vacio">Esta historia todavía no tiene consultas registradas.</div>
@endif

@if ($odontogramaReciente)
<div class="bloque">
    <table>
        <tr><td colspan="3" class="titulo-seccion">
            H. Odontograma ({{ ucfirst($odontogramaReciente->tipo) }}, {{ $odontogramaReciente->fecha->format('d/m/Y') }}, firmado por {{ $odontogramaReciente->odontologo->name }})
        </td></tr>
        <tr>
            <th style="width:15%">Pieza</th>
            <th style="width:20%">Cara</th>
            <th>Condición</th>
        </tr>
        @forelse ($odontogramaReciente->piezas as $pieza)
            @forelse ($pieza->hallazgos as $hallazgo)
                <tr>
                    <td>{{ $pieza->pieza }}</td>
                    <td>{{ $hallazgo->superficie ? ucfirst($hallazgo->superficie) : 'Pieza completa' }}</td>
                    <td>{{ $hallazgo->condicion->nombre }}</td>
                </tr>
            @empty
                <tr><td>{{ $pieza->pieza }}</td><td colspan="2">
                    @if ($pieza->movilidad !== null || $pieza->recesion !== null)
                        Movilidad: {{ $pieza->movilidad ?? '—' }} / Recesión: {{ $pieza->recesion ?? '—' }}
                    @endif
                </td></tr>
            @endforelse
        @empty
            <tr><td colspan="3" class="vacio">Sin hallazgos registrados en este odontograma.</td></tr>
        @endforelse
    </table>
</div>

<div class="bloque">
    <table>
        <tr><td colspan="6" class="titulo-seccion">I. Indicadores de salud bucal</td></tr>
        <tr>
            <td><span class="etq">Placa (prom.)</span>{{ $odontogramaReciente->ihos_placa_promedio ?? '—' }}</td>
            <td><span class="etq">Cálculo (prom.)</span>{{ $odontogramaReciente->ihos_calculo_promedio ?? '—' }}</td>
            <td><span class="etq">Gingivitis (prom.)</span>{{ $odontogramaReciente->ihos_gingivitis_promedio ?? '—' }}</td>
            <td><span class="etq">Enf. periodontal</span>{{ ucfirst($odontogramaReciente->enfermedad_periodontal ?? 'sin registrar') }}</td>
            <td><span class="etq">Oclusión</span>{{ $odontogramaReciente->tipo_oclusion ? 'Clase '.$odontogramaReciente->tipo_oclusion : '—' }}</td>
            <td><span class="etq">Fluorosis</span>{{ ucfirst($odontogramaReciente->fluorosis ?? 'sin registrar') }}</td>
        </tr>
    </table>
</div>

<div class="bloque">
    <table>
        <tr><td colspan="4" class="titulo-seccion">J. Índices CPO-D / ceo-d</td></tr>
        <tr>
            <th>C</th><th>P</th><th>O</th><th>Total CPO-D</th>
        </tr>
        <tr>
            <td>{{ $odontogramaReciente->cpod_c }}</td>
            <td>{{ $odontogramaReciente->cpod_p }}</td>
            <td>{{ $odontogramaReciente->cpod_o }}</td>
            <td>{{ $odontogramaReciente->cpod_c + $odontogramaReciente->cpod_p + $odontogramaReciente->cpod_o }}</td>
        </tr>
        <tr>
            <th>c</th><th>e</th><th>o</th><th>Total ceo-d</th>
        </tr>
        <tr>
            <td>{{ $odontogramaReciente->ceod_c }}</td>
            <td>{{ $odontogramaReciente->ceod_e }}</td>
            <td>{{ $odontogramaReciente->ceod_o }}</td>
            <td>{{ $odontogramaReciente->ceod_c + $odontogramaReciente->ceod_e + $odontogramaReciente->ceod_o }}</td>
        </tr>
    </table>
</div>
@else
<div class="bloque vacio">Esta historia todavía no tiene odontograma registrado.</div>
@endif

<div class="bloque">
    <table>
        <tr><td colspan="4" class="titulo-seccion">M. Diagnóstico (historial completo de esta historia clínica)</td></tr>
        <tr>
            <th style="width:15%">Fecha</th>
            <th style="width:15%">CIE-10</th>
            <th>Descripción</th>
            <th style="width:10%">Estado</th>
        </tr>
        @forelse ($diagnosticos as $entrada)
            <tr>
                <td>{{ $entrada['fecha']->format('d/m/Y') }}</td>
                <td>{{ $entrada['diagnostico']->cie10->codigo }}</td>
                <td>{{ $entrada['diagnostico']->descripcion }}</td>
                <td>
                    <span class="badge {{ $entrada['diagnostico']->estado === 'definitivo' ? 'badge-def' : 'badge-pre' }}">
                        {{ $entrada['diagnostico']->estado === 'definitivo' ? 'DEF' : 'PRE' }}
                    </span>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="vacio">Sin diagnósticos registrados.</td></tr>
        @endforelse
    </table>
</div>

<div class="bloque">
    <table>
        <tr><td colspan="5" class="titulo-seccion">O. Tratamiento (evolución completa de esta historia clínica)</td></tr>
        <tr>
            <th style="width:12%">Fecha</th>
            <th>Diagnóstico / complicaciones</th>
            <th>Procedimiento</th>
            <th>Prescripciones</th>
            <th style="width:15%">Estado</th>
        </tr>
        @forelse ($tratamientos as $entrada)
            <tr>
                <td>{{ $entrada['fecha']->format('d/m/Y') }}</td>
                <td>{{ $entrada['tratamiento']->diagnostico_complicaciones ?: '—' }}</td>
                <td>{{ $entrada['tratamiento']->procedimiento }}</td>
                <td>{{ $entrada['tratamiento']->prescripciones ?: '—' }}</td>
                <td>
                    <span class="badge {{ $entrada['tratamiento']->estado === 'alta' ? 'badge-alta' : 'badge-tratamiento' }}">
                        {{ $entrada['tratamiento']->estado === 'alta' ? 'ALTA' : 'EN TRATAMIENTO' }}
                    </span>
                    @if ($entrada['tratamiento']->proxima_cita)
                        <br><span style="font-size:7px">Próx.: {{ $entrada['tratamiento']->proxima_cita->format('d/m/Y') }}</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="vacio">Sin tratamientos registrados.</td></tr>
        @endforelse
    </table>
</div>

<div class="pie-pagina">
    Documento generado por DentariaSys — no reemplaza la firma manuscrita del profesional en el expediente físico.
</div>

</body>
</html>
