<?php

namespace App\Jobs;

use App\Exceptions\EmailException;
use App\Models\Email;
use App\Services\AsyncEmail\AsyncEmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EmailSender implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Email $email;

    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * @throws EmailException
     */
    public function handle(AsyncEmailService $asyncEmailService)
    {
        $asyncEmailService->sendEmail($this->email);
    }
}
