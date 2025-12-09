<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Act extends Model
{
    protected $fillable = [
        'partner_id',
        'act_date',
        'act_number'
    ];

    protected $casts = [
        'act_date' => 'date'
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function works(): BelongsToMany
    {
        return $this->belongsToMany(Work::class, 'act_works');
    }
}