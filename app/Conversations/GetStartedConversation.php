<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class GetStartedConversation extends Conversation
{
    public function run()
    {
        $this->say(
            collect(['Selamat datang!', 'Halo!', 'Hai!'])->random().' 🙌'
        );

        $this->say('Perkenalkan, saya Dr. Johnny 👨‍⚕️ Dokter virtual dalam bentuk chatbot 🤖');

        $this->getStarted();
    }

    protected function getStarted()
    {
        $question = $this->getStartedQuestion();

        $diagnoseConversation = new DiagnoseConversation;

        $this->ask($question, function (Answer $answer) use ($diagnoseConversation) {
            if ($answer->isInteractiveMessageReply()) {
                $value = $answer->getValue();

                switch ($value) {
                    case 'diagnose':
                        $this->bot->startConversation($diagnoseConversation);
                        break;
                    case 'disease_info':
                        break;
                    case 'later':
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

        if ($repeat) {
            $this->say('Maaf, saya tidak mengerti maksud Anda 🙏');

            $questionText = 'Silakan pilih salah satu dari tombol dibawah ini untuk memulai:';

            $buttons[] = Button::create('Nanti saja 👋')->value('later');
        }

        return Question::create($questionText)->addButtons($buttons);
    }
}
