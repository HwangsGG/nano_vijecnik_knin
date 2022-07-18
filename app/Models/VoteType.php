<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active'
    ];

    public function councilVotes()
    {
        return $this->hasMany(CouncilVote::class);
    }
}
