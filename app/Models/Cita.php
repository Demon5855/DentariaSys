<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Cita extends Model implements Auditable
{
    use HasFactory, AuditingTrait;

    protected $fillable = [
        'paciente_id',
        'profesional_id',
        'fecha_hora',
        'duracion_minutos',
        'estado',
        'motivo',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora' => 'datetime',
        ];
    }

    /**
     * Qué estados puede seguir cada estado. 'atendida' y 'cancelada' son
     * terminales — una cita atendida ya generó (o no) su consulta por el
     * flujo normal, y no tiene sentido revivir una cancelada; si el
     * paciente quiere reagendar, se crea una cita nueva.
     */
    public const TRANSICIONES = [
        'pendiente' => ['confirmada', 'cancelada'],
        'confirmada' => ['atendida', 'cancelada', 'no_asistio'],
        'atendida' => [],
        'cancelada' => [],
        'no_asistio' => ['pendiente'], // se puede volver a intentar agendar el mismo registro
    ];

    public function puedeTransicionarA(string $estadoNuevo): bool
    {
        return in_array($estadoNuevo, self::TRANSICIONES[$this->estado] ?? [], true);
    }

    protected function finRango(): Attribute
    {
        return new Attribute(get: fn () => $this->fecha_hora->copy()->addMinutes($this->duracion_minutos));
    }

    /**
     * Dos rangos se solapan si uno empieza antes de que el otro termine
     * Y termina después de que el otro empiece — la fórmula clásica de
     * intersección de intervalos, resuelta en PHP con Carbon en vez de
     * en SQL (portable entre SQLite, PostgreSQL y MySQL).
     */
    public function seSolapaCon(self $otra): bool
    {
        return $this->fecha_hora->lt($otra->fin_rango) && $this->fin_rango->gt($otra->fecha_hora);
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function profesional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'profesional_id');
    }

    public function scopeActivas(Builder $query): Builder
    {
        return $query->whereNotIn('estado', ['cancelada', 'no_asistio']);
    }

    /**
     * Citas ACTIVAS de un profesional en el mismo día que $inicio. Filtro
     * amplio pero portable (funciona igual en SQLite, PostgreSQL y MySQL);
     * la comparación de solapamiento real de rangos horarios se hace en
     * PHP con Carbon — ver CitaSolapadaRule — para no depender de sintaxis
     * de intervalos específica de un motor de base de datos.
     */
    public function scopeDelMismoDiaYProfesional(Builder $query, int $profesionalId, \DateTimeInterface $fecha): Builder
    {
        return $query
            ->where('profesional_id', $profesionalId)
            ->whereDate('fecha_hora', $fecha)
            ->activas();
    }
}
