<?php

namespace App\Helpers;

use Pusher\Pusher;

class NotificationHelper
{
    public static function triggerEvent($channel, $event, $message)
    {
        try {
            $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ]);

            $pusher->trigger($channel, $event, ['message' => $message]);

            return true;
        } catch (\Exception $e) {
            return false; 
        }
    }
}