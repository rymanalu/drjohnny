<?php

namespace App\Conversations;

use App\Disease;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class DiseaseInfoConversation extends Conversation
{
    public function run()
    {
        $this->askDisease();
    }

    protected function askDisease()
    {
        $question = Question::create('Silakan pilih salah satu penyakit di bawah ini, atau ketik saja 😊')
            ->addButtons($this->randomDiseases()->toArray());

        $getStartedConversation = new GetStartedConversation('Ada hal lain yang bisa saya bantu? 😊', false, true);

        $this->ask($question, function (Answer $answer) use ($getStartedConversation) {
            if ($answer->isInteractiveMessageReply()) {
                $disease = \App\Disease::find($answer->getValue());

                $this->say($disease->name);

                if ($disease->description) {
                    $this->say($disease->description);
                }
            } else {
                $this->findDiseaseByName($answer->getText());
            }

            $this->bot->startConversation($getStartedConversation);
        });
    }

    protected function randomDiseases()
    {
        return Disease::query()
            ->inRandomOrder()
            ->take(5)
            ->get()
            ->map(function ($disease) {
                return Button::create($disease->name)->value($disease->id);
            });
    }

    protected function findDiseaseByName($search)
    {
        $disease = Disease::search($search)->first();
        
        if (is_null($disease)) {
            $this->say('Maaf, saya tidak memiliki data penyakit '. $search);
        } else {
            $this->say($disease->name);

            if ($disease->description) {
                $this->say($disease->description);
            }
        }
    }
}
