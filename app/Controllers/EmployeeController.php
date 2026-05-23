<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\DepartmentModel;

class EmployeeController extends BaseController
{
    protected $employeeModel;
    protected $departmentModel;

    public function __construct()
    {
        $this->employeeModel   = new EmployeeModel();
        $this->departmentModel = new DepartmentModel();
    }

    public function index()
    {
        $employees = $this->employeeModel->getEmployeesWithDepartment();
        return view('admin/employees/index', [
            'title'     => 'Employees',
            'employees' => $employees,
        ]);
    }

    public function create()
    {
        return view('admin/employees/create', [
            'title'       => 'Add Employee',
            'departments' => $this->departmentModel->findAll(),
        ]);
    }

    public function store()
    {
        $rules = [
            'first_name'      => 'required|min_length[2]',
            'last_name'       => 'required|min_length[2]',
            'email'           => 'required|valid_email|is_unique[employees.email]',
            'gender'          => 'required',
            'birthdate'       => 'required',
            'date_hired'      => 'required',
            'employment_type' => 'required',
            'basic_salary'    => 'required|decimal',
            'profile_photo'   => 'permit_empty|uploaded[profile_photo]|max_size[profile_photo,2048]|is_image[profile_photo]|mime_in[profile_photo,image/jpg,image/jpeg,image/png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->with('error', implode('<br>', $this->validator->getErrors()))
                             ->withInput();
        }

        // Handle photo upload
        $photoName = null;
        $photo     = $this->request->getFile('profile_photo');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $photoName = $photo->getRandomName();
            $photo->move(ROOTPATH . 'public/uploads/profiles', $photoName);
        }

        $count = $this->employeeModel->countAll() + 1;
        $code  = 'EMP-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $this->employeeModel->insert([
            'employee_code'   => $code,
            'first_name'      => $this->request->getPost('first_name'),
            'last_name'       => $this->request->getPost('last_name'),
            'middle_name'     => $this->request->getPost('middle_name'),
            'gender'          => $this->request->getPost('gender'),
            'birthdate'       => $this->request->getPost('birthdate'),
            'address'         => $this->request->getPost('address'),
            'phone'           => $this->request->getPost('phone'),
            'email'           => $this->request->getPost('email'),
            'department_id'   => $this->request->getPost('department_id') ?: null,
            'position'        => $this->request->getPost('position'),
            'employment_type' => $this->request->getPost('employment_type'),
            'date_hired'      => $this->request->getPost('date_hired'),
            'status'          => $this->request->getPost('status') ?? 'Active',
            'basic_salary'    => $this->request->getPost('basic_salary'),
            'profile_photo'   => $photoName,
        ]);

        return redirect()->to('/admin/employees')
                         ->with('success', 'Employee added successfully!');
    }

    public function view(int $id)
    {
        $employee = $this->employeeModel->getEmployeeWithDepartment($id);
        if (!$employee) {
            return redirect()->to('/admin/employees')->with('error', 'Employee not found.');
        }

        return view('admin/employees/view', [
            'title'    => 'Employee Profile',
            'employee' => $employee,
        ]);
    }

    public function edit(int $id)
    {
        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return redirect()->to('/admin/employees')->with('error', 'Employee not found.');
        }

        return view('admin/employees/edit', [
            'title'       => 'Edit Employee',
            'employee'    => $employee,
            'departments' => $this->departmentModel->findAll(),
        ]);
    }

    public function update(int $id)
    {
        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return redirect()->to('/admin/employees')->with('error', 'Employee not found.');
        }

        $rules = [
            'first_name'    => 'required|min_length[2]',
            'last_name'     => 'required|min_length[2]',
            'email'         => "required|valid_email|is_unique[employees.email,id,{$id}]",
            'gender'        => 'required',
            'birthdate'     => 'required',
            'date_hired'    => 'required',
            'basic_salary'  => 'required|decimal',
            'profile_photo' => 'permit_empty|max_size[profile_photo,2048]|is_image[profile_photo]|mime_in[profile_photo,image/jpg,image/jpeg,image/png]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->with('error', implode('<br>', $this->validator->getErrors()))
                             ->withInput();
        }

        // Handle photo upload
        $photoName = $employee['profile_photo']; // Keep existing photo by default
        $photo     = $this->request->getFile('profile_photo');
        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            // Delete old photo if exists
            if ($employee['profile_photo'] && file_exists(ROOTPATH . 'public/uploads/profiles/' . $employee['profile_photo'])) {
                unlink(ROOTPATH . 'public/uploads/profiles/' . $employee['profile_photo']);
            }
            $photoName = $photo->getRandomName();
            $photo->move(ROOTPATH . 'public/uploads/profiles', $photoName);
        }

        // Handle photo removal
        if ($this->request->getPost('remove_photo') === '1') {
            if ($employee['profile_photo'] && file_exists(ROOTPATH . 'public/uploads/profiles/' . $employee['profile_photo'])) {
                unlink(ROOTPATH . 'public/uploads/profiles/' . $employee['profile_photo']);
            }
            $photoName = null;
        }

        $this->employeeModel->update($id, [
            'first_name'      => $this->request->getPost('first_name'),
            'last_name'       => $this->request->getPost('last_name'),
            'middle_name'     => $this->request->getPost('middle_name'),
            'gender'          => $this->request->getPost('gender'),
            'birthdate'       => $this->request->getPost('birthdate'),
            'address'         => $this->request->getPost('address'),
            'phone'           => $this->request->getPost('phone'),
            'email'           => $this->request->getPost('email'),
            'department_id'   => $this->request->getPost('department_id') ?: null,
            'position'        => $this->request->getPost('position'),
            'employment_type' => $this->request->getPost('employment_type'),
            'date_hired'      => $this->request->getPost('date_hired'),
            'status'          => $this->request->getPost('status'),
            'basic_salary'    => $this->request->getPost('basic_salary'),
            'profile_photo'   => $photoName,
        ]);

        return redirect()->to('/admin/employees')
                         ->with('success', 'Employee updated successfully!');
    }

    public function delete(int $id)
    {
        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return redirect()->to('/admin/employees')->with('error', 'Employee not found.');
        }

        // Delete photo if exists
        if ($employee['profile_photo'] && file_exists(ROOTPATH . 'public/uploads/profiles/' . $employee['profile_photo'])) {
            unlink(ROOTPATH . 'public/uploads/profiles/' . $employee['profile_photo']);
        }

        $this->employeeModel->delete($id);
        return redirect()->to('/admin/employees')
                         ->with('success', 'Employee deleted successfully!');
    }
}