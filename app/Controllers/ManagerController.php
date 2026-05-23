<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\AttendanceModel;
use App\Models\LeaveModel;
use App\Models\LeaveTypeModel;
use App\Models\PayrollModel;

class ManagerController extends BaseController
{
    protected $employeeModel;
    protected $attendanceModel;
    protected $leaveModel;
    protected $payrollModel;

    public function __construct()
    {
        $this->employeeModel   = new EmployeeModel();
        $this->attendanceModel = new AttendanceModel();
        $this->leaveModel      = new LeaveModel();
        $this->payrollModel    = new PayrollModel();
    }

    // Get the manager's own employee record
    private function getManagerEmployee()
    {
        $employeeId = session()->get('employee_id');
        if (!$employeeId) return null;
        return $this->employeeModel->getEmployeeWithDepartment($employeeId);
    }

    // Get all employees in the manager's department
    private function getTeamEmployees(int $departmentId)
    {
        return $this->employeeModel
            ->where('department_id', $departmentId)
            ->where('status', 'Active')
            ->findAll();
    }

    public function index()
    {
        $manager = $this->getManagerEmployee();

        $teamEmployees   = [];
        $pendingLeaves   = [];
        $todayAttendance = [];
        $presentCount    = 0;
        $absentCount     = 0;
        $lateCount       = 0;
        $teamCount       = 0;

        if ($manager && $manager['department_id']) {
            $deptId        = $manager['department_id'];
            $teamEmployees = $this->getTeamEmployees($deptId);
            $teamCount     = count($teamEmployees);
            $teamIds       = array_column($teamEmployees, 'id');

            // Today's attendance for team
            if (!empty($teamIds)) {
                $todayAttendance = $this->attendanceModel
                    ->select('attendance.*, employees.first_name, employees.last_name, employees.employee_code')
                    ->join('employees', 'employees.id = attendance.employee_id')
                    ->where('attendance.date', date('Y-m-d'))
                    ->whereIn('attendance.employee_id', $teamIds)
                    ->findAll();

                $presentCount = count(array_filter($todayAttendance, fn($a) => $a['status'] === 'Present'));
                $absentCount  = count(array_filter($todayAttendance, fn($a) => $a['status'] === 'Absent'));
                $lateCount    = count(array_filter($todayAttendance, fn($a) => $a['status'] === 'Late'));

                // Pending leave requests for team
                $pendingLeaves = $this->leaveModel
                    ->select('leave_requests.*, employees.first_name, employees.last_name, employees.employee_code, leave_types.name as leave_type_name')
                    ->join('employees',   'employees.id = leave_requests.employee_id')
                    ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
                    ->whereIn('leave_requests.employee_id', $teamIds)
                    ->where('leave_requests.status', 'Pending')
                    ->orderBy('leave_requests.created_at', 'DESC')
                    ->findAll();
            }
        }

        // Manager's own payroll
        $myPayroll = [];
        if ($manager) {
            $myPayroll = $this->payrollModel
                ->where('employee_id', $manager['id'])
                ->orderBy('period_start', 'DESC')
                ->limit(3)
                ->findAll();
        }

        return view('manager/dashboard', [
            'title'           => 'Manager Dashboard',
            'manager'         => $manager,
            'teamCount'       => $teamCount,
            'presentCount'    => $presentCount,
            'absentCount'     => $absentCount,
            'lateCount'       => $lateCount,
            'pendingLeaves'   => $pendingLeaves,
            'todayAttendance' => $todayAttendance,
            'myPayroll'       => $myPayroll,
        ]);
    }

    public function teamAttendance()
    {
        $manager = $this->getManagerEmployee();
        if (!$manager || !$manager['department_id']) {
            return redirect()->to('/manager/dashboard')
                             ->with('error', 'No department linked to your account.');
        }

        $date      = $this->request->getGet('date') ?? date('Y-m-d');
        $deptId    = $manager['department_id'];
        $teamIds   = array_column($this->getTeamEmployees($deptId), 'id');

        $records = [];
        if (!empty($teamIds)) {
            $records = $this->attendanceModel
                ->select('attendance.*, employees.first_name, employees.last_name, employees.employee_code')
                ->join('employees', 'employees.id = attendance.employee_id')
                ->where('attendance.date', $date)
                ->whereIn('attendance.employee_id', $teamIds)
                ->orderBy('employees.last_name', 'ASC')
                ->findAll();
        }

        return view('manager/team_attendance', [
            'title'   => 'Team Attendance',
            'manager' => $manager,
            'records' => $records,
            'date'    => $date,
        ]);
    }

