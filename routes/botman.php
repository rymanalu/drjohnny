<?php

$botman = resolve('botman');

$botman->middleware->received(new \App\Http\Middleware\MarkSeen);
$botman->middleware->sending(new \App\Http\Middleware\StartTyping);

// Facebook Messenger Get Started
$botman->hears(config('botman.facebook.start_button_payload'), function ($bot) {
    $bot->startConversation(new \App\Conversations\GetStartedConversation);
});

// Telegram Get Started
$botman->hears('/start', function ($bot) {
    $bot->startConversation(new \App\Conversations\GetStartedConversation);
});

$botman->hears('Mulai diagnosa', function ($bot) {
    $bot->startConversation(new \App\Conversations\DiagnoseConversation);
});

$botman->hears('Hai', function ($bot) {
    $bot->reply('Halo!');
});

$botman->hears('Halo', function ($bot) {
    $bot->reply('Hai!');
});

$botman->fallback(function ($bot) {
    $bot->reply('Maaf, saya tidak mengerti maksud Anda 🙏');
});
