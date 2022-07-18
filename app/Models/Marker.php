<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Ui\UiServiceProvider;

class Marker extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
        'description',
        'type',
        'video_path',
        'iset_files',
        'color',
        'value',
        'text',
        'file_marker',
        'image_marker',
        'user_id'
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class,'marker_groups','marker_id','group_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
