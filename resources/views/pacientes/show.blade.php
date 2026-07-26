<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Perfil de: <span class="text-indigo-600">{{ $paciente->nombre_completo }}</span>
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
                            <dt class="text-gray-500">Fecha de nacimiento</dt>
                            <dd class="font-medium">{{ $paciente->fecha_nacimiento->format('d/m/Y') }}</dd>
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

                    <div class="flex items-center justify-end gap-4 mt-6 pt-4 border-t">
                        <a href="{{ route('pacientes.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Volver al listado</a>
                        <a href="{{ route('pacientes.edit', $paciente) }}"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-md">
                            Editar datos
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Historia clínica</h3>

                    @if ($paciente->historiaClinica)
                        <p class="text-sm text-gray-600 mb-3">
                            Abierta el {{ $paciente->historiaClinica->fecha_apertura->format('d/m/Y') }}
                            · {{ $paciente->historiaClinica->consultas->count() }} consulta(s) registrada(s)
                        </p>
                        <a href="{{ route('historias.show', $paciente->historiaClinica) }}"
                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-md">
                            Ver historia clínica
                        </a>
                    @else
                        <p class="text-sm text-gray-600 mb-3">Este paciente todavía no tiene historia clínica.</p>
                        <a href="{{ route('historias.create', $paciente) }}"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-md">
                            + Abrir historia clínica
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
