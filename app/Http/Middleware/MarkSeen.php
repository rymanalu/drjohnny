<?php

namespace App\Http\Middleware;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Received;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\Drivers\Facebook\FacebookDriver;

class MarkSeen implements Received
{
    public function received(IncomingMessage $message, $next, BotMan $bot)
    {
        if ($bot->getDriver() instanceof FacebookDriver) {
            $bot->markSeen($message);
        }

        return $next($message);
    }
}
