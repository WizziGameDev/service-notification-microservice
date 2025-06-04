<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationMitraLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'transaction_id',
        'mitra_id',
        'mitra_name',
        'phone',
        'products',
        'status',
        'message',
    ];
}
