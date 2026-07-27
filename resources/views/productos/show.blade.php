<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $producto->nombre }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="text-sm text-gray-500">
                                {{ $producto->codigo_barras ?? 'Sin código de barras' }} · {{ $producto->unidad_medida }}
                                @if ($producto->categoria) · {{ $producto->categoria }} @endif
                            </p>
                            <p class="text-2xl font-bold mt-1">{{ $producto->stock_total }} {{ $producto->unidad_medida }}(s) en stock</p>
                        </div>
                        @can('gestionar', \App\Models\Producto::class)
                            <a href="{{ route('lotes.create', $producto) }}"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-md shadow-sm">
                                + Ingresar lote
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Lotes</h3>
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-xs uppercase text-gray-500 border-b">
                                <th class="pb-2">Lote</th>
                                <th class="pb-2">Caducidad</th>
                                <th class="pb-2 text-right">Cantidad</th>
                                <th class="pb-2">Estado</th>
                                @can('gestionar', \App\Models\Producto::class)
                                    <th class="pb-2"></th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($producto->lotes as $lote)
                                <tr class="border-b">
                                    <td class="py-2">{{ $lote->numero_lote ?? '—' }}</td>
                                    <td class="py-2">{{ $lote->fecha_caducidad->format('d/m/Y') }}</td>
                                    <td class="py-2 text-right">{{ $lote->cantidad_actual }}</td>
                                    <td class="py-2">
                                        @if ($lote->cantidad_actual === 0)
                                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">Agotado</span>
                                        @elseif ($lote->esta_caducado)
                                            <span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full">Caducado</span>
                                        @elseif ($lote->fecha_caducidad->lte(now()->addDays(30)))
                                            <span class="text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">Por vencer</span>
                                        @else
                                            <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Vigente</span>
                                        @endif
                                    </td>
                                    @can('gestionar', \App\Models\Producto::class)
                                        <td class="py-2 text-right">
                                            @if ($lote->cantidad_actual > 0)
                                                <a href="{{ route('lotes.crear-merma', $lote) }}" class="text-xs text-red-600 hover:underline">Merma / ajuste</a>
                                            @endif
                                        </td>
                                    @endcan
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-8 text-center text-gray-400">Sin lotes registrados todavía.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
