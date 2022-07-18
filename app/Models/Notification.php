<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_id',
        'council_session_id',
        'council_session_item_id',
        'status',
        'user_id',
        'account_id'
    ];

    public function council_session()
    {
        return $this->belongsTo(CouncilSession::class);
    }

    public function council_session_item()
    {
        return $this->belongsTo(CouncilSessionItem::class);
    }

}
