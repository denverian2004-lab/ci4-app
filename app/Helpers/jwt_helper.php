<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

if (!function_exists('generate_token')) {
    function generate_token(array $payload): string
    {
        $key = getenv('JWT_SECRET') ?: 'ems_portal_secret_key_2026_employee_management_system';
        $expiration = time() + (60 * 60 * 24); // 24 hours

        $payload = array_merge($payload, [
            'iat' => time(),
            'exp' => $expiration,
        ]);

        return JWT::encode($payload, $key, 'HS256');
    }
}

if (!function_exists('decode_token')) {
    function decode_token(string $token): ?object
    {
        try {
            $key = getenv('JWT_SECRET') ?: 'ems_portal_secret_key_2026_employee_management_system';
            return JWT::decode($token, new Key($key, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }
}

if (!function_exists('get_token_from_header')) {
    function get_token_from_header(): ?string
    {
        $header = apache_request_headers()['Authorization'] ?? '';
        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }
}