    public function teamLeaves()
    {
        $manager = $this->getManagerEmployee();
        if (!$manager || !$manager['department_id']) {
            return redirect()->to('/manager/dashboard')
                             ->with('error', 'No department linked to your account.');
        }

        $status  = $this->request->getGet('status') ?? '';
        $deptId  = $manager['department_id'];
        $teamIds = array_column($this->getTeamEmployees($deptId), 'id');

        $leaves = [];
        if (!empty($teamIds)) {
            $query = $this->leaveModel
                ->select('leave_requests.*, employees.first_name, employees.last_name, employees.employee_code, leave_types.name as leave_type_name')
                ->join('employees',   'employees.id = leave_requests.employee_id')
                ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
                ->whereIn('leave_requests.employee_id', $teamIds)
                ->orderBy('leave_requests.created_at', 'DESC');

            if ($status !== '') {
                $query->where('leave_requests.status', $status);
            }

            $leaves = $query->findAll();
        }

        return view('manager/team_leaves', [
            'title'   => 'Team Leave Requests',
            'manager' => $manager,
            'leaves'  => $leaves,
            'status'  => $status,
        ]);
    }

    public function viewLeave(int $id)
    {
        $manager = $this->getManagerEmployee();
        if (!$manager || !$manager['department_id']) {
            return redirect()->to('/manager/dashboard')
                             ->with('error', 'No department linked to your account.');
        }

        $leave = $this->leaveModel
            ->select('leave_requests.*, employees.first_name, employees.last_name, employees.employee_code, employees.position, employees.email, leave_types.name as leave_type_name')
            ->join('employees',   'employees.id = leave_requests.employee_id')
            ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
            ->find($id);

        if (!$leave) {
            return redirect()->to('/manager/team-leaves')
                             ->with('error', 'Leave request not found.');
        }

        // Make sure this employee belongs to manager's department
        $employee = $this->employeeModel->find($leave['employee_id']);
        if ($employee['department_id'] != $manager['department_id']) {
            return redirect()->to('/manager/team-leaves')
                             ->with('error', 'Access denied. This employee is not in your department.');
        }

        return view('manager/view_leave', [
            'title'   => 'Leave Request Details',
            'leave'   => $leave,
            'manager' => $manager,
        ]);
    }

    public function approveLeave(int $id)
{
    $leave     = $this->leaveModel->find($id);
    $leaveBalanceModel = new \App\Models\LeaveBalanceModel();
    $leaveTypeModel    = new \App\Models\LeaveTypeModel();

    if (!$leave) {
        return redirect()->to('/manager/team-leaves')->with('error', 'Leave request not found.');
    }

    if ($leave['status'] !== 'Pending') {
        return redirect()->to('/manager/team-leaves/view/' . $id)
                         ->with('error', 'This leave request has already been processed.');
    }

    $year      = date('Y', strtotime($leave['start_date']));
    $leaveType = $leaveTypeModel->find($leave['leave_type_id']);

    // Initialize and check balance
    $balance = $leaveBalanceModel->initBalance(
        $leave['employee_id'],
        $leave['leave_type_id'],
        $leaveType['max_days'],
        $year
    );

    if ($balance['remaining_days'] < $leave['total_days']) {
        return redirect()->to('/manager/team-leaves/view/' . $id)
                         ->with('error', "Cannot approve. Employee only has {$balance['remaining_days']} remaining {$leaveType['name']} day(s) but requested {$leave['total_days']} day(s).");
    }

    $this->leaveModel->update($id, [
        'status'      => 'Approved',
        'approved_by' => session()->get('user_id'),
    ]);

    $leaveBalanceModel->deductDays(
        $leave['employee_id'],
        $leave['leave_type_id'],
        $leave['total_days'],
        $year
    );

    return redirect()->to('/manager/team-leaves/view/' . $id)
                     ->with('success', 'Leave request approved and balance updated!');
}

public function rejectLeave(int $id)
{
    $leave             = $this->leaveModel->find($id);
    $leaveBalanceModel = new \App\Models\LeaveBalanceModel();

    if (!$leave) {
        return redirect()->to('/manager/team-leaves')->with('error', 'Leave request not found.');
    }

    $wasApproved = $leave['status'] === 'Approved';

    $this->leaveModel->update($id, [
        'status'      => 'Rejected',
        'approved_by' => session()->get('user_id'),
    ]);

    if ($wasApproved) {
        $year = date('Y', strtotime($leave['start_date']));
        $leaveBalanceModel->restoreDays(
            $leave['employee_id'],
            $leave['leave_type_id'],
            $leave['total_days'],
            $year
        );
    }

    return redirect()->to('/manager/team-leaves/view/' . $id)
                     ->with('success', 'Leave request rejected.');
    }
}