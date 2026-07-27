<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $usuarios = User::with('roles')->orderBy('name')->paginate(15);

        return view('admin.users.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->pluck('name');

        return view('admin.users.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $usuario = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'email_verified_at' => now(),
        ]);

        $usuario->assignRole($request->validated('role'));

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->pluck('name');

        return view('admin.users.edit', ['usuario' => $user, 'roles' => $roles]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->validated('password'))]);
        }

        $user->syncRoles([$request->validated('role')]);

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * "Eliminar" un usuario en realidad lo desactiva — nunca se borra la
     * fila. Un borrado real dejaría huérfanas las referencias de
     * consultas.profesional_id (nullOnDelete, pierde quién atendió) y
     * fallaría directamente contra odontogramas.odontologo_id
     * (restrictOnDelete), además de romper la trazabilidad médico-legal
     * que exige el 033. La cuenta desactivada no puede iniciar sesión
     * (ver LoginRequest::authenticate) pero su historial de auditoría y
     * sus registros clínicos firmados permanecen intactos.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['user' => 'No puedes desactivar tu propia cuenta.']);
        }

        $user->update(['activo' => ! $user->activo]);

        $mensaje = $user->activo ? 'Usuario reactivado.' : 'Usuario desactivado.';

        return redirect()->route('admin.users.index')->with('status', $mensaje);
    }
}
