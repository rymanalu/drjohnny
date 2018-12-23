<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class GetStartedConversation extends Conversation
{
    public function run()
    {
        $this->say(
            collect(['Selamat datang!', 'Halo!', 'Hai!'])->random().' 🙌'
        );

        $this->say('Perkenalkan, saya Dr. Johnny 👨‍⚕️');

        $this->say('Chat bot yang dapat mendiagnosa penyakit berdasarkan gejala yang Anda alami 😷');

        $this->say('Untuk memulai diagnosa, cukup dengan kirim "Mulai diagnosa" ✍️');

        $this->say('Atau tekan tombol [Mulai diagnosa] di menu 📲 (pengguna Facebook Messenger)');

        $this->say('Atau kirim "/diagnosa" ✍️ (pengguna Telegram)');
    }
}
