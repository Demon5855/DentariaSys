<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SextanteIhos extends Model
{
    protected $table = 'sextantes_ihos';

    protected $fillable = ['numero', 'pieza_primaria', 'pieza_alterna', 'pieza_temporal'];

    /** Piezas candidatas en orden de prioridad para dentición permanente. */
    public function piezasCandidatasPermanentes(): array
    {
        return [$this->pieza_primaria, $this->pieza_alterna];
    }
}
