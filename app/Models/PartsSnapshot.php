<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartsSnapshot extends Model
{
    protected $fillable = [
        'snapshot_date',
        'parts_data'
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'parts_data' => 'array'
    ];
}
