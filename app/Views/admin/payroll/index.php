<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span><i class="bi bi-cash-stack me-2 text-primary"></i>Payroll Records</span>
        <a href="/admin/payroll/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Generate Payroll
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Period</th>
                        <th>Basic Salary</th>
                        <th>Overtime</th>
                        <th>Deductions</th>
                        <th>Net Pay</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payrolls)): ?>
                        <?php foreach ($payrolls as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <div style="font-weight:600;"><?= esc($p['first_name'] . ' ' . $p['last_name']) ?></div>
                                <small class="text-muted"><?= esc($p['employee_code']) ?></small>
                            </td>
                            <td>
                                <small>
                                    <?= date('M d', strtotime($p['period_start'])) ?>
                                    — <?= date('M d, Y', strtotime($p['period_end'])) ?>
                                </small>
                            </td>
                            <td>₱ <?= number_format($p['basic_salary'], 2) ?></td>
                            <td>₱ <?= number_format($p['overtime_pay'], 2) ?></td>
                            <td>₱ <?= number_format($p['deductions'], 2) ?></td>
                            <td><strong>₱ <?= number_format($p['net_pay'], 2) ?></strong></td>
                            <td>
                                <?php if ($p['status'] === 'Finalized'): ?>
                                    <span class="badge bg-success">Finalized</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/admin/payroll/view/<?= $p['id'] ?>"
                                   class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="/admin/payroll/delete/<?= $p['id'] ?>"
                                   class="btn btn-sm btn-outline-danger" title="Delete"
                                   onclick="return confirm('Delete this payroll record?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No payroll records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>