<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'quantity',
        'unit_price',
        'drawing_number',
        'measure_unit',
        'description'
    ];

    public function partGroups()
    {
        return $this->belongsToMany(EquipmentPartGroup::class, 'pivot_part_groups_parts', 'part_id', 'equipment_part_group_id')
                    ->withPivot('quantity', 'unit_price', 'comment')
                    ->withTimestamps();
    }
}
