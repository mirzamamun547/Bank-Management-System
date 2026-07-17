<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $table = 'accounts';

    protected $fillable = [
        'user_id',
        'account_number',
        'account_type',
        'balance',
        'status',
        'branch',
        'branch_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Renamed from branch() to avoid conflict with the 'branch' string column.
     * Use $acc->branchInfo->branch_name to get branch name via relationship.
     */
    public function branchInfo()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }

    /**
     * Helper: returns branch name from the stored string column,
     * falling back to the relationship if the column is empty.
     */
    public function getBranchNameAttribute(): string
    {
        if (!empty($this->branch)) {
            return $this->branch;
        }
        return $this->branchInfo?->branch_name ?? 'N/A';
    }
}
