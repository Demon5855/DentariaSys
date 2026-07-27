<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Historia Clínica de: <span class="text-indigo-600">{{ $historiaClinica->paciente->nombre_completo }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status') || session('success') || session('info'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('status') ?? session('success') ?? session('info') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold">Datos generales</h3>
                        <p class="text-sm text-gray-600">
                            Abierta el {{ $historiaClinica->fecha_apertura->format('d/m/Y') }}
                            · Vigencia {{ ucfirst($historiaClinica->tipo_vigencia) }}
                            · Vence {{ $historiaClinica->fecha_vencimiento->format('d/m/Y') }}
                        </p>
                        <span class="inline-block mt-1 text-xs font-medium px-2.5 py-0.5 rounded-full {{ $historiaClinica->esta_vencida ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ $historiaClinica->esta_vencida ? 'Vencida' : 'Vigente' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('historias.pdf', $historiaClinica) }}" target="_blank"
                            class="inline-flex items-center px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white font-bold rounded-md shadow-sm">
                            Descargar PDF
                        </a>
                        @can('consultas.crear')
                            @if (!$historiaClinica->esta_vencida)
                                <a href="{{ route('consultas.create', $historiaClinica) }}"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-md shadow-sm">
                                    + Registrar consulta
                                </a>
                            @else
                                <a href="{{ route('historias.create', $historiaClinica->paciente) }}"
                                    class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-md shadow-sm">
                                    Abrir historia nueva
                                </a>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>

            @can('odontogramas.ver')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold">Odontogramas</h3>
                            @can('odontogramas.crear')
                                @if (!$historiaClinica->esta_vencida)
                                    <a href="{{ route('odontogramas.create', $historiaClinica) }}"
                                        class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-md shadow-sm">
                                        + Registrar odontograma
                                    </a>
                                @endif
                            @endcan
                        </div>

                        @forelse ($historiaClinica->odontogramas as $odontograma)
                            <a href="{{ route('odontogramas.show', $odontograma) }}"
                                class="flex justify-between items-center border rounded-md p-3 mb-2 hover:bg-gray-50 transition">
                                <div>
                                    <p class="text-sm font-medium">
                                        {{ ucfirst($odontograma->tipo) }} — {{ $odontograma->fecha->format('d/m/Y') }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        CPO-D: {{ $odontograma->cpod_c + $odontograma->cpod_p + $odontograma->cpod_o }}
                                        · ceo-d: {{ $odontograma->ceod_c + $odontograma->ceod_e + $odontograma->ceod_o }}
                                    </p>
                                </div>
                                <span class="text-xs bg-gray-800 text-white px-2 py-0.5 rounded-full">🔒</span>
                            </a>
                        @empty
                            <p class="text-sm text-gray-500">Todavía no hay odontogramas registrados.</p>
                        @endforelse
                    </div>
                </div>
            @endcan

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Consultas</h3>

                    @forelse ($historiaClinica->consultas as $consulta)
                        <a href="{{ route('consultas.show', $consulta) }}"
                            class="block border rounded-md p-4 mb-3 hover:bg-gray-50 transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium">{{ $consulta->fecha->format('d/m/Y') }}</p>
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $consulta->motivo_consulta }}</p>
                                </div>
                                @if ($consulta->profesional)
                                    <span class="text-xs text-gray-500 whitespace-nowrap ml-4">{{ $consulta->profesional->name }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <p class="text-gray-500 text-sm">Todavía no hay consultas registradas.</p>
                    @endforelse
                </div>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('pacientes.show', $historiaClinica->paciente) }}" class="text-sm text-gray-600 hover:text-gray-900">
                    Volver al perfil del paciente
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
