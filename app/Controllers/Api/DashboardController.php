<?php

namespace App\Controllers\Api;

use App\Models\EmployeeModel;
use App\Models\AttendanceModel;
use App\Models\LeaveModel;
use App\Models\PayrollModel;
use App\Models\DepartmentModel;

class DashboardController extends BaseApiController
{
    public function stats()
    {
        $employeeModel   = new EmployeeModel();
        $attendanceModel = new AttendanceModel();
        $leaveModel      = new LeaveModel();
        $departmentModel = new DepartmentModel();

        $monthStart = date('Y-m-01');
        $monthEnd   = date('Y-m-t');

        $departments = $departmentModel
            ->select('departments.name, COUNT(employees.id) as emp_count')
            ->join('employees', 'employees.department_id = departments.id', 'left')
            ->groupBy('departments.id')
            ->findAll();

        return $this->success([
            'total_employees'   => $employeeModel->countAll(),
            'active_employees'  => $employeeModel->countByStatus('Active'),
            'present_today'     => $attendanceModel->countTodayPresent(),
            'pending_leaves'    => $leaveModel->countPending(),
            'departments'       => $departments,
            'month'             => date('F Y'),
        ]);
    }

    public function attendanceReport()
    {
        $attendanceModel = new AttendanceModel();
        $from = $this->request->getGet('from') ?? date('Y-m-01');
        $to   = $this->request->getGet('to')   ?? date('Y-m-t');

        $records = $attendanceModel
            ->select('attendance.*, employees.first_name, employees.last_name, employees.employee_code')
            ->join('employees', 'employees.id = attendance.employee_id')
            ->where('attendance.date >=', $from)
            ->where('attendance.date <=', $to)
            ->orderBy('attendance.date', 'DESC')
            ->findAll();

        $summary = [
            'Present'  => 0,
            'Absent'   => 0,
            'Late'     => 0,
            'Half-day' => 0,
        ];

        foreach ($records as $r) {
            if (isset($summary[$r['status']])) {
                $summary[$r['status']]++;
            }
        }

        return $this->success([
            'summary' => $summary,
            'records' => $records,
            'from'    => $from,
            'to'      => $to,
        ]);
    }

    public function payrollReport()
    {
        $payrollModel = new PayrollModel();
        $from = $this->request->getGet('from') ?? date('Y-m-01');
        $to   = $this->request->getGet('to')   ?? date('Y-m-t');

        $records = $payrollModel
            ->select('payroll.*, employees.first_name, employees.last_name, employees.employee_code')
            ->join('employees', 'employees.id = payroll.employee_id')
            ->where('payroll.period_start >=', $from)
            ->where('payroll.period_end <=',   $to)
            ->findAll();

        return $this->success([
            'total_net_pay'   => array_sum(array_column($records, 'net_pay')),
            'total_basic'     => array_sum(array_column($records, 'basic_salary')),
            'total_overtime'  => array_sum(array_column($records, 'overtime_pay')),
            'total_deductions'=> array_sum(array_column($records, 'deductions')),
            'records'         => $records,
            'from'            => $from,
            'to'              => $to,
        ]);
    }

    public function leaveReport()
    {
        $leaveModel = new LeaveModel();
        $from = $this->request->getGet('from') ?? date('Y-m-01');
        $to   = $this->request->getGet('to')   ?? date('Y-m-t');

        $records = $leaveModel
            ->select('leave_requests.*, employees.first_name, employees.last_name, leave_types.name as leave_type_name')
            ->join('employees',   'employees.id = leave_requests.employee_id')
            ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
            ->where('leave_requests.start_date >=', $from)
            ->where('leave_requests.end_date <=',   $to)
            ->findAll();

        $summary = ['Pending' => 0, 'Approved' => 0, 'Rejected' => 0];
        foreach ($records as $r) {
            if (isset($summary[$r['status']])) $summary[$r['status']]++;
        }

        return $this->success([
            'summary' => $summary,
            'records' => $records,
            'from'    => $from,
            'to'      => $to,
        ]);
    }
}