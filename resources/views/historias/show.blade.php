<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Historia Clínica de: <span class="text-indigo-600">{{ $historiaClinica->paciente->nombre_completo }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">

                    <div class="border-b pb-4">
                        <h3 class="text-lg font-bold">Datos Generales</h3>
                        <p><strong>Fecha de Apertura:</strong> {{ \Carbon\Carbon::parse($historiaClinica->hcl_fecha_apertura)->format('d/m/Y') }}</p>
                    </div>

                    <div class="border-b pb-4">
                        <h3 class="text-lg font-bold">Antecedentes Personales</h3>
                        <p class="whitespace-pre-wrap">{{ $historiaClinica->hcl_antecedentes_personales ?? 'No registrados.' }}</p>
                    </div>

                    <div class="border-b pb-4">
                        <h3 class="text-lg font-bold">Antecedentes Familiares</h3>
                        <p class="whitespace-pre-wrap">{{ $historiaClinica->hcl_antecedentes_familiares ?? 'No registrados.' }}</p>
                    </div>

                    <div class="border-b pb-4">
                        <h3 class="text-lg font-bold">Examen Clínico General</h3>
                        <p class="whitespace-pre-wrap">{{ $historiaClinica->hcl_examen_clinico_general ?? 'No registrado.' }}</p>
                    </div>

                    <div class="flex justify-end mt-6">
                         <a href="{{ route('pacientes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-md">
                            Volver a Pacientes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>