<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card" style="max-width:650px;">
    <div class="card-header py-3">
        <i class="bi bi-calendar2-plus me-2 text-primary"></i>Apply for Leave
    </div>
    <div class="card-body">
        <form action="/employee/my-leaves/submit" method="POST">
            <?= csrf_field() ?>
            <div class="row g-3">

                <div class="col-12">
                    <label class="form-label fw-semibold">Leave Type <span class="text-danger">*</span></label>
                    <select name="leave_type_id" class="form-select" id="leaveTypeSelect" required>
                        <option value="">-- Select Leave Type --</option>
                        <?php foreach ($leaveTypes as $type): ?>
                            <option value="<?= $type['id'] ?>" <?= old('leave_type_id') == $type['id'] ? 'selected' : '' ?>>
                                <?= esc($type['name']) ?> (Max: <?= $type['max_days'] ?> days)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Balance Display -->
                <div class="col-12" id="balanceDisplay" style="display:none;">
                    <div class="card border-0" style="background:#f8f9fa;">
                        <div class="card-body py-3">
                            <p class="fw-semibold mb-2 text-muted" style="font-size:0.85rem;">
                                <i class="bi bi-info-circle me-1"></i>Your Leave Balance (<?= $year ?>)
                            </p>
                            <div class="row text-center g-2">
                                <div class="col-4">
                                    <div class="fw-bold text-primary fs-5" id="balAllocated">0</div>
                                    <small class="text-muted">Allocated</small>
                                </div>
                                <div class="col-4">
                                    <div class="fw-bold text-danger fs-5" id="balUsed">0</div>
                                    <small class="text-muted">Used</small>
                                </div>
                                <div class="col-4">
                                    <div class="fw-bold text-success fs-5" id="balRemaining">0</div>
                                    <small class="text-muted">Remaining</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control"
                           value="<?= old('start_date') ?>"
                           min="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">End Date <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" class="form-control"
                           value="<?= old('end_date') ?>"
                           min="<?= date('Y-m-d') ?>" required>
                </div>

                <!-- Total Days Preview -->
                <div class="col-12">
                    <div class="alert alert-info py-2 mb-0" id="daysPreview" style="display:none;">
                        <i class="bi bi-info-circle me-2"></i>Total days: <strong id="daysCount">0</strong>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control" rows="4"
                              placeholder="Please state your reason for leave..."
                              required><?= old('reason') ?></textarea>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i>Submit Application
                    </button>
                    <a href="/employee/my-leaves" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </a>
                </div>

            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const balances = <?= json_encode($balances) ?>;

    document.getElementById('leaveTypeSelect').addEventListener('change', function() {
        const id = this.value;
        if (id && balances[id]) {
            const b = balances[id];
            document.getElementById('balAllocated').textContent  = b.allocated_days + ' days';
            document.getElementById('balUsed').textContent       = b.used_days + ' days';
            document.getElementById('balRemaining').textContent  = b.remaining_days + ' days';
            document.getElementById('balanceDisplay').style.display = 'block';
        } else {
            document.getElementById('balanceDisplay').style.display = 'none';
        }
        computeDays();
    });

    function computeDays() {
        const start = document.querySelector('[name="start_date"]').value;
        const end   = document.querySelector('[name="end_date"]').value;

        if (start && end) {
            const s    = new Date(start);
            const e    = new Date(end);
            const diff = Math.floor((e - s) / (1000 * 60 * 60 * 24)) + 1;

            const preview = document.getElementById('daysPreview');

            if (diff > 0) {
                document.getElementById('daysCount').textContent = diff;
                preview.style.display = 'block';

                // Check against remaining balance
                const leaveTypeId = document.getElementById('leaveTypeSelect').value;
                if (leaveTypeId && balances[leaveTypeId]) {
                    const remaining = balances[leaveTypeId].remaining_days;
                    if (diff > remaining) {
                        preview.className = 'alert alert-danger py-2 mb-0';
                        preview.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i>You only have <strong>${remaining}</strong> remaining day(s) but requested <strong>${diff}</strong> day(s).`;
                    } else {
                        preview.className = 'alert alert-info py-2 mb-0';
                        preview.innerHTML = `<i class="bi bi-info-circle me-2"></i>Total days: <strong>${diff}</strong>`;
                    }
                }
            } else {
                preview.style.display = 'none';
            }
        }
    }

    document.querySelector('[name="start_date"]').addEventListener('change', computeDays);
    document.querySelector('[name="end_date"]').addEventListener('change', computeDays);
</script>
<?= $this->endSection() ?>