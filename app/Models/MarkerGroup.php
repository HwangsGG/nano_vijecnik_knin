<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkerGroup extends Model
{
    protected $table='marker_groups';
    use HasFactory;

    protected $fillable = [
        'marker_id', 'group_id'
    ];
}
