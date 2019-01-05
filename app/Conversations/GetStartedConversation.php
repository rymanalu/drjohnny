<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class GetStartedConversation extends Conversation
{
    protected $intro;

    protected $useGreeting;

    protected $useLater;

    public function __construct($intro = null, $useGreeting = true, $useLater = false)
    {
        $this->intro = $intro;
        $this->useGreeting = $useGreeting;
        $this->useLater = $useLater;
    }

    public function run()
    {
        if ($this->useGreeting) {
            $this->say(
                collect(['Selamat datang', 'Halo', 'Hai'])->random().', '.$this->bot->getUser()->getFirstName().'! 🙌'
            );
        }

        if (! $this->intro) {
            $this->say('Perkenalkan, saya Dr. Johnny 👨‍⚕️ Dokter virtual dalam bentuk chatbot 🤖');
        }

        $this->getStarted();
    }

    protected function getStarted()
    {
        $question = $this->getStartedQuestion();

        $diagnoseConversation = new DiagnoseConversation;
        $laterConversation = new LaterConversation;

        $this->ask($question, function (Answer $answer) use ($diagnoseConversation, $laterConversation) {
            if ($answer->isInteractiveMessageReply()) {
                $value = $answer->getValue();

                switch ($value) {
                    case 'diagnose':
                        $this->bot->startConversation($diagnoseConversation);
                        break;
                    case 'disease_info':
                        break;
                    case 'later':
                        $this->bot->startConversation($laterConversation);
                        break;
                }
            } else {
                $this->repeat($this->getStartedQuestion(true));
            }
        });
    }

    protected function getStartedQuestion($repeat = false)
    {
        $questionText = 'Layaknya dokter di dunia nyata, berikut ini adalah beberapa hal yang dapat saya lakukan:';

        $buttons = [
            Button::create('Diagnosa 🕵️‍♂️')->value('diagnose'),
            Button::create('Info Penyakit 🤒')->value('disease_info'),
        ];

        if ($repeat || $this->useLater) {
            if ($repeat) {
                $this->say('Mohon maaf, saya tidak mengerti maksud Anda 🙏');
            }

            $questionText = 'Silakan pilih salah satu dari tombol dibawah ini untuk memulai:';

            $buttons[] = Button::create('Nanti saja 👋')->value('later');
        }

        if ($this->intro && is_string($this->intro)) {
            $questionText = $this->intro;
        }

        return Question::create($questionText)->addButtons($buttons);
    }
}
