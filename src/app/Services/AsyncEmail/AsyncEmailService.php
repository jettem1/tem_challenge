<?php

namespace App\Services\AsyncEmail;

use App\Exceptions\EmailException;
use App\Models\Email;
use App\Services\AsyncEmail\DataTransferObjects\OutgoingEmailDTO;
use Illuminate\Support\Facades\DB;
use PDOException;

class AsyncEmailService
{
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

}