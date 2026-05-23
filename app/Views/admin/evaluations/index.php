<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center py-3">
        <span><i class="bi bi-star-fill me-2 text-primary"></i>Performance Evaluations</span>
        <a href="/admin/evaluations/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Add Evaluation
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Period</th>
                        <th>Score</th>
                        <th>Rating</th>
                        <th>Evaluated By</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($evaluations)): ?>
                        <?php foreach ($evaluations as $i => $eval): ?>
                        <?php
                            $score = (float)$eval['score'];
                            if      ($score >= 90) { $rating = 'Outstanding';    $color = 'success'; }
                            elseif  ($score >= 75) { $rating = 'Very Good';      $color = 'primary'; }
                            elseif  ($score >= 60) { $rating = 'Satisfactory';   $color = 'info';    }
                            elseif  ($score >= 50) { $rating = 'Fair';           $color = 'warning'; }
                            else                   { $rating = 'Unsatisfactory'; $color = 'danger';  }
                        ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <div style="font-weight:600;"><?= esc($eval['first_name'] . ' ' . $eval['last_name']) ?></div>
                                <small class="text-muted"><?= esc($eval['employee_code']) ?></small>
                            </td>
                            <td><?= esc($eval['period']) ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-fill" style="height:8px;min-width:80px;">
                                        <div class="progress-bar bg-<?= $color ?>"
                                             style="width:<?= $score ?>%;border-radius:10px;"></div>
                                    </div>
                                    <span style="font-weight:600;font-size:0.85rem;"><?= $score ?>%</span>
                                </div>
                            </td>
                            <td><span class="badge bg-<?= $color ?>"><?= $rating ?></span></td>
                            <td><?= esc($eval['evaluator']) ?></td>
                            <td><?= date('M d, Y', strtotime($eval['created_at'])) ?></td>
                            <td>
                                <a href="/admin/evaluations/view/<?= $eval['id'] ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No evaluations found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>