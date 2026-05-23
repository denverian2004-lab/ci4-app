<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\EmployeeModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->userModel     = new UserModel();
        $this->employeeModel = new EmployeeModel();
    }

    public function index()
    {
        $users = $this->userModel
            ->select('users.*, employees.first_name, employees.last_name, employees.employee_code')
            ->join('employees', 'employees.id = users.employee_id', 'left')
            ->findAll();

        return view('admin/users/index', [
            'title' => 'User Accounts',
            'users' => $users,
        ]);
    }

    public function create()
    {
        return view('admin/users/create', [
            'title'     => 'Add User Account',
            'employees' => $this->employeeModel->getActiveEmployees(),
        ]);
    }

    public function store()
    {
        $rules = [
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[admin,manager,employee]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->with('error', implode('<br>', $this->validator->getErrors()))
                             ->withInput();
        }

        $this->userModel->insert([
            'employee_id' => $this->request->getPost('employee_id') ?: null,
            'username'    => $this->request->getPost('username'),
            'password'    => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'        => $this->request->getPost('role'),
            'is_active'   => 1,
        ]);

        return redirect()->to('/admin/users')
                         ->with('success', 'User account created successfully!');
    }

    public function edit(int $id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        return view('admin/users/edit', [
            'title'     => 'Edit User Account',
            'user'      => $user,
            'employees' => $this->employeeModel->getActiveEmployees(),
        ]);
    }

    public function update(int $id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        $rules = [
            'username' => "required|min_length[3]|is_unique[users.username,id,{$id}]",
            'role'     => 'required|in_list[admin,manager,employee]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->with('error', implode('<br>', $this->validator->getErrors()))
                             ->withInput();
        }

        $data = [
            'employee_id' => $this->request->getPost('employee_id') ?: null,
            'username'    => $this->request->getPost('username'),
            'role'        => $this->request->getPost('role'),
            'is_active'   => $this->request->getPost('is_active') ?? 1,
        ];

        // Only update password if provided
        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                return redirect()->back()
                                 ->with('error', 'Password must be at least 6 characters.')
                                 ->withInput();
            }
            $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);

        return redirect()->to('/admin/users')
                         ->with('success', 'User account updated successfully!');
    }

    public function delete(int $id)
    {
        // Prevent deleting yourself
        if ($id == session()->get('user_id')) {
            return redirect()->to('/admin/users')
                             ->with('error', 'You cannot delete your own account.');
        }

        $this->userModel->delete($id);
        return redirect()->to('/admin/users')
                         ->with('success', 'User account deleted.');
    }
}