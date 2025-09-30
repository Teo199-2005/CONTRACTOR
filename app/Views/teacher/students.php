<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">My Students</h1>
    <span class="badge bg-primary"><?= count($students) ?> Students</span>
</div>

<?php if (!empty($students)): ?>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Class List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Grade Level</th>
                            <th>Section</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><strong><?= esc($student['student_id']) ?></strong></td>
                                <td><?= esc($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                <td>Grade <?= esc($student['grade_level']) ?></td>
                                <td><?= esc($student['section_name'] ?? 'No Section') ?></td>
                                <td>
                                    <span class="badge bg-success">Enrolled</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-people fs-1 text-muted mb-3"></i>
            <h5 class="text-muted">No Students Assigned</h5>
            <p class="text-muted mb-0">You are not currently assigned as an adviser to any section.</p>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?> 