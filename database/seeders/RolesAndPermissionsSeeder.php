<?php

namespace Database\Seeders;

use App\Support\Roles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Vider le cache des permissions avant (et après) modification.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Roles::all() as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Attribution cumulative : chaque rôle reçoit son niveau + les niveaux inférieurs.
        $this->createRole(Roles::CASHIER, array_merge(
            Roles::cashierPermissions(),
        ));

        $this->createRole(Roles::MANAGER, array_merge(
            Roles::cashierPermissions(),
            Roles::managerPermissions(),
        ));

        $this->createRole(Roles::SUPER_ADMIN, Roles::all());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @param  array<int, string>  $permissions
     */
    private function createRole(string $name, array $permissions): void
    {
        $role = Role::firstOrCreate(['name' => $name]);
        $role->syncPermissions($permissions);
    }
}
