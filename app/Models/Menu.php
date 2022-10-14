<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JsonSerializable;
use stdClass;

class Menu extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'parent_id',
        'name',
        'icon',
        'route_or_url',
        'counter_handler',
        'position',
        'enable',
        'actives',
        'deleteable',
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'permissions',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'counter',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parent()
    {
        return $this->hasOne(Menu::class, 'id', 'parent_id')->without(['childs'])->withCount(['childs']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs()
    {
        return $this->hasMany(Menu::class, 'parent_id', 'id')
                    ->with(['parent', 'childs'])
                    ->orderBy('position');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childsByPermissions()
    {
        return $this->hasMany(Menu::class, 'parent_id', 'id')
                    ->where(function (Builder $query) {
                        $user = request()->user();
                        $permissions = $user->permissions->pluck('id')->push(...$user->roles->pluck('permissions')->flatten()->pluck('id'));

                        $query->whereHas('permissions', function (Builder $query) use ($permissions) {
                            $query->whereIn('permissions.id', $permissions);
                        })->orDoesntHave('permissions');
                    })
                    ->with(['parent', 'childsByPermissions'])
                    ->orderBy('position');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function actives() : Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value),
            set: fn ($value) => is_array($value) || $value instanceof JsonSerializable || $value instanceof stdClass ? json_encode($value) : $value,
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function counter() : Attribute
    {
        return Attribute::make(
            get: fn () => $this->counter_handler ? new $this->counter_handler : null,
        );
    }

    /**
     * @inheritdoc
     */
    public static function boot()
    {
        parent::boot();
        static::bootTraits();

        static::creating(function (Menu $menu) {
            if (empty($menu->actives)) {
                $menu->actives = [];
            }
        });
    }
}
