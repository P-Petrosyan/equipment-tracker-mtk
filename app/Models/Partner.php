<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'region',
        'address',
        'tnoren',
        'hashvapah',
        'account_number',
        'notes'
    ];

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }

    public function structures()
    {
        return $this->hasMany(PartnerStructure::class);
    }
}
