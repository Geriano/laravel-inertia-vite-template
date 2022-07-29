<?php

namespace App\Listeners;

use App\Models\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use hisorange\BrowserDetect\Facade as Browser;

class SaveUserLoginIpBrowserAndPlatform
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        Login::create([
            'user_id' => $event->user->id,
            'ip_address' => request()->ip(),
            'browser' => Browser::browserName(),
            'platform' => Browser::platformName(),
        ]);
    }
}
