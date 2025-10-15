<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Paciente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form action="{{ route('pacientes.update', $paciente) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <x-input-label for="primer_nombre" :value="__('Primer Nombre')" />
                                <x-text-input id="primer_nombre" class="block mt-1 w-full" type="text" name="primer_nombre" :value="old('primer_nombre', $paciente->primer_nombre)" required autofocus />
                                <x-input-error :messages="$errors->get('primer_nombre')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="segundo_nombre" :value="__('Segundo Nombre (Opcional)')" />
                                <x-text-input id="segundo_nombre" class="block mt-1 w-full" type="text" name="segundo_nombre" :value="old('segundo_nombre', $paciente->segundo_nombre)" />
                                <x-input-error :messages="$errors->get('segundo_nombre')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="primer_apellido" :value="__('Primer Apellido')" />
                                <x-text-input id="primer_apellido" class="block mt-1 w-full" type="text" name="primer_apellido" :value="old('primer_apellido', $paciente->primer_apellido)" required />
                                <x-input-error :messages="$errors->get('primer_apellido')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="segundo_apellido" :value="__('Segundo Apellido (Opcional)')" />
                                <x-text-input id="segundo_apellido" class="block mt-1 w-full" type="text" name="segundo_apellido" :value="old('segundo_apellido', $paciente->segundo_apellido)" />
                                <x-input-error :messages="$errors->get('segundo_apellido')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="fecha_nacimiento" :value="__('Fecha de Nacimiento')" />
                                <x-text-input id="fecha_nacimiento" class="block mt-1 w-full" type="date" name="fecha_nacimiento" :value="old('fecha_nacimiento', $paciente->fecha_nacimiento)" required />
                                <x-input-error :messages="$errors->get('fecha_nacimiento')" class="mt-2" />
                            </div>
                             <div>
                                <x-input-label for="telefono" :value="__('Teléfono (Opcional)')" />
                                <x-text-input id="telefono" class="block mt-1 w-full" type="text" name="telefono" :value="old('telefono', $paciente->telefono)" />
                                <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                            </div>
                             <div class="md:col-span-2">
                                <x-input-label for="email" :value="__('Correo Electrónico (Opcional)')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $paciente->email)" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                             <div class="md:col-span-2">
                                <x-input-label for="direccion" :value="__('Dirección (Opcional)')" />
                                <x-text-input id="direccion" class="block mt-1 w-full" type="text" name="direccion" :value="old('direccion', $paciente->direccion)" />
                                <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('pacientes.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <x-primary-button>{{ __('Actualizar Paciente') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>