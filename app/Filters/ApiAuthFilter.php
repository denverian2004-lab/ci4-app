<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class ApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $token = get_token_from_header();

        if (!$token) {
            return response()->setJSON([
                'status'  => 'error',
                'message' => 'No token provided. Please login first.',
            ])->setStatusCode(401);
        }

        $decoded = decode_token($token);

        if (!$decoded) {
            return response()->setJSON([
                'status'  => 'error',
                'message' => 'Invalid or expired token. Please login again.',
            ])->setStatusCode(401);
        }

        // Check role if arguments provided
        if ($arguments) {
            if (!in_array($decoded->role, $arguments)) {
                return response()->setJSON([
                    'status'  => 'error',
                    'message' => 'Access denied. Insufficient permissions.',
                ])->setStatusCode(403);
            }
        }

        // Store decoded token in request for controllers to use
        $request->decoded_token = $decoded;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Add CORS headers
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    }
}