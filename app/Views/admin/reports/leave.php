<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="/admin/reports/leave" class="row g-2 align-items-end">
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
                <a href="/admin/reports/export/leave/pdf?from=<?= esc($from) ?>&to=<?= esc($to) ?>"
                class="btn btn-danger" target="_blank">
                    <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                </a>
                <a href="/admin/reports/export/leave/excel?from=<?= esc($from) ?>&to=<?= esc($to) ?>"
                class="btn btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>Excel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Summary -->
<div class="row g-3 mb-4">
    <?php
        $summaryItems = [
            ['label' => 'Pending',  'color' => 'warning', 'key' => 'Pending'],
            ['label' => 'Approved', 'color' => 'success', 'key' => 'Approved'],
            ['label' => 'Rejected', 'color' => 'danger',  'key' => 'Rejected'],
        ];
    ?>
    <?php foreach ($summaryItems as $s): ?>
    <div class="col-4">
        <div class="card border-0 text-center" style="background:#f8f9fa;">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-<?= $s['color'] ?>"><?= $summary[$s['key']] ?></div>
                <small class="text-muted"><?= $s['label'] ?></small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header py-3">
        <i class="bi bi-calendar2-x-fill me-2 text-primary"></i>
        Leave Report: <?= date('M d, Y', strtotime($from)) ?> — <?= date('M d, Y', strtotime($to)) ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Days</th>
                        <th>Reason</th>
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
                            <td><?= esc($r['leave_type_name']) ?></td>
                            <td><?= date('M d, Y', strtotime($r['start_date'])) ?></td>
                            <td><?= date('M d, Y', strtotime($r['end_date'])) ?></td>
                            <td><?= $r['total_days'] ?> day(s)</td>
                            <td><?= esc($r['reason'] ?? '—') ?></td>
                            <td>
                                <span class="badge badge-<?= strtolower($r['status']) ?>">
                                    <?= esc($r['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">No records found for selected period.</td></tr>
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