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
                'display_name' => 'Citas - listado',
                'name' => Str::slug('admin-appointments-list'),
                'description' => 'Citas - lista'
            ],
            [
                'display_name' => 'Citas - crear',
                'name' => Str::slug('admin-appointments-create'),
                'description' => 'Citas - crear'
            ],
            [
                'display_name' => 'Citas - actualizar',
                'name' => Str::slug('admin-appointments-update'),
                'description' => 'Citas - actualizar'
            ],
            [
                'display_name' => 'Citas - actualizar todos',
                'name' => Str::slug('admin-appointments-update-all'),
                'description' => 'Citas - actualizar todos'
            ],
            [
                'display_name' => 'Citas - ver',
                'name' => Str::slug('admin-appointments-read'),
                'description' => 'Citas - ver'
            ],
            [
                'display_name' => 'Citas - borrar',
                'name' => Str::slug('admin-appointments-delete'),
                'description' => 'Citas - borrar'
            ],
            [
                'display_name' => 'Citas - borrar todos',
                'name' => Str::slug('admin-appointments-delete-all'),
                'description' => 'Citas - borrar Todos'
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
