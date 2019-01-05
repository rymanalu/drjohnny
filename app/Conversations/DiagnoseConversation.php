<?php

namespace App\Conversations;

use App\Disease;
use App\Symptom;
use App\SymptomVariant;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class DiagnoseConversation extends Conversation
{
    const MAX_BUTTONS = 5;

    const MIN_SYMPTOMS = 3;

    protected $symptomIds = [];

    protected $symptomCounter = 0;

    public function run()
    {
        $this->confirmation();
    }

    protected function confirmation()
    {
        $question = $this->confirmationQuestion();

        $laterConversation = new LaterConversation('Baiklah kalau begitu 😊');

        $this->ask($question, function (Answer $answer) use ($laterConversation) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'yes') {
                    $this->askUserSymptoms();
                } else {
                    $this->bot->startConversation($laterConversation);
                }
            } else {
                $reply = strtolower($answer->getText());

                if ($reply === 'ya') {
                    $this->askUserSymptoms();
                } elseif ($reply === 'tidak') {
                    $this->bot->startConversation($laterConversation);
                } else {
                    $this->repeat($this->confirmationQuestion(true));
                }
            }
        });
    }

    protected function confirmationQuestion($repeat = false)
    {
        $questionText = $repeat ? 'Saya ulangi, bersedia?' : 'Untuk memulai diagnosa, saya akan menanyakan setidaknya '.static::MIN_SYMPTOMS.' gejala yang Anda alami. Bersedia?';

        return Question::create($questionText)
            ->addButtons([
                Button::create('Ya 👍')->value('yes'),
                Button::create('Tidak 🙅‍♂️')->value('no'),
            ]);
    }

    protected function askUserSymptoms($more = false)
    {
        $question = $this->askUserSymptomsQuestion($more);

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->symptomIds[] = $answer->getValue();
            } else {
                $this->findSymptom($answer->getText());
            }

            $this->symptomCounter++;

            $this->say('Baik, gejala Anda sudah tercatat 📝');

            if ($this->symptomCounter > 2) {
                $this->askAnythingElse();
            } else {
                $this->askUserSymptoms(true);
            }
        });
    }

    protected function askUserSymptomsQuestion($more = false)
    {
        return Question::create('Silakan pilih salah satu gejala'.($more ? ' lagi ' : ' ').'di bawah ini, atau ketik saja 😊')
            ->addButtons($this->createSymptomQuestionButtons());
    }

    protected function createSymptomQuestionButtons()
    {
        $buttons = [];

        $symptoms = Symptom::query()
            ->whereNotIn('id', $this->symptomIds)
            ->inRandomOrder()
            ->take(static::MAX_BUTTONS)
            ->cursor();

        foreach ($symptoms as $symptom) {
            $variant = $symptom->variants()->inRandomOrder()->first();

            $buttons[] = Button::create($variant->name)->value($symptom->id);
        }

        return $buttons;
    }

    protected function askAnythingElse()
    {
        $getStartedConversation = new GetStartedConversation('Ada hal lain yang bisa saya bantu? 😊', false, true);

        $question = $this->askAnythingElseQuestion();

        $this->ask($question, function (Answer $answer) use ($getStartedConversation) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'yes') {
                    $this->askUserSymptoms();
                } else {
                    if ($this->symptomCounter < static::MIN_SYMPTOMS) {
                        $this->askMoreSymptoms();
                    } else {
                        $this->diagnose();
                        $this->bot->startConversation($getStartedConversation);
                    }
                }
            } else {
                $this->repeat($this->askAnythingElseQuestion(true));
            }
        });
    }

    protected function askAnythingElseQuestion($repeat = false)
    {
        $questionText = $repeat ? 'Ya atau tidak? 🤔' : 'Adakah gejala lain yang ingin Anda bagikan? 😊';

        return Question::create($questionText)
            ->addButtons([
                Button::create('Ya 👍')->value('yes'),
                Button::create('Tidak 🙅‍♂️')->value('no'),
            ]);
    }

    protected function askMoreSymptoms()
    {
        $this->say('Demi akurasi analisa, baiknya Anda memberitahu setidaknya '.static::MIN_SYMPTOMS.' gejala 😊');

        $question = $this->askMoreSymptomsQuestion();

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->askUserSymptoms();
            } else {
                $this->repeat($this->askMoreSymptomsQuestion(true));
            }
        });
    }

    protected function askMoreSymptomsQuestion($repeat = false)
    {
        $questionText = $repeat ? 'Tekan OK untuk melanjutkan' : 'Lanjutkan?';

        return Question::create($questionText)->addButton(Button::create('OK 👌')->value('ok'));
    }

    protected function findSymptom($search)
    {
        $search = remove_stop_words($search);

        $symptomVariant = SymptomVariant::search($search)
            ->whereNotIn('symptom_id', $this->symptomIds)
            ->first();

        if ($symptomVariant) {
            $this->symptomIds[] = $symptomVariant->symptom->id;
        }
    }

    protected function diagnose()
    {
        $this->say('Baik, saat ini saya akan mulai menganalisa... 🤔');

        $disease = Disease::predictBySymptomIds($this->symptomIds);

        if ($disease) {
            $this->say('Sepertinya saat ini Anda mengidap penyakit: ' . $disease->name);

            if ($disease->description) {
                $this->say($disease->description);
            }

            $this->say('Segera lakukan pengobatan yang diperlukan 💊');
        } else {
            $this->say('Mohon maaf, saat ini saya tidak bisa memprediksi penyakit Anda 😞');

            $this->say('Silakan ulangi diagnosa bila diperlukan 🙏');
        }
    }
}
