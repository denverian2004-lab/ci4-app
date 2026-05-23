<?php

namespace App\Controllers\Api;

use App\Models\PayrollModel;
use App\Models\EmployeeModel;

class PayrollController extends BaseApiController
{
    protected $payrollModel;
    protected $employeeModel;

    public function __construct()
    {
        $this->payrollModel  = new PayrollModel();
        $this->employeeModel = new EmployeeModel();
    }

    public function index()
    {
        $empId = $this->request->getGet('employee_id');
        $from  = $this->request->getGet('from');
        $to    = $this->request->getGet('to');

        $query = $this->payrollModel
            ->select('payroll.*, employees.first_name, employees.last_name, employees.employee_code, employees.position')
            ->join('employees', 'employees.id = payroll.employee_id')
            ->orderBy('payroll.period_start', 'DESC');

        if ($empId) $query->where('payroll.employee_id', $empId);
        if ($from)  $query->where('payroll.period_start >=', $from);
        if ($to)    $query->where('payroll.period_end <=', $to);

        $payrolls = $query->findAll();

        return $this->success($payrolls);
    }

    public function show(int $id)
    {
        $payroll = $this->payrollModel
            ->select('payroll.*, employees.first_name, employees.last_name, employees.employee_code, employees.position, departments.name as department_name')
            ->join('employees',   'employees.id = payroll.employee_id')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->find($id);

        if (!$payroll) {
            return $this->error('Payroll record not found.', 404);
        }

        return $this->success($payroll);
    }

    public function byEmployee(int $employeeId)
    {
        $employee = $this->employeeModel->find($employeeId);
        if (!$employee) {
            return $this->error('Employee not found.', 404);
        }

        $payrolls = $this->payrollModel
            ->where('employee_id', $employeeId)
            ->orderBy('period_start', 'DESC')
            ->findAll();

        return $this->success([
            'employee' => $employee,
            'payrolls' => $payrolls,
        ]);
    }

    public function store()
    {
        $input = $this->request->getJSON(true);

        $rules = [
            'employee_id'  => 'required|integer',
            'period_start' => 'required|valid_date',
            'period_end'   => 'required|valid_date',
        ];

        if (!$this->validate($rules, $input)) {
            return $this->error('Validation failed', 422, $this->validator->getErrors());
        }

        $employee    = $this->employeeModel->find($input['employee_id']);
        $basicSalary = $employee['basic_salary'];
        $overtimePay = (float)($input['overtime_pay'] ?? 0);
        $deductions  = (float)($input['deductions']   ?? 0);
        $netPay      = $basicSalary + $overtimePay - $deductions;

        $id = $this->payrollModel->insert([
            'employee_id'  => $input['employee_id'],
            'period_start' => $input['period_start'],
            'period_end'   => $input['period_end'],
            'basic_salary' => $basicSalary,
            'overtime_pay' => $overtimePay,
            'deductions'   => $deductions,
            'net_pay'      => $netPay,
            'status'       => $input['status'] ?? 'Draft',
        ]);

        $payroll = $this->payrollModel->find($id);

        return $this->success($payroll, 'Payroll generated successfully.', 201);
    }
}