<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $fillable = [
        'equipment_id',
        'description',
        'type',
        'start_date',
        'end_date',
        'labor_cost',
        'technician_name'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function parts()
    {
        return $this->hasMany(Part::class);
    }
}
