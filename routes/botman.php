<?php

use App\Http\Controllers\BotManController;
use App\Http\Middleware\MarkSeen;

$botman = resolve('botman');

$botman->middleware->received(new MarkSeen);

$botman->hears('Hi', function ($bot) {
    $bot->typesAndWaits(2);
    $bot->reply('Hello!');
});

$botman->hears('Hello', function ($bot) {
    $bot->typesAndWaits(2);
    $bot->reply('Hi!');
});

$botman->hears('Start conversation', BotManController::class.'@startConversation');
