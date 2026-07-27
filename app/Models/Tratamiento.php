<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Tratamiento extends Model implements Auditable
{
    use HasFactory, AuditingTrait;

    protected $fillable = [
        'consulta_id',
        'profesional_id',
        'diagnostico_complicaciones',
        'procedimiento',
        'prescripciones',
        'proxima_cita',
        'estado',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'proxima_cita' => 'date',
        ];
    }

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(Consulta::class);
    }

    public function profesional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'profesional_id');
    }

    /**
     * Insumos consumidos por este tratamiento. La cantidad vive en el
     * pivote; el descuento de stock real (con FIFO/FEFO) ya ocurrió al
     * guardarse — esta relación es para CONSULTAR qué se usó, no para
     * volver a descontar.
     */
    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'tratamiento_producto')
            ->withPivot('cantidad');
    }
}
