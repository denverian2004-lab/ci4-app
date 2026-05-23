<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\AttendanceModel;
use App\Models\LeaveModel;
use App\Models\LeaveTypeModel;
use App\Models\PayrollModel;
use App\Models\UserModel;

class EmployeeDashboardController extends BaseController
{
    protected $employeeModel;
    protected $attendanceModel;
    protected $leaveModel;
    protected $leaveTypeModel;
    protected $payrollModel;
    protected $userModel;

    public function __construct()
    {
        $this->employeeModel   = new EmployeeModel();
        $this->attendanceModel = new AttendanceModel();
        $this->leaveModel      = new LeaveModel();
        $this->leaveTypeModel  = new LeaveTypeModel();
        $this->payrollModel    = new PayrollModel();
        $this->userModel       = new UserModel();
    }

    private function getEmployee()
    {
        // Try session first
        $employeeId = session()->get('employee_id');

        // If not in session, look up from user record
        if (!$employeeId) {
            $userModel = new UserModel();
            $user      = $userModel->find(session()->get('user_id'));
            if ($user && $user['employee_id']) {
                $employeeId = $user['employee_id'];
                session()->set('employee_id', $employeeId);
            }
        }

        if (!$employeeId) return null;

        return $this->employeeModel->getEmployeeWithDepartment($employeeId);
    }

    public function index()
    {
        $employee      = $this->getEmployee();
        $recentLeaves  = [];
        $recentPayroll = [];
        $attendance    = [];
        $presentCount  = 0;
        $absentCount   = 0;
        $lateCount     = 0;

        if ($employee) {
            $attendance = $this->attendanceModel
                ->where('employee_id', $employee['id'])
                ->where('date >=', date('Y-m-01'))
                ->orderBy('date', 'DESC')
                ->findAll();

            $recentLeaves = $this->leaveModel
                ->select('leave_requests.*, leave_types.name as leave_type_name')
                ->join('leave_types', 'leave_types.id = leave_requests.leave_type_id')
                ->where('leave_requests.employee_id', $employee['id'])
                ->orderBy('leave_requests.created_at', 'DESC')
                ->limit(3)
                ->findAll();

            $recentPayroll = $this->payrollModel
                ->where('employee_id', $employee['id'])
                ->orderBy('period_start', 'DESC')
                ->limit(3)
                ->findAll();

            $presentCount = count(array_filter($attendance, fn($a) => $a['status'] === 'Present'));
            $absentCount  = count(array_filter($attendance, fn($a) => $a['status'] === 'Absent'));
            $lateCount    = count(array_filter($attendance, fn($a) => $a['status'] === 'Late'));
        }

        return view('employee/dashboard', [
            'title'         => 'My Dashboard',
            'employee'      => $employee,
            'presentCount'  => $presentCount,
            'absentCount'   => $absentCount,
            'lateCount'     => $lateCount,
            'recentLeaves'  => $recentLeaves,
            'recentPayroll' => $recentPayroll,
        ]);
    }

    public function profile()
    {
        $employee = $this->getEmployee();
        if (!$employee) {
            return redirect()->to('/employee/dashboard')
                             ->with('error', 'No employee profile linked to your account. Please contact HR.');
        }

        return view('employee/profile', [
            'title'    => 'My Profile',
            'employee' => $employee,
        ]);
    }

    public function attendance()
    {
        $employee = $this->getEmployee();
        if (!$employee) {
            return redirect()->to('/employee/dashboard')
                             ->with('error', 'No employee profile linked to your account.');
        }

        $records = $this->attendanceModel->getByEmployee($employee['id']);

        return view('employee/attendance', [
            'title'    => 'My Attendance',
            'employee' => $employee,
            'records'  => $records,
        ]);
    }

    public function leaves()
    {
        $employee = $this->getEmployee();
        if (!$employee) {
            return redirect()->to('/employee/dashboard')
                             ->with('error', 'No employee profile linked to your account.');
        }

        $leaves = $this->leaveModel->getByEmployee($employee['id']);

        return view('employee/leaves', [
            'title'    => 'My Leaves',
            'employee' => $employee,
            'leaves'   => $leaves,
        ]);
    }

