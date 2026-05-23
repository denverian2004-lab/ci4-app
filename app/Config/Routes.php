<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// -------------------------------------------------------
// PUBLIC ROUTES (no login required)
// -------------------------------------------------------
$routes->get('/',            'AuthController::login');
$routes->get('login',        'AuthController::login');
$routes->post('login',       'AuthController::loginProcess');
$routes->get('logout',       'AuthController::logout');

// -------------------------------------------------------
// NOTIFICATION ROUTES (all logged in users)
// -------------------------------------------------------
$routes->group('notifications', ['filter' => 'auth:admin,manager,employee'], function($routes) {
    $routes->get('fetch',        'NotificationController::fetch');
    $routes->get('read/(:num)',  'NotificationController::read/$1');
    $routes->post('read-all',    'NotificationController::readAll');
    $routes->post('clear-all',   'NotificationController::clearAll');
});

// -------------------------------------------------------
// API ROUTES
// -------------------------------------------------------
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {

    // Auth (public)
    $routes->post('auth/login',  'AuthController::login');
    $routes->post('auth/logout', 'AuthController::logout', ['filter' => 'apiauth']);
    $routes->get('auth/me',      'AuthController::me',     ['filter' => 'apiauth']);

    // Protected routes
    $routes->group('', ['filter' => 'apiauth'], function($routes) {

        // Employees
        $routes->get('employees',              'EmployeeController::index');
        $routes->get('employees/(:num)',       'EmployeeController::show/$1');
        $routes->post('employees',             'EmployeeController::store');
        $routes->put('employees/(:num)',       'EmployeeController::update/$1');
        $routes->delete('employees/(:num)',    'EmployeeController::delete/$1');

        // Attendance
        $routes->get('attendance',                      'AttendanceController::index');
        $routes->post('attendance/clock-in',            'AttendanceController::clockIn');
        $routes->post('attendance/clock-out',           'AttendanceController::clockOut');
        $routes->get('attendance/employee/(:num)',      'AttendanceController::byEmployee/$1');

        // Leave
        $routes->get('leaves',                  'LeaveController::index');
        $routes->post('leaves',                 'LeaveController::store');
        $routes->put('leaves/(:num)/approve',   'LeaveController::approve/$1');
        $routes->put('leaves/(:num)/reject',    'LeaveController::reject/$1');
        $routes->get('leaves/balance/(:num)',   'LeaveController::balance/$1');

        // Payroll
        $routes->get('payroll',                  'PayrollController::index');
        $routes->get('payroll/(:num)',           'PayrollController::show/$1');
        $routes->get('payroll/employee/(:num)',  'PayrollController::byEmployee/$1');
        $routes->post('payroll',                 'PayrollController::store');

        // Notifications
        $routes->get('notifications',              'NotificationController::index');
        $routes->put('notifications/(:num)/read', 'NotificationController::markRead/$1');
        $routes->put('notifications/read-all',    'NotificationController::markAllRead');

        // Dashboard & Reports
        $routes->get('dashboard/stats',          'DashboardController::stats');
        $routes->get('reports/attendance',       'DashboardController::attendanceReport');
        $routes->get('reports/payroll',          'DashboardController::payrollReport');
        $routes->get('reports/leave',            'DashboardController::leaveReport');
    });
});

