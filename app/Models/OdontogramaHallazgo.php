<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

class OdontogramaHallazgo extends Model implements Auditable
{
    use AuditingTrait;

    protected $fillable = ['odontograma_pieza_id', 'condicion_id', 'superficie'];

    protected static function booted(): void
    {
        static::updating(fn () => throw new \RuntimeException('Un hallazgo de un odontograma firmado no puede modificarse.'));
        static::deleting(fn () => throw new \RuntimeException('Un hallazgo de un odontograma firmado no puede eliminarse.'));
    }

    public function odontogramaPieza(): BelongsTo
    {
        return $this->belongsTo(OdontogramaPieza::class);
    }

    public function condicion(): BelongsTo
    {
        return $this->belongsTo(Condicion::class);
    }
}
