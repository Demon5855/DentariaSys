<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Inventario') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-4">
                <form method="GET" class="flex-1 mr-4">
                    <input type="text" name="buscar" value="{{ $buscar }}" placeholder="Buscar producto..."
                        class="w-full max-w-xs border-gray-300 rounded-md shadow-sm text-sm">
                </form>
                <div class="flex gap-2">
                    <a href="{{ route('productos.alertas') }}" class="px-4 py-2 border rounded-md text-sm hover:bg-gray-50">⚠ Alertas</a>
                    @can('gestionar', \App\Models\Producto::class)
                        <a href="{{ route('movimientos.crear-salida') }}" class="px-4 py-2 border rounded-md text-sm hover:bg-gray-50">Registrar salida</a>
                        <a href="{{ route('productos.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-md shadow-sm text-sm">
                            + Nuevo producto
                        </a>
                    @endcan
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase text-gray-500 border-b">
                                <th class="pb-2">Producto</th>
                                <th class="pb-2">Código de barras</th>
                                <th class="pb-2">Unidad</th>
                                <th class="pb-2 text-right">Stock actual</th>
                                <th class="pb-2 text-right">Mínimo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($productos as $producto)
                                @php $stock = (int) ($producto->stock_actual ?? 0); @endphp
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2">
                                        <a href="{{ route('productos.show', $producto) }}" class="text-brand-600 hover:underline">{{ $producto->nombre }}</a>
                                        @if ($producto->categoria)
                                            <span class="text-xs text-gray-400">({{ $producto->categoria }})</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-gray-500">{{ $producto->codigo_barras ?? '—' }}</td>
                                    <td class="py-2">{{ $producto->unidad_medida }}</td>
                                    <td class="py-2 text-right font-medium {{ $stock < $producto->stock_minimo ? 'text-red-600' : '' }}">
                                        {{ $stock }}
                                    </td>
                                    <td class="py-2 text-right text-gray-400">{{ $producto->stock_minimo }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-8 text-center text-gray-400">No hay productos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $productos->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