// -------------------------------------------------------
// ADMIN & HR ROUTES
// -------------------------------------------------------
$routes->group('admin', ['filter' => 'auth:admin'], function($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // Report Exports
    $routes->get('reports/export/attendance/pdf',   'ReportController::exportAttendancePdf');
    $routes->get('reports/export/attendance/excel', 'ReportController::exportAttendanceExcel');
    $routes->get('reports/export/payroll/pdf',      'ReportController::exportPayrollPdf');
    $routes->get('reports/export/payroll/excel',    'ReportController::exportPayrollExcel');
    $routes->get('reports/export/leave/pdf',        'ReportController::exportLeavePdf');
    $routes->get('reports/export/leave/excel',      'ReportController::exportLeaveExcel');

    // Change Password
    $routes->get('change-password',          'AdminController::changePassword');
    $routes->post('change-password/update',  'AdminController::updatePassword');

    // Employees
    $routes->get('employees',                'EmployeeController::index');
    $routes->get('employees/create',         'EmployeeController::create');
    $routes->post('employees/store',         'EmployeeController::store');
    $routes->get('employees/view/(:num)',    'EmployeeController::view/$1');
    $routes->get('employees/edit/(:num)',    'EmployeeController::edit/$1');
    $routes->post('employees/update/(:num)', 'EmployeeController::update/$1');
    $routes->get('employees/delete/(:num)', 'EmployeeController::delete/$1');

    // Departments
    $routes->get('departments',                'DepartmentController::index');
    $routes->get('departments/create',         'DepartmentController::create');
    $routes->post('departments/store',         'DepartmentController::store');
    $routes->get('departments/edit/(:num)',    'DepartmentController::edit/$1');
    $routes->post('departments/update/(:num)', 'DepartmentController::update/$1');
    $routes->get('departments/delete/(:num)',  'DepartmentController::delete/$1');

    // Attendance Threshold (must be before attendance routes)
    $routes->get('attendance/threshold',         'AttendanceController::threshold');
    $routes->post('attendance/threshold/update', 'AttendanceController::updateThreshold');

    // Attendance
    $routes->get('attendance',                'AttendanceController::index');
    $routes->get('attendance/create',         'AttendanceController::create');
    $routes->post('attendance/store',         'AttendanceController::store');
    $routes->get('attendance/edit/(:num)',    'AttendanceController::edit/$1');
    $routes->post('attendance/update/(:num)', 'AttendanceController::update/$1');
    $routes->get('attendance/delete/(:num)', 'AttendanceController::delete/$1');

    // Leave Management
    $routes->get('leaves',                   'LeaveController::index');
    $routes->get('leaves/view/(:num)',       'LeaveController::view/$1');
    $routes->post('leaves/approve/(:num)',   'LeaveController::approve/$1');
    $routes->post('leaves/reject/(:num)',    'LeaveController::reject/$1');

    // Payroll
    $routes->get('payroll',                  'PayrollController::index');
    $routes->get('payroll/create',           'PayrollController::create');
    $routes->post('payroll/store',           'PayrollController::store');
    $routes->get('payroll/view/(:num)',      'PayrollController::view/$1');
    $routes->get('payroll/delete/(:num)',    'PayrollController::delete/$1');

    // Performance Evaluations
    $routes->get('evaluations',              'EvaluationController::index');
    $routes->get('evaluations/create',       'EvaluationController::create');
    $routes->post('evaluations/store',       'EvaluationController::store');
    $routes->get('evaluations/view/(:num)', 'EvaluationController::view/$1');

    // Reports
    $routes->get('reports',            'ReportController::index');
    $routes->get('reports/attendance', 'ReportController::attendance');
    $routes->get('reports/payroll',    'ReportController::payroll');
    $routes->get('reports/leave',      'ReportController::leave');

    // Users
    $routes->get('users',                    'UserController::index');
    $routes->get('users/create',             'UserController::create');
    $routes->post('users/store',             'UserController::store');
    $routes->get('users/edit/(:num)',        'UserController::edit/$1');
    $routes->post('users/update/(:num)',     'UserController::update/$1');
    $routes->get('users/delete/(:num)',      'UserController::delete/$1');
});

// -------------------------------------------------------
// MANAGER ROUTES
// -------------------------------------------------------
$routes->group('manager', ['filter' => 'auth:manager'], function($routes) {

    // Dashboard
    $routes->get('dashboard', 'ManagerController::index');

    // Team Attendance
    $routes->get('team-attendance', 'ManagerController::teamAttendance');

    // Team Leave Requests
    $routes->get('team-leaves',                 'ManagerController::teamLeaves');
    $routes->get('team-leaves/view/(:num)',     'ManagerController::viewLeave/$1');
    $routes->post('team-leaves/approve/(:num)', 'ManagerController::approveLeave/$1');
    $routes->post('team-leaves/reject/(:num)',  'ManagerController::rejectLeave/$1');
});

// -------------------------------------------------------
// EMPLOYEE SELF-SERVICE ROUTES
// -------------------------------------------------------
$routes->group('employee', ['filter' => 'auth:employee,manager,admin'], function($routes) {
    $routes->get('dashboard',               'EmployeeDashboardController::index');
    $routes->get('profile',                 'EmployeeDashboardController::profile');
    $routes->get('my-attendance',           'EmployeeDashboardController::attendance');
    $routes->get('my-leaves',               'EmployeeDashboardController::leaves');
    $routes->get('my-leaves/apply',         'EmployeeDashboardController::applyLeave');
    $routes->post('my-leaves/submit',       'EmployeeDashboardController::submitLeave');
    $routes->get('my-payroll',              'EmployeeDashboardController::payroll');
    $routes->get('change-password',         'EmployeeDashboardController::changePassword');
    $routes->post('change-password/update', 'EmployeeDashboardController::updatePassword');
});