<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OdontogramaPieza extends Model
{
    protected $fillable = ['odontograma_id', 'pieza', 'movilidad', 'recesion'];

    public function odontograma(): BelongsTo
    {
        return $this->belongsTo(Odontograma::class);
    }

    public function hallazgos(): HasMany
    {
        return $this->hasMany(OdontogramaHallazgo::class);
    }
}
