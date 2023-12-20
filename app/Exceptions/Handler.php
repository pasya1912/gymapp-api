<?php

namespace App\Exceptions;

use App\Http\Resources\GeneralResource;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;


class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    protected function unauthenticated($request, AuthenticationException $ex){

        if( $request->is('api/*') ) { // for routes starting with `/api`
            return response()->json(GeneralResource::formatResponse([
                'status' => 401,
                'message' => '401 Unauthenticated',
            ]), 401);
        }
    
        return redirect('/login'); // for normal routes 
    }
}
