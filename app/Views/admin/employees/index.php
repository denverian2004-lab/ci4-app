<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span><i class="bi bi-people-fill me-2 text-primary"></i>Employee List</span>
        <a href="/admin/employees/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Add Employee
        </a>
    </div>
    <div class="card-body">

        <!-- Search -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search employees..." style="max-width:300px;">
        </div>

        <div class="table-responsive">
            <table class="table table-hover" id="employeeTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Code</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Type</th>
                        <th>Date Hired</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($employees)): ?>
                        <?php foreach ($employees as $i => $emp): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                <?php if (!empty($emp['profile_photo'])): ?>
                                    <img src="<?= base_url('uploads/profiles/' . $emp['profile_photo']) ?>"
                                        style="width:36px;height:36px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                <?php else: ?>
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                        style="width:36px;height:36px;font-size:0.8rem;font-weight:600;flex-shrink:0;">
                                        <?= strtoupper(substr($emp['first_name'],0,1) . substr($emp['last_name'],0,1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div style="font-weight:600;font-size:0.88rem;">
                                        <?= esc($emp['first_name'] . ' ' . $emp['last_name']) ?>
                                    </div>
                                    <div style="font-size:0.75rem;color:#999;"><?= esc($emp['email']) ?></div>
                                </div>
                            </div>
                            </td>
                            <td><span class="badge bg-light text-dark"><?= esc($emp['employee_code']) ?></span></td>
                            <td><?= esc($emp['department_name'] ?? '—') ?></td>
                            <td><?= esc($emp['position'] ?? '—') ?></td>
                            <td><?= esc($emp['employment_type']) ?></td>
                            <td><?= date('M d, Y', strtotime($emp['date_hired'])) ?></td>
                            <td>
                                <span class="badge badge-<?= strtolower($emp['status']) ?>">
                                    <?= esc($emp['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="/admin/employees/view/<?= $emp['id'] ?>" class="btn btn-sm btn-outline-primary" title="View">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="/admin/employees/edit/<?= $emp['id'] ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="/admin/employees/delete/<?= $emp['id'] ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   title="Delete"
                                   onclick="return confirm('Are you sure you want to delete this employee?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">No employees found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('#employeeTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
        });
    });
</script>
<?= $this->endSection() ?>