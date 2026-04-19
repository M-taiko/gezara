<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractItem extends Model
{
    protected $fillable = ['contract_id', 'animal_id', 'group_id', 'share_type', 'shares_count', 'weight', 'unit_price', 'total_price', 'delivered_at'];
    protected $casts = ['shares_count' => 'integer', 'weight' => 'float', 'unit_price' => 'float', 'total_price' => 'float', 'delivered_at' => 'datetime'];

    public function contract(): BelongsTo      { return $this->belongsTo(Contract::class); }
    public function animal(): BelongsTo        { return $this->belongsTo(Animal::class); }
    public function group(): BelongsTo         { return $this->belongsTo(SlaughterGroup::class, 'group_id'); }
    public function groupMember(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(SlaughterGroupMember::class, 'contract_item_id');
    }

    public function shareLabel(): string
    {
        if ($this->share_type === 'full') return 'كامل';
        return Animal::SHARE_LABELS[$this->share_type] ?? $this->share_type;
    }
}
