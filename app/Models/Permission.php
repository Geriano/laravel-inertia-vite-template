<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as Model;

class Permission extends Model
{
  /**
   * @inheritdoc
   */
  public static function boot()
  {
    parent::boot();

    static::creating(function (Permission $permission) {
      if (!$permission->guard_name) {
        $permission->guard_name = 'web';
      }
    });

    static::created(function (Permission $permission) {
      if ($role = Role::where('name', 'superuser')->first()) {
        $role->permissions()->attach([$permission->id]);
      }
    });
  }
}