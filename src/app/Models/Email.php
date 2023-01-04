<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $created_at
 * @property string $updated_at
 * @property string $recipient
 * @property string $subject
 * @property string $body
 * @property int $status
 */
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
