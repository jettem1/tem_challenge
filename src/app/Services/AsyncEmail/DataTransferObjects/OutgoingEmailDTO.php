<?php

namespace App\Services\AsyncEmail\DataTransferObjects;

use App\Exceptions\ValidationException;
use Exception;

class OutgoingEmailDTO
{
    private $recipients;

    private $subject;

    private $body;

    /**
     * @throws Exception
     */
    public function __construct($recipients, $subject, $body)
    {
        $this->recipients = $recipients;
        $this->subject    = $subject;
        $this->body       = $body;

        $this->validateOrThrow();
    }

    public function getRecipients()
    {
        return $this->recipients;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getBody()
    {
        return $this->body;
    }

    /**
     * @throws Exception
     */
    private function validateOrThrow()
    {
        if (!is_array($this->getRecipients())) {
            throw new ValidationException('Recipients must be an array');
        }

        if (!count($this->getRecipients())) {
            throw new ValidationException('There must be at least 1 recipient');
        }

        foreach ($this->getRecipients() as $recipient) {
            if (!filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                throw new ValidationException('Invalid recipient: ' . $recipient);
            }
        }

        if (empty($this->getSubject())) {
            throw new ValidationException('Subject can not be empty');
        }

        if (empty($this->getBody())) {
            throw new ValidationException('Body can not be empty');
        }
    }

}