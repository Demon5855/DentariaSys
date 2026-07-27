<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductoRequest;
use App\Models\Lote;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Producto::class, 'producto');
    }

    public function index(Request $request)
    {
        $buscar = $request->get('buscar');

        $productos = Producto::activos()
            ->withSum('lotes as stock_actual', 'cantidad_actual')
            ->when($buscar, fn ($q) => $q->where('nombre', 'like', '%' . mb_strtolower($buscar) . '%'))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('productos.index', compact('productos', 'buscar'));
    }

    public function create()
    {
        return view('productos.create');
    }

    public function store(StoreProductoRequest $request)
    {
        $producto = Producto::create($request->validated());

        return redirect()->route('productos.show', $producto)->with('success', 'Producto creado. Ahora registra su primer lote.');
    }

    public function show(Producto $producto)
    {
        $producto->load(['lotes' => fn ($q) => $q->orderBy('fecha_caducidad')]);

        return view('productos.show', compact('producto'));
    }

    /**
     * Vista de alertas: productos bajo el mínimo configurado + lotes que
     * vencen en los próximos 30 días. Es el reemplazo práctico de un
     * "recordatorio" automático — el personal la revisa periódicamente.
     */
    public function alertas()
    {
        $this->authorize('viewAny', Producto::class);

        $bajoMinimo = Producto::bajoMinimo()->with('lotes')->orderBy('nombre')->get();
        $porVencer = Lote::porVencer(30)->with('producto')->orderBy('fecha_caducidad')->get();

        return view('productos.alertas', compact('bajoMinimo', 'porVencer'));
    }

    /**
     * Endpoint para el escáner de código de barras: un lector USB escribe
     * el código y un Enter — el JS del formulario de salida hace un fetch
     * aquí apenas detecta el Enter, sin librería especial (ver
     * movimientos/crear-salida.blade.php).
     */
    public function buscarPorCodigo(Request $request)
    {
        $this->authorize('viewAny', Producto::class);

        $producto = Producto::activos()
            ->where('codigo_barras', $request->get('codigo'))
            ->withSum('lotes as stock_actual', 'cantidad_actual')
            ->first();

        if (! $producto) {
            return response()->json(['encontrado' => false]);
        }

        return response()->json([
            'encontrado' => true,
            'id' => $producto->id,
            'nombre' => $producto->nombre,
            'unidad_medida' => $producto->unidad_medida,
            'stock_actual' => (int) ($producto->stock_actual ?? 0),
        ]);
    }
}