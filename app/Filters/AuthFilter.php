<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Not logged in at all
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to continue.');
        }

        // Role check if arguments provided
        if ($arguments) {
            $userRole = session()->get('role');
            if (!in_array($userRole, $arguments)) {
                return redirect()->to('/login')->with('error', 'Access denied.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing needed here
    }
}