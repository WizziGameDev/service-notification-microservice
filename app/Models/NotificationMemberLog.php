<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class NotificationMemberLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'transaction_id',
        'member_id',
        'member_name',
        'phone',
        'products',
        'status',
        'message',
    ];
}
