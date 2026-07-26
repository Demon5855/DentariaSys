<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Abrir Historia Clínica para: <span class="text-indigo-600">{{ $paciente->nombre_completo }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if ($anterior ?? null)
                <div class="bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded relative mb-4 text-sm">
                    Su historia anterior (abierta el {{ $anterior->fecha_apertura->format('d/m/Y') }})
                    venció el {{ $anterior->fecha_vencimiento->format('d/m/Y') }}. Se abrirá una nueva,
                    según el instructivo del Form 033.
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <p class="text-sm text-gray-600 mb-6">
                        Esto abre la carpeta clínica del paciente. Los datos de la primera consulta
                        (motivo, antecedentes, examen) se registran en el siguiente paso.
                    </p>

                    <form action="{{ route('historias.store', $paciente) }}" method="POST"
                        x-data="{ tipoVigencia: '{{ old('tipo_vigencia', 'general') }}' }">
                        @csrf

                        <div class="space-y-6">
                            <div>
                                <x-input-label for="fecha_apertura" value="Fecha de Apertura" />
                                <x-text-input id="fecha_apertura" class="block mt-1 w-full" type="date"
                                    name="fecha_apertura" :value="old('fecha_apertura', now()->toDateString())" required />
                                <x-input-error :messages="$errors->get('fecha_apertura')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="tipo_vigencia" value="Vigencia de esta historia" />
                                <select id="tipo_vigencia" name="tipo_vigencia" x-model="tipoVigencia" required
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="general" @selected(old('tipo_vigencia', 'general') === 'general')>General (1 año calendario)</option>
                                    <option value="embarazo" @selected(old('tipo_vigencia') === 'embarazo')>Embarazo (hasta la fecha probable de parto)</option>
                                    <option value="escolar" @selected(old('tipo_vigencia') === 'escolar')>Escolar (hasta fin del período lectivo)</option>
                                </select>
                                <x-input-error :messages="$errors->get('tipo_vigencia')" class="mt-2" />
                                <p class="text-xs text-gray-500 mt-1">
                                    Según el instructivo del Form 033: el diagnóstico dura un año calendario,
                                    salvo en embarazadas (dura la gestación) y escolares (dura el año lectivo).
                                </p>
                            </div>

                            <div x-show="tipoVigencia === 'embarazo'" x-cloak>
                                <x-input-label for="fecha_probable_parto" value="Fecha probable de parto" />
                                <x-text-input id="fecha_probable_parto" class="block mt-1 w-full" type="date"
                                    name="fecha_probable_parto" :value="old('fecha_probable_parto')"
                                    x-bind:required="tipoVigencia === 'embarazo'" />
                                <x-input-error :messages="$errors->get('fecha_probable_parto')" class="mt-2" />
                            </div>

                            <div x-show="tipoVigencia === 'escolar'" x-cloak>
                                <x-input-label for="fecha_fin_periodo_lectivo" value="Fin del período lectivo" />
                                <x-text-input id="fecha_fin_periodo_lectivo" class="block mt-1 w-full" type="date"
                                    name="fecha_fin_periodo_lectivo" :value="old('fecha_fin_periodo_lectivo')"
                                    x-bind:required="tipoVigencia === 'escolar'" />
                                <x-input-error :messages="$errors->get('fecha_fin_periodo_lectivo')" class="mt-2" />
                            </div>
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
