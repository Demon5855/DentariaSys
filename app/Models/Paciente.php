<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Paciente extends Model implements Auditable
{
    use HasFactory, AuditingTrait;

    protected $fillable = [
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'fecha_nacimiento',
        'telefono',
        'direccion',
        'email',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'activo' => 'boolean',
        ];
    }

    protected function nombreCompleto(): Attribute
    {
        return new Attribute(
            get: fn () => trim("{$this->primer_nombre} {$this->segundo_nombre} {$this->primer_apellido} {$this->segundo_apellido}"),
        );
    }

    public function historiaClinica(): HasOne
    {
        return $this->hasOne(HistoriaClinica::class);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function scopeInactivos(Builder $query): Builder
    {
        return $query->where('activo', false);
    }

    /**
     * Búsqueda portable: LOWER()/LIKE funciona igual en SQLite, PostgreSQL
     * y MySQL, a diferencia de ILIKE que solo existe en PostgreSQL.
     */
    public function scopeBuscar(Builder $query, ?string $texto): Builder
    {
        if (! $texto) {
            return $query;
        }

        $termino = '%' . mb_strtolower($texto) . '%';

        return $query->where(function (Builder $q) use ($termino) {
            $q->whereRaw('LOWER(primer_nombre) LIKE ?', [$termino])
                ->orWhereRaw('LOWER(segundo_nombre) LIKE ?', [$termino])
                ->orWhereRaw('LOWER(primer_apellido) LIKE ?', [$termino])
                ->orWhereRaw('LOWER(segundo_apellido) LIKE ?', [$termino])
                ->orWhereRaw('LOWER(email) LIKE ?', [$termino]);
        });
    }
}
