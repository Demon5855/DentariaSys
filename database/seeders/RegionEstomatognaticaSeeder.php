<?php

namespace Database\Seeders;

use App\Models\RegionEstomatognatica;
use Illuminate\Database\Seeder;

class RegionEstomatognaticaSeeder extends Seeder
{
    /**
     * Lista completa según la metodología de examen del instructivo
     * oficial (14 puntos, no 12). "Glándula parótida" queda como
     * sub-punto de "Glándulas salivales" en el instructivo, no como
     * región propia, así que no se agrega aparte. Sigue pendiente
     * verificar contra el formulario impreso si estas 14 corresponden
     * exactamente a las casillas dibujadas (ver pendientes-dentariasys.md,
     * ítem C.9), pero la metodología de examen ya no deja ambigüedad
     * sobre cuáles son los 14 puntos.
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
        10 => 'Cavidad oral',
        11 => 'Características de la saliva',
        12 => 'Oro faringe',
        13 => 'Articulación témporo mandibular',
        14 => 'Ganglios',
    ];

    public function run(): void
    {
        foreach (self::REGIONES as $numero => $nombre) {
            RegionEstomatognatica::firstOrCreate(['numero' => $numero], ['nombre' => $nombre]);
        }
    }
}
