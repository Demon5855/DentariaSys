<?php

namespace Database\Seeders;

use App\Models\RegionEstomatognatica;
use Illuminate\Database\Seeder;

class RegionEstomatognaticaSeeder extends Seeder
{
    /**
     * Regiones tal como aparecen en la réplica del formulario 033 que
     * armamos en la fase del formulario. El instructivo describe la
     * metodología de examen para más puntos (cavidad oral, características
     * de la saliva) que no incluí aquí como casillas independientes —
     * pendiente de confirmar contra el formulario impreso.
     */
    private const REGIONES = [
        1 => 'Labios',
        2 => 'Mejillas',
        3 => 'Maxilar superior',
        4 => 'Maxilar inferior',
        5 => 'Lengua',
        6 => 'Paladar',
        7 => 'Piso de boca',
        8 => 'Carrillos',
        9 => 'Glándulas salivales',
        10 => 'Oro faringe',
        11 => 'Articulación témporo mandibular',
        12 => 'Ganglios',
    ];

    public function run(): void
    {
        foreach (self::REGIONES as $numero => $nombre) {
            RegionEstomatognatica::firstOrCreate(['numero' => $numero], ['nombre' => $nombre]);
        }
    }
}
