<?php

namespace App\Models\Expenses;

use App\Models\User;
use App\Models\coreApp\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenseClaimDetails extends BaseModel
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    

    public function expenseClaim()
    {
        return $this->belongsTo(ExpenseClaim::class,'expense_claim_id','id');
    }

    public function hrmExpense()
    {
        return $this->belongsTo(HrmExpense::class,'hrm_expense_id','id');
    }
}
