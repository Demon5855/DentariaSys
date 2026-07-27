<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Cita') }}: {{ $cita->paciente->nombre_completo }}
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

                    <p class="text-xs text-gray-500 mb-4">
                        Para cambiar de paciente, cancela esta cita y agenda una nueva —
                        el paciente no se reasigna aquí para no perder el historial de quién era originalmente.
                    </p>

                    <form action="{{ route('citas.update', $cita) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="paciente_id" value="{{ $cita->paciente_id }}">

                        <div class="space-y-6">
                            <div>
                                <x-input-label for="profesional_id" value="Profesional (opcional)" />
                                <select id="profesional_id" name="profesional_id"
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Sin asignar todavía</option>
                                    @foreach ($profesionales as $profesional)
                                        <option value="{{ $profesional->id }}" @selected(old('profesional_id', $cita->profesional_id) == $profesional->id)>
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
                                        class="block mt-1 w-full"
                                        :value="old('fecha_hora', $cita->fecha_hora->format('Y-m-d\TH:i'))" required />
                                    <x-input-error :messages="$errors->get('fecha_hora')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="duracion_minutos" value="Duración (minutos)" />
                                    <x-text-input id="duracion_minutos" type="number" name="duracion_minutos" min="5" max="480"
                                        class="block mt-1 w-full" :value="old('duracion_minutos', $cita->duracion_minutos)" required />
                                    <x-input-error :messages="$errors->get('duracion_minutos')" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <x-input-label for="motivo" value="Motivo (opcional)" />
                                <x-text-input id="motivo" type="text" name="motivo" class="block mt-1 w-full" :value="old('motivo', $cita->motivo)" />
                                <x-input-error :messages="$errors->get('motivo')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="notas" value="Notas internas (opcional)" />
                                <textarea id="notas" name="notas" rows="3"
                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('notas', $cita->notas) }}</textarea>
                                <x-input-error :messages="$errors->get('notas')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('citas.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <x-primary-button>{{ __('Guardar cambios') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
