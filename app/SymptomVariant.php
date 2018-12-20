<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SymptomVariant extends Model
{
    protected $fillable = ['symptom_id', 'name'];

    public function symptom()
    {
        return $this->belongsTo(Symptom::class);
    }
}
