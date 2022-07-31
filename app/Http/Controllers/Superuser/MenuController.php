<?php

namespace App\Http\Controllers\Superuser;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
            'menus' => $this->get(),
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
                            path: '/vendors/fontawesome/svgs/solid/*.svg'
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

                return redirect()->back()->with('error', __(
                    $e->getMessage(),
                ));
            }
        }

        return redirect()->back()->with('error', __(
            'can\'t delete menu',
        ));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $request->validate([
            'menus' => 'required',
        ]);

        $menus = collect($this->positions($request->menus))->flatMap([$this, 'flatMap']);

        DB::beginTransaction();

        try {
            $menus->each(function (Menu $menu) {
                Menu::where('id', $menu->id)->update($menu->only([
                    'parent_id',
                    'position',
                ]));
            });

            DB::commit();

            return redirect()->back()->with('success', __(
                'menu positions has been saved',
            ));
        } catch (Throwable $e) {
            DB::rollBack();

            return redirect()->back()->with('error', __(
                $e->getMessage(),
            ));
        }
    }

    /**
     * @param \App\Models\Menu $menu
     * @return array
     */
    public function flatMap(Menu $menu)
    {
        return [
            $menu,
            ...collect($menu->childs)->flatMap([$this, 'flatMap']),
        ];
    }

    /**
     * @param array $menu
     * @return array
     */
    public function updateWithChild(array $menu)
    {
        if (array_key_exists('childs', $menu) && $menu['childs']) {
            return $this->updateWithChilds($menu['childs']);
        }

        return new Menu($menu);
    }

    /**
     * @param \Illuminate\Support\Collection|\App\Models\Menu|array
     * @param int $parent
     */
    private function positions(Collection|Menu|array $menus, int $parent = null)
    {
        return array_map(function ($menu) use (&$i, $parent) {
            $new = new Menu($menu);
            $new->id = $menu['id'];
            $new->position = ++$i;
            $new->parent_id = $parent;
            $new->childs = array_key_exists('childs', $menu) ? $this->positions($menu['childs'], $new->id) : [];

            return $new;
        }, $menus);
    }
}
