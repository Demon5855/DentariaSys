<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Registrar salida') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900"
                    x-data="{
                        codigo: '',
                        producto: null,
                        buscando: false,
                        noEncontrado: false,
                        async buscar() {
                            if (!this.codigo.trim()) return;
                            this.buscando = true;
                            this.noEncontrado = false;
                            const respuesta = await fetch(`{{ route('productos.buscar-por-codigo') }}?codigo=${encodeURIComponent(this.codigo)}`);
                            const datos = await respuesta.json();
                            this.buscando = false;
                            if (datos.encontrado) {
                                this.producto = datos;
                            } else {
                                this.producto = null;
                                this.noEncontrado = true;
                            }
                        },
                        reiniciar() {
                            this.codigo = '';
                            this.producto = null;
                            this.noEncontrado = false;
                            $refs.entradaCodigo.focus();
                        }
                    }">

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            @foreach ($errors->all() as $error)
                                <p class="text-sm">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div x-show="!producto">
                        <x-input-label value="Escanea el código de barras" />
                        <p class="text-xs text-gray-500 mb-2">
                            El cursor ya está en el campo — apunta el lector y dispara. Si no tienes lector,
                            escribe el código y presiona Enter.
                        </p>
                        <input type="text" x-ref="entradaCodigo" x-model="codigo" @keydown.enter.prevent="buscar()"
                            autofocus placeholder="Código de barras..."
                            class="block w-full border-gray-300 rounded-md shadow-sm text-lg tracking-wide">

                        <p x-show="buscando" class="text-sm text-gray-500 mt-2">Buscando...</p>
                        <p x-show="noEncontrado" x-cloak class="text-sm text-red-600 mt-2">
                            No se encontró ningún producto activo con ese código.
                        </p>
                    </div>

                    <form x-show="producto" x-cloak action="{{ route('movimientos.guardar-salida') }}" method="POST">
                        @csrf
                        <input type="hidden" name="producto_id" :value="producto?.id">

                        <div class="bg-gray-50 border rounded-md p-3 mb-4">
                            <p class="font-medium" x-text="producto?.nombre"></p>
                            <p class="text-xs text-gray-500">
                                Stock actual: <span x-text="producto?.stock_actual"></span>
                                <span x-text="producto?.unidad_medida"></span>(s)
                            </p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <x-input-label for="cantidad" value="Cantidad a descontar" />
                                <x-text-input id="cantidad" type="number" min="1" name="cantidad" class="block mt-1 w-full" :value="old('cantidad', 1)" required />
                            </div>
                            <div>
                                <x-input-label for="motivo" value="Motivo" />
                                <x-text-input id="motivo" name="motivo" class="block mt-1 w-full" :value="old('motivo')" placeholder="Uso en tratamiento, consumo general..." required />
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <button type="button" @click="reiniciar()" class="text-sm text-gray-600 hover:text-gray-900">
                                ← Escanear otro producto
                            </button>
                            <x-primary-button>{{ __('Registrar salida') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
