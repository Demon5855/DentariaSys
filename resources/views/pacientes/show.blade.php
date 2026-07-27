<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Perfil de: <span class="text-brand-600">{{ $paciente->nombre_completo }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('status') || session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('status') ?? session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-bold">Datos del paciente</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paciente->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $paciente->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">Nombre completo</dt>
                            <dd class="font-medium">{{ $paciente->nombre_completo }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Documento</dt>
                            <dd class="font-medium">
                                {{ $paciente->numero_documento }}
                                <span class="text-gray-400 text-xs">({{ ucfirst(str_replace('_', ' ', $paciente->tipo_documento)) }})</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Sexo</dt>
                            <dd class="font-medium">{{ $paciente->sexo === 'H' ? 'Hombre' : 'Mujer' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Fecha de nacimiento</dt>
                            <dd class="font-medium">
                                {{ $paciente->fecha_nacimiento->format('d/m/Y') }}
                                <span class="text-gray-400 text-xs">({{ $paciente->edad_detallada }})</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Teléfono</dt>
                            <dd class="font-medium">{{ $paciente->telefono ?? 'No registrado' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Correo electrónico</dt>
                            <dd class="font-medium">{{ $paciente->email ?? 'No registrado' }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-gray-500">Dirección</dt>
                            <dd class="font-medium">{{ $paciente->direccion ?? 'No registrada' }}</dd>
                        </div>
                    </dl>

                    @if ($paciente->es_menor_de_edad)
                        <div class="mt-4 pt-4 border-t">
                            <h4 class="text-sm font-bold text-gray-700 mb-2">Representante legal</h4>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <dt class="text-gray-500">Nombre</dt>
                                    <dd class="font-medium">{{ $paciente->representante_nombre ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Documento</dt>
                                    <dd class="font-medium">{{ $paciente->representante_documento ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Parentesco</dt>
                                    <dd class="font-medium">{{ $paciente->representante_parentesco ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500">Teléfono</dt>
                                    <dd class="font-medium">{{ $paciente->representante_telefono ?? '—' }}</dd>
                                </div>
                            </dl>
                        </div>
                    @endif

                    <div class="flex items-center justify-end gap-4 mt-6 pt-4 border-t">
                        <a href="{{ route('pacientes.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Volver al listado</a>
                        <a href="{{ route('pacientes.edit', $paciente) }}"
                            class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-md">
                            Editar datos
                        </a>
                    </div>
                </div>
            </div>

            @can('historias.ver')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold">Historias clínicas</h3>
                            @if (!$paciente->historiaClinicaVigente)
                                @can('historias.abrir')
                                    <a href="{{ route('historias.create', $paciente) }}"
                                        class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-md">
                                        + Abrir historia clínica
                                    </a>
                                @endcan
                            @endif
                        </div>

                        @forelse ($paciente->historiasClinicas as $historia)
                            <a href="{{ route('historias.show', $historia) }}"
                                class="flex justify-between items-center border rounded-md p-3 mb-2 hover:bg-gray-50 transition">
                                <div>
                                    <p class="text-sm font-medium">
                                        Abierta el {{ $historia->fecha_apertura->format('d/m/Y') }}
                                        · {{ $historia->consultas->count() }} consulta(s)
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Vigencia: {{ ucfirst($historia->tipo_vigencia) }}
                                        — vence {{ $historia->fecha_vencimiento->format('d/m/Y') }}
                                    </p>
                                </div>
                                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full {{ $historia->esta_vencida ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $historia->esta_vencida ? 'Vencida' : 'Vigente' }}
                                </span>
                            </a>
                        @empty
                            <p class="text-sm text-gray-500">Este paciente todavía no tiene historia clínica.</p>
                        @endforelse
                    </div>
                </div>
            @endcan

        </div>
    </div>
</x-app-layout>
