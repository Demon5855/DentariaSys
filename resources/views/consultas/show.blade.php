<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Consulta del {{ $consulta->fecha->format('d/m/Y') }}
            — <span class="text-indigo-600">{{ $consulta->historiaClinica->paciente->nombre_completo }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">

                    <div class="border-b pb-4">
                        <h3 class="text-lg font-bold">Motivo de consulta</h3>
                        <p class="whitespace-pre-wrap">{{ $consulta->motivo_consulta }}</p>
                    </div>

                    <div class="border-b pb-4">
                        <h3 class="text-lg font-bold">Enfermedad actual</h3>
                        <p class="whitespace-pre-wrap">{{ $consulta->enfermedad_actual ?? 'No registrada.' }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b pb-4">
                        <div>
                            <h3 class="text-lg font-bold">Antecedentes personales</h3>
                            @if ($consulta->antecedentesPersonalesMarcados->isNotEmpty())
                                <ul class="text-sm text-gray-700 list-disc list-inside mb-2">
                                    @foreach ($consulta->antecedentesPersonalesMarcados as $antecedente)
                                        <li>{{ $antecedente->codigo }}. {{ $antecedente->nombre }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-400 mb-2">No refiere antecedentes.</p>
                            @endif
                            <p class="whitespace-pre-wrap text-sm">{{ $consulta->antecedentes_personales }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold">Antecedentes familiares</h3>
                            @if ($consulta->antecedentesFamiliaresMarcados->isNotEmpty())
                                <ul class="text-sm text-gray-700 list-disc list-inside mb-2">
                                    @foreach ($consulta->antecedentesFamiliaresMarcados as $antecedente)
                                        <li>{{ $antecedente->codigo }}. {{ $antecedente->nombre }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-sm text-gray-400 mb-2">No refiere antecedentes.</p>
                            @endif
                            <p class="whitespace-pre-wrap text-sm">{{ $consulta->antecedentes_familiares }}</p>
                        </div>
                    </div>

                    <div class="border-b pb-4">
                        <h3 class="text-lg font-bold mb-2">Constantes vitales</h3>
                        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div><dt class="text-gray-500">Presión arterial</dt><dd class="font-medium">{{ $consulta->presion_arterial ?? 'N/A' }}</dd></div>
                            <div><dt class="text-gray-500">Temperatura</dt><dd class="font-medium">{{ $consulta->temperatura ? $consulta->temperatura.' °C' : 'N/A' }}</dd></div>
                            <div><dt class="text-gray-500">Pulso</dt><dd class="font-medium">{{ $consulta->pulso ?? 'N/A' }}</dd></div>
                            <div><dt class="text-gray-500">Frec. respiratoria</dt><dd class="font-medium">{{ $consulta->frecuencia_respiratoria ?? 'N/A' }}</dd></div>
                        </dl>
                    </div>

                    <div class="border-b pb-4">
                        <h3 class="text-lg font-bold">Examen del sistema estomatognático</h3>
                        @if ($consulta->regionesAfectadas->isNotEmpty())
                            <ul class="text-sm text-gray-700 list-disc list-inside mb-2">
                                @foreach ($consulta->regionesAfectadas as $region)
                                    <li>{{ $region->numero }}. {{ $region->nombre }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-400 mb-2">Sin patología aparente.</p>
                        @endif
                        <p class="whitespace-pre-wrap text-sm">{{ $consulta->examen_estomatognatico }}</p>
                    </div>

                    @if ($consulta->profesional)
                        <p class="text-sm text-gray-500">Registrado por {{ $consulta->profesional->name }}</p>
                    @endif

                    <div class="flex justify-end">
                        <a href="{{ route('historias.show', $consulta->historiaClinica) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-md">
                            Volver a la historia clínica
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
