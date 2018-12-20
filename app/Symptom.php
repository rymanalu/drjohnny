<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Symptom extends Model
{
    protected $fillable = ['name'];

    public function variants()
    {
        return $this->hasMany(SymptomVariant::class);
    }

    public function diseases()
    {
        return $this->belongsToMany(Disease::class);
    }
}
