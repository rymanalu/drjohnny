<?php

use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$botman->hears('Hi', function ($bot) {
    $bot->typesAndWaits(1.5);
    $bot->reply('Hello!');
});

$botman->hears('Hello', function ($bot) {
    $bot->typesAndWaits(1.5);
    $bot->reply('Hi!');
});

$botman->hears('Start conversation', BotManController::class.'@startConversation');
