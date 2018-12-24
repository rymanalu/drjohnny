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

    protected $symptoms;

    public function run()
    {
        $this->symptoms = collect();

        $this->askUserSymptoms();
    }

    protected function askUserSymptoms()
    {
        $question = Question::create('Silakan pilih salah satu gejala di bawah ini, atau ketik saja 😊')
            ->addButtons($this->createSymptomQuestionButtons());

        $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->symptoms->push(
                    Symptom::find($answer->getValue())
                );
            } else {
                $this->findSymptom($answer->getText());
            }

            $this->askAnythingElse();
        });
    }

    protected function createSymptomQuestionButtons()
    {
        $buttons = [];

        $symptoms = Symptom::query()
            ->whereNotIn('id', $this->symptoms->pluck('id')->toArray())
            ->inRandomOrder()
            ->take(5)
            ->cursor();

        foreach ($symptoms as $symptom) {
            $variant = $symptom->variants()->inRandomOrder()->first();

            $buttons[] = Button::create($variant->name)->value($symptom->id);
        }

        return $buttons;
    }

    protected function askAnythingElse()
    {
        $this->say('Baik, keluhan Anda sudah saya catat 📝');

        $question = Question::create('Adakah gejala lain yang ingin Anda bagikan? 😊')
            ->addButtons([
                Button::create('Ya')->value('yes'),
                Button::create('Tidak')->value('no'),
            ]);

        $this->ask($question, function (Answer $answer) {
            $answerText = $answer->isInteractiveMessageReply() ? $answer->getValue() : $answer->getText();

            if (strtolower($answerText) === 'yes' || strtolower($answerText) === 'ya') {
                $this->askUserSymptoms();
            } else {
                $this->diagnose();
            }
        });
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
            $this->say('Ini masih prediksi dan tidak bisa dijadikan acuan 🙏');
            $this->say('Silakan segera melakukan pengobatan yang diperlukan 💊');
        } else {
            $this->say('Mohon maaf, saat ini saya tidak bisa memprediksi penyakit Anda 😞');
            $this->say('Silakan ulangi diagnosa bila diperlukan 🙏');
        }

        $this->say('Terima kasih 😊');
    }
}
