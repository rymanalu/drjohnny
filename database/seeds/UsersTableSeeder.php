<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->truncate();

        $users = [
            'feri.ferdiana' => 'Feri Ferdiana',
            'roni.yusuf' => 'Roni Yusuf',
        ];

        foreach ($users as $email => $name) {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => bcrypt('password'),
            ]);
        }
    }
}
