<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-stat-1">
            <div class="stat-label">Total Employees</div>
            <div class="stat-number"><?= $totalEmployees ?></div>
            <i class="bi bi-people-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-stat-2">
            <div class="stat-label">Active Employees</div>
            <div class="stat-number"><?= $activeEmployees ?></div>
            <i class="bi bi-person-check-fill stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-stat-3">
            <div class="stat-label">Payroll This Month</div>
            <div class="stat-number" style="font-size:1.4rem;">₱<?= number_format($payrollTotal, 0) ?></div>
            <i class="bi bi-cash-stack stat-icon"></i>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card bg-stat-4">
            <div class="stat-label">Pending Leaves</div>
            <div class="stat-number"><?= $pendingLeaves ?></div>
            <i class="bi bi-calendar2-x-fill stat-icon"></i>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">

    <!-- Attendance This Month -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar-check-fill me-2 text-primary"></i>Attendance This Month</span>
                <a href="/admin/reports/attendance" class="btn btn-sm btn-outline-primary">Full Report</a>
            </div>
            <div class="card-body">
                <?php
                    $attTotal = $presentCount + $absentCount + $lateCount;
                    $items = [
                        ['label' => 'Present',  'count' => $presentCount, 'color' => 'success'],
                        ['label' => 'Absent',   'count' => $absentCount,  'color' => 'danger'],
                        ['label' => 'Late',     'count' => $lateCount,    'color' => 'warning'],
                    ];
                ?>
                <?php foreach ($items as $item): ?>
                    <?php $pct = $attTotal > 0 ? round(($item['count'] / $attTotal) * 100) : 0; ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size:0.85rem;font-weight:600;"><?= $item['label'] ?></span>
                            <span style="font-size:0.8rem;color:#999;"><?= $item['count'] ?> (<?= $pct ?>%)</span>
                        </div>
                        <div class="progress" style="height:10px;border-radius:10px;">
                            <div class="progress-bar bg-<?= $item['color'] ?>"
                                 style="width:<?= $pct ?>%;border-radius:10px;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <p class="text-muted small mt-3 mb-0">
                    Period: <?= date('F d', strtotime($monthStart)) ?> — <?= date('F d, Y', strtotime($monthEnd)) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Leave Summary -->
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-calendar2-x-fill me-2 text-primary"></i>Leave Summary</span>
                <a href="/admin/reports/leave" class="btn btn-sm btn-outline-primary">Full Report</a>
            </div>
            <div class="card-body">
                <div class="row g-3 text-center">
                    <div class="col-4">
                        <div class="p-3 rounded" style="background:#fff3cd;">
                            <div style="font-size:1.8rem;font-weight:700;color:#856404;"><?= $pendingLeaves ?></div>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded" style="background:#d4edda;">
                            <div style="font-size:1.8rem;font-weight:700;color:#155724;"><?= $approvedLeaves ?></div>
                            <small class="text-muted">Approved</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 rounded" style="background:#f8d7da;">
                            <div style="font-size:1.8rem;font-weight:700;color:#721c24;"><?= $rejectedLeaves ?></div>
                            <small class="text-muted">Rejected</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Department Breakdown -->
<div class="row g-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <span><i class="bi bi-diagram-3-fill me-2 text-primary"></i>Employees by Department</span>
                <a href="/admin/reports/payroll" class="btn btn-sm btn-outline-primary">Payroll Report</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Employees</th>
                                <th>Distribution</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $deptTotal = array_sum(array_column($departments, 'emp_count'));
                            ?>
                            <?php foreach ($departments as $dept): ?>
                            <?php $pct = $deptTotal > 0 ? round(($dept['emp_count'] / $deptTotal) * 100) : 0; ?>
                            <tr>
                                <td><strong><?= esc($dept['name']) ?></strong></td>
                                <td><?= $dept['emp_count'] ?></td>
                                <td style="min-width:200px;">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-fill" style="height:8px;border-radius:10px;">
                                            <div class="progress-bar"
                                                 style="width:<?= $pct ?>%;background:linear-gradient(90deg,#1e3a5f,#2e5090);border-radius:10px;">
                                            </div>
                                        </div>
                                        <small class="text-muted"><?= $pct ?>%</small>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>