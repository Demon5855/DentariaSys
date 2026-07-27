<?php

namespace App\Http\Controllers;

use App\Exceptions\StockInsuficienteException;
use App\Http\Requests\StoreSalidaRequest;
use App\Models\Producto;

class MovimientoStockController extends Controller
{
    public function create()
    {
        $this->authorize('gestionar', Producto::class);

        return view('movimientos.crear-salida');
    }

    public function store(StoreSalidaRequest $request)
    {
        $this->authorize('gestionar', Producto::class);

        $datos = $request->validated();
        $producto = Producto::findOrFail($datos['producto_id']);

        try {
            $producto->descontarStock($datos['cantidad'], [
                'usuario_id' => $request->user()->id,
                'motivo' => $datos['motivo'],
            ]);
        } catch (StockInsuficienteException $e) {
            return back()->withErrors(['cantidad' => $e->getMessage()])->withInput();
        }

        return redirect()->route('productos.index')->with('success', "Salida de {$datos['cantidad']} {$producto->unidad_medida}(s) de \"{$producto->nombre}\" registrada.");
    }
}
