<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">My Students</h1>
    <div class="d-flex gap-2 align-items-center">
        <span class="badge bg-primary"><?= count($students) ?> Students</span>
        <?php if (!empty($students)): ?>
            <button class="btn btn-success btn-sm" onclick="sendAllReportCards()">
                <i class="bi bi-send"></i> Send All Report Cards
            </button>
        <?php endif; ?>
    </div>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><strong><?= esc($student['lrn']) ?></strong></td>
                                <td><?= esc($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                <td>Grade <?= esc($student['grade_level']) ?></td>
                                <td><?= esc($student['section_name'] ?? 'No Section') ?></td>
                                <td>
                                    <span class="badge bg-success">Enrolled</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('teacher/report-card/' . $student['id']) ?>" class="btn btn-outline-primary btn-sm" title="Generate Report Card" target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i> Report Card
                                        </a>
                                        <button class="btn btn-success btn-sm" onclick="sendReportCard(<?= $student['id'] ?>, '<?= esc($student['first_name'] . ' ' . $student['last_name']) ?>')" title="Send Report Card to Student">
                                            <i class="bi bi-send"></i> Send
                                        </button>
                                    </div>
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

<script>
function sendReportCard(studentId, studentName) {
    if (confirm(`Send report card notification to ${studentName}?`)) {
        // Here you would make an AJAX call to notify the student
        // For now, we'll simulate the action
        fetch('<?= base_url('teacher/send-report-card') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                student_id: studentId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Report card notification sent to ${studentName}!`);
            } else {
                alert('Failed to send notification. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}

function sendAllReportCards() {
    if (confirm('Send report card notifications to all students in your class?')) {
        fetch('<?= base_url('teacher/send-all-report-cards') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Report card notifications sent to all ${data.count} students!`);
            } else {
                alert('Failed to send notifications. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}
</script>

<?= $this->endSection() ?> 