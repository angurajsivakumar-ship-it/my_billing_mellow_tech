<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse as JsonResponseAlias;

trait ResponseTraits
{
    /**
     * @param $data
     * @param string $message
     * @param null $pagination
     * @param bool $status
     * @return JsonResponseAlias
     * @param $url
     * @param $httpStatus
     */
    public function successResponse($data, $message = "Operation Successful", $url = '', $pagination = null, $status = true, $httpStatus = 200): JsonResponseAlias
    {
        return response()->json([
            'success'       => true,
            'status'        => $status,
            'message'       => $message,
            'data'          => $data,
            'pagination'    => $pagination,
            'errors'        => null,
            'url'           => $url,
            'meta'          =>  [
                'timestamp' => now()->toIso8601String(),
                'request_id' => request()->header('X-Request-ID') ?? uniqid(),
            ],
        ], $httpStatus);
    }

    /**
     * @param $errors
     * @param string $message
     * @param bool $status
     * @return JsonResponseAlias
     * @param $statusCode
     */
    public function errorResponse($errors, $message = "Operation Failed", $statusCode = 500, $status = false): JsonResponseAlias
    {
        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
            'meta'          =>  [
                'timestamp' => now()->toIso8601String(),
                'request_id' => request()->header('X-Request-ID') ?? uniqid(),
            ],
        ], $statusCode);
    }

    public function handleAPIValidationResponses(Validator $validator){
        throw new HttpResponseException(
            $this->errorResponse($validator->errors()->all(), config('common.errors.validation'))
        );
    }

    public function handleValidationErrorResponse($validatorErrors, $message = "Validation Failed", $status = 422): JsonResponseAlias
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'status' => false,
                'message' => $message,
                'data' => null,
                'errors' => $validatorErrors,
                'meta' => [
                    'timestamp' => now()->toIso8601String(),
                    'request_id' => request()->header('X-Request-ID') ?? uniqid(),
                ],
            ], $status)
        );
    }
}
