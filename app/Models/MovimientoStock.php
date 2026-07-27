<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Es el libro mayor de inventario: cada fila explica cómo se llegó al
 * `cantidad_actual` de un lote. Igual que los registros clínicos, una vez
 * escrito no se corrige editándolo — un ajuste o merma se registra como
 * un movimiento nuevo — o la bitácora deja de servir para auditar.
 */
class MovimientoStock extends Model implements Auditable
{
    use HasFactory, AuditingTrait;

    protected $table = 'movimientos_stock';

    protected $fillable = ['lote_id', 'usuario_id', 'tratamiento_id', 'tipo', 'cantidad', 'motivo'];

    protected static function booted(): void
    {
        static::updating(fn () => throw new \RuntimeException('Un movimiento de stock ya registrado no puede modificarse.'));
        static::deleting(fn () => throw new \RuntimeException('Un movimiento de stock ya registrado no puede eliminarse.'));
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function tratamiento(): BelongsTo
    {
        return $this->belongsTo(Tratamiento::class);
    }
}
