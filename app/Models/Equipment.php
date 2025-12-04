<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $fillable = [
        'name',
        'stug_price',
        'notes'
    ];

    public function partGroups()
    {
        return $this->hasMany(EquipmentPartGroup::class);
    }
}
