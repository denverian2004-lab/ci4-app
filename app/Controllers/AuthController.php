<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EmployeeModel;

class AuthController extends BaseController
{
    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('logged_in')) {
            $role = session()->get('role');
            if ($role === 'admin')   return redirect()->to('/admin/dashboard');
            if ($role === 'manager') return redirect()->to('/manager/dashboard');
            return redirect()->to('/employee/dashboard');
        }

        return view('auth/login');
    }

    public function loginProcess()
    {
        $rules = [
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/login')
                             ->with('error', 'Please fill in all fields correctly.')
                             ->withInput();
        }

        $userModel = new UserModel();
        $username  = $this->request->getPost('username');
        $password  = $this->request->getPost('password');
        $user      = $userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->to('/login')
                             ->with('error', 'Invalid username or password.')
                             ->withInput();
        }

        if (!$user['is_active']) {
            return redirect()->to('/login')
                             ->with('error', 'Your account has been deactivated. Please contact HR.')
                             ->withInput();
        }

        // Get employee info if linked
        $employeeData = [];
        if ($user['employee_id']) {
            $employeeModel = new EmployeeModel();
            $emp = $employeeModel
                ->select('employees.*, departments.name as department_name')
                ->join('departments', 'departments.id = employees.department_id', 'left')
                ->find($user['employee_id']);

            if ($emp) {
                $employeeData = [
                    'employee_id'   => $emp['id'],
                    'employee_name' => $emp['first_name'] . ' ' . $emp['last_name'],
                    'employee_code' => $emp['employee_code'],
                    'profile_photo' => $emp['profile_photo'],
                ];
            }
        }

        session()->set(array_merge([
            'logged_in' => true,
            'user_id'   => $user['id'],
            'username'  => $user['username'],
            'role'      => $user['role'],
        ], $employeeData));

        // Redirect by role
        if ($user['role'] === 'admin') {
            return redirect()->to('/admin/dashboard');
        }

        if ($user['role'] === 'manager') {
            return redirect()->to('/manager/dashboard');
        }

        return redirect()->to('/employee/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }
}