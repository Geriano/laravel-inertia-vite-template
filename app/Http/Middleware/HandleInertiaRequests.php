<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            '$config' => fn () => [
                'app' => [
                    'name' => config('app.name'),
                ],
            ],
            '$flash' => fn () => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'info' => $request->session()->get('info'),
                'warning' => $request->session()->get('warning'),
            ],

            '$roles' => $roles = fn () => $request->user()?->roles()->with('permissions:id,name')->get(['id', 'name']) ?: [],
            '$permissions' => function () use ($roles, $request) {
                $user = $request->user();

                if (!$user) {
                    return [];
                }

                $permissions = $user->permissions()->get(['id', 'name']);
                
                return $roles()?->reduce(function (Collection $prev, Role $role) {
                    $role->permissions->each(fn (Permission $permission) => $prev->push($permission));
    
                    return $prev;
                }, $permissions);
            },
            '$menus' => fn () => $request->user()?->menus(),

            '$token' => fn () => session('token'),
        ]);
    }
}
