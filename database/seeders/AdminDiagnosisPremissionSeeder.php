<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class AdminDiagnosisPremissionSeeder  extends BaseSeeder
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
        $permExists = Permission::where('name', Str::slug('admin-diagnosis'))->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de diagnosis
        $permissions = [
            [
                'display_name' => 'Diasgnósticos',
                'name' => Str::slug('admin-diagnosis'),
                'description' => 'Diasgnósticos - Módulo'
            ],
            [
                'display_name' => 'Diasgnósticos - listado',
                'name' => Str::slug('admin-diagnosis-list'),
                'description' => 'Diasgnósticos - lista'
            ],
            [
                'display_name' => 'Diasgnósticos - crear',
                'name' => Str::slug('admin-diagnosis-create'),
                'description' => 'Diasgnósticos - crear'
            ],
            [
                'display_name' => 'Diasgnósticos - actualizar',
                'name' => Str::slug('admin-diagnosis-update'),
                'description' => 'Diasgnósticos - actualizar'
            ],
            [
                'display_name' => 'Diasgnósticos - borrar',
                'name' => Str::slug('admin-diagnosis-delete'),
                'description' => 'Diasgnósticos - borrar'
            ],
            [
                'display_name' => 'Diasgnósticos - ver',
                'name' => Str::slug('admin-diagnosis-read'),
                'description' => 'Diasgnósticos - ver'
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
