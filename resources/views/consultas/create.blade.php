<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nueva Consulta: <span class="text-indigo-600">{{ $historiaClinica->paciente->nombre_completo }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('consultas.store', $historiaClinica) }}" method="POST">
                        @csrf
                        <div class="space-y-6">

                            <div>
                                <x-input-label for="fecha" value="Fecha de la consulta" />
                                <x-text-input id="fecha" class="block mt-1 w-full" type="date"
                                    name="fecha" :value="old('fecha', now()->toDateString())" required />
                                <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="motivo_consulta" value="Motivo de consulta (palabras textuales del paciente)" />
                                <textarea id="motivo_consulta" name="motivo_consulta" rows="2" required
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('motivo_consulta') }}</textarea>
                                <x-input-error :messages="$errors->get('motivo_consulta')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="enfermedad_actual" value="Enfermedad o problema actual" />
                                <textarea id="enfermedad_actual" name="enfermedad_actual" rows="3"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('enfermedad_actual') }}</textarea>
                                <x-input-error :messages="$errors->get('enfermedad_actual')" class="mt-2" />
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="antecedentes_personales" value="Antecedentes patológicos personales" />
                                    <textarea id="antecedentes_personales" name="antecedentes_personales" rows="3"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('antecedentes_personales') }}</textarea>
                                    <x-input-error :messages="$errors->get('antecedentes_personales')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="antecedentes_familiares" value="Antecedentes patológicos familiares" />
                                    <textarea id="antecedentes_familiares" name="antecedentes_familiares" rows="3"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('antecedentes_familiares') }}</textarea>
                                    <x-input-error :messages="$errors->get('antecedentes_familiares')" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-700 mb-2">Constantes vitales</p>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div>
                                        <x-input-label for="presion_arterial" value="Presión arterial" />
                                        <x-text-input id="presion_arterial" class="block mt-1 w-full" type="text"
                                            name="presion_arterial" :value="old('presion_arterial')" placeholder="120/80" />
                                        <x-input-error :messages="$errors->get('presion_arterial')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="temperatura" value="Temperatura (°C)" />
                                        <x-text-input id="temperatura" class="block mt-1 w-full" type="number" step="0.1"
                                            name="temperatura" :value="old('temperatura')" />
                                        <x-input-error :messages="$errors->get('temperatura')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="pulso" value="Pulso / min" />
                                        <x-text-input id="pulso" class="block mt-1 w-full" type="number"
                                            name="pulso" :value="old('pulso')" />
                                        <x-input-error :messages="$errors->get('pulso')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="frecuencia_respiratoria" value="Frec. respiratoria / min" />
                                        <x-text-input id="frecuencia_respiratoria" class="block mt-1 w-full" type="number"
                                            name="frecuencia_respiratoria" :value="old('frecuencia_respiratoria')" />
                                        <x-input-error :messages="$errors->get('frecuencia_respiratoria')" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="examen_estomatognatico" value="Examen del sistema estomatognático" />
                                <textarea id="examen_estomatognatico" name="examen_estomatognatico" rows="3"
                                    placeholder="Sin patología aparente, o describir hallazgo por región"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('examen_estomatognatico') }}</textarea>
                                <x-input-error :messages="$errors->get('examen_estomatognatico')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('historias.show', $historiaClinica) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <x-primary-button>{{ __('Guardar Consulta') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
