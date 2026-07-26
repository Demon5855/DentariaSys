<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Consulta extends Model implements Auditable
{
    use HasFactory, AuditingTrait;

    protected $fillable = [
        'historia_clinica_id',
        'profesional_id',
        'fecha',
        'motivo_consulta',
        'enfermedad_actual',
        'antecedentes_personales',
        'antecedentes_familiares',
        'presion_arterial',
        'temperatura',
        'pulso',
        'frecuencia_respiratoria',
        'examen_estomatognatico',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'temperatura' => 'decimal:1',
        ];
    }

    public function historiaClinica(): BelongsTo
    {
        return $this->belongsTo(HistoriaClinica::class);
    }

    public function profesional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'profesional_id');
    }
}
