<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Nuevo Producto') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('productos.store') }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="nombre" value="Nombre" />
                                <x-text-input id="nombre" name="nombre" class="block mt-1 w-full" :value="old('nombre')" required autofocus />
                                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="codigo_barras" value="Código de barras (opcional)" />
                                <x-text-input id="codigo_barras" name="codigo_barras" class="block mt-1 w-full" :value="old('codigo_barras')" />
                                <p class="text-xs text-gray-500 mt-1">Puedes escanearlo aquí directamente si tienes el lector conectado.</p>
                                <x-input-error :messages="$errors->get('codigo_barras')" class="mt-2" />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="unidad_medida" value="Unidad de medida" />
                                    <x-text-input id="unidad_medida" name="unidad_medida" class="block mt-1 w-full" :value="old('unidad_medida', 'unidad')" required />
                                    <x-input-error :messages="$errors->get('unidad_medida')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="stock_minimo" value="Stock mínimo (alerta de reposición)" />
                                    <x-text-input id="stock_minimo" type="number" min="0" name="stock_minimo" class="block mt-1 w-full" :value="old('stock_minimo', 0)" required />
                                    <x-input-error :messages="$errors->get('stock_minimo')" class="mt-2" />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="categoria" value="Categoría (opcional)" />
                                <x-text-input id="categoria" name="categoria" class="block mt-1 w-full" :value="old('categoria')" placeholder="Anestésicos, restauración, desechables..." />
                                <x-input-error :messages="$errors->get('categoria')" class="mt-2" />
                            </div>
                        </div>
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('productos.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <x-primary-button>{{ __('Guardar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
