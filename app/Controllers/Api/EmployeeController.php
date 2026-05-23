<?php

namespace App\Controllers\Api;

use App\Models\EmployeeModel;
use App\Models\DepartmentModel;

class EmployeeController extends BaseApiController
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
        $status = $this->request->getGet('status');
        $deptId = $this->request->getGet('department_id');

        $query = $this->employeeModel
            ->select('employees.*, departments.name as department_name')
            ->join('departments', 'departments.id = employees.department_id', 'left');

        if ($status) $query->where('employees.status', $status);
        if ($deptId) $query->where('employees.department_id', $deptId);

        $employees = $query->findAll();

        return $this->success($employees);
    }

    public function show(int $id)
    {
        $employee = $this->employeeModel->getEmployeeWithDepartment($id);
        if (!$employee) {
            return $this->error('Employee not found.', 404);
        }

        return $this->success($employee);
    }

    public function store()
    {
        $input = $this->request->getJSON(true);

        $rules = [
            'first_name'      => 'required|min_length[2]',
            'last_name'       => 'required|min_length[2]',
            'email'           => 'required|valid_email|is_unique[employees.email]',
            'gender'          => 'required',
            'birthdate'       => 'required',
            'date_hired'      => 'required',
            'employment_type' => 'required',
            'basic_salary'    => 'required|decimal',
        ];

        if (!$this->validate($rules, $input)) {
            return $this->error('Validation failed', 422, $this->validator->getErrors());
        }

        $count = $this->employeeModel->countAll() + 1;
        $code  = 'EMP-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $id = $this->employeeModel->insert(array_merge($input, [
            'employee_code' => $code,
        ]));

        $employee = $this->employeeModel->getEmployeeWithDepartment($id);

        return $this->success($employee, 'Employee created successfully.', 201);
    }

    public function update(int $id)
    {
        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return $this->error('Employee not found.', 404);
        }

        $input = $this->request->getJSON(true);

        $rules = [
            'first_name'   => 'permit_empty|min_length[2]',
            'last_name'    => 'permit_empty|min_length[2]',
            'email'        => "permit_empty|valid_email|is_unique[employees.email,id,{$id}]",
            'basic_salary' => 'permit_empty|decimal',
        ];

        if (!$this->validate($rules, $input)) {
            return $this->error('Validation failed', 422, $this->validator->getErrors());
        }

        $this->employeeModel->update($id, $input);
        $updated = $this->employeeModel->getEmployeeWithDepartment($id);

        return $this->success($updated, 'Employee updated successfully.');
    }

    public function delete(int $id)
    {
        $employee = $this->employeeModel->find($id);
        if (!$employee) {
            return $this->error('Employee not found.', 404);
        }

        $this->employeeModel->delete($id);

        return $this->success([], 'Employee deleted successfully.');
    }
}