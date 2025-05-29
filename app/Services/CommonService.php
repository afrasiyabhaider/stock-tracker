<?php

namespace App\Services;

class CommonService
{
    /**
     * Return a standardized API response.
     *
     * @param string $message
     * @param array $data
     * @param int $code
     * @return array
     */
    public function returnResponse($message = 'Success', $data = [], $code = 200)
    {
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];
    }
}
