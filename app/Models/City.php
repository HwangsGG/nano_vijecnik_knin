<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'post_code',
        'updated_at',
    ];

    protected $perPage=50;

    public function users()
    {
        return $this->hasMany(User::class);
    }


}
