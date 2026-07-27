<?php

namespace Database\Seeders;

use App\Models\DiagnosticoCie10;
use Illuminate\Database\Seeder;

class DiagnosticoCie10Seeder extends Seeder
{
    /**
     * ⚠ Subconjunto de arranque, bloque K00-K14 (enfermedades de la
     * cavidad oral, glándulas salivales y maxilares) + un código Z común
     * en odontología. Es un estándar internacional estable, pero NO es la
     * tabla CIE-10 oficial completa del MSP — falta verificar contra esa
     * fuente antes de producción. Ampliar esta lista no requiere tocar
     * ningún otro archivo: solo agregar filas aquí.
     */
    private const CODIGOS = [
        ['K00.6', 'Trastornos en la erupción dentaria'],
        ['K00.7', 'Síndrome de erupción dentaria'],
        ['K01.1', 'Diente retenido'],
        ['K02.0', 'Caries limitada al esmalte'],
        ['K02.1', 'Caries de la dentina'],
        ['K02.9', 'Caries dental, no especificada'],
        ['K03.6', 'Depósitos [acreciones] en los dientes'],
        ['K04.0', 'Pulpitis'],
        ['K04.1', 'Necrosis de la pulpa'],
        ['K04.4', 'Periodontitis apical aguda de origen pulpar'],
        ['K04.5', 'Periodontitis apical crónica'],
        ['K04.6', 'Absceso periapical con fístula'],
        ['K04.7', 'Absceso periapical sin fístula'],
        ['K05.0', 'Gingivitis aguda'],
        ['K05.1', 'Gingivitis crónica'],
        ['K05.2', 'Periodontitis aguda'],
        ['K05.3', 'Periodontitis crónica'],
        ['K06.0', 'Retracción gingival'],
        ['K07.4', 'Maloclusión, no especificada'],
        ['K08.1', 'Pérdida de dientes por accidente, extracción o enfermedad periodontal'],
        ['K08.3', 'Raíz dentaria retenida'],
        ['K09.0', 'Quistes odontogénicos de desarrollo'],
        ['K12.0', 'Aftas orales recurrentes'],
        ['K12.3', 'Estomatitis, no especificada'],
        ['K13.7', 'Otras lesiones y las no especificadas de la mucosa oral'],
        ['Z01.2', 'Examen dental (chequeo de rutina, sin patología)'],
    ];

    public function run(): void
    {
        foreach (self::CODIGOS as [$codigo, $descripcion]) {
            DiagnosticoCie10::firstOrCreate(['codigo' => $codigo], ['descripcion' => $descripcion]);
        }
    }
}
