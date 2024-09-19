<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Trait for sending API responses.
 */
trait ResponseTrait
{
    // TODO: Send a success response.
    public function sendResponse($result, $message)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'payload'    => $result,
        ];


        return response()->json($response, 200);
    }

    // TODO: Send an error response.
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        // TODO: Include additional error messages in the response payload if available
        if(!empty($errorMessages)){
            $response['payload'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    // TODO: Handle a failed validation attempt.
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException( $this->sendError('validation_error', $validator->errors(), 400));
    }
}
