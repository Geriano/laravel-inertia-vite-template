<?php

namespace App\Http\Controllers\Superuser;

use App\Http\Controllers\Controller;
use App\Http\Requests\DataTableRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Superuser/Role/Index')->with([
            'permissions' => Permission::get(),
        ]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        return Role::get();
    }

    /**
     * @param \App\Http\Requests\DataTableRequest $request
     * @return \Illuminate\Http\Response
     */
    public function paginate(DataTableRequest $request)
    {
        $request->validated();

        return Role::where(function (Builder $query) use ($request) {
                        $search = '%' . $request->search . '%';

                        $query->orWhereRelation('permissions', 'name', 'like', $search)
                                ->orWhere('name', 'like', $search);
                    })
                    ->orderBy($request->input('order.key') ?: 'name', $request->input('order.dir') ?: 'asc')
                    ->with('permissions')
                    ->paginate($request->per_page ?: 10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles',
            'permissions.*' => 'nullable|integer|exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        if ($role) {
            $role->permissions()->sync($request->input('permissions', []));

            return redirect()->back()->with('success', __(
                'role `:name` has been created', [
                    'name' => $request->name,
                ]
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t create role',
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['required', 'string', Rule::unique('roles')->ignore($role->id)],
            'permissions.*' => 'nullable|integer|exists:permissions,id',
        ]);

        if ($role->update([ 'name' => $request->name ])) {
            $role->permissions()->sync($request->input('permissions', []));

            return redirect()->back()->with('success', __(
                'role `:name` has been updated', [
                    'name' => $request->name,
                ]
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t update role',
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        if ($role->delete()) {
            return redirect()->back()->with('success', __(
                'role `:name` has been deleted', [
                    'name' => $role->name,
                ]
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t delete role',
        ));
    }

    /**
     * @param \App\Models\Role $role
     * @param \App\Models\Permission $permission
     * @return \Illuminate\Http\Response
     */
    public function detach(Role $role, Permission $permission)
    {
        if ($role->permissions()->detach([$permission->id])) {
            return redirect()->back()->with('success', __(
                'permission `:permission` has been detached from role `:role`', [
                    'permission' => $permission->name,
                    'role' => $role->name,
                ]
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t detach permission',
        ));
    }
}
