<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Enter Grades</h1>
    <span class="badge bg-info">Quarter <?= $currentQuarter ?> - SY 2024-2025</span>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($students) && !empty($subjects)): ?>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Grade Entry Form</h5>
        </div>
        <div class="card-body">
            <form method="post" action="<?= base_url('teacher/grades') ?>">
                <?= csrf_field() ?>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="student_id" class="form-label">Student</label>
                        <select class="form-select" name="student_id" id="student_id" required>
                            <option value="">Select Student</option>
                            <?php foreach ($students as $student): ?>
                                <option value="<?= $student['id'] ?>">
                                    <?= esc($student['student_id']) ?> - <?= esc($student['first_name'] . ' ' . $student['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="subject_id" class="form-label">Subject</label>
                        <select class="form-select" name="subject_id" id="subject_id" required>
                            <option value="">Select Subject</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>">
                                    <?= esc($subject['subject_code']) ?> - <?= esc($subject['subject_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="quarter" class="form-label">Quarter</label>
                        <select class="form-select" name="quarter" id="quarter" required>
                            <option value="1" <?= $currentQuarter == 1 ? 'selected' : '' ?>>Quarter 1</option>
                            <option value="2" <?= $currentQuarter == 2 ? 'selected' : '' ?>>Quarter 2</option>
                            <option value="3" <?= $currentQuarter == 3 ? 'selected' : '' ?>>Quarter 3</option>
                            <option value="4" <?= $currentQuarter == 4 ? 'selected' : '' ?>>Quarter 4</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="grade" class="form-label">Grade</label>
                        <input type="number" class="form-control" name="grade" id="grade" 
                               min="60" max="100" step="0.1" placeholder="60-100" required>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <label for="remarks" class="form-label">Remarks (Optional)</label>
                        <input type="text" class="form-control" name="remarks" id="remarks" 
                               placeholder="Additional comments about the grade">
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="reset" class="btn btn-outline-secondary me-md-2">Clear Form</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Save Grade
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= count($students) ?></h5>
                    <p class="card-text text-muted mb-0">Total Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= count($subjects) ?></h5>
                    <p class="card-text text-muted mb-0">Available Subjects</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h5 class="card-title">Q<?= $currentQuarter ?></h5>
                    <p class="card-text text-muted mb-0">Current Quarter</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Student Grades Table -->
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Student Grades - Quarter <?= $currentQuarter ?></h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <?php foreach ($subjects as $subject): ?>
                                <th><?= esc($subject['subject_code']) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($student['first_name'] . ' ' . $student['last_name']) ?></strong><br>
                                    <small class="text-muted"><?= esc($student['student_id']) ?></small>
                                </td>
                                <?php foreach ($subjects as $subject): ?>
                                    <td>
                                        <?php 
                                        $grade = $studentGrades[$student['id']][$subject['id']] ?? null;
                                        if ($grade): 
                                        ?>
                                            <span class="badge bg-success"><?= $grade['grade'] ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">Not graded</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
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
            <i class="bi bi-clipboard-data fs-1 text-muted mb-3"></i>
            <h5 class="text-muted">No Students or Subjects Available</h5>
            <p class="text-muted mb-0">
                <?php if (empty($students)): ?>
                    You are not assigned as an adviser to any section with enrolled students.
                <?php elseif (empty($subjects)): ?>
                    No subjects are available for your students' grade levels.
                <?php endif; ?>
            </p>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?> 