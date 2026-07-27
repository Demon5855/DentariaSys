<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Personal del sistema') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="flex justify-between items-center mb-6">
                        <a href="{{ route('admin.users.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-md shadow-sm">
                            + Nuevo usuario
                        </a>
                    </div>

                    @if (session('status') || session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('status') ?? session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            @foreach ($errors->all() as $error)
                                <span class="block sm:inline">{{ $error }}</span>
                            @endforeach
                        </div>
                    @endif

                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left">Nombre</th>
                                <th class="py-3 px-4 text-left">Correo</th>
                                <th class="py-3 px-4 text-left">Rol</th>
                                <th class="py-3 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($usuarios as $usuario)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium">{{ $usuario->name }}</td>
                                    <td class="py-3 px-4">{{ $usuario->email }}</td>
                                    <td class="py-3 px-4">
                                        @foreach ($usuario->roles as $rol)
                                            <span class="bg-brand-100 text-brand-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                {{ $rol->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('admin.users.edit', $usuario) }}" class="text-brand-600 hover:text-brand-900">Editar</a>
                                            @if ($usuario->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $usuario) }}" method="POST"
                                                    onsubmit="return confirm('¿Eliminar esta cuenta?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Eliminar</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-8 px-4 text-center text-gray-500">No hay usuarios registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $usuarios->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
