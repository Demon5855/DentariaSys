<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ingresar lote: {{ $producto->nombre }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('lotes.store', $producto) }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="numero_lote" value="Número de lote del fabricante (opcional)" />
                                <x-text-input id="numero_lote" name="numero_lote" class="block mt-1 w-full" :value="old('numero_lote')" />
                                <x-input-error :messages="$errors->get('numero_lote')" class="mt-2" />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="fecha_ingreso" value="Fecha de ingreso" />
                                    <x-text-input id="fecha_ingreso" type="date" name="fecha_ingreso" class="block mt-1 w-full" :value="old('fecha_ingreso', now()->toDateString())" required />
                                    <x-input-error :messages="$errors->get('fecha_ingreso')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="fecha_caducidad" value="Fecha de caducidad" />
                                    <x-text-input id="fecha_caducidad" type="date" name="fecha_caducidad" class="block mt-1 w-full" :value="old('fecha_caducidad')" required />
                                    <x-input-error :messages="$errors->get('fecha_caducidad')" class="mt-2" />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="cantidad_inicial" value="Cantidad" />
                                    <x-text-input id="cantidad_inicial" type="number" min="1" name="cantidad_inicial" class="block mt-1 w-full" :value="old('cantidad_inicial')" required />
                                    <x-input-error :messages="$errors->get('cantidad_inicial')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="costo_unitario" value="Costo unitario (opcional)" />
                                    <x-text-input id="costo_unitario" type="number" step="0.01" min="0" name="costo_unitario" class="block mt-1 w-full" :value="old('costo_unitario')" />
                                    <x-input-error :messages="$errors->get('costo_unitario')" class="mt-2" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="proveedor" value="Proveedor (opcional)" />
                                <x-text-input id="proveedor" name="proveedor" class="block mt-1 w-full" :value="old('proveedor')" />
                                <x-input-error :messages="$errors->get('proveedor')" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('productos.show', $producto) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <x-primary-button>{{ __('Registrar lote') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
