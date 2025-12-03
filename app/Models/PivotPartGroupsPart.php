<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PivotPartGroupsPart extends Model
{
    protected $table = 'pivot_part_groups_parts';
    
    protected $fillable = [
        'equipment_part_group_id',
        'part_id',
        'quantity',
        'unit_price',
        'comment'
    ];

    public function partGroup()
    {
        return $this->belongsTo(EquipmentPartGroup::class, 'equipment_part_group_id');
    }

    public function part()
    {
        return $this->belongsTo(Part::class);
    }
}
