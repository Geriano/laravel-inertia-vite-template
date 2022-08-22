<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Throwable;

class TranslationController extends Controller
{
  /**
   * @return string
   */
  private function path()
  {
    return lang_path(app()->getLocale() . '.json');
  }

  /**
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    return Inertia::render('Translation/Index')->with([
      'translations' => $this->all(),
    ]);
  }

  /**
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request)
  {
    $request->validate([
      'key' => 'required|string',
      'value' => 'required|string',
    ]);

    $all = $this->all();
    $all[$request->key] = $request->value;

    try {
      File::put($this->path(), json_encode(
        $all, JSON_PRETTY_PRINT
      ));

      return redirect()->back()->with('success', __(
        '`:key` has been translated to `:value`', [
          'key' => $request->key,
          'value' => $request->value,
        ],
      ));
    } catch (Throwable $e) {
      return redirect()->back()->with('error', __(
        $e->getMessage()
      ));
    }
  }

  /**
   * @param string $locale
   * @return \Illuminate\Http\Response
   */
  public function get(string $locale = 'id')
  {
    app()->setLocale($locale);
    
    return $this->all() ?: '{}';
  }

  /**
   * @return array|null
   */
  private function all()
  {
    return File::exists($this->path()) ? json_decode(File::get($this->path()), true) : [];
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

    $all = $this->all();
    $key = mb_strtolower($request->text);

    if (!array_key_exists($key, $all)) {
      $all[$key] = $request->text;
    }

    return File::put($this->path(), json_encode(
      $all, JSON_PRETTY_PRINT
    ));
  }
}
