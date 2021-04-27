<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;

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
        'current_password',
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
        if (request()->route() !== null && 0 === strpos(request()->route()->getPrefix(), 'api')) {
            $this->reportable(function (AuthenticationException $e) {
                return respond(config("messages.API_UNAUTH"),[],401);
            });
        }
    }

    /**
     * Set response for logout user
     * @param  mix $request
     * @param AuthenticationException $exception
     * @return json response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return respond(config("messages.API_UNAUTH"),[],401);
        }
        return redirect()->guest(route('login'));
    }
}
