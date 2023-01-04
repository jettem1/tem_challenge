<?php

namespace App\Services\AsyncEmail;

use App\Exceptions\EmailException;
use App\Models\Email;
use App\Services\AsyncEmail\DataTransferObjects\OutgoingEmailDTO;
use App\Services\AsyncEmail\Transport\IEmailTransport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDOException;

class AsyncEmailService
{
    /**
     * @var array<IEmailTransport> List of email transports, ordered by preference (priority)
     */
    private array $emailTransportList = [];

    public function addEmailTransport(IEmailTransport $emailTransport)
    {
        $this->emailTransportList[] = $emailTransport;
    }

    /**
     * Stores the Email records in the database and
     * adds them to the Queueing system
     *
     * @param OutgoingEmailDTO $outgoingEmailDTO
     * @return array List of successfully saved Email IDs
     * @throws EmailException
     */
    public function storeAndQueueEmails(OutgoingEmailDTO $outgoingEmailDTO): array
    {
        $emailIds = [];

        DB::beginTransaction();

        foreach ($outgoingEmailDTO->getRecipients() as $recipient) {

            try {
                $email = Email::create([
                    'recipient' => $recipient,
                    'subject' => $outgoingEmailDTO->getSubject(),
                    'body' => $outgoingEmailDTO->getBody()
                ]);

                $emailIds[] = $email->id;

                //TODO: add $email->id to Queue to be sent later

            } catch (PDOException $e) {
                DB::rollBack();
                throw new EmailException('Failed to save email record.');
            }
        }

        DB::commit();

        return $emailIds;
    }

    /**
     * Sends the Email and updates the record status
     *
     * @param Email $email
     * @return bool
     * @throws EmailException
     */
    public function sendEmail(Email $email): bool
    {
        if ($email->status != Email::STATUS_PENDING) {
            throw new EmailException('Email already processed, aborting.');
        }

        if (!count($this->emailTransportList)) {
            throw new EmailException('No email transport options provided, aborting.');
        }

        $email->status = Email::STATUS_IN_PROGRESS;
        $email->save();

        /*
         * Try to use all available transports until
         * email is successfully sent
         */
        foreach ($this->emailTransportList as $emailTransport) {
            $isSent = $emailTransport->send($email);
            if ($isSent) {
                $email->status = Email::STATUS_SENT;
                $email->save();

                Log::info('[Email: ' . $email->id . '] Sent OK.');

                return true;
            }
        }

        /*
         * Email could not be sent using any transport,
         * mark it as failed
         */
        $email->status = Email::STATUS_FAILED;
        $email->save();

        Log::warning('[Email: ' . $email->id . '] Failed to send.');

        return false;
    }

}