<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'tipo_vigencia',
        'fecha_vencimiento',
        'fecha_probable_parto',
        'fecha_fin_periodo_lectivo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_apertura' => 'date',
            'fecha_vencimiento' => 'date',
            'fecha_probable_parto' => 'date',
            'fecha_fin_periodo_lectivo' => 'date',
        ];
    }

    /**
     * Calcula fecha_vencimiento según el instructivo del Form 033:
     *   general  -> fecha_apertura + 365 días (un año calendario)
     *   embarazo -> la fecha probable de parto
     *   escolar  -> el fin del período lectivo
     *
     * Se usa desde el controlador antes de crear el registro, para no
     * duplicar esta regla de negocio en dos sitios.
     */
    public static function calcularFechaVencimiento(
        string $tipoVigencia,
        Carbon $fechaApertura,
        ?Carbon $fechaProbableParto,
        ?Carbon $fechaFinPeriodoLectivo,
    ): Carbon {
        return match ($tipoVigencia) {
            'embarazo' => $fechaProbableParto,
            'escolar' => $fechaFinPeriodoLectivo,
            default => $fechaApertura->copy()->addDays(365),
        };
    }

    protected function estaVencida(): Attribute
    {
        return new Attribute(get: fn () => now()->startOfDay()->greaterThan($this->fecha_vencimiento));
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
