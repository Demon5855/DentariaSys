<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Odontograma extends Model
{
    use HasFactory;

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
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'firmado_at' => 'datetime',
        ];
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
     * columnas cpod_*/ceod_*) como para verificación posterior.
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
