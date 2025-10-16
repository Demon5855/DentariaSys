<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Paciente extends Model
{
    use HasFactory;

    protected $primaryKey = 'pac_id';

    protected $fillable = [
        'pac_primer_nombre',
        'pac_segundo_nombre',
        'pac_primer_apellido',
        'pac_segundo_apellido',
        'pac_fecha_nacimiento',
        'pac_telefono',
        'pac_direccion',
        'pac_email',
        'pac_activo',
    ];

    protected function nombreCompleto(): Attribute
    {
        return new Attribute(
            get: fn () => trim("{$this->pac_primer_nombre} {$this->pac_segundo_nombre} {$this->pac_primer_apellido} {$this->pac_segundo_apellido}"),
        );
    }

    public function historiaClinica(): HasOne
    {
        return $this->hasOne(HistoriaClinica::class, 'hcl_pac_id', 'pac_id');
    }
}