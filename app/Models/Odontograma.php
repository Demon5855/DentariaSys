<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable as AuditingTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Odontograma extends Model implements Auditable
{
    use HasFactory, AuditingTrait;

    /** FDI: terceros molares, excluidos del CPO-D cuando la causa es prótesis total. */
    private const TERCEROS_MOLARES = [18, 28, 38, 48];

    protected $fillable = [
        'historia_clinica_id',
        'consulta_id',
        'odontologo_id',
        'tipo',
        'denticion',
        'fecha',
        'firmado_at',
        'cpod_c', 'cpod_p', 'cpod_o',
        'ceod_c', 'ceod_e', 'ceod_o',
        'ihos_placa_promedio', 'ihos_calculo_promedio', 'ihos_gingivitis_promedio',
        'enfermedad_periodontal', 'tipo_oclusion', 'fluorosis',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'firmado_at' => 'datetime',
            'ihos_placa_promedio' => 'decimal:2',
            'ihos_calculo_promedio' => 'decimal:2',
            'ihos_gingivitis_promedio' => 'decimal:2',
        ];
    }

    /**
     * Guarda de inmutabilidad a nivel de modelo. El instructivo es
     * explícito: "una vez registrado el odontograma no podrá ser
     * alterado (repintados, tachado, aumentado)". Hasta ahora esto solo
     * se cumplía porque no existe ruta de 'update' — una garantía de
     * convención, no del modelo. Aquí se bloquea cualquier UPDATE que no
     * venga de la propia transacción de creación (OdontogramaController::
     * store() sí necesita actualizar los índices cpod, ceod e ihos una
     * vez calculados, y eso sigue funcionando porque `wasRecentlyCreated`
     * permanece true durante esa misma request).
     * También se bloquea el borrado por completo: no existe caso de
     * negocio para borrar un documento médico-legal firmado.
     */
    protected static function booted(): void
    {
        static::updating(function (self $odontograma) {
            if (! $odontograma->wasRecentlyCreated) {
                throw new \RuntimeException(
                    'Un odontograma firmado no puede modificarse. Registra uno nuevo de tipo "evolutivo" para corregir.'
                );
            }
        });

        static::deleting(function () {
            throw new \RuntimeException('Un odontograma firmado no puede eliminarse.');
        });
    }

    public function historiaClinica(): BelongsTo
    {
        return $this->belongsTo(HistoriaClinica::class);
    }

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(Consulta::class);
    }

    public function odontologo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'odontologo_id');
    }

    public function piezas(): HasMany
    {
        return $this->hasMany(OdontogramaPieza::class)->orderBy('pieza');
    }

    public function ihosRegistros(): HasMany
    {
        return $this->hasMany(OdontogramaIhos::class)->orderBy('sextante_ihos_id');
    }

    /**
     * Promedia placa/cálculo/gingivitis SOLO sobre los sextantes que
     * tuvieron una pieza examinada (pieza_examinada no nulo). El
     * instructivo pide dividir "para el número de dientes examinados", es
     * decir, los sextantes marcados "—" no entran ni al numerador ni al
     * denominador. Y pide explícitamente NO redondear hacia arriba —aquí
     * se guarda el promedio real, sin ceil().
     *
     * @param  \Illuminate\Support\Collection<int, OdontogramaIhos>  $registros
     */
    public static function calcularPromediosIhos($registros): array
    {
        $conPieza = collect($registros)->filter(fn (OdontogramaIhos $r) => $r->pieza_examinada !== null);

        $promedio = function (string $campo) use ($conPieza) {
            $valores = $conPieza->pluck($campo)->filter(fn ($v) => $v !== null);

            return $valores->isEmpty() ? null : round($valores->avg(), 2);
        };

        return [
            'placa' => $promedio('placa'),
            'calculo' => $promedio('calculo'),
            'gingivitis' => $promedio('gingivitis'),
        ];
    }

    public static function esPiezaPermanente(int $pieza): bool
    {
        return $pieza >= 11 && $pieza <= 48;
    }

    public static function esPiezaTemporal(int $pieza): bool
    {
        return $pieza >= 51 && $pieza <= 85;
    }

    /**
     * Calcula CPO-D / ceo-d desde piezas YA GUARDADAS (con sus hallazgos
     * cargados). Se usa tanto al firmar (para congelar el resultado en las
     * columnas cpod_c/p/o y ceod_c/e/o) como para verificación posterior.
     *
     * Precedencia MSP: Pérdida > Cariada > Obturada. Prótesis total no
     * cuenta terceros molares.
     */
    public static function calcularIndices(Collection $piezas): array
    {
        $cpod = ['c' => 0, 'p' => 0, 'o' => 0];
        $ceod = ['c' => 0, 'e' => 0, 'o' => 0];

        foreach ($piezas as $pieza) {
            $condiciones = $pieza->hallazgos->map(fn (OdontogramaHallazgo $h) => $h->condicion);

            $excluida = $condiciones->contains(
                fn (Condicion $c) => $c->excluye_terceros_molares && in_array($pieza->pieza, self::TERCEROS_MOLARES)
            );
            if ($excluida) {
                continue;
            }

            $indices = $condiciones->pluck('afecta_indice')->filter();

            $clase = match (true) {
                $indices->contains('P') => 'P',
                $indices->contains('C') => 'C',
                $indices->contains('O') => 'O',
                default => null,
            };

            if (! $clase) {
                continue;
            }

            if (self::esPiezaPermanente($pieza->pieza)) {
                match ($clase) {
                    'C' => $cpod['c']++,
                    'P' => $cpod['p']++,
                    'O' => $cpod['o']++,
                };
            } elseif (self::esPiezaTemporal($pieza->pieza)) {
                match ($clase) {
                    'C' => $ceod['c']++,
                    'P' => $ceod['e']++, // en temporal, la "pérdida" se llama "e" (extraída)
                    'O' => $ceod['o']++,
                };
            }
        }

        return ['cpod' => $cpod, 'ceod' => $ceod];
    }
}
