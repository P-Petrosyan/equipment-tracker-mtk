<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentPartGroup extends Model
{
    protected $fillable = [
        'equipment_id',
        'name',
        'total_price',
        'total_price_alt',
        'notes'
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function parts()
    {
        return $this->belongsToMany(Part::class, 'pivot_part_groups_parts', 'equipment_part_group_id', 'part_id')
                    ->withPivot('quantity', 'unit_price', 'comment')
                    ->withTimestamps();
    }
}
