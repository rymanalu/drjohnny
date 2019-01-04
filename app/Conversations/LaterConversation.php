<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class LaterConversation extends Conversation
{
    protected $intro;

    public function __construct($intro = null)
    {
        $this->intro = $intro;
    }

    public function run()
    {
        if (is_string($this->intro)) {
            $this->say($this->intro);
        }

        $question = Question::create('Ketik saja "hai" untuk menghubungi saya lagi')
            ->addButton(Button::create('OK 👌')->value('ok'));

        $this->ask($question, function () {});
    }
}
