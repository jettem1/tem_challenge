<?php

namespace App\Services\AsyncEmail\Transport;

use App\Models\Email;
use Exception;
use Mailjet\Client;
use Mailjet\Resources;

class MailJetEmailTransport implements IEmailTransport
{
    private Client $mailJetClient;

    private string $senderEmail;

    public function __construct(string $key, string $secret, string $senderEmail)
    {
        $this->mailJetClient = new Client($key, $secret, true, ['version' => 'v3.1']);
        $this->senderEmail   = $senderEmail;
    }

    public function send(Email $email): bool
    {
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $this->senderEmail,
                    ],
                    'To' => [
                        [
                            'Email' => $email->recipient,
                        ]
                    ],
                    'Subject' => $email->subject,
                    'TextPart' => $email->body,
                ]
            ]
        ];

        try {
            $response = $this->mailJetClient->post(Resources::$Email, ['body' => $body]);
            return $response->success();
        } catch (Exception $e) {
            return false;
        }

    }
}