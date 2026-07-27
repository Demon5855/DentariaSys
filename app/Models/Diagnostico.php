<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Diagnostico extends Model implements Auditable
{
    use HasFactory, AuditingTrait;

    protected $fillable = ['consulta_id', 'diagnostico_cie10_id', 'descripcion', 'estado', 'orden'];

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(Consulta::class);
    }

    public function cie10(): BelongsTo
    {
        return $this->belongsTo(DiagnosticoCie10::class, 'diagnostico_cie10_id');
    }
}
