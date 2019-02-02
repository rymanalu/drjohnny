<?php

namespace App\Http\Middleware;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Sending;

class StartTyping implements Sending
{
    public function sending($payload, $next, BotMan $bot)
    {
        $bot->typesAndWaits(0.5);

        return $next($payload);
    }
}
