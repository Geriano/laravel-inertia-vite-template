<?php

namespace App\Menus;

use Countable;

abstract class Menu implements Countable
{
  /**
   * @return int
   */
  abstract public function count() : int;
}