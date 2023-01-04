<?php

use App\Exceptions\ValidationException;
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
        $outgoingEmailDTO = new OutgoingEmailDTO($request->get('recipients'), $request->get('subject'), $request->get('body'));
    } catch (ValidationException $e) {
        return response(['error' => $e->getMessage()], ResponseAlias::HTTP_BAD_REQUEST);
    }

    //TODO: send the mail

    return response('OK');
});
