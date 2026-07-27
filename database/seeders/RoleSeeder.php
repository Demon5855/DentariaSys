<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Permisos agrupados por módulo. Los nombres son verbos de negocio
     * ("pacientes.crear"), no acciones HTTP — así una policy nunca depende
     * de cómo está construida la ruta.
     */
    private const PERMISOS = [
        'pacientes.ver',
        'pacientes.crear',
        'pacientes.editar',
        'pacientes.desactivar',
        'historias.ver',
        'historias.abrir',
        'consultas.ver',
        'consultas.crear',
        'odontogramas.ver',
        'odontogramas.crear',
        'citas.ver',
        'citas.crear',
        'citas.gestionar',
        'usuarios.gestionar',
    ];

    /**
     * Quién puede hacer qué. El instructivo del Form 033 es explícito:
     * "este formulario debe ser llenado por profesionales odontólogos" —
     * por eso solo odontólogo/admin abren historias, registran consultas
     * y firman odontogramas. La agenda es distinta: es trabajo de
     * recepción, no clínico, así que responde a otra lógica de permisos.
     */
    private const ROLES = [
        'admin' => self::PERMISOS, // todos

        'odontologo' => [
            'pacientes.ver', 'pacientes.crear', 'pacientes.editar',
            'historias.ver', 'historias.abrir',
            'consultas.ver', 'consultas.crear',
            'odontogramas.ver', 'odontogramas.crear',
            'citas.ver', // ve su agenda, pero no la agenda ni la cancela
        ],

        // Ve el expediente clínico (apoyo en consulta, toma de signos vitales
        // en fases futuras) pero no abre historias ni registra consultas.
        'auxiliar' => [
            'pacientes.ver',
            'historias.ver',
            'consultas.ver',
            'odontogramas.ver',
            'citas.ver',
        ],

        // Front desk: administra el dato administrativo del paciente y la
        // agenda completa, sin acceso al contenido clínico.
        'recepcion' => [
            'pacientes.ver', 'pacientes.crear', 'pacientes.editar', 'pacientes.desactivar',
            'citas.ver', 'citas.crear', 'citas.gestionar',
        ],
    ];

    public function run(): void
    {
        // Evita que una caché de permisos vieja interfiera con este seed.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISOS as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        foreach (self::ROLES as $nombreRol => $permisos) {
            $rol = Role::firstOrCreate(['name' => $nombreRol, 'guard_name' => 'web']);
            $rol->syncPermissions($permisos);
        }
    }
}
