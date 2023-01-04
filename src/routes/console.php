<?php

use App\Exceptions\EmailException;
use App\Exceptions\ValidationException;
use App\Services\AsyncEmail\AsyncEmailService;
use App\Services\AsyncEmail\DataTransferObjects\OutgoingEmailDTO;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('test', function () {
    $this->comment('Test command executed successfully!');
});

Artisan::command('
        mail:send 
        {--recipient=* : One or more recipients} 
        {--subject= : Email subject}
        {--body= : Email body}
        {--format= : Format of the "body"}
        ', function () {

    try {
        $outgoingEmailDTO = new OutgoingEmailDTO($this->option('recipient'), $this->option('subject'), $this->option('body'), $this->option('format'));

        /**
         * @var AsyncEmailService $asyncEmailService
         */
        $asyncEmailService = app(AsyncEmailService::class);
        $emailIds          = $asyncEmailService->storeAndQueueEmails($outgoingEmailDTO);

        return $this->info('Successfully queued for sending emails with ID: ' . implode(',', $emailIds));

    } catch (ValidationException $e) {
        return $this->error('Invalid data provided: ' . $e->getMessage());
    } catch (EmailException $e) {
        return $this->error('Error: ' . $e->getMessage());
    }

});