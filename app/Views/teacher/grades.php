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
                                    <?= esc($student['student_id'] ?? $student['lrn'] ?? 'N/A') ?> - <?= esc($student['first_name'] . ' ' . $student['last_name']) ?>
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
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Student Grades - Quarter <?= $currentQuarter ?></h5>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-warning btn-sm" onclick="demoFillGrades()" title="Fill all empty grades with random demo values">
                    <i class="bi bi-stars me-1"></i>Demo Fill
                </button>
                <button type="button" class="btn btn-success btn-sm" id="toggleInputMode" onclick="toggleInputMode()">
                    <i class="bi bi-pencil-square me-1"></i>Enable Input Mode
                </button>
                <select class="form-select form-select-sm" id="sectionFilter" style="width: auto; min-width: 120px;">
                    <option value="">All Sections</option>
                    <?php 
                    $sections = [];
                    foreach ($students as $student) {
                        $sectionKey = $student['grade_level'] . ' - ' . $student['section_name'];
                        if (!in_array($sectionKey, $sections)) {
                            $sections[] = $sectionKey;
                        }
                    }
                    sort($sections);
                    foreach ($sections as $section): ?>
                        <option value="<?= esc($section) ?>"><?= esc($section) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="card-body">
            <form id="bulkGradesForm" method="post" action="<?= base_url('teacher/grades/bulk') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="quarter" value="<?= $currentQuarter ?>">
                
                <div id="saveButtonContainer" class="mb-3" style="display: none;">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Save All Grades
                    </button>
                    <button type="button" class="btn ms-2" style="background-color: #495057; color: white;" onclick="cancelInputMode()">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <?php foreach ($subjects as $subject): ?>
                                    <th><?= esc($subject['subject_code']) ?></th>
                                <?php endforeach; ?>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr data-section="<?= esc($student['grade_level'] . ' - ' . $student['section_name']) ?>">
                                    <td>
                                        <strong><?= esc($student['first_name'] . ' ' . $student['last_name']) ?></strong><br>
                                        <small class="text-muted"><?= esc($student['student_id'] ?? $student['lrn'] ?? 'N/A') ?></small><br>
                                        <span class="badge bg-secondary"><?= esc($student['grade_level'] . ' - ' . $student['section_name']) ?></span>
                                    </td>
                                    <?php 
                                    $totalGrades = 0;
                                    $gradeCount = 0;
                                    foreach ($subjects as $subject): 
                                    ?>
                                        <td>
                                            <?php 
                                            $grade = $studentGrades[$student['id']][$subject['id']] ?? null;
                                            $gradeValue = $grade ? $grade['grade'] : '';
                                            if ($grade): 
                                                $totalGrades += $grade['grade'];
                                                $gradeCount++;
                                            endif;
                                            ?>
                                            <span class="grade-display">
                                                <?php if ($grade): ?>
                                                    <span class="badge bg-success"><?= $grade['grade'] ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">Not graded</span>
                                                <?php endif; ?>
                                            </span>
                                            <input type="number" class="form-control form-control-sm grade-input" 
                                                   name="grades[<?= $student['id'] ?>][<?= $subject['id'] ?>]" 
                                                   value="<?= $gradeValue ?>" 
                                                   min="0" max="100" step="0.1" 
                                                   style="display: none; width: 80px;" 
                                                   onchange="updateTotal(<?= $student['id'] ?>)" 
                                                   oninput="validateGrade(this)">
                                        </td>
                                    <?php endforeach; ?>
                                    <td>
                                        <span class="total-display" data-student="<?= $student['id'] ?>">
                                            <?php if ($gradeCount > 0): ?>
                                                <strong><?= number_format($totalGrades / $gradeCount, 1) ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="card-footer">
            <nav>
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $currentPage - 1 ?>">
                            <i class="bi bi-chevron-left"></i> Previous
                        </a>
                    </li>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $currentPage + 1 ?>">
                            Next <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
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

<script>
let inputModeEnabled = false;

function toggleInputMode() {
    const toggleBtn = document.getElementById('toggleInputMode');
    const saveContainer = document.getElementById('saveButtonContainer');
    const gradeDisplays = document.querySelectorAll('.grade-display');
    const gradeInputs = document.querySelectorAll('.grade-input');
    
    inputModeEnabled = !inputModeEnabled;
    
    if (inputModeEnabled) {
        toggleBtn.innerHTML = '<i class="bi bi-eye me-1"></i>View Mode';
        toggleBtn.className = 'btn btn-outline-secondary btn-sm';
        saveContainer.style.display = 'block';
        
        gradeDisplays.forEach(display => display.style.display = 'none');
        gradeInputs.forEach(input => input.style.display = 'block');
    } else {
        toggleBtn.innerHTML = '<i class="bi bi-pencil-square me-1"></i>Enable Input Mode';
        toggleBtn.className = 'btn btn-success btn-sm';
        saveContainer.style.display = 'none';
        
        gradeDisplays.forEach(display => display.style.display = 'block');
        gradeInputs.forEach(input => input.style.display = 'none');
    }
}

function cancelInputMode() {
    // Reset all inputs to original values
    const gradeInputs = document.querySelectorAll('.grade-input');
    gradeInputs.forEach(input => {
        const originalValue = input.getAttribute('data-original') || '';
        input.value = originalValue;
    });
    
    toggleInputMode();
}

function validateGrade(input) {
    const value = parseFloat(input.value);
    if (value < 0) {
        input.value = 0;
    } else if (value > 100) {
        input.value = 100;
    }
}

function updateTotal(studentId) {
    const inputs = document.querySelectorAll(`input[name^="grades[${studentId}]"]`);
    let total = 0;
    let count = 0;
    
    inputs.forEach(input => {
        const value = parseFloat(input.value);
        if (!isNaN(value) && value > 0) {
            total += value;
            count++;
        }
    });
    
    const totalDisplay = document.querySelector(`.total-display[data-student="${studentId}"]`);
    if (count > 0) {
        totalDisplay.innerHTML = `<strong>${(total / count).toFixed(1)}</strong>`;
    } else {
        totalDisplay.innerHTML = '<span class="text-muted">-</span>';
    }
}

function demoFillGrades() {
    if (!confirm('This will fill all empty grades with random demo values. Continue?')) {
        return;
    }
    
    // Enable input mode first if not already enabled
    if (!inputModeEnabled) {
        toggleInputMode();
    }
    
    const gradeInputs = document.querySelectorAll('.grade-input');
    gradeInputs.forEach(input => {
        if (!input.value || input.value === '') {
            // Generate random grade between 75-95
            const randomGrade = (Math.random() * 20 + 75).toFixed(1);
            input.value = randomGrade;
            
            // Update the total for this student
            const studentId = input.name.match(/grades\[(\d+)\]/)[1];
            updateTotal(studentId);
        }
    });
    
    alert('Demo grades filled successfully!');
}

document.addEventListener('DOMContentLoaded', function() {
    const sectionFilter = document.getElementById('sectionFilter');
    const tableRows = document.querySelectorAll('tbody tr[data-section]');
    
    // Store original values for cancel functionality
    const gradeInputs = document.querySelectorAll('.grade-input');
    gradeInputs.forEach(input => {
        input.setAttribute('data-original', input.value);
    });
    
    if (sectionFilter) {
        sectionFilter.addEventListener('change', function() {
            const selectedSection = this.value;
            
            tableRows.forEach(row => {
                if (selectedSection === '' || row.dataset.section === selectedSection) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>

<?= $this->endSection() ?>