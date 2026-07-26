<?php

namespace Database\Seeders;

use App\Models\Condicion;
use Illuminate\Database\Seeder;

class CondicionSeeder extends Seeder
{
    /**
     * Reglas verificadas contra el instructivo SNS-MSP/HCU-form.033/2021,
     * bloque H. Dos reglas contraintuitivas que NO son error de tipeo:
     *   - endodoncia por realizar  -> cuenta como CARIADA (afecta_indice C)
     *   - endodoncia realizada     -> cuenta como OBTURADA (afecta_indice O)
     *   - prótesis total excluye terceros molares del CPO-D
     */
    private const CONDICIONES = [
        ['clave' => 'caries', 'nombre' => 'Caries', 'nivel' => 'superficie', 'color' => 'rojo', 'simbolo' => null, 'afecta_indice' => 'C'],
        ['clave' => 'obturado', 'nombre' => 'Obturado', 'nivel' => 'superficie', 'color' => 'azul', 'simbolo' => null, 'afecta_indice' => 'O'],

        ['clave' => 'sellante_nec', 'nombre' => 'Sellante necesario', 'nivel' => 'pieza', 'color' => 'rojo', 'simbolo' => '✱', 'afecta_indice' => null, 'solo_definitivas' => true],
        ['clave' => 'sellante_real', 'nombre' => 'Sellante realizado', 'nivel' => 'pieza', 'color' => 'azul', 'simbolo' => '✱', 'afecta_indice' => null, 'solo_definitivas' => true],

        ['clave' => 'extraccion_ind', 'nombre' => 'Extracción indicada', 'nivel' => 'pieza', 'color' => 'rojo', 'simbolo' => '✕', 'afecta_indice' => null],
        ['clave' => 'perdida_caries', 'nombre' => 'Pérdida por caries', 'nivel' => 'pieza', 'color' => 'azul', 'simbolo' => '✕', 'afecta_indice' => 'P'],
        ['clave' => 'perdida_otra', 'nombre' => 'Pérdida (otra causa)', 'nivel' => 'pieza', 'color' => 'azul', 'simbolo' => '⊗', 'afecta_indice' => null, 'solo_definitivas' => true],

        ['clave' => 'endodoncia_ind', 'nombre' => 'Endodoncia por realizar', 'nivel' => 'pieza', 'color' => 'rojo', 'simbolo' => '▲', 'afecta_indice' => 'C'],
        ['clave' => 'endodoncia_real', 'nombre' => 'Endodoncia realizada', 'nivel' => 'pieza', 'color' => 'azul', 'simbolo' => '▲', 'afecta_indice' => 'O'],

        ['clave' => 'corona', 'nombre' => 'Corona', 'nivel' => 'pieza', 'color' => 'azul', 'simbolo' => '▣', 'afecta_indice' => 'O'],

        ['clave' => 'protesis_total', 'nombre' => 'Prótesis total', 'nivel' => 'pieza', 'color' => 'azul', 'simbolo' => '═', 'afecta_indice' => 'P', 'excluye_terceros_molares' => true],
        ['clave' => 'protesis_remov', 'nombre' => 'Prótesis removible', 'nivel' => 'pieza', 'color' => 'azul', 'simbolo' => '⁙', 'afecta_indice' => 'P'],

        // Prótesis fija (puente): el instructivo distingue dos posiciones
        // dentro del mismo puente, cada una con su propio efecto en el
        // índice — "las coronas utilizadas como pilares" cuentan Obturado,
        // "las [piezas] reemplazadas [por pónticos]" cuentan Perdida. Por
        // eso son dos condiciones de nivel 'pieza', no una de nivel 'tramo':
        // se marcan individualmente en cada posición del puente.
        ['clave' => 'pf_pilar', 'nombre' => 'Prótesis fija · pilar', 'nivel' => 'pieza', 'color' => 'azul', 'simbolo' => '⊓', 'afecta_indice' => 'O'],
        ['clave' => 'pf_pontico', 'nombre' => 'Prótesis fija · póntico', 'nivel' => 'pieza', 'color' => 'azul', 'simbolo' => '⌐', 'afecta_indice' => 'P'],
    ];

    public function run(): void
    {
        foreach (self::CONDICIONES as $orden => $condicion) {
            Condicion::firstOrCreate(
                ['clave' => $condicion['clave']],
                $condicion + ['orden' => $orden, 'solo_definitivas' => false, 'excluye_terceros_molares' => false]
            );
        }
    }
}
