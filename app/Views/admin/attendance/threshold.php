<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="row g-3">

    <!-- Threshold Settings -->
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-sliders me-2 text-primary"></i>Threshold Settings
            </div>
            <div class="card-body">
                <form action="/admin/attendance/threshold/update" method="POST">
                    <?= csrf_field() ?>
                    <div class="row g-3">

                        <!-- Absence Threshold -->
                        <div class="col-12">
                            <h6 class="fw-bold text-danger border-bottom pb-2">
                                <i class="bi bi-calendar-x-fill me-2"></i>Absence Threshold
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Max Allowed Absences</label>
                            <input type="number" name="absent_max" class="form-control"
                                   min="1" max="31"
                                   value="<?= $absentThreshold['max_allowed'] ?>" required>
                            <small class="text-muted">Flag if absences reach this number</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Period</label>
                            <select name="absent_period" class="form-select">
                                <option value="monthly" <?= $absentThreshold['period'] === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                                <option value="weekly"  <?= $absentThreshold['period'] === 'weekly'  ? 'selected' : '' ?>>Weekly</option>
                            </select>
                        </div>

                        <!-- Late Threshold -->
                        <div class="col-12 mt-2">
                            <h6 class="fw-bold text-warning border-bottom pb-2">
                                <i class="bi bi-clock-fill me-2"></i>Late Threshold
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Max Allowed Lates</label>
                            <input type="number" name="late_max" class="form-control"
                                   min="1" max="31"
                                   value="<?= $lateThreshold['max_allowed'] ?>" required>
                            <small class="text-muted">Flag if lates reach this number</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Period</label>
                            <select name="late_period" class="form-select">
                                <option value="monthly" <?= $lateThreshold['period'] === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                                <option value="weekly"  <?= $lateThreshold['period'] === 'weekly'  ? 'selected' : '' ?>>Weekly</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-save me-1"></i>Save Thresholds
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Current Violators -->
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header py-3">
                <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                Current Violators —
                <small class="text-muted"><?= date('F Y', strtotime($monthStart)) ?></small>
            </div>
            <div class="card-body">

                <!-- Absent Violators -->
                <h6 class="fw-bold text-danger mb-3">
                    <i class="bi bi-calendar-x-fill me-1"></i>
                    Excessive Absences
                    <span class="badge bg-danger ms-1"><?= count($absentViolators) ?></span>
                    <small class="text-muted fw-normal">(≥ <?= $absentThreshold['max_allowed'] ?> absences)</small>
                </h6>
                <?php if (!empty($absentViolators)): ?>
                <div class="table-responsive mb-4">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th class="text-center">Absences</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($absentViolators as $v): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($v['first_name'] . ' ' . $v['last_name']) ?></strong><br>
                                    <small class="text-muted"><?= esc($v['employee_code']) ?></small>
                                </td>
                                <td><?= esc($v['department_name'] ?? '—') ?></td>
                                <td><?= esc($v['position'] ?? '—') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-danger fs-6"><?= $v['count'] ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p class="text-muted mb-4">
                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                        No employees exceeded the absence threshold this month.
                    </p>
                <?php endif; ?>

                <hr>

                <!-- Late Violators -->
                <h6 class="fw-bold text-warning mb-3">
                    <i class="bi bi-clock-fill me-1"></i>
                    Excessive Lates
                    <span class="badge bg-warning text-dark ms-1"><?= count($lateViolators) ?></span>
                    <small class="text-muted fw-normal">(≥ <?= $lateThreshold['max_allowed'] ?> lates)</small>
                </h6>
                <?php if (!empty($lateViolators)): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th class="text-center">Lates</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lateViolators as $v): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($v['first_name'] . ' ' . $v['last_name']) ?></strong><br>
                                    <small class="text-muted"><?= esc($v['employee_code']) ?></small>
                                </td>
                                <td><?= esc($v['department_name'] ?? '—') ?></td>
                                <td><?= esc($v['position'] ?? '—') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-warning text-dark fs-6"><?= $v['count'] ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <p class="text-muted mb-0">
                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                        No employees exceeded the late threshold this month.
                    </p>
                <?php endif; ?>

            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>