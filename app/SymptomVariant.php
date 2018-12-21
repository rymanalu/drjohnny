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

    public function scopeSearch($query, $search)
    {
        $tsQuery = 'plainto_tsquery(\'pg_catalog.simple\', ?)';

        return $query->whereRaw('variant @@ '.$tsQuery, [$search])
            ->orderByRaw('ts_rank(variant, '.$tsQuery.') desc', [$search]);
    }
}
