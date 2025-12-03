<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'region',
        'region_hf',
        'address',
        'bank',
        'region_r',
        'tnoren',
        'hashvapah',
        'account_number',
        'tax_id',
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
