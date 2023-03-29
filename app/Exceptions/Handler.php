<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Mail;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use App\Mail\ExceptionOccured;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {

        if ($request->expectsJson()) {
             $response = [
                'status'  => -2,
                'message' =>'User authentication failed',
                'data' => null
                /*'data'    => [
                    "test" => "test",
                ]*/
             ];

            return response()->json($response);

        }

        return redirect()->guest('login');

    }

    public function sendExceptionEmail(Exception $exception)
    {
/*        try {
            $e = FlattenException::create($exception);
            $handler = new HtmlErrorRenderer(true); // boolean, true raises debug flag...
            $css = $handler->getStylesheet();
            $content = ExceptionHandler::convertExceptionToResponse($exception);
            $emails  = Settings::where('key','exception_email_notification')->first();
            $emails  = isset($emails->value) && !empty($emails->value) ? explode(',', $emails->value) : [env('APP_EMAIL')];

            \Mail::send('exception', compact('css','content'), function ($message) use($emails) {
                $message
                    ->to($emails)
                    ->subject('E-Commarce App : Exception: ' . \Request::fullUrl())
                ;
            });
        } catch (Exception $ex) {
            dd($ex);
        }*/
    }
}
