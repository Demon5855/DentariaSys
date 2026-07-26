<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegionEstomatognatica extends Model
{
    protected $table = 'regiones_estomatognaticas';

    protected $fillable = ['numero', 'nombre'];

    public $timestamps = false;
}
