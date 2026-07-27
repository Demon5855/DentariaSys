<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Hereda la inmutabilidad de Odontograma: el controlador solo la crea,
 * nunca la actualiza tras el insert inicial, así que aquí el bloqueo de
 * 'updating'/'deleting' puede ser incondicional.
 */
class OdontogramaPieza extends Model implements Auditable
{
    use AuditingTrait;

    protected $fillable = ['odontograma_id', 'pieza', 'movilidad', 'recesion'];

    protected static function booted(): void
    {
        static::updating(fn () => throw new \RuntimeException('Una pieza de un odontograma firmado no puede modificarse.'));
        static::deleting(fn () => throw new \RuntimeException('Una pieza de un odontograma firmado no puede eliminarse.'));
    }

    public function odontograma(): BelongsTo
    {
        return $this->belongsTo(Odontograma::class);
    }

    public function hallazgos(): HasMany
    {
        return $this->hasMany(OdontogramaHallazgo::class);
    }
}
