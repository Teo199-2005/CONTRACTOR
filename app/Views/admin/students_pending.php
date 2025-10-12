<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history text-warning"></i> 
                        Pending Applications
                        <span class="badge bg-warning text-dark ms-2"><?= count($pendingStudents) ?></span>
                    </h5>
                    <div>
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
                        <div class="text-center py-5">
                            <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                            <h4 class="mt-3">No Pending Applications</h4>
                            <p class="text-muted">All student applications have been processed.</p>
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
                                                <?= date('M j, Y', strtotime($student['created_at'])) ?>
                                                <br><small class="text-muted"><?= date('g:i A', strtotime($student['created_at'])) ?></small>
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