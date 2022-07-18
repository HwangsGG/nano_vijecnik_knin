<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouncilSessionItemType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'data',
        'active',
    ];

    public function council_session_item()
    {
        return $this->hasMany(CouncilSessionItem::class);
    }

    public function council_votes()
    {
        return $this->hasMany(CouncilVote::class);
    }
}
