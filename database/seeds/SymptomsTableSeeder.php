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

        foreach ($this->symptoms() as $name => $data) {
            $symptomData = compact('name');

            $symptom = Symptom::create($symptomData);

            $symptom->variants()->create($symptomData);

            foreach ($data['variants'] as $variant) {
                $symptom->variants()->create(['name' => $variant]);
            }

            $diseases = Disease::query()->whereIn('name', $data['diseases'])->get();

            $symptom->diseases()->attach($diseases->pluck('id')->toArray());
        }
    }

    protected function symptoms()
    {
        return [
            'Bersin' => [
                'diseases' => [
                    'Alergi',
                ],
                'variants' => [
                    'Bersin-bersin',
                ],
            ],
            'Batuk' => [
                'diseases' => [
                    'Alergi',
                ],
                'variants' => [
                    'Batuk-batuk',
                ],
            ],
            'Sesak napas' => [
                'diseases' => [
                    'Alergi',
                ],
                'variants' => [],
            ],
            'Ruam' => [
                'diseases' => [
                    'Alergi',
                ],
                'variants' => [
                    'Ruam pada kulit',
                ],
            ],
            'Ingus' => [
                'diseases' => [
                    'Alergi',
                ],
                'variants' => [
                    'Hidung beringus',
                ],
            ],
            'Bengkak' => [
                'diseases' => [
                    'Alergi',
                ],
                'variants' => [
                    'Bengkak pada wajah',
                    'Bengkak pada mulut',
                    'Bengkak pada lidah',
                    'Pembengkakan pada wajah atau pipi',
                ],
            ],
            'Gatal' => [
                'diseases' => [
                    'Alergi',
                ],
                'variants' => [
                    'Gatal pada mata',
                ],
            ],
            'Mata merah' => [
                'diseases' => [
                    'Alergi',
                ],
                'variants' => [],
            ],
            'Mata berair' => [
                'diseases' => [
                    'Alergi',
                ],
                'variants' => [],
            ],
            'Sakit perut' => [
                'diseases' => [
                    'Alergi',
                ],
                'variants' => [],
            ],
            'Muntah' => [
                'diseases' => [
                    'Alergi',
                ],
                'variants' => [
                    'Muntah-muntah',
                ],
            ],
            'Demam' => [
                'diseases' => [
                    'Abses Gigi',
                ],
                'variants' => [],
            ],
            'Sensitif pada tekanan saat mengunyah atau menggigit' => [
                'diseases' => [
                    'Abses Gigi',
                ],
                'variants' => [],
            ],
            'Sensitif pada suhu panas atau dingin' => [
                'diseases' => [
                    'Abses Gigi',
                ],
                'variants' => [],
            ],
            'Nyeri parah dan berdenyut pada gigi, yang dapat menyebar ke tulang rahang, leher, atau telinga' => [
                'diseases' => [
                    'Abses Gigi',
                ],
                'variants' => [],
            ],
            'Kemerahan pada mulut dan wajah' => [
                'diseases' => [
                    'Abses Gigi',
                ],
                'variants' => [],
            ],
        ];
    }
}
