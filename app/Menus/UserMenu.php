<?php

namespace App\Menus;

use App\Models\User;

class UserMenu extends Menu
{
  /**
   * @inheritdoc
   */
  public function count() : int
  {
    return User::count();
  }
}