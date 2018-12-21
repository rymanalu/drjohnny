<?php

namespace App\Http\Middleware;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Received;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class MarkSeen implements Received
{
    public function received(IncomingMessage $message, $next, BotMan $bot)
    {
        $bot->markSeen($message);

        return $next($message);
    }
}
