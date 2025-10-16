<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Historia Clínica para: <span class="text-indigo-600">{{ $paciente->nombre_completo }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('historias.store', $paciente) }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="hcl_fecha_apertura" value="Fecha de Apertura" />
                                <x-text-input id="hcl_fecha_apertura" class="block mt-1 w-full" type="date" name="hcl_fecha_apertura" :value="old('hcl_fecha_apertura', now()->toDateString())" required />
                                <x-input-error :messages="$errors->get('hcl_fecha_apertura')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="hcl_antecedentes_personales" value="Antecedentes Personales (Patológicos y no Patológicos)" />
                                <textarea id="hcl_antecedentes_personales" name="hcl_antecedentes_personales" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('hcl_antecedentes_personales') }}</textarea>
                                <x-input-error :messages="$errors->get('hcl_antecedentes_personales')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="hcl_antecedentes_familiares" value="Antecedentes Familiares" />
                                <textarea id="hcl_antecedentes_familiares" name=hcl_antecedentes_familiares" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('hcl_antecedentes_familiares') }}</textarea>
                                <x-input-error :messages="$errors->get('hcl_antecedentes_familiares')" class="mt-2" />
                            </div>

                             <div>
                                <x-input-label for="hcl_examen_clinico_general" value="Examen Clínico General" />
                                <textarea id="hcl_examen_clinico_general" name="hcl_examen_clinico_general" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('hcl_examen_clinico_general') }}</textarea>
                                <x-input-error :messages="$errors->get('hcl_examen_clinico_general')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('pacientes.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <x-primary-button>{{ __('Guardar Historia Clínica') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>