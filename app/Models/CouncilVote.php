<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouncilVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'council_session_id',
        'council_session_item_id',
        'council_session_item_type_id',
        'user_id',
        'vote_type_id',
    ];

    public function council_session()
    {
        return $this->belongsTo(CouncilSession::class);
    }

    public function council_session_item()
    {
        return $this->belongsTo(CouncilSessionItem::class);
    }

    public function council_session_item_type()
    {
        return $this->belongsTo(CouncilSessionItemType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->select([
            'id',
            'account_id',
            'name',
            'data',
        ])->without([
            'permissions',
            'roles',
            'roles.permissions',
            'city',
        ]);
    }

    public function vote_type()
    {
        return $this->belongsTo(VoteType::class);
    }
}
