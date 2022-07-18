<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city_id',
        'telephone',
        'data',
        'account_type_id',
        'active'
    ];

    public function users()
    {
        return $this->hasMany(User::class)
            ->select([
            'id',
            'account_id',
            'name',
            'data'
        ])->without([
            'permissions',
            'roles',
            'roles.permissions',
            'city',
        ]);
    }

    public function account_type()
    {
        return $this->belongsTo(AccountType::class);
    }
}
