<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description','user_id', 'updated_at',
    ];

    protected $perPage=50;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*public function markers()
    {
        return $this->hasMany(Marker::class);
    }*/

    public function markers()
    {
        return $this->belongsToMany(Marker::class,'marker_groups','group_id','marker_id');
    }
}
