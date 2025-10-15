<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history text-warning"></i> 
                        New Student Applications
                        <span class="badge bg-warning text-dark ms-2"><?= count($pendingStudents) ?></span>
                    </h5>
                    <div>
                        <a href="<?= base_url('admin/students/pending/history') ?>" class="btn btn-outline-info btn-sm me-2">
                            <i class="bi bi-clock-history"></i> History
                        </a>
                        <a href="<?= base_url('admin/students/email-instructions') ?>" class="btn btn-outline-warning btn-sm me-2">
                            <i class="bi bi-envelope-gear"></i> Email Setup
                        </a>
                        <a href="<?= base_url('admin/students') ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Students
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($pendingStudents)): ?>
                        <div class="text-center py-3">
                            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                            <h5 class="mt-2">No New Student Applications</h5>
                            <p class="text-muted mb-0">All new student applications have been processed.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Grade Level</th>
                                        <th>Applied Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingStudents as $student): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <?= strtoupper(substr($student['first_name'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <strong><?= esc($student['first_name'] . ' ' . $student['last_name']) ?></strong>
                                                        <?php if ($student['middle_name']): ?>
                                                            <br><small class="text-muted"><?= esc($student['middle_name']) ?></small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= esc($student['email']) ?></td>
                                            <td>
                                                <span class="badge bg-info">Grade <?= $student['grade_level'] ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                $createdDate = new DateTime($student['created_at']);
                                                $createdDate->setTimezone(new DateTimeZone('Asia/Manila'));
                                                ?>
                                                <?= $createdDate->format('M j, Y') ?>
                                                <br><small class="text-muted"><?= $createdDate->format('g:i A') ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-success btn-sm" 
                                                            onclick="approveStudent(<?= $student['id'] ?>, '<?= esc($student['first_name'] . ' ' . $student['last_name']) ?>')">
                                                        <i class="bi bi-check-lg"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            onclick="rejectStudent(<?= $student['id'] ?>, '<?= esc($student['first_name'] . ' ' . $student['last_name']) ?>')">
                                                        <i class="bi bi-x-lg"></i> Reject
                                                    </button>
                                                    <button type="button" class="btn btn-outline-info btn-sm" 
                                                            onclick="viewStudentDetails(<?= $student['id'] ?>)">
                                                        <i class="bi bi-eye"></i> View
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Next School Year Enrollment Applications -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-arrow-up-circle text-info"></i> 
                        Next School Year Enrollments
                        <span class="badge bg-info text-white ms-2"><?= isset($nextYearApplications) ? count($nextYearApplications) : 0 ?></span>
                    </h5>
                    <small class="text-muted">Students applying for grade promotion</small>
                </div>
                <div class="card-body">
                    <?php if (empty($nextYearApplications ?? [])): ?>
                        <div class="text-center py-3">
                            <i class="bi bi-calendar-plus text-info" style="font-size: 2rem;"></i>
                            <h5 class="mt-2">No Next Year Applications</h5>
                            <p class="text-muted mb-0">No students have applied for next school year enrollment yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Current Grade</th>
                                        <th>Applying for</th>
                                        <th>GWA</th>
                                        <th>Status</th>
                                        <th>Applied Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($nextYearApplications as $application): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <?= strtoupper(substr($application['first_name'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <strong><?= esc($application['first_name'] . ' ' . $application['last_name']) ?></strong>
                                                        <br><small class="text-muted">LRN: <?= esc($application['lrn']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">Grade <?= $application['current_grade_level'] ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Grade <?= $application['next_grade_level'] ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $application['gwa'] >= 90 ? 'success' : ($application['gwa'] >= 85 ? 'info' : ($application['gwa'] >= 75 ? 'warning' : 'danger')) ?> text-white">
                                                    <?= number_format($application['gwa'], 2) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($application['gwa'] >= 75): ?>
                                                    <span class="badge bg-success">Qualified</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Not Qualified</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $appliedDate = new DateTime($application['applied_at']);
                                                $appliedDate->setTimezone(new DateTimeZone('Asia/Manila'));
                                                ?>
                                                <?= $appliedDate->format('M j, Y') ?>
                                                <br><small class="text-muted"><?= $appliedDate->format('g:i A') ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <?php if ($application['gwa'] >= 75): ?>
                                                        <button type="button" class="btn btn-success btn-sm" 
                                                                onclick="approvePromotion(<?= $application['student_id'] ?>, <?= $application['next_grade_level'] ?>, '<?= esc($application['first_name'] . ' ' . $application['last_name']) ?>')">
                                                            <i class="bi bi-arrow-up"></i> Promote
                                                        </button>
                                                    <?php else: ?>
                                                        <button type="button" class="btn btn-warning btn-sm" 
                                                                onclick="requireRetake(<?= $application['student_id'] ?>, '<?= esc($application['first_name'] . ' ' . $application['last_name']) ?>')">
                                                            <i class="bi bi-arrow-repeat"></i> Retake
                                                        </button>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            onclick="rejectApplication(<?= $application['id'] ?>, '<?= esc($application['first_name'] . ' ' . $application['last_name']) ?>')">
                                                        <i class="bi bi-x-circle"></i> Reject
                                                    </button>
                                                    <button type="button" class="btn btn-outline-info btn-sm" 
                                                            onclick="viewGrades(<?= $application['student_id'] ?>)">
                                                        <i class="bi bi-graph-up"></i> Grades
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
function approveStudent(studentId, studentName) {
    if (confirm(`Are you sure you want to APPROVE ${studentName}'s application?\n\nThis will:\n• Activate their account\n• Send them login credentials via email\n• Allow them to access the student portal`)) {
        fetch(`<?= base_url('admin/students/approve/') ?>${studentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let message = data.message;
                if (data.credentials) {
                    message += `\n\nLogin Credentials:\nEmail: ${data.credentials.email}\nPassword: ${data.credentials.password}`;
                }
                showAlert('success', message);
                setTimeout(() => location.reload(), 2500);
            } else {
                showAlert('error', data.error || 'Failed to approve student');
            }
        })
        .catch(error => {
            showAlert('error', 'Network error occurred');
        });
    }
}

function rejectStudent(studentId, studentName) {
    if (confirm(`Are you sure you want to REJECT ${studentName}'s application?\n\nThis action cannot be undone.`)) {
        fetch(`<?= base_url('admin/students/reject/') ?>${studentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('error', data.error || 'Failed to reject student');
            }
        })
        .catch(error => {
            showAlert('error', 'Network error occurred');
        });
    }
}

function viewStudentDetails(studentId) {
    window.location.href = `<?= base_url('admin/students/view/') ?>${studentId}`;
}

function approvePromotion(studentId, nextGradeLevel, studentName) {
    if (confirm(`Promote ${studentName} to Grade ${nextGradeLevel}?\n\nThis will:\n• Update their grade level\n• Enroll them for next school year\n• Send confirmation notification`)) {
        fetch(`<?= base_url('admin/students/promote/') ?>${studentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                next_grade_level: nextGradeLevel
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 2000);
            } else {
                showAlert('error', data.error || 'Failed to promote student');
            }
        })
        .catch(error => {
            showAlert('error', 'Network error occurred');
        });
    }
}

function requireRetake(studentId, studentName) {
    if (confirm(`Require ${studentName} to retake failed subjects?\n\nThis will:\n• Mark them for remedial classes\n• Notify them of retake requirements\n• Keep them in current grade level`)) {
        fetch(`<?= base_url('admin/students/require-retake/') ?>${studentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => location.reload(), 2000);
            } else {
                showAlert('error', data.error || 'Failed to process retake requirement');
            }
        })
        .catch(error => {
            showAlert('error', 'Network error occurred');
        });
    }
}

function viewGrades(studentId) {
    window.open(`<?= base_url('admin/students/grades/') ?>${studentId}`, '_blank');
}

function rejectApplication(applicationId, studentName) {
    const reason = prompt(`Reject ${studentName}'s application for next school year?\n\nPlease provide a reason (optional):`);
    if (reason !== null) {
        if (confirm(`Are you sure you want to reject this application?\n\nThis action cannot be undone.`)) {
            fetch(`<?= base_url('admin/students/reject-application/') ?>${applicationId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showAlert('error', data.error || 'Failed to reject application');
                }
            })
            .catch(error => {
                showAlert('error', 'Network error occurred');
            });
        }
    }
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const formattedMessage = message.replace(/\n/g, '<br>');
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${formattedMessage}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-dismiss after 8 seconds for success messages with credentials
    const dismissTime = message.includes('Login Credentials') ? 8000 : 5000;
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, dismissTime);
}

// Document viewer functions
function showImageModal(imageUrl, title) {
    const modal = document.getElementById('documentViewerModal');
    const image = document.getElementById('documentViewerImage');
    const titleElement = document.getElementById('documentViewerTitle');
    
    if (modal && image && titleElement) {
        titleElement.textContent = title;
        image.src = imageUrl;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

function closeDocumentModal() {
    const modal = document.getElementById('documentViewerModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDocumentModal();
    }
});
</script>

<style>
/* Fix modal z-index issues */
.modal {
    z-index: 999999 !important;
}

.modal-backdrop {
    z-index: 999998 !important;
}

.modal-content {
    position: relative;
    z-index: 999999 !important;
    pointer-events: auto;
}

.modal-body {
    pointer-events: auto;
}

/* Ensure all modal content is clickable */
.modal * {
    pointer-events: auto;
}
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 16px;
    font-weight: bold;
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}
</style>

<?= $this->endSection() ?>