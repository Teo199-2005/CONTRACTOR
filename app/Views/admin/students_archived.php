<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3">Archived Students</h1>
  <div>
    <a href="<?= base_url('admin/students') ?>" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Back to Students
    </a>
  </div>
</div>

<div class="alert alert-info">
  <i class="bi bi-info-circle"></i>
  <strong>Archived Students:</strong> These students have been archived and are no longer active in the system. 
  You can restore them to active status or permanently delete them from the system.
</div>

<div class="card">
  <div class="card-body p-0">
    <?php if (!empty($archivedStudents)): ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead>
            <tr>
              <th>LRN</th>
              <th>Name</th>
              <th>Grade</th>
              <th>Archived Date</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($archivedStudents as $st): ?>
              <tr>
                <td><?= esc($st['lrn'] ?? '—') ?></td>
                <td><?= esc($st['first_name'].' '.$st['last_name']) ?></td>
                <td>Grade <?= esc($st['grade_level']) ?></td>
                <td><?= date('M j, Y g:i A', strtotime($st['deleted_at'])) ?></td>
                <td class="text-end">
                  <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-success" onclick="restoreStudent(<?= $st['id'] ?>, '<?= esc($st['first_name'] . ' ' . $st['last_name']) ?>')" title="Restore Student">
                      <i class="bi bi-arrow-clockwise"></i> Restore
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteStudentPermanently(<?= $st['id'] ?>, '<?= esc($st['first_name'] . ' ' . $st['last_name']) ?>')" title="Delete Permanently">
                      <i class="bi bi-trash"></i> Delete
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="p-4 text-center text-muted">
        <i class="bi bi-archive" style="font-size: 3rem; opacity: 0.3;"></i>
        <h5 class="mt-3">No Archived Students</h5>
        <p>There are currently no archived students in the system.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
// Restore student function
function restoreStudent(studentId, studentName) {
  if (confirm(`Are you sure you want to restore student "${studentName}"? They will be moved back to active students.`)) {
    fetch(`<?= base_url('admin/students/restore') ?>/${studentId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert(data.message);
        location.reload();
      } else {
        alert('Error: ' + data.error);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to restore student');
    });
  }
}

// Delete student permanently function
function deleteStudentPermanently(studentId, studentName) {
  if (confirm(`Are you sure you want to PERMANENTLY DELETE student "${studentName}"? This action cannot be undone and will remove all their data from the system.`)) {
    if (confirm(`This is your final warning. Permanently deleting "${studentName}" will remove all their records, grades, and documents. Are you absolutely sure?`)) {
      fetch(`<?= base_url('admin/students/delete-permanently') ?>/${studentId}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          location.reload();
        } else {
          alert('Error: ' + data.error);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Failed to delete student permanently');
      });
    }
  }
}
</script>

<?= $this->endSection() ?>