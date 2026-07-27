<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agenda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-6">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('citas.index', ['fecha' => $fecha->copy()->subDay()->toDateString()]) }}"
                                class="px-3 py-1.5 border rounded-md text-sm hover:bg-gray-50">← Día anterior</a>
                            <form method="GET" action="{{ route('citas.index') }}" class="inline-flex items-center gap-2">
                                <input type="date" name="fecha" value="{{ $fecha->toDateString() }}"
                                    onchange="this.form.submit()"
                                    class="border-gray-300 rounded-md shadow-sm text-sm">
                            </form>
                            <a href="{{ route('citas.index', ['fecha' => $fecha->copy()->addDay()->toDateString()]) }}"
                                class="px-3 py-1.5 border rounded-md text-sm hover:bg-gray-50">Día siguiente →</a>
                        </div>
                        @can('citas.crear')
                            <a href="{{ route('citas.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-md shadow-sm">
                                + Agendar cita
                            </a>
                        @endcan
                    </div>

                    <p class="text-sm text-gray-600 mb-4">
                        {{ $fecha->translatedFormat('l d \d\e F \d\e Y') }}
                        · {{ $citas->count() }} cita(s)
                    </p>

                    @forelse ($citas as $cita)
                        <div class="flex justify-between items-center border rounded-md p-3 mb-2">
                            <div>
                                <p class="text-sm font-medium">
                                    {{ $cita->fecha_hora->format('H:i') }} — {{ $cita->paciente->nombre_completo }}
                                    <span class="text-gray-400 text-xs">({{ $cita->duracion_minutos }} min)</span>
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $cita->profesional->name ?? 'Sin profesional asignado' }}
                                    @if ($cita->motivo) · {{ $cita->motivo }} @endif
                                </p>
                                @if ($cita->notas)
                                    <p class="text-xs text-gray-400 mt-1">{{ $cita->notas }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <span @class([
                                    'text-xs font-medium px-2.5 py-0.5 rounded-full whitespace-nowrap',
                                    'bg-gray-100 text-gray-700' => $cita->estado === 'pendiente',
                                    'bg-blue-100 text-blue-800' => $cita->estado === 'confirmada',
                                    'bg-green-100 text-green-800' => $cita->estado === 'atendida',
                                    'bg-red-100 text-red-800' => in_array($cita->estado, ['cancelada', 'no_asistio']),
                                ])>
                                    {{ str_replace('_', ' ', ucfirst($cita->estado)) }}
                                </span>

                                @can('gestionar', $cita)
                                    <div class="flex gap-1">
                                        @foreach (\App\Models\Cita::TRANSICIONES[$cita->estado] as $siguiente)
                                            <form action="{{ route('citas.cambiar-estado', $cita) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="estado" value="{{ $siguiente }}">
                                                <button type="submit" class="text-xs border rounded px-2 py-1 hover:bg-gray-50">
                                                    {{ str_replace('_', ' ', ucfirst($siguiente)) }}
                                                </button>
                                            </form>
                                        @endforeach
                                        <a href="{{ route('citas.edit', $cita) }}" class="text-xs border rounded px-2 py-1 hover:bg-gray-50">Editar</a>
                                    </div>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">No hay citas agendadas para este día.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
