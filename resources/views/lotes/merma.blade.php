<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Merma / ajuste: {{ $lote->producto->nombre }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-sm text-gray-600 mb-4">
                        Lote {{ $lote->numero_lote ?? 's/n' }} · vence {{ $lote->fecha_caducidad->format('d/m/Y') }}
                        · <strong>{{ $lote->cantidad_actual }}</strong> {{ $lote->producto->unidad_medida }}(s) disponibles
                    </p>

                    <form action="{{ route('lotes.guardar-merma', $lote) }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="tipo" value="Tipo" />
                                <select id="tipo" name="tipo" required class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="merma">Merma (dañado, vencido, roto)</option>
                                    <option value="ajuste">Ajuste (corrección de conteo)</option>
                                </select>
                                <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="cantidad" value="Cantidad a descontar" />
                                <x-text-input id="cantidad" type="number" min="1" max="{{ $lote->cantidad_actual }}" name="cantidad" class="block mt-1 w-full" :value="old('cantidad')" required />
                                <x-input-error :messages="$errors->get('cantidad')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="motivo" value="Motivo" />
                                <x-text-input id="motivo" name="motivo" class="block mt-1 w-full" :value="old('motivo')" required />
                                <x-input-error :messages="$errors->get('motivo')" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('productos.show', $lote->producto) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <x-primary-button>{{ __('Registrar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
