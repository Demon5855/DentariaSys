<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Pacientes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-6">
                        <a href="{{ route('pacientes.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-md shadow-sm transition ease-in-out duration-150">
                            + Agregar Paciente
                        </a>

                        <div class="relative">
                            <form method="GET" action="{{ route('pacientes.index') }}" class="flex items-center">
                                <input type="hidden" name="estado" value="{{ $estado }}">
                                <div class="relative">
                                    <input type="text" name="buscar" value="{{ request('buscar') }}"
                                        placeholder="Buscar paciente..."
                                        class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                @if(request('buscar'))
                                    <a href="{{ route('pacientes.index', ['estado' => $estado]) }}"
                                        class="ml-2 px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm">
                                        Limpiar
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>

                    @if (session('status') || session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('status') ?? session('success') }}</span>
                        </div>
                    @endif

                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex space-x-8">
                            <a href="{{ route('pacientes.index', ['estado' => 'activos', 'buscar' => request('buscar')]) }}"
                                class="py-2 px-1 border-b-2 font-medium text-sm {{ $estado === 'activos' ? 'border-brand-500 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                Pacientes Activos
                                <span
                                    class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $totalActivos }}
                                </span>
                            </a>
                            <a href="{{ route('pacientes.index', ['estado' => 'inactivos', 'buscar' => request('buscar')]) }}"
                                class="py-2 px-1 border-b-2 font-medium text-sm {{ $estado === 'inactivos' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                Pacientes Inactivos
                                <span
                                    class="ml-2 bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                    {{ $totalInactivos }}
                                </span>
                            </a>
                        </nav>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-3 px-4 text-left">Documento</th>
                                    <th class="py-3 px-4 text-left">Nombre Completo</th>
                                    <th class="py-3 px-4 text-left">Teléfono</th>
                                    @can('historias.ver')
                                        <th class="py-3 px-4 text-left">Historia Clínica</th>
                                    @endcan
                                        <th class="py-3 px-4 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pacientes as $paciente)
                                    <tr
                                        class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-4 text-gray-600">{{ $paciente->numero_documento }}</td>
                                        <td class="py-3 px-4 font-medium">
                                            <a href="{{ route('pacientes.show', $paciente) }}" class="hover:text-brand-600 hover:underline">
                                                {{ $paciente->nombre_completo }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-4">{{ $paciente->telefono ?? 'N/A' }}</td>

                                        @can('historias.ver')
                                            <td class="py-3 px-4">
                                                @if ($paciente->historiaClinicaVigente)
                                                    <a href="{{ route('historias.show', $paciente->historiaClinicaVigente) }}"
                                                        class="text-green-600 hover:text-green-900 font-semibold">
                                                        Ver Historia
                                                    </a>
                                                @elseif (auth()->user()->can('historias.abrir'))
                                                    <a href="{{ route('historias.create', $paciente) }}"
                                                        class="text-brand-600 hover:text-brand-900">
                                                        + Crear Historia
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">Sin historia vigente</span>
                                                @endif
                                            </td>
                                        @endcan

                                        <td class="py-3 px-4 text-center">
                                            @if($estado === 'activos')
                                                <div class="flex justify-center space-x-2">
                                                    <a href="{{ route('pacientes.edit', $paciente) }}"
                                                        class="text-brand-600 hover:text-brand-900">Editar</a>
                                                    <form action="{{ route('pacientes.destroy', $paciente) }}" method="POST"
                                                        onsubmit="return confirm('¿Desactivar paciente?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-900">Desactivar</button>
                                                    </form>
                                                </div>
                                            @else
                                                <form action="{{ route('pacientes.restore', $paciente) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="text-green-600 hover:text-green-900">Reactivar</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 px-4 text-center text-gray-500">No se encontraron pacientes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $pacientes->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>