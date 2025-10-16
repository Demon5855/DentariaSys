<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Agregar Nuevo Paciente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('pacientes.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <x-input-label for="pac_primer_nombre" :value="__('Primer Nombre')" />
                                <x-text-input id="pac_primer_nombre" class="block mt-1 w-full" type="text" name="pac_primer_nombre" :value="old('pac_primer_nombre')" required autofocus />
                                <x-input-error :messages="$errors->get('pac_primer_nombre')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="pac_segundo_nombre" :value="__('Segundo Nombre (Opcional)')" />
                                <x-text-input id="pac_segundo_nombre" class="block mt-1 w-full" type="text" name="pac_segundo_nombre" :value="old('pac_segundo_nombre')" />
                                <x-input-error :messages="$errors->get('pac_segundo_nombre')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="pac_primer_apellido" :value="__('Primer Apellido')" />
                                <x-text-input id="pac_primer_apellido" class="block mt-1 w-full" type="text" name="pac_primer_apellido" :value="old('pac_primer_apellido')" required />
                                <x-input-error :messages="$errors->get('pac_primer_apellido')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="pac_segundo_apellido" :value="__('Segundo Apellido (Opcional)')" />
                                <x-text-input id="pac_segundo_apellido" class="block mt-1 w-full" type="text" name="pac_segundo_apellido" :value="old('pac_segundo_apellido')" />
                                <x-input-error :messages="$errors->get('pac_segundo_apellido')" class="mt-2" />
                            </div>
                            
                            <div>
                                <x-input-label for="pac_fecha_nacimiento" :value="__('Fecha de Nacimiento')" />
                                <x-text-input id="pac_fecha_nacimiento" class="block mt-1 w-full" type="date" name="pac_fecha_nacimiento" :value="old('pac_fecha_nacimiento')" required />
                                <x-input-error :messages="$errors->get('pac_fecha_nacimiento')" class="mt-2" />
                            </div>
                             <div>
                                <x-input-label for="pac_telefono" :value="__('Teléfono (Opcional)')" />
                                <x-text-input id="pac_telefono" class="block mt-1 w-full" type="text" name="pac_telefono" :value="old('pac_telefono')" />
                                <x-input-error :messages="$errors->get('pac_telefono')" class="mt-2" />
                            </div>
                             <div class="md:col-span-2">
                                <x-input-label for="pac_email" :value="__('Correo Electrónico (Opcional)')" />
                                <x-text-input id="pac_email" class="block mt-1 w-full" type="email" name="pac_email" :value="old('pac_email')" />
                                <x-input-error :messages="$errors->get('pac_email')" class="mt-2" />
                            </div>
                             <div class="md:col-span-2">
                                <x-input-label for="pac_direccion" :value="__('Dirección (Opcional)')" />
                                <x-text-input id="pac_direccion" class="block mt-1 w-full" type="text" name="pac_direccion" :value="old('pac_direccion')" />
                                <x-input-error :messages="$errors->get('pac_direccion')" class="mt-2" />
                            </div>

                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('pacientes.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <x-primary-button>{{ __('Guardar Paciente') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>