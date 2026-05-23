<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table         = 'employees';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'employee_code', 'first_name', 'last_name', 'middle_name',
        'gender', 'birthdate', 'address', 'phone', 'email',
        'department_id', 'position', 'employment_type',
        'date_hired', 'status', 'basic_salary', 'profile_photo'
    ];
    protected $useTimestamps = false;

    public function getEmployeesWithDepartment()
    {
        return $this->select('employees.*, departments.name as department_name')
                    ->join('departments', 'departments.id = employees.department_id', 'left')
                    ->findAll();
    }

    public function getEmployeeWithDepartment(int $id)
    {
        return $this->select('employees.*, departments.name as department_name')
                    ->join('departments', 'departments.id = employees.department_id', 'left')
                    ->find($id);
    }

    public function getActiveEmployees()
    {
        return $this->where('status', 'Active')->findAll();
    }

    public function countByStatus(string $status)
    {
        return $this->where('status', $status)->countAllResults();
    }
}