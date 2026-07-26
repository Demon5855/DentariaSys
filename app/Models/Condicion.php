<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Condicion extends Model
{
    protected $table = 'condiciones';

    protected $fillable = [
        'clave', 'nombre', 'nivel', 'color', 'simbolo',
        'afecta_indice', 'solo_definitivas', 'excluye_terceros_molares', 'orden',
    ];

    protected function casts(): array
    {
        return [
            'solo_definitivas' => 'boolean',
            'excluye_terceros_molares' => 'boolean',
        ];
    }
}