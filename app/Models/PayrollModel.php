<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollModel extends Model
{
    protected $table         = 'payroll';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'employee_id', 'period_start', 'period_end',
        'basic_salary', 'overtime_pay', 'deductions', 'net_pay', 'status'
    ];
    protected $useTimestamps = false;

    public function getPayrollWithEmployee()
    {
        return $this->select('payroll.*, employees.first_name, employees.last_name, employees.employee_code, employees.position')
                    ->join('employees', 'employees.id = payroll.employee_id')
                    ->orderBy('payroll.created_at', 'DESC')
                    ->findAll();
    }

    public function getByEmployee(int $employeeId)
    {
        return $this->where('employee_id', $employeeId)
                    ->orderBy('period_start', 'DESC')
                    ->findAll();
    }
}