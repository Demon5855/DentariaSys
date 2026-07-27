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
                    <form action="{{ route('consultas.store', $historiaClinica) }}" method="POST"
                        x-data="{ diagnosticos: {{ old('diagnosticos') ? \Illuminate\Support\Js::from(old('diagnosticos')) : '[]' }} }">
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
                                    <x-input-label value="Antecedentes patológicos personales" />
                                    <div class="grid grid-cols-1 gap-1 mt-2 mb-3 border rounded-md p-3 bg-gray-50">
                                        @foreach ($antecedentes as $antecedente)
                                            <label class="flex items-center gap-2 text-sm">
                                                <input type="checkbox" name="antecedentes_personales_marcados[]" value="{{ $antecedente->id }}"
                                                    @checked(in_array($antecedente->id, old('antecedentes_personales_marcados', [])))
                                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                {{ $antecedente->codigo }}. {{ $antecedente->nombre }}
                                            </label>
                                        @endforeach
                                    </div>
                                    <x-input-error :messages="$errors->get('antecedentes_personales_marcados')" class="mt-2" />

                                    <x-input-label for="antecedentes_personales" value="Describir (ej: '1. Penicilina')" />
                                    <textarea id="antecedentes_personales" name="antecedentes_personales" rows="2"
                                        placeholder="No refiere antecedentes"
                                        class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('antecedentes_personales') }}</textarea>
                                    <x-input-error :messages="$errors->get('antecedentes_personales')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label value="Antecedentes patológicos familiares" />
                                    <p class="text-xs text-gray-500 mb-1">Hasta 3er grado de consanguinidad, 1ro de afinidad</p>
                                    <div class="grid grid-cols-1 gap-1 mt-2 mb-3 border rounded-md p-3 bg-gray-50">
                                        @foreach ($antecedentes as $antecedente)
                                            <label class="flex items-center gap-2 text-sm">
                                                <input type="checkbox" name="antecedentes_familiares_marcados[]" value="{{ $antecedente->id }}"
                                                    @checked(in_array($antecedente->id, old('antecedentes_familiares_marcados', [])))
                                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                {{ $antecedente->codigo }}. {{ $antecedente->nombre }}
                                            </label>
                                        @endforeach
                                    </div>
                                    <x-input-error :messages="$errors->get('antecedentes_familiares_marcados')" class="mt-2" />

                                    <x-input-label for="antecedentes_familiares" value="Describir" />
                                    <textarea id="antecedentes_familiares" name="antecedentes_familiares" rows="2"
                                        placeholder="No refiere antecedentes"
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
                                <x-input-label value="Examen del sistema estomatognático — regiones afectadas" />
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-1 mt-2 mb-3 border rounded-md p-3 bg-gray-50">
                                    @foreach ($regiones as $region)
                                        <label class="flex items-center gap-2 text-sm">
                                            <input type="checkbox" name="regiones_afectadas[]" value="{{ $region->id }}"
                                                @checked(in_array($region->id, old('regiones_afectadas', [])))
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            {{ $region->numero }}. {{ $region->nombre }}
                                        </label>
                                    @endforeach
                                </div>
                                <x-input-error :messages="$errors->get('regiones_afectadas')" class="mt-2" />

                                <x-input-label for="examen_estomatognatico" value="Describir hallazgo por región (ej: '5. Úlcera en borde lateral')" />
                                <textarea id="examen_estomatognatico" name="examen_estomatognatico" rows="3"
                                    placeholder="Sin patología aparente"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('examen_estomatognatico') }}</textarea>
                                <x-input-error :messages="$errors->get('examen_estomatognatico')" class="mt-2" />
                            </div>
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <x-input-label value="Diagnóstico (CIE-10)" />
                                    <button type="button" @click="diagnosticos.push({diagnostico_cie10_id: '', descripcion: '', estado: 'presuntivo'})"
                                        class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                                        + Agregar diagnóstico
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mb-3">
                                    El orden en que los agregues aquí queda como el orden de complejidad/urgencia
                                    del diagnóstico, según tu criterio clínico.
                                </p>

                                <template x-for="(diagnostico, index) in diagnosticos" :key="index">
                                    <div class="border rounded-md p-3 mb-3 bg-gray-50">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <div>
                                                <label class="text-xs text-gray-500">Código CIE-10</label>
                                                <select :name="`diagnosticos[${index}][diagnostico_cie10_id]`"
                                                    x-model="diagnostico.diagnostico_cie10_id" required
                                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                                    <option value="">Selecciona un código</option>
                                                    @foreach ($codigosCie10 as $codigo)
                                                        <option value="{{ $codigo->id }}">{{ $codigo->codigo }} — {{ $codigo->descripcion }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="text-xs text-gray-500">Estado</label>
                                                <select :name="`diagnosticos[${index}][estado]`" x-model="diagnostico.estado" required
                                                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm">
                                                    <option value="presuntivo">Presuntivo (PRE)</option>
                                                    <option value="definitivo">Definitivo (DEF)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <label class="text-xs text-gray-500">Descripción</label>
                                            <textarea :name="`diagnosticos[${index}][descripcion]`" x-model="diagnostico.descripcion" rows="2" required
                                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>
                                        </div>
                                        <button type="button" @click="diagnosticos.splice(index, 1)"
                                            class="text-xs text-red-600 hover:text-red-900 mt-2">
                                            Quitar este diagnóstico
                                        </button>
                                    </div>
                                </template>

                                <p x-show="diagnosticos.length === 0" class="text-sm text-gray-400">
                                    Sin diagnósticos agregados todavía.
                                </p>
                                <x-input-error :messages="$errors->get('diagnosticos')" class="mt-2" />
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
