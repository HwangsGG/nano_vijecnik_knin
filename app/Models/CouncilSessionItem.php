<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouncilSessionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'council_session_id',
        'council_session_item_type_id',
        'name',
        'data',
        'locked',
        'active',
        'item_number'
    ];

    public function council_session()
    {
        return $this->belongsTo(CouncilSession::class);
    }

    public function council_session_item_type()
    {
        return $this->belongsTo(CouncilSessionItemType::class);
    }

    public function council_votes()
    {
        return $this->hasMany(CouncilVote::class);
    }
}
