<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">✏️ Edit & Resend Notification</h1>
  <div class="d-flex gap-2">
    <a href="<?= base_url('admin/notifications/show/' . $notification['id']) ?>" class="btn btn-outline-info btn-sm">
      <i class="bi bi-eye me-2"></i>View Details
    </a>
    <a href="<?= base_url('admin/notifications') ?>" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-2"></i>Back to List
    </a>
  </div>
</div>

<?php if ($errors = session('errors')): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
<?php if ($success = session('success')): ?>
  <div class="alert alert-success"><?= esc($success) ?></div>
<?php endif; ?>

<!-- Original Notification Info -->
<div class="card border-0 shadow-sm mb-4">
  <div class="card-header bg-info text-white">
    <h5 class="card-title mb-0">
      <i class="bi bi-info-circle me-2"></i>Original Notification
    </h5>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-md-6">
        <strong>Title:</strong> <?= esc($notification['title']) ?>
      </div>
      <div class="col-md-6">
        <strong>Sent:</strong> <?= date('M j, Y g:i A', strtotime($notification['created_at'])) ?>
      </div>
      <div class="col-12 mt-2">
        <strong>Message:</strong>
        <div class="bg-light p-2 rounded mt-1">
          <?= nl2br(esc($notification['message'])) ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Form -->
<div class="card border-0 shadow-sm">
  <div class="card-header bg-warning text-dark">
    <h5 class="card-title mb-0">
      <i class="bi bi-pencil me-2"></i>Edit & Resend Notification
    </h5>
  </div>
  <div class="card-body">
    <div class="alert alert-warning">
      <i class="bi bi-exclamation-triangle me-2"></i>
      <strong>Note:</strong> This will send a new notification to the selected audience. The original notification will remain unchanged.
    </div>
    
    <form method="post" action="<?= base_url('admin/notifications/update/' . $notification['id']) ?>" id="editNotificationForm">
      <?= csrf_field() ?>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Target Audience</label>
          <select name="target" class="form-select" required>
            <option value="">Select Target</option>
            <option value="all">All Users</option>
            <option value="admin">Admins</option>
            <option value="teacher">Teachers</option>
            <option value="student">Students</option>
            <option value="parent">Parents</option>
          </select>
          <small class="text-muted">Choose who should receive the updated notification</small>
        </div>
        <div class="col-md-8">
          <label class="form-label fw-semibold">Title</label>
          <input type="text" class="form-control" name="title" value="<?= esc($notification['title']) ?>" required />
          <small class="text-muted">Edit the notification title if needed</small>
        </div>
        <div class="col-12">
          <label class="form-label fw-semibold">Message</label>
          <textarea class="form-control" name="message" rows="6" required><?= esc($notification['message']) ?></textarea>
          <small class="text-muted">Edit the notification message</small>
        </div>
      </div>
      
      <div class="mt-4 d-flex gap-2">
        <button class="btn btn-warning" type="submit">
          <i class="bi bi-send me-2"></i>Send Updated Notification
        </button>
        <button class="btn btn-outline-secondary" type="button" onclick="resetForm()">
          <i class="bi bi-arrow-clockwise me-2"></i>Reset to Original
        </button>
        <a href="<?= base_url('admin/notifications') ?>" class="btn btn-outline-danger">
          <i class="bi bi-x me-2"></i>Cancel
        </a>
      </div>
    </form>
  </div>
</div>

<script>
// Reset form to original values
function resetForm() {
  document.querySelector('input[name="title"]').value = '<?= esc($notification['title']) ?>';
  document.querySelector('textarea[name="message"]').value = '<?= esc($notification['message']) ?>';
  document.querySelector('select[name="target"]').value = '';
}

// Form validation
document.getElementById('editNotificationForm').addEventListener('submit', function(e) {
  const target = document.querySelector('select[name="target"]').value;
  const title = document.querySelector('input[name="title"]').value.trim();
  const message = document.querySelector('textarea[name="message"]').value.trim();
  
  if (!target || !title || !message) {
    e.preventDefault();
    alert('Please fill in all required fields.');
    return false;
  }
  
  if (title.length > 255) {
    e.preventDefault();
    alert('Title must be 255 characters or less.');
    return false;
  }
  
  // Confirm before sending
  const userCount = getUserCountEstimate(target);
  if (!confirm(`Are you sure you want to send this updated notification to ${userCount}?`)) {
    e.preventDefault();
    return false;
  }
});

// Estimate user count for confirmation
function getUserCountEstimate(target) {
  const estimates = {
    'all': 'all users',
    'admin': 'all administrators',
    'teacher': 'all teachers',
    'student': 'all students',
    'parent': 'all parents'
  };
  return estimates[target] || 'selected users';
}

// Character counter for title
document.querySelector('input[name="title"]').addEventListener('input', function() {
  const length = this.value.length;
  const maxLength = 255;
  const remaining = maxLength - length;
  
  // Find or create character counter
  let counter = this.parentNode.querySelector('.char-counter');
  if (!counter) {
    counter = document.createElement('small');
    counter.className = 'char-counter text-muted';
    this.parentNode.appendChild(counter);
  }
  
  counter.textContent = `${length}/${maxLength} characters`;
  counter.className = `char-counter ${remaining < 20 ? 'text-warning' : 'text-muted'}`;
});
</script>

<?= $this->endSection() ?>
