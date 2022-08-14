<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TranslationController extends Controller
{
  /**
   * @param string $locale
   * @return \Illuminate\Http\Response
   */
  public function get(string $locale = 'id')
  {
    app()->setLocale($locale);
    
    return $this->all() ?? '{}';
  }

  /**
   * @return array|null
   */
  public function all()
  {
    $path = lang_path(app()->getLocale() . '.json');

    if (File::exists($path)) {
      return json_decode(File::get($path), true);
    }
  }

  /**
   * @param \Illuminate\Http\Request $request
   * @param string $locale
   * @return \Illuminate\Http\Response
   */
  public function register(Request $request, string $locale = 'id')
  {
    $request->validate([
      'text' => 'required|string',
    ]);

    app()->setLocale($locale);

    $all = $this->all() ?? [];

    if (!array_key_exists($request->text, $all)) {
      $all[$request->text] = $request->text;
    }

    $path = lang_path($locale . '.json');

    return File::put($path, json_encode(
      $all, JSON_PRETTY_PRINT
    ));
  }
}
