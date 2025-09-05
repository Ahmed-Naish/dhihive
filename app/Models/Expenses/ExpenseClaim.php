<?php

namespace App\Models\Expenses;

use App\Models\User;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\coreApp\Traits\Relationship\StatusRelationTrait;

class ExpenseClaim extends BaseModel
{
    use HasFactory,StatusRelationTrait;

    protected $fillable = [
        'company_id',
        'user_id',
        'invoice_number',
        'claim_date',
        'remarks',
        'payable_amount',
        'due_amount',
        'attachment_file_id',
        'status_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    public function claimDetails():HasMany
    {
        return$this->hasMany(ClaimDetail::class,'expense_claim_id','id');
    }


}
