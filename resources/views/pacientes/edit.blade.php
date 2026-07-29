<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Paciente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form action="{{ route('pacientes.update', $paciente) }}" method="POST"
                        x-data="{
                            tipoDocumento: '{{ old('tipo_documento', $paciente->tipo_documento) }}',
                            fechaNacimiento: '{{ old('fecha_nacimiento', $paciente->fecha_nacimiento->format('Y-m-d')) }}',
                            esMenor() {
                                if (!this.fechaNacimiento) return false;
                                const nacimiento = new Date(this.fechaNacimiento);
                                const edad = (new Date() - nacimiento) / (1000 * 60 * 60 * 24 * 365.25);
                                return edad < 18;
                            }
                        }">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <x-input-label for="tipo_documento" :value="__('Tipo de documento')" />
                                <select id="tipo_documento" name="tipo_documento" x-model="tipoDocumento" required
                                    class="block mt-1 w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                    <option value="cedula" @selected(old('tipo_documento', $paciente->tipo_documento) === 'cedula')>Cédula</option>
                                    <option value="pasaporte" @selected(old('tipo_documento', $paciente->tipo_documento) === 'pasaporte')>Pasaporte</option>
                                    <option value="carnet_refugiado" @selected(old('tipo_documento', $paciente->tipo_documento) === 'carnet_refugiado')>Carné de refugiado</option>
                                    <option value="temporal" @selected(old('tipo_documento', $paciente->tipo_documento) === 'temporal')>Sin documento (código temporal)</option>
                                </select>
                                <x-input-error :messages="$errors->get('tipo_documento')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="numero_documento" :value="__('Número de documento')" />
                                <x-text-input id="numero_documento" class="block mt-1 w-full" type="text"
                                    name="numero_documento" :value="old('numero_documento', $paciente->numero_documento)" required />
                                <x-input-error :messages="$errors->get('numero_documento')" class="mt-2" />
                            </div>

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
                                <x-input-label for="sexo" :value="__('Sexo')" />
                                <select id="sexo" name="sexo" required
                                    class="block mt-1 w-full border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm">
                                    <option value="H" @selected(old('sexo', $paciente->sexo) === 'H')>Hombre</option>
                                    <option value="M" @selected(old('sexo', $paciente->sexo) === 'M')>Mujer</option>
                                </select>
                                <x-input-error :messages="$errors->get('sexo')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="fecha_nacimiento" :value="__('Fecha de Nacimiento')" />
                                <x-text-input id="fecha_nacimiento" class="block mt-1 w-full" type="date"
                                    name="fecha_nacimiento" x-model="fechaNacimiento"
                                    :value="old('fecha_nacimiento', $paciente->fecha_nacimiento->format('Y-m-d'))" required />
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

                        <div x-show="esMenor()" x-cloak class="mt-6 border-t pt-6">
                            <h3 class="text-sm font-bold text-gray-700 mb-1">Representante legal</h3>
                            <p class="text-sm text-gray-500 mb-4">La fecha de nacimiento indica que es menor de edad — se requieren estos datos.</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="representante_nombre" :value="__('Nombre completo')" />
                                    <x-text-input id="representante_nombre" class="block mt-1 w-full" type="text" name="representante_nombre" :value="old('representante_nombre', $paciente->representante_nombre)" />
                                    <x-input-error :messages="$errors->get('representante_nombre')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="representante_tipo_documento" :value="__('Tipo de documento')" />
                                    <select id="representante_tipo_documento" name="representante_tipo_documento"
                                        class="border-gray-300 focus:border-brand-500 focus:ring-brand-500 rounded-md shadow-sm block mt-1 w-full">
                                        <option value="cedula" @selected(old('representante_tipo_documento', $paciente->representante_tipo_documento ?? 'cedula') === 'cedula')>Cédula</option>
                                        <option value="pasaporte" @selected(old('representante_tipo_documento', $paciente->representante_tipo_documento) === 'pasaporte')>Pasaporte</option>
                                        <option value="carnet_refugiado" @selected(old('representante_tipo_documento', $paciente->representante_tipo_documento) === 'carnet_refugiado')>Carné de refugiado</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('representante_tipo_documento')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="representante_documento" :value="__('Documento de identidad')" />
                                    <x-text-input id="representante_documento" class="block mt-1 w-full" type="text" name="representante_documento" :value="old('representante_documento', $paciente->representante_documento)" />
                                    <x-input-error :messages="$errors->get('representante_documento')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="representante_parentesco" :value="__('Parentesco')" />
                                    <x-text-input id="representante_parentesco" class="block mt-1 w-full" type="text" name="representante_parentesco" :value="old('representante_parentesco', $paciente->representante_parentesco)" placeholder="Madre, padre, tutor..." />
                                    <x-input-error :messages="$errors->get('representante_parentesco')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="representante_telefono" :value="__('Teléfono de contacto')" />
                                    <x-text-input id="representante_telefono" class="block mt-1 w-full" type="text" name="representante_telefono" :value="old('representante_telefono', $paciente->representante_telefono)" />
                                    <x-input-error :messages="$errors->get('representante_telefono')" class="mt-2" />
                                </div>
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
