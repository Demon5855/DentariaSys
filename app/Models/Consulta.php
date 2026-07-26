<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

    /**
     * Casillas D marcadas ("X") — el catálogo es el mismo para personal y
     * familiar, el pivote 'tipo' distingue cuál es cuál. La descripción en
     * texto libre sigue viviendo en $antecedentes_personales (columna de
     * texto), tal como en el formulario impreso: casillas + una línea
     * narrativa abajo, no una descripción por casilla.
     */
    public function antecedentesPersonalesMarcados(): BelongsToMany
    {
        return $this->belongsToMany(Antecedente::class, 'consulta_antecedente')
            ->wherePivot('tipo', 'personal')
            ->orderBy('codigo');
    }

    /**
     * Casillas E marcadas — mismo catálogo, tipo 'familiar'.
     */
    public function antecedentesFamiliaresMarcados(): BelongsToMany
    {
        return $this->belongsToMany(Antecedente::class, 'consulta_antecedente')
            ->wherePivot('tipo', 'familiar')
            ->orderBy('codigo');
    }

    /**
     * Regiones G marcadas como afectadas. La descripción narrativa sigue en
     * $examen_estomatognatico (texto libre), igual que en el formulario.
     */
    public function regionesAfectadas(): BelongsToMany
    {
        return $this->belongsToMany(
            RegionEstomatognatica::class,
            'consulta_region_estomatognatica',
            'consulta_id',
            'region_estomatognatica_id'
        )->orderBy('numero');
    }
}
