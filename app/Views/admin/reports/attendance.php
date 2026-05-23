<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="/admin/reports/attendance" class="row g-2 align-items-end">
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
                <a href="/admin/reports/export/attendance/pdf?from=<?= esc($from) ?>&to=<?= esc($to) ?>"
                class="btn btn-danger" target="_blank">
                    <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                </a>
                <a href="/admin/reports/export/attendance/excel?from=<?= esc($from) ?>&to=<?= esc($to) ?>"
                class="btn btn-success">
                    <i class="bi bi-file-earmark-excel me-1"></i>Excel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Badges -->
<div class="row g-3 mb-4">
    <?php
        $summaryItems = [
            ['label' => 'Present',  'color' => 'success', 'key' => 'Present'],
            ['label' => 'Absent',   'color' => 'danger',  'key' => 'Absent'],
            ['label' => 'Late',     'color' => 'warning', 'key' => 'Late'],
            ['label' => 'Half-day', 'color' => 'info',    'key' => 'Half-day'],
        ];
    ?>
    <?php foreach ($summaryItems as $s): ?>
    <div class="col-6 col-md-3">
        <div class="card text-center border-0" style="background:#f8f9fa;">
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
        <i class="bi bi-calendar-check-fill me-2 text-primary"></i>
        Attendance Report:
        <?= date('M d, Y', strtotime($from)) ?> — <?= date('M d, Y', strtotime($to)) ?>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Department</th>
                        <th>Date</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($records)): ?>
                        <?php foreach ($records as $i => $r): ?>
                        <?php
                            $badgeMap = [
                                'Present'  => 'success',
                                'Absent'   => 'danger',
                                'Late'     => 'warning',
                                'Half-day' => 'info',
                            ];
                            $badge = $badgeMap[$r['status']] ?? 'secondary';
                        ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <strong><?= esc($r['first_name'] . ' ' . $r['last_name']) ?></strong><br>
                                <small class="text-muted"><?= esc($r['employee_code']) ?></small>
                            </td>
                            <td><?= esc($r['department_name'] ?? '—') ?></td>
                            <td><?= date('M d, Y', strtotime($r['date'])) ?></td>
                            <td><?= $r['time_in']  ? date('h:i A', strtotime($r['time_in']))  : '—' ?></td>
                            <td><?= $r['time_out'] ? date('h:i A', strtotime($r['time_out'])) : '—' ?></td>
                            <td><span class="badge bg-<?= $badge ?>"><?= esc($r['status']) ?></span></td>
                            <td><?= esc($r['remarks'] ?? '—') ?></td>
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