<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriaClinica extends Model
{
    use HasFactory;

    protected $primaryKey = 'hcl_id';

    protected $fillable = [
        'hcl_pac_id',
        'hcl_fecha_apertura',
        'hcl_antecedentes_personales',
        'hcl_antecedentes_familiares',
        'hcl_examen_clinico_general',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'hcl_pac_id', 'pac_id');
    }
}