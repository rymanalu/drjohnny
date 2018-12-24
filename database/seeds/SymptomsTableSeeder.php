<?php

use App\Disease;
use App\Symptom;
use Illuminate\Database\Seeder;

class SymptomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Symptom::query()->truncate();

        $data = json_decode(file_get_contents(base_path('data.json')), true);

        foreach ($data['symptoms'] as $data) {
            $symptomData = ['name' => $data['name']];

            $symptom = Symptom::create($symptomData);

            $symptom->variants()->create($symptomData);

            foreach ($data['variants'] as $variant) {
                $symptom->variants()->create(['name' => $variant]);
            }

            $diseases = Disease::query()->whereIn('name', $data['diseases'])->get();

            $symptom->diseases()->attach($diseases->pluck('id')->toArray());
        }
    }
}
