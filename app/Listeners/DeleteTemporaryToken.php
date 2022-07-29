<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class DeleteTemporaryToken
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
        $user = $event->user;

        $temporaryToken = DB::table('temporary_tokens')->where('user_id', $user->id)->first();

        if ($temporaryToken) {
            DB::table('personal_access_tokens')->delete($temporaryToken->token_id);
        }

        session()->remove('token');
    }
}
