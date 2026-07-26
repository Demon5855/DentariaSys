<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OdontogramaHallazgo extends Model
{
    protected $fillable = ['odontograma_pieza_id', 'condicion_id', 'superficie'];

    public function odontogramaPieza(): BelongsTo
    {
        return $this->belongsTo(OdontogramaPieza::class);
    }

    public function condicion(): BelongsTo
    {
        return $this->belongsTo(Condicion::class);
    }
}
