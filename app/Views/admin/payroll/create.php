<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card" style="max-width:700px;">
    <div class="card-header py-3">
        <i class="bi bi-cash-stack me-2 text-primary"></i>Generate Payroll
    </div>
    <div class="card-body">
        <form action="/admin/payroll/store" method="POST">
            <?= csrf_field() ?>
            <div class="row g-3">

                <div class="col-12">
                    <label class="form-label fw-semibold">Employee <span class="text-danger">*</span></label>
                    <select name="employee_id" class="form-select" id="employeeSelect" required>
                        <option value="">-- Select Employee --</option>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>"
                                    data-salary="<?= $emp['basic_salary'] ?>"
                                    <?= old('employee_id') == $emp['id'] ? 'selected' : '' ?>>
                                <?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                (<?= esc($emp['employee_code']) ?>) — ₱<?= number_format($emp['basic_salary'], 2) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Basic Salary Display -->
                <div class="col-md-12">
                    <div class="alert alert-info py-2 mb-0" id="salaryDisplay" style="display:none;">
                        <i class="bi bi-info-circle me-2"></i>
                        Basic Salary: <strong id="salaryAmount">₱0.00</strong>
                        <small class="text-muted ms-2">(auto-fetched from employee record)</small>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Period Start <span class="text-danger">*</span></label>
                    <input type="date" name="period_start" class="form-control"
                           value="<?= old('period_start') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Period End <span class="text-danger">*</span></label>
                    <input type="date" name="period_end" class="form-control"
                           value="<?= old('period_end') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Overtime Pay</label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" name="overtime_pay" step="0.01" min="0"
                               class="form-control" value="<?= old('overtime_pay', 0) ?>"
                               id="overtimePay" oninput="computeNet()">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Deductions</label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" name="deductions" step="0.01" min="0"
                               class="form-control" value="<?= old('deductions', 0) ?>"
                               id="deductions" oninput="computeNet()">
                    </div>
                </div>

                <!-- Net Pay Preview -->
                <div class="col-12">
                    <div class="card bg-light border-0">
                        <div class="card-body py-3 d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Computed Net Pay:</span>
                            <span class="fs-5 fw-bold text-success" id="netPayPreview">₱ 0.00</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-select">
                        <option value="Draft"     <?= old('status') === 'Draft'     ? 'selected' : '' ?>>Draft</option>
                        <option value="Finalized" <?= old('status') === 'Finalized' ? 'selected' : '' ?>>Finalized</option>
                    </select>
                </div>

                <div class="col-12 d-flex gap-2 mt-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Save Payroll
                    </button>
                    <a href="/admin/payroll" class="btn btn-secondary">
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
    let basicSalary = 0;

    document.getElementById('employeeSelect').addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        basicSalary = parseFloat(selected.getAttribute('data-salary')) || 0;

        if (basicSalary > 0) {
            document.getElementById('salaryDisplay').style.display = 'block';
            document.getElementById('salaryAmount').textContent = '₱' + basicSalary.toLocaleString('en-PH', {minimumFractionDigits: 2});
        } else {
            document.getElementById('salaryDisplay').style.display = 'none';
        }
        computeNet();
    });

    function computeNet() {
        const overtime   = parseFloat(document.getElementById('overtimePay').value) || 0;
        const deductions = parseFloat(document.getElementById('deductions').value)  || 0;
        const net        = basicSalary + overtime - deductions;
        document.getElementById('netPayPreview').textContent = '₱ ' + net.toLocaleString('en-PH', {minimumFractionDigits: 2});
    }
</script>
<?= $this->endSection() ?>