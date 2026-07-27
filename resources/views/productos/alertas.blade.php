<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Alertas de inventario') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-1">Bajo el stock mínimo</h3>
                    <p class="text-xs text-gray-500 mb-4">Productos con menos unidades de las configuradas como mínimo — hora de reponer.</p>

                    @forelse ($bajoMinimo as $producto)
                        <div class="flex justify-between items-center border rounded-md p-3 mb-2">
                            <a href="{{ route('productos.show', $producto) }}" class="text-sm text-indigo-600 hover:underline">{{ $producto->nombre }}</a>
                            <span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full">
                                {{ $producto->stock_total }} / {{ $producto->stock_minimo }} mínimo
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">Todo el inventario está sobre el mínimo configurado.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-1">Lotes que vencen en los próximos 30 días</h3>
                    <p class="text-xs text-gray-500 mb-4">Prioriza usarlos primero (el sistema ya lo hace automáticamente en las salidas — esto es para que sepas qué se acerca).</p>

                    @forelse ($porVencer as $lote)
                        <div class="flex justify-between items-center border rounded-md p-3 mb-2">
                            <div>
                                <a href="{{ route('productos.show', $lote->producto) }}" class="text-sm text-indigo-600 hover:underline">{{ $lote->producto->nombre }}</a>
                                <span class="text-xs text-gray-500">{{ $lote->numero_lote ? '· Lote '.$lote->numero_lote : '' }} · {{ $lote->cantidad_actual }} {{ $lote->producto->unidad_medida }}(s)</span>
                            </div>
                            <span class="text-xs bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full">
                                Vence {{ $lote->fecha_caducidad->format('d/m/Y') }} ({{ now()->diffInDays($lote->fecha_caducidad) }} días)
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">No hay lotes por vencer en los próximos 30 días.</p>
                    @endforelse
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('productos.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Volver al inventario</a>
            </div>
        </div>
    </div>
</x-app-layout>