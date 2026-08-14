<?php

namespace App\Listeners;

use App\Events\CheckUser;

class UserEventListener
{
    /**
     * Handle user login events.
     */
    public function onUserLogin($event)
    {
        try {
            // Get user
            $user = $event->user;

            \Cookie::queue(\Cookie::forever('_inst_id', session('institution')->codigo_institucion));
            \Cookie::queue(\Cookie::forever('_user_id', $user->codigo_usuario));

            // CheckUser on Broadcast
            event(new CheckUser($user));

        }catch(\Predis\Connection\ConnectionException $e){
            \Log::error( sprintf('Broadcasting server: %s, %s.', $e->getMessage(), 'onUserLogin') );
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen('Illuminate\Auth\Events\Login', 'App\Listeners\UserEventListener@onUserLogin');
    }
}
