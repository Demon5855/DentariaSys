<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Paciente extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'fecha_nacimiento',
        'telefono',
        'direccion',
        'email',
        'activo', // Añadimos 'activo' para poder desactivar pacientes
    ];

    /**
     * Interact with the user's first name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function nombreCompleto(): Attribute
    {
        return new Attribute(
            get: fn () => "{$this->primer_nombre} {$this->segundo_nombre} {$this->primer_apellido} {$this->segundo_apellido}",
        );
    }

    /**
     * Define la relación "uno a uno" con HistoriaClinica.
     */
    public function historiaClinica(): HasOne
    {
        return $this->hasOne(HistoriaClinica::class);
    }
}