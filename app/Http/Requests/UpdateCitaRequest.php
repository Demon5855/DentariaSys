<?php

namespace App\Http\Requests;

/**
 * Hereda reglas y el chequeo de solapamiento de StoreCitaRequest tal cual
 * — la única diferencia real es que aquí sí existe un parámetro de ruta
 * {cita}, que el withValidator heredado ya sabe excluir de los candidatos
 * de solapamiento (para no comparar la cita contra sí misma).
 */
class UpdateCitaRequest extends StoreCitaRequest
{
    //
}
