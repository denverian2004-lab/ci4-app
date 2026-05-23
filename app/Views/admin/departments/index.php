<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span><i class="bi bi-diagram-3-fill me-2 text-primary"></i>Departments</span>
        <a href="/admin/departments/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Add Department
        </a>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <?php if (!empty($departments)): ?>
                <?php foreach ($departments as $dept): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border" style="border-radius:12px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                     style="width:48px;height:48px;font-size:1.2rem;flex-shrink:0;">
                                    <i class="bi bi-diagram-3-fill"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0"><?= esc($dept['name']) ?></h6>
                                    <small class="text-muted"><?= $dept['emp_count'] ?> employee(s)</small>
                                </div>
                            </div>
                            <p class="text-muted small mb-3" style="min-height:36px;">
                                <?= esc($dept['description'] ?? 'No description provided.') ?>
                            </p>
                            <div class="d-flex gap-2">
                                <a href="/admin/departments/edit/<?= $dept['id'] ?>"
                                   class="btn btn-sm btn-outline-warning flex-fill">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </a>
                                <a href="/admin/departments/delete/<?= $dept['id'] ?>"
                                   class="btn btn-sm btn-outline-danger flex-fill"
                                   onclick="return confirm('Delete this department?')">
                                    <i class="bi bi-trash me-1"></i>Delete
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted py-5">
                    <i class="bi bi-diagram-3 fs-1 d-block mb-2"></i>
                    No departments found. <a href="/admin/departments/create">Add one now.</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>