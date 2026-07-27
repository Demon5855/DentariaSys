<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Lote extends Model implements Auditable
{
    use HasFactory, AuditingTrait;

    protected $fillable = [
        'producto_id', 'numero_lote', 'fecha_caducidad', 'fecha_ingreso',
        'proveedor', 'costo_unitario', 'cantidad_inicial', 'cantidad_actual',
    ];

    protected function casts(): array
    {
        return [
            'fecha_caducidad' => 'date',
            'fecha_ingreso' => 'date',
            'costo_unitario' => 'decimal:2',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoStock::class)->orderByDesc('created_at');
    }

    protected function estaCaducado(): Attribute
    {
        return new Attribute(get: fn () => now()->startOfDay()->greaterThan($this->fecha_caducidad));
    }

    public function scopePorVencer(Builder $query, int $dias = 30): Builder
    {
        return $query
            ->where('cantidad_actual', '>', 0)
            ->whereBetween('fecha_caducidad', [now()->startOfDay(), now()->addDays($dias)->endOfDay()]);
    }

    public function scopeConStock(Builder $query): Builder
    {
        return $query->where('cantidad_actual', '>', 0);
    }
}
