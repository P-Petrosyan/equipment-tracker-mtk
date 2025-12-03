<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartnerStructure extends Model
{
    protected $fillable = ['partner_id', 'name'];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
