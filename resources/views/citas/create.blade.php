<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agendar Cita') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <p class="font-bold mb-1">Revisa lo siguiente:</p>
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('citas.store') }}" method="POST"
                        x-data="{
                            terminoBusqueda: '',
                            resultados: [],
                            pacienteSeleccionado: null,
                            async buscar() {
                                if (this.terminoBusqueda.length < 2) { this.resultados = []; return; }
                                const respuesta = await fetch(`{{ route('citas.buscar-pacientes') }}?q=${encodeURIComponent(this.terminoBusqueda)}`);
                                this.resultados = await respuesta.json();
                            },
                            seleccionar(paciente) {
                                this.pacienteSeleccionado = paciente;
                                this.terminoBusqueda = paciente.texto;
                                this.resultados = [];
                            }
                        }">
                        @csrf

                        <div class="space-y-6">
                            <div class="relative">
                                <x-input-label value="Paciente" />
                                <input type="text" x-model="terminoBusqueda" @input.debounce.300ms="buscar()"
                                    placeholder="Busca por nombre o documento..."
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <input type="hidden" name="paciente_id" :value="pacienteSeleccionado?.id">

                                <ul x-show="resultados.length > 0" x-cloak
                                    class="absolute z-10 w-full bg-white border rounded-md shadow-lg mt-1 max-h-48 overflow-y-auto">
                                    <template x-for="resultado in resultados" :key="resultado.id">
                                        <li @click="seleccionar(resultado)"
                                            class="px-3 py-2 text-sm hover:bg-brand-50 cursor-pointer"
                                            x-text="resultado.texto"></li>
                                    </template>
                                </ul>
                                <x-input-error :messages="$errors->get('paciente_id')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="profesional_id" value="Profesional (opcional)" />
                                <select id="profesional_id" name="profesional_id"
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Sin asignar todavía</option>
                                    @foreach ($profesionales as $profesional)
                                        <option value="{{ $profesional->id }}" @selected(old('profesional_id') == $profesional->id)>
                                            {{ $profesional->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('profesional_id')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="fecha_hora" value="Fecha y hora" />
                                    <x-text-input id="fecha_hora" type="datetime-local" name="fecha_hora"
                                        class="block mt-1 w-full" :value="old('fecha_hora')" required />
                                    <x-input-error :messages="$errors->get('fecha_hora')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="duracion_minutos" value="Duración (minutos)" />
                                    <x-text-input id="duracion_minutos" type="number" name="duracion_minutos" min="5" max="480"
                                        class="block mt-1 w-full" :value="old('duracion_minutos', 30)" required />
                                    <x-input-error :messages="$errors->get('duracion_minutos')" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="motivo" value="Motivo (opcional)" />
                                <x-text-input id="motivo" type="text" name="motivo" class="block mt-1 w-full" :value="old('motivo')" />
                                <x-input-error :messages="$errors->get('motivo')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('citas.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <x-primary-button>{{ __('Agendar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
