<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card" style="max-width:600px;">
    <div class="card-header py-3">
        <i class="bi bi-plus-circle me-2 text-primary"></i>Add Department
    </div>
    <div class="card-body">
        <form action="/admin/departments/store" method="POST">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Department Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control"
                       value="<?= old('name') ?>" placeholder="e.g. Human Resources" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="3"
                          placeholder="Brief description..."><?= old('description') ?></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Save
                </button>
                <a href="/admin/departments" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>