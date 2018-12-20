<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    protected $fillable = ['name'];

    public function symptoms()
    {
        return $this->belongsToMany(Symptom::class);
    }
}
