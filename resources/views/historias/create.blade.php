<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Abrir Historia Clínica para: <span class="text-indigo-600">{{ $paciente->nombre_completo }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <p class="text-sm text-gray-600 mb-6">
                        Esto abre la carpeta clínica del paciente. Los datos de la primera consulta
                        (motivo, antecedentes, examen) se registran en el siguiente paso.
                    </p>

                    <form action="{{ route('historias.store', $paciente) }}" method="POST">
                        @csrf
                        <div>
                            <x-input-label for="fecha_apertura" value="Fecha de Apertura" />
                            <x-text-input id="fecha_apertura" class="block mt-1 w-full" type="date"
                                name="fecha_apertura" :value="old('fecha_apertura', now()->toDateString())" required />
                            <x-input-error :messages="$errors->get('fecha_apertura')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('pacientes.show', $paciente) }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <x-primary-button>{{ __('Abrir y continuar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
