<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait HttpResponseTrait
{
    public function success($message, $data = [], $status = 200)
    {
        return response([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    //messages from failure to be shown in app with 200 - like vlaidation messages
    public function failure($message, $errors = [], $status = 200)
    {
        return response([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    //TODO ADD ANOTHER FAILURE TO SHOW POPUP ALERT ERROR IN APP AS APPOSED TO INLINE LIKE ABOVE IS USED FOR

    public function serverFailure($message = "Server error, please try again.", $errors = [], $status = 500)
    {
        //Log
        log::error(
            $message,
            [
                'errors' => $errors,
                'status' => $status
            ]
        );

        //Response
        return response([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
