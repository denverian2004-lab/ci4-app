<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;

class BaseApiController extends Controller
{
    protected function success($data = [], string $message = 'Success', int $code = 200)
    {
        return $this->response->setJSON([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ])->setStatusCode($code);
    }

    protected function error(string $message = 'Error', int $code = 400, $errors = null)
    {
        $response = [
            'status'  => 'error',
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return $this->response->setJSON($response)->setStatusCode($code);
    }

    protected function getAuthUser()
    {
        $token = get_token_from_header();
        if (!$token) return null;
        return decode_token($token);
    }
}