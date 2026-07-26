<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OdontogramaIhos extends Model
{
    protected $table = 'odontograma_ihos';

    protected $fillable = ['odontograma_id', 'sextante_ihos_id', 'pieza_examinada', 'placa', 'calculo', 'gingivitis'];

    public function odontograma(): BelongsTo
    {
        return $this->belongsTo(Odontograma::class);
    }

    public function sextante(): BelongsTo
    {
        return $this->belongsTo(SextanteIhos::class, 'sextante_ihos_id');
    }
}
