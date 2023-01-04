<?php

use App\Exceptions\ValidationException;
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
        ', function () {

    try {
        $outgoingEmailDTO = new OutgoingEmailDTO($this->option('recipient'), $this->option('subject'), $this->option('body'));
    } catch (ValidationException $e) {
        return $this->error('Error: ' . $e->getMessage());
    }

    //TODO: send the mail

    $this->info('OK');
});