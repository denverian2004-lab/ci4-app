<?php

namespace App\Controllers;

use App\Models\DepartmentModel;
use App\Models\EmployeeModel;

class DepartmentController extends BaseController
{
    protected $departmentModel;

    public function __construct()
    {
        $this->departmentModel = new DepartmentModel();
    }

    public function index()
    {
        $departments = $this->departmentModel->findAll();

        // Count employees per department
        $employeeModel = new EmployeeModel();
        foreach ($departments as &$dept) {
            $dept['emp_count'] = $employeeModel->where('department_id', $dept['id'])->countAllResults();
        }

        return view('admin/departments/index', [
            'title'       => 'Departments',
            'departments' => $departments,
        ]);
    }

    public function create()
    {
        return view('admin/departments/create', [
            'title' => 'Add Department',
        ]);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|is_unique[departments.name]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->with('error', implode('<br>', $this->validator->getErrors()))
                             ->withInput();
        }

        $this->departmentModel->insert([
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('/admin/departments')
                         ->with('success', 'Department added successfully!');
    }

    public function edit(int $id)
    {
        $department = $this->departmentModel->find($id);
        if (!$department) {
            return redirect()->to('/admin/departments')->with('error', 'Department not found.');
        }

        return view('admin/departments/edit', [
            'title'      => 'Edit Department',
            'department' => $department,
        ]);
    }

    public function update(int $id)
    {
        $department = $this->departmentModel->find($id);
        if (!$department) {
            return redirect()->to('/admin/departments')->with('error', 'Department not found.');
        }

        $rules = [
            'name' => "required|min_length[2]|is_unique[departments.name,id,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->with('error', implode('<br>', $this->validator->getErrors()))
                             ->withInput();
        }

        $this->departmentModel->update($id, [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
        ]);

        return redirect()->to('/admin/departments')
                         ->with('success', 'Department updated successfully!');
    }

    public function delete(int $id)
    {
        $department = $this->departmentModel->find($id);
        if (!$department) {
            return redirect()->to('/admin/departments')->with('error', 'Department not found.');
        }

        $this->departmentModel->delete($id);
        return redirect()->to('/admin/departments')
                         ->with('success', 'Department deleted successfully!');
    }
}