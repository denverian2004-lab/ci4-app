<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="/admin/reports/payroll" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">From</label>
                <input type="date" name="from" class="form-control" value="<?= esc($from) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold mb-1">To</label>
                <input type="date" name="to" class="form-control" value="<?= esc($to) ?>">
            </div>
            <div class="col-md-4 d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>Generate
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>Print
                </button>
                <a href="/admin/reports/export/payroll/pdf?from=<?= esc($from) ?>&to=<?= esc($to) ?>"
                class="btn btn-danger" target="_blank">
                    <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                </a>
                <a href="/admin/reports/export/payroll/excel?from=<?= esc($from) ?>&to=<?= esc($to) ?>"
                class="btn btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>Excel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Totals -->
<div class="row g-3 mb-4">
    <?php
        $totalItems = [
            ['label' => 'Total Basic',    'value' => $totals['basic'],      'color' => 'primary'],
            ['label' => 'Total Overtime', 'value' => $totals['overtime'],   'color' => 'success'],
            ['label' => 'Total Deductions','value'=> $totals['deductions'], 'color' => 'danger'],
            ['label' => 'Total Net Pay',  'value' => $totals['net'],        'color' => 'dark'],
        ];
    ?>
    <?php foreach ($totalItems as $t): ?>
    <div class="col-6 col-md-3">
        <div class="card border-0 text-center" style="background:#f8f9fa;">
            <div class="card-body py-3">
                <div class="fs-5 fw-bold text-<?= $t['color'] ?>">
                    ₱<?= number_format($t['value'], 2) ?>
                </div>
                <small class="text-muted"><?= $t['label'] ?></small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-cash-stack me-2 text-primary"></i>
        Payroll Report: <?= date('M d, Y', strtotime($from)) ?> — <?= date('M d, Y', strtotime($to)) ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Period</th>
                        <th>Basic</th>
                        <th>Overtime</th>
                        <th>Deductions</th>
                        <th>Net Pay</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($records)): ?>
                        <?php foreach ($records as $i => $r): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <strong><?= esc($r['first_name'] . ' ' . $r['last_name']) ?></strong><br>
                                <small class="text-muted"><?= esc($r['employee_code']) ?></small>
                            </td>
                            <td><?= esc($r['department_name'] ?? '—') ?></td>
                            <td>
                                <small>
                                    <?= date('M d', strtotime($r['period_start'])) ?>
                                    — <?= date('M d, Y', strtotime($r['period_end'])) ?>
                                </small>
                            </td>
                            <td>₱<?= number_format($r['basic_salary'], 2) ?></td>
                            <td>₱<?= number_format($r['overtime_pay'], 2) ?></td>
                            <td>₱<?= number_format($r['deductions'], 2) ?></td>
                            <td><strong>₱<?= number_format($r['net_pay'], 2) ?></strong></td>
                            <td>
                                <span class="badge <?= $r['status'] === 'Finalized' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                    <?= esc($r['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">No records found for selected period.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<style>
    @media print {
        #sidebar, #topbar, .card:first-child, .btn { display: none !important; }
        #main-content { margin-left: 0 !important; }
    }
</style>
<?= $this->endSection() ?>