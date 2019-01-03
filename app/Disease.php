<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    protected $fillable = ['name', 'description'];

    public function symptoms()
    {
        return $this->belongsToMany(Symptom::class)->withTimestamps();
    }

    public static function predictBySymptomIds(array $symptomIds)
    {
        return static::query()
            ->join('disease_symptom', 'diseases.id', '=', 'disease_symptom.disease_id')
            ->selectRaw('diseases.*, count(diseases.id) as count')
            ->whereIn('disease_symptom.symptom_id', $symptomIds)
            ->groupBy('diseases.id')
            ->orderBy('count', 'desc')
            ->first();
    }
}
