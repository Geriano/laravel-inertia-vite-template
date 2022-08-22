<?php

use Illuminate\Support\Facades\File;

if (! function_exists('__')) {
  /**
   * Translate the given message.
   *
   * @param  string|null  $key
   * @param  array  $replace
   * @param  string|null  $locale
   * @return string|array|null
   */
  function __($key = null, $replace = [], $locale = null)
  {
    $allDefinedTranslation = function () {
      $path = lang_path(app()->getLocale() . '.json');
      
      return File::exists($path) ? json_decode(File::get($path), true) : [];
    };

    if (is_null($key)) {
      return $key;
    }

    if (is_string($key)) {
      $value = $key;
      $key = mb_strtolower($key);
      $all = $allDefinedTranslation();

      if (!array_key_exists($key, $all)) {
        $all[$key] = $value;

        $path = lang_path(app()->getLocale() . '.json');

        File::put($path, json_encode($all, JSON_PRETTY_PRINT));
      }
    }

    return trans($key, $replace, $locale);
  }
}