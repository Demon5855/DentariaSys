<?php

namespace App\Exceptions;

use RuntimeException;

class StockInsuficienteException extends RuntimeException
{
    public function __construct(
        public readonly string $productoNombre,
        public readonly int $solicitado,
        public readonly int $disponible,
    ) {
        parent::__construct(
            "Stock insuficiente de \"{$productoNombre}\": se pidieron {$solicitado}, hay {$disponible} disponibles."
        );
    }
}
