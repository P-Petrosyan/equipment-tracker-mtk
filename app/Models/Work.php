<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Work extends Model
{
    protected $fillable = [
        'receive_date',
        'exit_date',
        'partner_id',
        'partner_structure_id',
        'equipment_id',
        'equipment_part_group_id',
        'equipment_part_group_total_price',
        'old_serial_number',
        'new_serial_number',
        'partner_representative',
        'non_repairable',
        'conclusion_number',
        'defects_description',
        'status', //  $work->status == 1 ? 'archived' : 'active'
        'work_order_status' // Կատարողական: 0 -> Չկա կատարողական : 1 -> Կատարողականով
    ];

    protected $casts = [
        'receive_date' => 'date:Y-m-d',
        'exit_date' => 'date:Y-m-d',
        'non_repairable' => 'boolean',
        'equipment_part_group_total_price' => 'decimal:2',
        'status' => 'integer'
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function partnerStructure(): BelongsTo
    {
        return $this->belongsTo(PartnerStructure::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function equipmentPartGroup(): BelongsTo
    {
        return $this->belongsTo(EquipmentPartGroup::class);
    }

    public function acts(): BelongsToMany
    {
        return $this->belongsToMany(Act::class, 'act_works');
    }
}
