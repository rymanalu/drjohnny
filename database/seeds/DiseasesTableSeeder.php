<?php

use App\Disease;
use Illuminate\Database\Seeder;

class DiseasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Disease::query()->truncate();

        foreach ($this->diseases() as $name) {
            Disease::create(compact('name'));
        }
    }

    protected function diseases()
    {
        return [
            'Abses Gigi', 'Alergi',
        ];
    }
}
