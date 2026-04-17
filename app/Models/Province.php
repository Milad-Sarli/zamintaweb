<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'latitude',
        'longitude',
    ];

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
