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

        $data = json_decode(file_get_contents(base_path('data.json')), true);

        foreach ($data['diseases'] as $disease) {
            Disease::create($disease);
        }
    }
}
