<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antecedente extends Model
{
    protected $fillable = ['codigo', 'nombre'];

    public $timestamps = false;
}
