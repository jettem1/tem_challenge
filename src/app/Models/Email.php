<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    public const STATUS_FAILED = -1;
    public const STATUS_PENDING = 0;
    public const STATUS_IN_PROGRESS = 1;
    public const STATUS_SENT = 2;

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    protected $fillable = ['recipient', 'subject', 'body'];
}
