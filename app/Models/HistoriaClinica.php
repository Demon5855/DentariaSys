<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

class HistoriaClinica extends Model implements Auditable
{
    use HasFactory, AuditingTrait;

    protected $fillable = [
        'paciente_id',
        'fecha_apertura',
    ];

    protected function casts(): array
    {
        return [
            'fecha_apertura' => 'date',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function consultas(): HasMany
    {
        return $this->hasMany(Consulta::class)->orderByDesc('fecha');
    }
}
