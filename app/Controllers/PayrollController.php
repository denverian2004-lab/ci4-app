<?php

namespace App\Controllers;

use App\Models\PayrollModel;
use App\Models\EmployeeModel;
use App\Models\AttendanceModel;
use App\Models\UserModel;

class PayrollController extends BaseController
{
    protected $payrollModel;
    protected $employeeModel;
    protected $attendanceModel;
    protected $userModel;

    public function __construct()
    {
        $this->payrollModel    = new PayrollModel();
        $this->employeeModel   = new EmployeeModel();
        $this->attendanceModel = new AttendanceModel();
        $this->userModel       = new UserModel();
    }

    public function index()
    {
        $payrolls = $this->payrollModel->getPayrollWithEmployee();

        return view('admin/payroll/index', [
            'title'    => 'Payroll',
            'payrolls' => $payrolls,
        ]);
    }

    public function create()
    {
        return view('admin/payroll/create', [
            'title'     => 'Generate Payroll',
            'employees' => $this->employeeModel->getActiveEmployees(),
        ]);
    }

    public function store()
    {
        $rules = [
            'employee_id'   => 'required|integer',
            'period_start'  => 'required|valid_date',
            'period_end'    => 'required|valid_date',
            'overtime_pay'  => 'permit_empty|decimal',
            'deductions'    => 'permit_empty|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->with('error', implode('<br>', $this->validator->getErrors()))
                             ->withInput();
        }

        $employeeId  = $this->request->getPost('employee_id');
        $employee    = $this->employeeModel->find($employeeId);
        $basicSalary = $employee['basic_salary'];
        $overtimePay = (float)($this->request->getPost('overtime_pay') ?? 0);
        $deductions  = (float)($this->request->getPost('deductions')   ?? 0);
        $netPay      = $basicSalary + $overtimePay - $deductions;

        $this->payrollModel->insert([
            'employee_id'  => $employeeId,
            'period_start' => $this->request->getPost('period_start'),
            'period_end'   => $this->request->getPost('period_end'),
            'basic_salary' => $basicSalary,
            'overtime_pay' => $overtimePay,
            'deductions'   => $deductions,
            'net_pay'      => $netPay,
            'status'       => $this->request->getPost('status') ?? 'Draft',
        ]);

        // Notify employee about payroll generation
        $empUser = $this->userModel->where('employee_id', $employeeId)->first();
        if ($empUser) {
            notify_user(
                $empUser['id'],
                'Payroll Generated 💰',
                'Your payroll has been generated. Net Pay: ₱' . number_format($netPay, 2) . ' for the period ' . date('M d, Y', strtotime($this->request->getPost('period_start'))) . ' — ' . date('M d, Y', strtotime($this->request->getPost('period_end'))) . '.',
                '/employee/my-payroll'
            );
        }

        return redirect()->to('/admin/payroll')
                         ->with('success', 'Payroll record created successfully!');
    }

    public function view(int $id)
    {
        $payroll = $this->payrollModel
            ->select('payroll.*, employees.first_name, employees.last_name, employees.employee_code, employees.position, employees.employment_type, departments.name as department_name')
            ->join('employees',   'employees.id = payroll.employee_id')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->find($id);

        if (!$payroll) {
            return redirect()->to('/admin/payroll')->with('error', 'Payroll record not found.');
        }

        return view('admin/payroll/view', [
            'title'   => 'Payroll Details',
            'payroll' => $payroll,
        ]);
    }

    public function delete(int $id)
    {
        $this->payrollModel->delete($id);
        return redirect()->to('/admin/payroll')
                         ->with('success', 'Payroll record deleted.');
    }
}