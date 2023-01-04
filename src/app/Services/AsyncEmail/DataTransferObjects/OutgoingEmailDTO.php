<?php

namespace App\Services\AsyncEmail\DataTransferObjects;

use App\Exceptions\ValidationException;
use Exception;
use Parsedown;

class OutgoingEmailDTO
{
    public const FORMAT_TEXT = 'text';
    public const FORMAT_HTML = 'html';
    public const FORMAT_MARKDOWN = 'markdown';

    public const VALID_FORMATS = [self::FORMAT_TEXT, self::FORMAT_HTML, self::FORMAT_MARKDOWN];

    private $recipients;

    private $subject;

    private $body;

    private $format;

    /**
     * @throws Exception
     */
    public function __construct($recipients, $subject, $body, $format)
    {
        $this->recipients = $recipients;
        $this->subject    = $subject;
        $this->body       = $body;
        $this->format     = $format ?? self::FORMAT_TEXT;

        $this->validateOrThrow();

        if ($this->getFormat() == self::FORMAT_MARKDOWN) {
            $this->body = $this->convertMarkdownToHtml($this->body);
        }
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

    public function getFormat()
    {
        return $this->format;
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

        if (!in_array($this->getFormat(), self::VALID_FORMATS)) {
            throw new ValidationException('Invalid format. Must be one of: text, html, markdown.');
        }

    }

    private function convertMarkdownToHtml($body): string
    {
        return ((new Parsedown())->text($body));
    }
}