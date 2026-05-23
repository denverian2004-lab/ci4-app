<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php if (!$employee): ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        Your account is not linked to an employee profile. Please contact HR.
    </div>
<?php else: ?>

<!-- Welcome Banner -->
<div class="card mb-4" style="background:linear-gradient(135deg,#1e3a5f,#2e5090);border:none;">
    <div class="card-body py-4 d-flex align-items-center gap-4">
        <div class="rounded-circle bg-white d-flex align-items-center justify-content-center flex-shrink-0"
             style="width:64px;height:64px;font-size:1.5rem;font-weight:700;color:#1e3a5f;">
            <?= strtoupper(substr($employee['first_name'],0,1) . substr($employee['last_name'],0,1)) ?>
        </div>
        <div class="text-white">
            <h5 class="fw-bold mb-1">Welcome, <?= esc($employee['first_name']) ?>!</h5>
            <p class="mb-0 opacity-75">
                <?= esc($employee['position'] ?? 'Employee') ?> &bull;
                <?= esc($employee['department_name'] ?? 'No Department') ?> &bull;
                <?= esc($employee['employee_code']) ?>
            </p>
        </div>
    </div>
</div>

<!-- Attendance Stats This Month -->
<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="stat-card bg-stat-2">
            <div class="stat-label">Present This Month</div>
            <div class="stat-number"><?= $presentCount ?></div>
            <i class="bi bi-calendar-check-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card bg-stat-3">
            <div class="stat-label">Late This Month</div>
            <div class="stat-number"><?= $lateCount ?></div>
            <i class="bi bi-clock-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="stat-card bg-stat-4">
            <div class="stat-label">Absent This Month</div>
            <div class="stat-number"><?= $absentCount ?></div>
            <i class="bi bi-calendar-x-fill stat-icon"></i>
        </div>
    </div>
</div>

<div class="row g-3">

    <!-- Recent Leave Requests -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar2-x-fill me-2 text-primary"></i>Recent Leave Requests</span>
                <a href="/employee/my-leaves" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Dates</th>
                                <th>Days</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentLeaves)): ?>
                                <?php foreach ($recentLeaves as $leave): ?>
                                <tr>
                                    <td><?= esc($leave['leave_type_name']) ?></td>
                                    <td>
                                        <small>
                                            <?= date('M d', strtotime($leave['start_date'])) ?>
                                            — <?= date('M d, Y', strtotime($leave['end_date'])) ?>
                                        </small>
                                    </td>
                                    <td><?= $leave['total_days'] ?></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($leave['status']) ?>">
                                            <?= esc($leave['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted py-3">No leave requests yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payroll -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cash-stack me-2 text-primary"></i>Recent Payroll</span>
                <a href="/employee/my-payroll" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Net Pay</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentPayroll)): ?>
                                <?php foreach ($recentPayroll as $p): ?>
                                <tr>
                                    <td>
                                        <small>
                                            <?= date('M d', strtotime($p['period_start'])) ?>
                                            — <?= date('M d, Y', strtotime($p['period_end'])) ?>
                                        </small>
                                    </td>
                                    <td><strong>₱ <?= number_format($p['net_pay'], 2) ?></strong></td>
                                    <td>
                                        <span class="badge <?= $p['status'] === 'Finalized' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                            <?= esc($p['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center text-muted py-3">No payroll records yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<?php endif; ?>

<?= $this->endSection() ?>