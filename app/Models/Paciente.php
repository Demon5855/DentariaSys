<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Paciente extends Model implements Auditable
{
    use HasFactory, AuditingTrait;

    protected $fillable = [
        'tipo_documento',
        'numero_documento',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'sexo',
        'fecha_nacimiento',
        'telefono',
        'direccion',
        'email',
        'representante_nombre',
        'representante_documento',
        'representante_parentesco',
        'representante_telefono',
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

    /**
     * Edad expresada como la pide la sección A del Form 033: en horas, días
     * o meses para bebés, y en años para el resto. Los cortes entre
     * categorías (< 1 día, < 24 meses) son una interpretación mía — el
     * instructivo no fija el umbral exacto, solo pide marcar la unidad.
     */
    protected function edadDetallada(): Attribute
    {
        return new Attribute(get: function () {
            $ahora = now();

            if ($this->fecha_nacimiento->diffInDays($ahora) < 1) {
                return $this->fecha_nacimiento->diffInHours($ahora) . ' horas';
            }

            if ($this->fecha_nacimiento->diffInMonths($ahora) < 1) {
                return $this->fecha_nacimiento->diffInDays($ahora) . ' días';
            }

            if ($this->fecha_nacimiento->diffInMonths($ahora) < 24) {
                return $this->fecha_nacimiento->diffInMonths($ahora) . ' meses';
            }

            return $this->fecha_nacimiento->age . ' años';
        });
    }

    protected function esMenorDeEdad(): Attribute
    {
        return new Attribute(get: fn () => $this->fecha_nacimiento->age < 18);
    }

    /**
     * Genera un código temporal de 17 dígitos para pacientes sin cédula,
     * pasaporte, ni carné de refugiado — tal como lo describe el
     * instructivo del Form 033 ("código de 17 dígitos temporales que será
     * emitido por el servicio de estadística"). Aquí no existe todavía un
     * módulo de estadística/establecimiento, así que se genera localmente;
     * se reintenta si por azar colisiona con uno existente.
     */
    public static function generarDocumentoTemporal(): string
    {
        do {
            $codigo = str_pad((string) random_int(0, 99999999999999999), 17, '0', STR_PAD_LEFT);
        } while (self::where('numero_documento', $codigo)->exists());

        return $codigo;
    }

    /**
     * Todas las historias clínicas del paciente a lo largo del tiempo.
     * Puede haber más de una: el instructivo obliga a abrir un 033 nuevo
     * cuando vence la vigencia (ver migración de historia_clinicas).
     */
    public function historiasClinicas(): HasMany
    {
        return $this->hasMany(HistoriaClinica::class)->orderByDesc('fecha_apertura');
    }

    /**
     * La historia clínica que sigue vigente hoy, si existe. Si el paciente
     * nunca tuvo una, o la última ya venció, esto es null — y en ese caso
     * corresponde abrir una nueva (ver HistoriaClinicaPolicy::create).
     */
    public function historiaClinicaVigente(): HasOne
    {
        return $this->hasOne(HistoriaClinica::class)
            ->where('fecha_vencimiento', '>=', now()->toDateString())
            ->latestOfMany('fecha_apertura');
    }

    /**
     * La historia clínica más reciente exista o no vigencia, para poder
     * mostrar "tu última historia venció el..." en vez de solo "no tiene".
     */
    public function historiaClinicaMasReciente(): HasOne
    {
        return $this->hasOne(HistoriaClinica::class)->latestOfMany('fecha_apertura');
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
                ->orWhereRaw('LOWER(numero_documento) LIKE ?', [$termino])
                ->orWhereRaw('LOWER(email) LIKE ?', [$termino]);
        });
    }
}
