<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoteRequest;
use App\Http\Requests\StoreMermaRequest;
use App\Models\Lote;
use App\Models\MovimientoStock;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class LoteController extends Controller
{
    public function create(Producto $producto)
    {
        $this->authorize('gestionar', Producto::class);

        return view('lotes.create', compact('producto'));
    }

    public function store(StoreLoteRequest $request, Producto $producto)
    {
        $this->authorize('gestionar', Producto::class);

        $datos = $request->validated();

        DB::transaction(function () use ($datos, $producto, $request) {
            $lote = $producto->lotes()->create($datos + ['cantidad_actual' => $datos['cantidad_inicial']]);

            MovimientoStock::create([
                'lote_id' => $lote->id,
                'usuario_id' => $request->user()->id,
                'tipo' => 'entrada',
                'cantidad' => $lote->cantidad_inicial,
                'motivo' => 'Ingreso de lote' . ($lote->numero_lote ? " #{$lote->numero_lote}" : ''),
            ]);
        });

        return redirect()->route('productos.show', $producto)->with('success', 'Lote registrado exitosamente.');
    }

    public function crearMerma(Lote $lote)
    {
        $this->authorize('gestionar', Producto::class);

        $lote->load('producto');

        return view('lotes.merma', compact('lote'));
    }

    public function guardarMerma(StoreMermaRequest $request, Lote $lote)
    {
        $this->authorize('gestionar', Producto::class);

        $datos = $request->validated();

        DB::transaction(function () use ($datos, $lote, $request) {
            $lote->decrement('cantidad_actual', $datos['cantidad']);

            MovimientoStock::create([
                'lote_id' => $lote->id,
                'usuario_id' => $request->user()->id,
                'tipo' => $datos['tipo'],
                'cantidad' => $datos['cantidad'],
                'motivo' => $datos['motivo'],
            ]);
        });

        return redirect()->route('productos.show', $lote->producto)->with('success', 'Movimiento registrado exitosamente.');
    }
}