    public function applyLeave()
    {
        $employee = $this->getEmployee();
        if (!$employee) {
            return redirect()->to('/employee/dashboard')
                             ->with('error', 'No employee profile linked to your account.');
        }

        $leaveTypes        = $this->leaveTypeModel->findAll();
        $leaveBalanceModel = new \App\Models\LeaveBalanceModel();
        $year              = date('Y');

        $balances = [];
        foreach ($leaveTypes as $type) {
            $balance = $leaveBalanceModel->initBalance(
                $employee['id'],
                $type['id'],
                $type['max_days'],
                $year
            );
            $balances[$type['id']] = $balance;
        }

        return view('employee/apply_leave', [
            'title'      => 'Apply for Leave',
            'employee'   => $employee,
            'leaveTypes' => $leaveTypes,
            'balances'   => $balances,
            'year'       => $year,
        ]);
    }

    public function submitLeave()
    {
        $employee = $this->getEmployee();
        if (!$employee) {
            return redirect()->to('/employee/dashboard')
                             ->with('error', 'No employee profile linked to your account.');
        }

        $rules = [
            'leave_type_id' => 'required|integer',
            'start_date'    => 'required|valid_date',
            'end_date'      => 'required|valid_date',
            'reason'        => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->with('error', implode('<br>', $this->validator->getErrors()))
                             ->withInput();
        }

        $start     = new \DateTime($this->request->getPost('start_date'));
        $end       = new \DateTime($this->request->getPost('end_date'));
        $totalDays = $start->diff($end)->days + 1;

        if ($end < $start) {
            return redirect()->back()
                             ->with('error', 'End date cannot be before start date.')
                             ->withInput();
        }

        $leaveTypeId       = $this->request->getPost('leave_type_id');
        $year              = $start->format('Y');
        $leaveBalanceModel = new \App\Models\LeaveBalanceModel();
        $leaveType         = $this->leaveTypeModel->find($leaveTypeId);

        $balance = $leaveBalanceModel->initBalance(
            $employee['id'],
            $leaveTypeId,
            $leaveType['max_days'],
            $year
        );

        if ($totalDays > $balance['remaining_days']) {
            return redirect()->back()
                             ->with('error', "You only have {$balance['remaining_days']} remaining {$leaveType['name']} day(s) for {$year}. You requested {$totalDays} day(s).")
                             ->withInput();
        }

        $this->leaveModel->insert([
            'employee_id'   => $employee['id'],
            'leave_type_id' => $leaveTypeId,
            'start_date'    => $this->request->getPost('start_date'),
            'end_date'      => $this->request->getPost('end_date'),
            'total_days'    => $totalDays,
            'reason'        => $this->request->getPost('reason'),
            'status'        => 'Pending',
        ]);

        // Notify all admins about new leave request
        notify_all_admins(
            'New Leave Request 📋',
            $employee['first_name'] . ' ' . $employee['last_name'] . ' submitted a ' . $leaveType['name'] . ' request for ' . $totalDays . ' day(s).',
            '/admin/leaves'
        );

        // Notify all managers about new leave request
        notify_all_managers(
            'New Leave Request 📋',
            $employee['first_name'] . ' ' . $employee['last_name'] . ' submitted a ' . $leaveType['name'] . ' request for ' . $totalDays . ' day(s).',
            '/manager/team-leaves'
        );

        return redirect()->to('/employee/my-leaves')
                         ->with('success', 'Leave application submitted successfully!');
    }

    public function payroll()
    {
        $employee = $this->getEmployee();
        if (!$employee) {
            return redirect()->to('/employee/dashboard')
                             ->with('error', 'No employee profile linked to your account.');
        }

        $payrolls = $this->payrollModel->getByEmployee($employee['id']);

        return view('employee/payroll', [
            'title'    => 'My Payroll',
            'employee' => $employee,
            'payrolls' => $payrolls,
        ]);
    }

    public function changePassword()
    {
        return view('employee/change_password', [
            'title' => 'Change Password',
        ]);
    }

    public function updatePassword()
    {
        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->with('error', implode('<br>', $this->validator->getErrors()))
                             ->withInput();
        }

        $userModel       = new \App\Models\UserModel();
        $userId          = session()->get('user_id');
        $user            = $userModel->find($userId);
        $currentPassword = $this->request->getPost('current_password');

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->back()
                             ->with('error', 'Your current password is incorrect.')
                             ->withInput();
        }

        // Update password
        $userModel->update($userId, [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT),
        ]);

        return redirect()->to('/employee/change-password')
                         ->with('success', 'Password changed successfully!');
    }
}