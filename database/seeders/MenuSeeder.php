<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dashboard = Menu::create([
            'name' => 'dashboard',
            'icon' => 'tachometer-alt',
            'route_or_url' => 'dashboard',
            'position' => 1,
            'deleteable' => false,
        ]);

        $builtin = Menu::create([
            'name' => 'builtin',
            'icon' => 'circle',
            'position' => 2,
            'deleteable' => false,
        ]);

        $permission = $builtin->childs()->create([
            'name' => 'permission',
            'route_or_url' => 'superuser.permission.index',
            'icon' => 'key',
            'position' => 1,
            'deleteable' => false,
        ]);

        $permission->permissions()->attach(
            Permission::whereIn('name', [
                'create permission', 'read permission', 'update permission', 'delete permission',
            ])->get(['id'])
        );

        $role = $builtin->childs()->create([
            'name' => 'role',
            'route_or_url' => 'superuser.role.index',
            'icon' => 'user-cog',
            'position' => 2,
            'deleteable' => false,
        ]);

        $role->permissions()->attach(
            Permission::whereIn('name', [
                'create role', 'read role', 'update role', 'delete role',
            ])->get(['id'])
        );

        $user = $builtin->childs()->create([
            'name' => 'user',
            'route_or_url' => 'superuser.user.index',
            'icon' => 'user',
            'position' => 3,
            'deleteable' => false,
        ]);

        $user->permissions()->attach(
            Permission::whereIn('name', [
                'create user', 'read user', 'update user', 'delete user',
            ])->get(['id'])
        );

        $menu = $builtin->childs()->create([
            'name' => 'menu',
            'route_or_url' => 'superuser.menu.index',
            'icon' => 'bars',
            'position' => 4,
            'deleteable' => false,
        ]);

        $menu->permissions()->attach(
            Permission::whereIn('name', [
                'create menu', 'read menu', 'update menu', 'delete menu',
            ])->get(['id'])
        );
    }
}
