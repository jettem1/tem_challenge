<?php

use App\Exceptions\EmailException;
use App\Exceptions\ValidationException;
use App\Services\AsyncEmail\AsyncEmailService;
use App\Services\AsyncEmail\DataTransferObjects\OutgoingEmailDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/test', function (Request $request) {
    return 'Test API route';
});

Route::post('/send-mail', function (Request $request) {

    try {
        $outgoingEmailDTO = new OutgoingEmailDTO($request->get('recipients'), $request->get('subject'), $request->get('body'),  $request->get('format'));

        /**
         * @var AsyncEmailService $asyncEmailService
         */
        $asyncEmailService = app(AsyncEmailService::class);
        $emailIds          = $asyncEmailService->storeAndQueueEmails($outgoingEmailDTO);

        return response(['emailIds' => $emailIds], ResponseAlias::HTTP_CREATED);
    } catch (ValidationException $e) {
        return response(['error' => $e->getMessage()], ResponseAlias::HTTP_BAD_REQUEST);
    } catch (EmailException $e) {
        return response(['error' => $e->getMessage()], ResponseAlias::HTTP_SERVICE_UNAVAILABLE);
    }
});
