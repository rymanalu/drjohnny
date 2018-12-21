<?php

use App\Http\Controllers\BotManController;
use App\Http\Middleware\MarkSeen;

$botman = resolve('botman');

$botman->middleware->received(new MarkSeen);

$botman->hears('Hai', function ($bot) {
    $bot->typesAndWaits(2);
    $bot->reply('Halo!');
});

$botman->hears('Halo', function ($bot) {
    $bot->typesAndWaits(2);
    $bot->reply('Hai!');
});

$botman->hears('Start conversation', BotManController::class.'@startConversation');

$botman->fallback(function ($bot) {
    $bot->typesAndWaits(2);
    $bot->reply('Sorry, I did not understand these commands. Here is a list of commands I understand: ...');
});
