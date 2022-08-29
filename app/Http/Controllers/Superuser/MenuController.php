<?php

namespace App\Http\Controllers\Superuser;

use App\Http\Controllers\Controller;
use App\Menus\Menu as MenusMenu;
use App\Models\Menu;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
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
            'handlers' => $this->handlers(),
        ]);
    }

    /**
     * @return array
     */
    private function handlers() : array
    {
        $handlers = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
            app_path('Menus')
        ));

        foreach ($iterator as $splFileInfo) {
            /**
             * @var \SplFileInfo $splFileInfo
             */
            if ($splFileInfo->isDir()) {
                continue;
            }
            
            if ($splFileInfo->getFilename() === 'Menu.php') {
                continue;
            }

            $class = str_replace(dirname(app_path()), '', $splFileInfo->getRealPath());
            $class = mb_substr(trim($class, DIRECTORY_SEPARATOR), 0, -4);
            $class = str_replace(DIRECTORY_SEPARATOR, '\\', ucfirst($class));

            if (!class_exists($class) && !is_subclass_of($class, MenusMenu::class)) {
                continue;
            }

            $name = str_replace(['App\\Menus\\', '\\'], ['', ' '], $class);
            
            $handlers[] = [
                'class' => $class,
                'name' => ucwords(Str::snake($name, ' ')),
            ];
        }

        return $handlers;
    }

    /**
     * @return array
     */
    public function icons() : array
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
        Validator::make($request->all(), [
            'name' => 'required|string',
            'icon' => 'nullable|string',
            'route_or_url' => 'nullable|string',
            'counter_handler' => 'nullable|string',
            'actives.*' => 'nullable|string',
            'permissions.*' => 'nullable|integer|exists:permissions,id',
        ])->after(function ($validator) use ($request) {
            if (($class = $request->counter_handler) && !class_exists($class)) {
                $validator->errors()->add('counter_handler', __(
                    'class :name doesn\'t exists', [
                        'name' => $class,
                    ],
                ));
            }
        })->validate();

        $post = $request->all();
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
        Validator::make($request->all(), [
            'name' => 'required|string',
            'icon' => 'nullable|string',
            'route_or_url' => 'nullable|string',
            'counter_handler' => 'nullable|string',
            'actives.*' => 'nullable|string',
            'permissions.*' => 'nullable|integer|exists:permissions,id',
        ])->after(function ($validator) use ($request) {
            if (($class = $request->counter_handler) && !class_exists($class)) {
                $validator->errors()->add('counter_handler', __(
                    'class :name doesn\'t exists', [
                        'name' => $class,
                    ],
                ));
            }
        })->validate();

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

    /**
     * @param \App\Models\Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function counter(Menu $menu)
    {
        $menu = Menu::with('childs')->find($menu->id);

        return [
            'count' => $this->counters(0, $menu),
        ];
    }

    /**
     * @param int $total
     * @param \App\Models\Menu $menu
     * @return int
     */
    public function counters(int $total, Menu $menu)
    {
        return $total + $menu->counter?->count() + $menu->childs->reduce([$this, 'counters'], 0);
    }
}
