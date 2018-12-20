<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    const GENDER_NOT_KNOWN = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const GENDER_NOT_APPLICABLE = 9;

    protected $fillable = [
        'access_id', 'username', 'first_name', 'last_name', 'gender', 'avatar',
    ];
}
