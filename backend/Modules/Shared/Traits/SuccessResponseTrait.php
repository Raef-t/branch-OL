<?php

namespace Modules\Shared\Traits;

use Illuminate\Http\JsonResponse;

trait SuccessResponseTrait
{
    /**
     * Return a standardized success response.
     *
     * @param mixed  $data       The response data (can be Resource, Collection, Array, or Object)
     * @param string $message    Success message (default: 'تمت العملية بنجاح')
     * @param int    $statusCode HTTP status code (default: 200)
     * @param array  $meta       Additional metadata (optional)
     * @return JsonResponse
     */
    protected function successResponse($data = null, string $message = 'تمت العملية بنجاح', int $statusCode = 200, array $meta = []): JsonResponse
    {
        $response = [
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $statusCode, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Return a standardized error response.
     *
     * @param string $message    Error message
     * @param int    $code       HTTP status code
     * @param mixed  $errors     Validation errors or additional error data
     * @return JsonResponse
     */
    protected function error(string $message = 'حدث خطأ', int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'status' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code, [], JSON_UNESCAPED_UNICODE);
    }
}
