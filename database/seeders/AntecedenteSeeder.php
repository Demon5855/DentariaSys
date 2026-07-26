<?php

namespace Database\Seeders;

use App\Models\Antecedente;
use Illuminate\Database\Seeder;

class AntecedenteSeeder extends Seeder
{
    /**
     * Lista literal del instructivo SNS-MSP/HCU-form.033/2021, secciones D
     * y E — es el mismo catálogo de 10 ítems para antecedentes personales
     * y familiares.
     */
    private const ANTECEDENTES = [
        1 => 'Alergia a antibiótico',
        2 => 'Alergia a anestesia',
        3 => 'Hemorragias',
        4 => 'VIH/SIDA',
        5 => 'Tuberculosis',
        6 => 'Asma',
        7 => 'Diabetes',
        8 => 'Hipertensión',
        9 => 'Enfermedad cardíaca',
        10 => 'Otros',
    ];

    public function run(): void
    {
        foreach (self::ANTECEDENTES as $codigo => $nombre) {
            Antecedente::firstOrCreate(['codigo' => $codigo], ['nombre' => $nombre]);
        }
    }
}
