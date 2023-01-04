<?php

namespace App\Services\AsyncEmail\Transport;

use App\Models\Email;

interface IEmailTransport
{
    public function send(Email $email): bool;
}