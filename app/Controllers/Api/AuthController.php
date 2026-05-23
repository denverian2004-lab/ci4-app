<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use App\Models\EmployeeModel;

class AuthController extends BaseApiController
{
    public function login()
    {
        $rules = [
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[6]',
        ];

        $input = $this->request->getJSON(true);

        if (!$this->validate($rules, $input)) {
            return $this->error('Validation failed', 422, $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $user      = $userModel->findByUsername($input['username']);

        if (!$user || !password_verify($input['password'], $user['password'])) {
            return $this->error('Invalid username or password.', 401);
        }

        if (!$user['is_active']) {
            return $this->error('Your account has been deactivated.', 403);
        }

        // Get employee info
        $employeeData = [];
        if ($user['employee_id']) {
            $employeeModel = new EmployeeModel();
            $emp           = $employeeModel->getEmployeeWithDepartment($user['employee_id']);
            if ($emp) {
                $employeeData = [
                    'employee_id'     => $emp['id'],
                    'employee_code'   => $emp['employee_code'],
                    'employee_name'   => $emp['first_name'] . ' ' . $emp['last_name'],
                    'department'      => $emp['department_name'] ?? null,
                    'position'        => $emp['position'] ?? null,
                    'profile_photo'   => $emp['profile_photo'] ?? null,
                ];
            }
        }

        // Generate token
        $token = generate_token([
            'user_id'     => $user['id'],
            'username'    => $user['username'],
            'role'        => $user['role'],
            'employee_id' => $user['employee_id'],
        ]);

        return $this->success([
            'token'    => $token,
            'user'     => [
                'id'       => $user['id'],
                'username' => $user['username'],
                'role'     => $user['role'],
            ],
            'employee' => $employeeData,
        ], 'Login successful');
    }

    public function me()
    {
        $decoded = $this->getAuthUser();
        if (!$decoded) {
            return $this->error('Unauthorized', 401);
        }

        $userModel = new UserModel();
        $user      = $userModel->find($decoded->user_id);

        $employeeData = [];
        if ($user['employee_id']) {
            $employeeModel = new EmployeeModel();
            $emp           = $employeeModel->getEmployeeWithDepartment($user['employee_id']);
            if ($emp) {
                $employeeData = $emp;
            }
        }

        return $this->success([
            'user'     => [
                'id'       => $user['id'],
                'username' => $user['username'],
                'role'     => $user['role'],
            ],
            'employee' => $employeeData,
        ]);
    }

    public function logout()
    {
        // JWT is stateless — client just discards the token
        return $this->success([], 'Logged out successfully');
    }
}