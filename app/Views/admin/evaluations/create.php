<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card" style="max-width:650px;">
    <div class="card-header py-3">
        <i class="bi bi-star-fill me-2 text-primary"></i>Add Performance Evaluation
    </div>
    <div class="card-body">
        <form action="/admin/evaluations/store" method="POST">
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
                    <label class="form-label fw-semibold">Evaluation Period <span class="text-danger">*</span></label>
                    <input type="text" name="period" class="form-control"
                           placeholder="e.g. Q1 2025, Jan-Mar 2025"
                           value="<?= old('period') ?>" required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">
                        Score (0–100) <span class="text-danger">*</span>
                        <span class="text-muted fw-normal ms-2" id="scoreLabel"></span>
                    </label>
                    <input type="range" name="score" class="form-range"
                           min="0" max="100" step="1"
                           value="<?= old('score', 75) ?>"
                           id="scoreRange" oninput="updateScore(this.value)">
                    <div class="d-flex justify-content-between">
                        <small class="text-muted">0</small>
                        <strong id="scoreValue" class="fs-5">75</strong>
                        <small class="text-muted">100</small>
                    </div>
                </div>

                <!-- Rating Preview -->
                <div class="col-12">
                    <div class="alert py-2 mb-0" id="ratingPreview">
                        <i class="bi bi-star-fill me-2"></i>
                        Rating: <strong id="ratingText">Very Good</strong>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Comments</label>
                    <textarea name="comments" class="form-control" rows="4"
                              placeholder="Evaluator's remarks..."><?= old('comments') ?></textarea>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Submit Evaluation
                    </button>
                    <a href="/admin/evaluations" class="btn btn-secondary">
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
    function updateScore(value) {
        document.getElementById('scoreValue').textContent = value;
        const v = parseInt(value);
        let rating, color;
        if      (v >= 90) { rating = 'Outstanding';    color = 'success'; }
        else if (v >= 75) { rating = 'Very Good';      color = 'primary'; }
        else if (v >= 60) { rating = 'Satisfactory';   color = 'info';    }
        else if (v >= 50) { rating = 'Fair';           color = 'warning'; }
        else              { rating = 'Unsatisfactory'; color = 'danger';  }

        document.getElementById('ratingText').textContent = rating;
        const alert = document.getElementById('ratingPreview');
        alert.className = `alert alert-${color} py-2 mb-0`;
    }

    // Init on load
    updateScore(document.getElementById('scoreRange').value);
</script>
<?= $this->endSection() ?>