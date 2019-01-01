<?php

namespace App\Conversations;

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

    protected $symptoms;

    protected $symptomCounter = 0;

    public function run()
    {
        $this->symptoms = collect();

        $this->confirmation();
    }

    protected function confirmation()
    {
        $question = $this->confirmationQuestion();

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'yes') {
                    $this->askUserSymptoms();
                } else {
                    $this->say('Well, bye!');
                }
            } else {
                $reply = strtolower($answer->getText());

                if ($reply === 'ya') {
                    $this->askUserSymptoms();
                } elseif ($reply === 'tidak') {
                    $this->say('Well, bye!');
                } else {
                    $this->repeat($this->confirmationQuestion(true));
                }
            }
        });
    }

    protected function confirmationQuestion($repeat = false)
    {
        $questionText = $repeat ? 'Saya ulangi, bersedia?' : 'Untuk memulai diagnosa, saya akan menanyakan gejala yang Anda rasakan. Bersedia?';

        return Question::create($questionText)
            ->addButtons([
                Button::create('Ya 👍')->value('yes'),
                Button::create('Tidak 👎')->value('no'),
            ]);
    }

    protected function askUserSymptoms()
    {
        $question = Question::create('Silakan pilih salah satu gejala di bawah ini, atau ketik saja 😊')
            ->addButtons($this->createSymptomQuestionButtons());

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->symptoms->push(Symptom::find($answer->getValue()));
            } else {
                $this->findSymptom($answer->getText());
            }

            $this->symptomCounter++;

            $this->askAnythingElse();
        });
    }

    protected function createSymptomQuestionButtons()
    {
        $buttons = [];

        $symptoms = Symptom::query()
            ->whereNotIn('id', $this->symptoms->pluck('id')->toArray())
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
        $this->say('Baik, gejala Anda sudah tercatat 📝');

        $question = $this->askAnythingElseQuestion();

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'yes') {
                    $this->askUserSymptoms();
                } else {
                    if ($this->symptomCounter < static::MIN_SYMPTOMS) {
                        $this->askMoreSymptoms();
                    } else {
                        $this->diagnose();
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
                Button::create('Tidak 👎')->value('no'),
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
            ->whereNotIn('symptom_id', $this->symptoms->pluck('id')->toArray())
            ->first();

        if ($symptomVariant) {
            $this->symptoms->push($symptomVariant->symptom);
        }
    }

    protected function diagnose()
    {
        $this->say('Baik, saat ini saya akan mulai menganalisa... 🤔');

        $possibilityDiseases = [];

        foreach ($this->symptoms as $symptom) {
            foreach ($symptom->diseases as $disease) {
                if (array_key_exists($disease->name, $possibilityDiseases)) {
                    $count = $possibilityDiseases[$disease->name];
                    $possibilityDiseases[$disease->name] = $count + 1;
                } else {
                    $possibilityDiseases[$disease->name] = 1;
                }
            }
        }

        if (count($possibilityDiseases) > 0) {
            arsort($possibilityDiseases);

            $this->say('Sepertinya saat ini Anda mengidap penyakit: ' . array_keys($possibilityDiseases)[0]);
            $this->say('Silakan segera melakukan pengobatan yang diperlukan 💊');
        } else {
            $this->say('Mohon maaf, saat ini saya tidak bisa memprediksi penyakit Anda 😞');
            $this->say('Silakan ulangi diagnosa bila diperlukan 🙏');
        }

        $this->say('Terima kasih 😊');
    }
}
