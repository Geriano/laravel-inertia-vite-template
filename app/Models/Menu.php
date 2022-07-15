<?php

namespace App\Models;

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
        'position',
        'enable',
        'actives',
        'deleteable',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parent()
    {
        return $this->hasOne(Menu::class, 'id', 'parent_id')->without(['childs'])->with(['parent']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs()
    {
        return $this->hasMany(Menu::class, 'parent_id', 'id')->with(['parent', 'childs'])->orderBy('position');
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
}
