<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $primaryKey = 'branch_id';

    protected $fillable = [
        'branch_name',
        'location',
        'manager_employee_id',
        'status',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_employee_id', 'customer_id');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'branch_id', 'branch_id');
    }
}
