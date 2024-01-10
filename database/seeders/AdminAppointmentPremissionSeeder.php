<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class AdminAppointmentPremissionSeeder  extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->init();

        // Si los permisos los hemos creados volvemos
        $permExists = Permission::where('name', Str::slug('admin-appointments'))->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de patients
        $permissions = [
            [
                'display_name' => 'Citas',
                'name' => Str::slug('admin-appointments'),
                'description' => 'Citas - Módulo'
            ],

            [
                'display_name' => 'Citas - listado todas',
                'name' => Str::slug('admin-appointments-list-all'),
                'description' => 'Citas - listado todas'
            ],
            [
                'display_name' => 'Citas - listado (Creadas por el usuario',
                'name' => Str::slug('admin-appointments-list-created-by-user'),
                'description' => 'Citas - listado (Creadas por el usuario'
            ],
            [
                'display_name' => 'Citas - listado (Solo las Asignadas a mi como doctor)',
                'name' => Str::slug('admin-appointments-list-doctor'),
                'description' => 'Citas - listado (Solo las Asignadas a mi como doctor)'
            ],  

            

            [
                'display_name' => 'Citas - crear',
                'name' => Str::slug('admin-appointments-create'),
                'description' => 'Citas - crear'
            ],

            [
                'display_name' => 'Citas - actualizar todas',
                'name' => Str::slug('admin-appointments-update-all'),
                'description' => 'Citas - actualizar todas'
            ],
            [
                'display_name' => 'Citas - actualizar (Creadas por el usuario',
                'name' => Str::slug('admin-appointments-update-created-by-user'),
                'description' => 'Citas - actualizar (Creadas por el usuario'
            ],
            [
                'display_name' => 'Citas - actualizar (Solo las Asignadas a mi como doctor)',
                'name' => Str::slug('admin-appointments-update-doctor'),
                'description' => 'Citas - actualizar (Solo las Asignadas a mi como doctor)'
            ],  

            [
                'display_name' => 'Citas - ver todas',
                'name' => Str::slug('admin-appointments-read-all'),
                'description' => 'Citas - ver todas'
            ],
            [
                'display_name' => 'Citas - ver (Creadas por el usuario',
                'name' => Str::slug('admin-appointments-read-created-by-user'),
                'description' => 'Citas - ver (Creadas por el usuario'
            ],
            [
                'display_name' => 'Citas - ver (Solo las Asignadas a mi como doctor)',
                'name' => Str::slug('admin-appointments-read-doctor'),
                'description' => 'Citas - ver (Solo las Asignadas a mi como doctor)'
            ],  

            [
                'display_name' => 'Citas - facturar todas',
                'name' => Str::slug('admin-appointments-facturar-all'),
                'description' => 'Citas - facturar todas'
            ],
            [
                'display_name' => 'Citas - facturar (Creadas por el usuario',
                'name' => Str::slug('admin-appointments-facturar-created-by-user'),
                'description' => 'Citas - facturar (Creadas por el usuario'
            ],
            [
                'display_name' => 'Citas - facturar (Solo las Asignadas a mi como doctor)',
                'name' => Str::slug('admin-appointments-facturar-doctor'),
                'description' => 'Citas - facturar (Solo las Asignadas a mi como doctor)'
            ],  

            [
                'display_name' => 'Citas - finalizar todas',
                'name' => Str::slug('admin-appointments-end-all'),
                'description' => 'Citas - finalizar todas'
            ],
            [
                'display_name' => 'Citas - finalizar (Creadas por el usuario',
                'name' => Str::slug('admin-appointments-end-created-by-user'),
                'description' => 'Citas - finalizar (Creadas por el usuario'
            ],
            [
                'display_name' => 'Citas - finalizar (Solo las Asignadas a mi como doctor)',
                'name' => Str::slug('admin-appointments-end-doctor'),
                'description' => 'Citas - finalizar (Solo las Asignadas a mi como doctor)'
            ],  

            [
                'display_name' => 'Citas - eliminar todas',
                'name' => Str::slug('admin-appointments-delete-all'),
                'description' => 'Citas - eliminar todas'
            ],
            [
                'display_name' => 'Citas - eliminar (Creadas por el usuario',
                'name' => Str::slug('admin-appointments-delete-created-by-user'),
                'description' => 'Citas - eliminar (Creadas por el usuario'
            ],
            [
                'display_name' => 'Citas - eliminar (Solo las Asignadas a mi como doctor)',
                'name' => Str::slug('admin-appointments-delete-doctor'),
                'description' => 'Citas - eliminar (Solo las Asignadas a mi como doctor)'
            ],  



        ];
        $MenuChild = $this->insertPermissions($permissions, $this->childAdmin, $this->a_permission_admin);

        // Rol de administrador
        $roleAdmin = Role::where("name", "=", Str::slug('admin'))->first();
        if (!empty($this->a_permission_admin)) {
            $roleAdmin->attachPermissions($this->a_permission_admin);
        }
        $roleUser = Role::where("name", "=", Str::slug('usuario-front'))->first();
        if (!empty($this->a_permission_front)) {
            $roleUser->attachPermissions($this->a_permission_front);
        }
    }
}
