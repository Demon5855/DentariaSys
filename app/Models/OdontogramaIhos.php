<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

class OdontogramaIhos extends Model implements Auditable
{
    use AuditingTrait;

    protected $table = 'odontograma_ihos';

    protected $fillable = ['odontograma_id', 'sextante_ihos_id', 'pieza_examinada', 'placa', 'calculo', 'gingivitis'];

    protected static function booted(): void
    {
        static::updating(fn () => throw new \RuntimeException('Un registro IHOS de un odontograma firmado no puede modificarse.'));
        static::deleting(fn () => throw new \RuntimeException('Un registro IHOS de un odontograma firmado no puede eliminarse.'));
    }

    public function odontograma(): BelongsTo
    {
        return $this->belongsTo(Odontograma::class);
    }

    public function sextante(): BelongsTo
    {
        return $this->belongsTo(SextanteIhos::class, 'sextante_ihos_id');
    }
}
