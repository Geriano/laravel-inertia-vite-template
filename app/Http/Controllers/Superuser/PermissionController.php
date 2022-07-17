<?php

namespace App\Http\Controllers\Superuser;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Superuser/Permission/Index');
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        return Permission::get();
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
            'name' => 'required|string|unique:permissions',
        ]);

        if ($permission = Permission::create([ 'name' => $request->name, 'guard_name' => 'web' ])) {
            return redirect()->back()->with('success', __(
                'permission `:name` has been created', [
                    'name' => $permission->name,
                ]
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t create permission'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Permission $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => ['required', 'string', Rule::unique('permissions')->ignore($permission->id)]
        ]);

        if ($permission->update(['name' => $request->name])) {
            return redirect()->back()->with('success', __(
                'permission has been updated',
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t update permission'
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Permission $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        if ($permission->delete()) {
            return redirect()->back()->with('success', __(
                'permission has been deleted'
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t delete permission'
        ));
    }
}
