<?php

namespace App\Models;

use Spatie\Permission\Models\Role as Model;

class Role extends Model
{
  /**
   * @inheritdoc
   */
  public static function boot()
  {
    parent::boot();
    static::bootTraits();

    static::creating(function (Role $role) {
      if (!$role->guard_name) {
        $role->guard_name = 'web';
      }
    });
  }
}