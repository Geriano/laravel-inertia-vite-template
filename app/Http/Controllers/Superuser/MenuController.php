<?php

namespace App\Http\Controllers\Superuser;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use SplFileInfo;
use Throwable;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $routes = array_filter(array_map(fn ($route) => $route->getName(), Route::getRoutes()->getRoutes()));

        return Inertia::render('Superuser/Menu/Index')->with([
            'permissions' => Permission::get(),
            'routes' => array_values($routes),
            'icons' => $this->icons(),
        ]);
    }

    /**
     * @return array
     */
    public function icons()
    {
        return array_map(
            callback: fn (string $name) => mb_substr(
                string: $name,
                start: 0,
                length: -4
            ),
            array: array_map(
                callback: fn (SplFileInfo $path) => $path->getFilename(),
                array: array_map(
                    callback: fn (string $path) => new SplFileInfo(
                        filename: $path
                    ),
                    array: glob(
                        pattern: public_path(
                            path: '/vendors/fontawesome/svgs/**/*.svg'
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        return Menu::with(['childs', 'permissions'])
                    ->whereNull('parent_id')
                    ->orderBy('position')
                    ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post = $request->validate([
            'name' => 'required|string',
            'icon' => 'nullable|string',
            'route_or_url' => 'nullable|string',
            'actives.*' => 'nullable|string',
            'permissions.*' => 'nullable|integer|exists:permissions,id',
        ]);

        $post['position'] = Menu::whereNull('parent_id')->count() + 1;

        if ($menu = Menu::create($post)) {
            $menu->permissions()->sync($request->input('permissions', []));

            return redirect()->back()->with('success', __(
                'menu `:name` has been created', [
                    'name' => $menu->name,
                ]
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t create menu',
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string',
            'icon' => 'nullable|string',
            'route_or_url' => 'nullable|string',
            'actives.*' => 'nullable|string',
            'permissions.*' => 'nullable|integer|exists:permissions,id',
        ]);

        if ($menu->update($request->all())) {
            $menu->permissions()->sync($request->input('permissions', []));

            return redirect()->back()->with('success', __(
                'menu `:name` has been updated', [
                    'name' => $menu->name,
                ]
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t update menu',
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menu $menu)
    {
        if ($menu->deleteable) {
            DB::beginTransaction();

            try {
                if ($menu->delete()) {
                    Menu::where('parent_id', $menu->parent_id)
                            ->where('position', '>', $menu->position)
                            ->decrement('position');

                    DB::commit();
                    
                    return redirect()->back()->with('success', __(
                        'menu `:name` has been deleted', [
                            'name' => $menu->name,
                        ]
                    ));
                }
            } catch (Throwable $e) {
                DB::rollBack();
            }
        }

        return redirect()->back()->with('error', __(
            'can\'t delete menu',
        ));
    }

    /**
     * @param \App\Models\Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function up(Menu $menu)
    {
        $before = Menu::where('parent_id', $menu->parent_id)
                        ->where('position', $menu->position - 1)
                        ->first();

        if (!$before) {
            return redirect()->back()->with('error', __(
                'can\'t find menu before `:name`', [
                    'name' => $menu->name,
                ]
            ));
        }

        DB::beginTransaction();

        try {
            Menu::where('id', $before->id)->update([
                'position' => $menu->position,
            ]);
    
            $menu->update([
                'position' => $before->position,
            ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', __($e->getMessage()));
        }

        return redirect()->back()->with('success', __(
            'position has been updated',
        ));
    }

    /**
     * @param \App\Models\Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function down(Menu $menu)
    {
        $after = Menu::where('parent_id', $menu->parent_id)
                        ->where('position', $menu->position + 1)
                        ->first();

        if (!$after) {
            return redirect()->back()->with('error', __(
                'can\'t find menu after `:name`', [
                    'name' => $menu->name,
                ]
            ));
        }

        DB::beginTransaction();

        try {
            Menu::where('id', $after->id)->update([
                'position' => $menu->position,
            ]);
    
            $menu->update([
                'position' => $after->position,
            ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', __($e->getMessage()));
        }

        return redirect()->back()->with('success', __(
            'position has been updated',
        ));
    }

    /**
     * @param \App\Models\Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function right(Menu $menu)
    {
        $before = Menu::where('parent_id', $menu->parent_id)
                        ->where('position', $menu->position - 1)
                        ->withCount('childs')
                        ->first();

        if (!$before) {
            return redirect()->back()->with('error', __(
                'can\'t find menu before `:name`', [
                    'name' => $menu->name,
                ]
            ));
        }

        DB::beginTransaction();

        try {
            Menu::where('parent_id', $menu->parent_id)
                    ->where('position', '>', $menu->position)
                    ->decrement('position');

            $menu->update([
                'parent_id' => $before->id,
                'position' => $before->childs_count + 1,
            ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', __($e->getMessage()));
        }

        return redirect()->back()->with('success', __(
            'position has been updated',
        ));
    }

    /**
     * @param \App\Models\Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function left(Menu $menu)
    {
        $parent = $menu->parent;

        if (!$parent) {
            return redirect()->back()->with('error', __(
                'can\'t find parent menu `:name`', [
                    'name' => $menu->name,
                ]
            ));
        }

        DB::beginTransaction();

        try {
            Menu::where('parent_id', $parent->parent_id)
                    ->where('position', '>', $parent->position)
                    ->increment('position');

            Menu::where('parent_id', $menu->parent_id)
                    ->where('position', '>', $menu->position)
                    ->decrement('position');

            $menu->update([
                'parent_id' => $parent->parent_id,
                'position' => $parent->position + 1,
            ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', __($e->getMessage()));
        }

        return redirect()->back()->with('success', __(
            'position has been updated',
        ));
    }
}
