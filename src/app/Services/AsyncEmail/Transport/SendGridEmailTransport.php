<?php

namespace App\Services\AsyncEmail\Transport;


use App\Models\Email;
use Exception;
use SendGrid;
use SendGrid\Mail\Mail;

class SendGridEmailTransport implements IEmailTransport
{
    private SendGrid $sendGridClient;

    private string $senderEmail;

    public function __construct(string $apiKey, string $senderEmail)
    {
        $this->sendGridClient = new SendGrid($apiKey);
        $this->senderEmail    = $senderEmail;
    }

    public function send(Email $email): bool
    {
        try {
            $sendGridEmail = new Mail();
            $sendGridEmail->setFrom($this->senderEmail);
            $sendGridEmail->setSubject($email->subject);
            $sendGridEmail->addTo($email->recipient);
            $sendGridEmail->addContent("text/plain", $email->body);

            $response = $this->sendGridClient->send($sendGridEmail);

            return in_array($response->statusCode(), [200, 201, 202]);
        } catch (Exception $e) {
            return false;
        }
    }
}