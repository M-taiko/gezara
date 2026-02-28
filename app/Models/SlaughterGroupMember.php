<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SlaughterGroupMember extends Model
{
    protected $fillable = ['group_id', 'customer_id', 'contract_item_id', 'shares_count', 'notes'];

    protected $casts = ['shares_count' => 'integer'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(SlaughterGroup::class, 'group_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function contractItem(): BelongsTo
    {
        return $this->belongsTo(ContractItem::class);
    }
}
