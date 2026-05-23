<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-cash-stack me-2 text-primary"></i>My Payroll Records
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Period</th>
                        <th>Basic Salary</th>
                        <th>Overtime</th>
                        <th>Deductions</th>
                        <th>Net Pay</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payrolls)): ?>
                        <?php foreach ($payrolls as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <?= date('M d', strtotime($p['period_start'])) ?>
                                — <?= date('M d, Y', strtotime($p['period_end'])) ?>
                            </td>
                            <td>₱ <?= number_format($p['basic_salary'], 2) ?></td>
                            <td>₱ <?= number_format($p['overtime_pay'], 2) ?></td>
                            <td>₱ <?= number_format($p['deductions'], 2) ?></td>
                            <td><strong>₱ <?= number_format($p['net_pay'], 2) ?></strong></td>
                            <td>
                                <span class="badge <?= $p['status'] === 'Finalized' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= esc($p['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">No payroll records yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>