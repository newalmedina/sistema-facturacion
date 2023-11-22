<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PermissionsTree;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminPatientsInsurancesPremissionSeeder  extends BaseSeeder
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
        $permExists = Permission::where('name', 'like', '%' . Str::slug('admin-patients-insurance') . '%')->first();
        if (!empty($permExists)) {
            return;
        }

        // Módulo de patients
        $permissions = [

            [
                'display_name' => 'Pacientes seguros médicos- actualizar',
                'name' => Str::slug('admin-patients-insurance-carriers-update'),
                'description' => 'Pacientes - actualizar'
            ],
            [
                'display_name' => 'Pacientes seguros médicos- ver',
                'name' => Str::slug('admin-patients-insurance-carriers-read'),
                'description' => 'Pacientes - actualizar'
            ],


        ];
        $parent = Permission::where('name', Str::slug('admin-patients'))->first();
        $roleAdmin = Role::where("name", "=", Str::slug('admin'))->first();

        try {
            foreach ($permissions as  $value) {
                DB::beginTransaction();
                $lastTree = PermissionsTree::orderBy("id", "desc")->first();

                $permission = new Permission();
                $permission->name = $value["name"];
                $permission->display_name = $value["display_name"];
                $permission->description = $value["description"];
                $permission->save();

                $treePermission = new PermissionsTree();
                $treePermission->permissions_id =  $permission->id;
                $treePermission->_lft =  $lastTree->_lft;
                $treePermission->_rgt =  $lastTree->_rgt;
                $treePermission->parent_id =  $parent->id;
                $treePermission->save();

                $roleAdmin->attachPermissions([$permission->id]);
                DB::commit();
            }
        } catch (\Exception $ex) {
            dd($ex);
        }
        // $MenuChild = $this->insertPermissions($permissions, $this->childAdmin, $this->a_permission_admin);

        // // Rol de administrador
        // $roleAdmin = Role::where("name", "=", Str::slug('admin'))->first();
        // if (!empty($this->a_permission_admin)) {
        //     $roleAdmin->attachPermissions($this->a_permission_admin);
        // }
        // $roleUser = Role::where("name", "=", Str::slug('usuario-front'))->first();
        // if (!empty($this->a_permission_front)) {
        //     $roleUser->attachPermissions($this->a_permission_front);
        // }
    }
}
