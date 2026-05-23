<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card" style="max-width:650px;">
    <div class="card-header py-3">
        <i class="bi bi-calendar-plus me-2 text-primary"></i>Log Attendance
    </div>
    <div class="card-body">
        <form action="/admin/attendance/store" method="POST">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee <span class="text-danger">*</span></label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">-- Select Employee --</option>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>" <?= old('employee_id') == $emp['id'] ? 'selected' : '' ?>>
                                <?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?> (<?= esc($emp['employee_code']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control"
                           value="<?= old('date', date('Y-m-d')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Time In</label>
                    <input type="time" name="time_in" class="form-control" value="<?= old('time_in') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Time Out</label>
                    <input type="time" name="time_out" class="form-control" value="<?= old('time_out') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="Present"  <?= old('status') === 'Present'  ? 'selected' : '' ?>>Present</option>
                        <option value="Absent"   <?= old('status') === 'Absent'   ? 'selected' : '' ?>>Absent</option>
                        <option value="Late"     <?= old('status') === 'Late'     ? 'selected' : '' ?>>Late</option>
                        <option value="Half-day" <?= old('status') === 'Half-day' ? 'selected' : '' ?>>Half-day</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Remarks</label>
                    <input type="text" name="remarks" class="form-control"
                           value="<?= old('remarks') ?>" placeholder="Optional notes...">
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Save
                    </button>
                    <a href="/admin/attendance" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>