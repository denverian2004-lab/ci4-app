<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\AttendanceModel;
use App\Models\LeaveModel;
use App\Models\PayrollModel;
use App\Models\DepartmentModel;
use App\Models\AttendanceThresholdModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $employeeModel           = new EmployeeModel();
        $attendanceModel         = new AttendanceModel();
        $leaveModel              = new LeaveModel();
        $departmentModel         = new DepartmentModel();
        $attendanceThresholdModel = new AttendanceThresholdModel();

        // Stats
        $totalEmployees  = $employeeModel->countAll();
        $activeEmployees = $employeeModel->countByStatus('Active');
        $presentToday    = $attendanceModel->countTodayPresent();
        $pendingLeaves   = $leaveModel->countPending();

        // Recent employees
        $recentEmployees = $employeeModel
            ->select('employees.*, departments.name as department_name')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->orderBy('employees.id', 'DESC')
            ->limit(5)
            ->findAll();

        // Recent leave requests
        $recentLeaves = $leaveModel
            ->select('leave_requests.*, employees.first_name, employees.last_name, leave_types.name as leave_type_name')
            ->join('employees',   'employees.id = leave_requests.employee_id')
            ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
            ->orderBy('leave_requests.created_at', 'DESC')
            ->limit(5)
            ->findAll();

        // Department breakdown
        $departments = $departmentModel
            ->select('departments.name, COUNT(employees.id) as emp_count')
            ->join('employees', 'employees.department_id = departments.id', 'left')
            ->groupBy('departments.id')
            ->findAll();

        // Attendance threshold violators this month
        $monthStart      = date('Y-m-01');
        $monthEnd        = date('Y-m-t');
        $absentViolators = $attendanceThresholdModel->getViolators('absent', $monthStart, $monthEnd);
        $lateViolators   = $attendanceThresholdModel->getViolators('late',   $monthStart, $monthEnd);
        $absentThreshold = $attendanceThresholdModel->getThreshold('absent');
        $lateThreshold   = $attendanceThresholdModel->getThreshold('late');

        return view('admin/dashboard', [
            'title'            => 'Dashboard',
            'totalEmployees'   => $totalEmployees,
            'activeEmployees'  => $activeEmployees,
            'presentToday'     => $presentToday,
            'pendingLeaves'    => $pendingLeaves,
            'recentEmployees'  => $recentEmployees,
            'recentLeaves'     => $recentLeaves,
            'departments'      => $departments,
            'absentViolators'  => $absentViolators,
            'lateViolators'    => $lateViolators,
            'absentThreshold'  => $absentThreshold,
            'lateThreshold'    => $lateThreshold,
            'monthStart'       => $monthStart,
            'monthEnd'         => $monthEnd,
        ]);
    }
}