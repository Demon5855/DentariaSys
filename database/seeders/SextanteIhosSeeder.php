<?php

namespace Database\Seeders;

use App\Models\SextanteIhos;
use Illuminate\Database\Seeder;

class SextanteIhosSeeder extends Seeder
{
    /**
     * [numero, pieza_primaria, pieza_alterna, pieza_temporal] — tabla
     * ejemplo del instructivo del Form 033, sección I. Dentición
     * permanente: primaria/alterna. Dentición temporal: pieza equivalente.
     */
    private const SEXTANTES = [
        [1, 16, 17, 55],
        [2, 11, 21, 51],
        [3, 26, 27, 65],
        [4, 36, 37, 75],
        [5, 31, 41, 71],
        [6, 46, 47, 85],
    ];

    public function run(): void
    {
        foreach (self::SEXTANTES as [$numero, $primaria, $alterna, $temporal]) {
            SextanteIhos::firstOrCreate(
                ['numero' => $numero],
                ['pieza_primaria' => $primaria, 'pieza_alterna' => $alterna, 'pieza_temporal' => $temporal]
            );
        }
    }
}
