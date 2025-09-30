<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<style>
.blue-divider {
  height: 3px;
  background: linear-gradient(90deg, #007bff, #0056b3);
  border-radius: 2px;
  margin: 1rem 0;
}

.form-floating textarea {
  min-height: 120px;
}

.preview-card {
  background: #f8f9fa;
  border: 1px dashed #dee2e6;
  border-radius: 8px;
}
</style>

<!-- Header Section -->
<div class="dashboard-header mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 fw-bold text-primary mb-1">Create Announcement</h1>
      <p class="text-muted mb-0 small">Create and publish announcements to your community</p>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= base_url('admin/announcements') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Back to List
      </a>
    </div>
  </div>
  
  <!-- Blue Divider -->
  <div class="blue-divider"></div>
</div>

<?php if ($errors = session('errors')): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="row">
  <!-- Form Section -->
  <div class="col-lg-8">
    <div class="card bg-white border-0 shadow-sm rounded-3">
      <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">
          <i class="bi bi-plus-circle me-2"></i>New Announcement
        </h5>
      </div>
      <div class="card-body p-4">
        <form method="post" action="<?= base_url('admin/announcements/store') ?>" id="announcementForm">
          <?= csrf_field() ?>
          
          <div class="row g-3">
            <div class="col-12">
              <div class="form-floating">
                <input type="text" class="form-control" id="title" name="title" 
                       placeholder="Enter announcement title" required 
                       value="<?= old('title') ?>" maxlength="255">
                <label for="title">Announcement Title *</label>
              </div>
              <div class="form-text">
                <span id="titleCounter">0/255</span> characters
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-floating">
                <select class="form-select" id="target_roles" name="target_roles" required>
                  <option value="">Select Target Audience</option>
                  <option value="all" <?= old('target_roles') === 'all' ? 'selected' : '' ?>>All Users</option>
                  <option value="admin" <?= old('target_roles') === 'admin' ? 'selected' : '' ?>>Administrators</option>
                  <option value="teacher" <?= old('target_roles') === 'teacher' ? 'selected' : '' ?>>Teachers</option>
                  <option value="student" <?= old('target_roles') === 'student' ? 'selected' : '' ?>>Students</option>
                  <option value="parent" <?= old('target_roles') === 'parent' ? 'selected' : '' ?>>Parents</option>
                </select>
                <label for="target_roles">Target Audience *</label>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-floating">
                <select class="form-select" id="priority" name="priority">
                  <option value="normal">Normal</option>
                  <option value="high">High Priority</option>
                  <option value="urgent">Urgent</option>
                </select>
                <label for="priority">Priority Level</label>
              </div>
            </div>
            
            <div class="col-12">
              <div class="form-floating">
                <textarea class="form-control" id="body" name="body" 
                          placeholder="Enter announcement content" required 
                          style="min-height: 150px;"><?= old('body') ?></textarea>
                <label for="body">Announcement Content *</label>
              </div>
              <div class="form-text">
                Use clear and concise language. This will be visible to your selected audience.
              </div>
            </div>
          </div>
          
          <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-success">
              <i class="bi bi-check-circle me-2"></i>Publish Announcement
            </button>
            <button type="button" class="btn btn-outline-info" onclick="showPreview()">
              <i class="bi bi-eye me-2"></i>Preview
            </button>
            <a href="<?= base_url('admin/announcements') ?>" class="btn btn-outline-secondary">
              <i class="bi bi-x me-2"></i>Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Preview Section -->
  <div class="col-lg-4">
    <div class="card bg-white border-0 shadow-sm rounded-3">
      <div class="card-header bg-info text-white">
        <h5 class="card-title mb-0">
          <i class="bi bi-eye me-2"></i>Live Preview
        </h5>
      </div>
      <div class="card-body">
        <div id="previewContent" class="preview-card p-3">
          <div class="text-muted text-center">
            <i class="bi bi-eye-slash display-6"></i>
            <p class="mt-2 mb-0">Start typing to see preview</p>
          </div>
        </div>
        
        <div class="mt-3">
          <h6 class="fw-semibold">Tips for Great Announcements:</h6>
          <ul class="small text-muted">
            <li>Keep titles clear and descriptive</li>
            <li>Use simple, easy-to-understand language</li>
            <li>Include important dates and deadlines</li>
            <li>Choose the right target audience</li>
            <li>Proofread before publishing</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
// Character counter for title
document.getElementById('title').addEventListener('input', function() {
  const length = this.value.length;
  const counter = document.getElementById('titleCounter');
  counter.textContent = `${length}/255`;
  counter.className = length > 240 ? 'text-warning' : length > 250 ? 'text-danger' : 'text-muted';
  
  updatePreview();
});

// Update preview on content change
document.getElementById('body').addEventListener('input', updatePreview);
document.getElementById('target_roles').addEventListener('change', updatePreview);

function updatePreview() {
  const title = document.getElementById('title').value;
  const body = document.getElementById('body').value;
  const target = document.getElementById('target_roles').value;
  const previewContent = document.getElementById('previewContent');
  
  if (!title && !body) {
    previewContent.innerHTML = `
      <div class="text-muted text-center">
        <i class="bi bi-eye-slash display-6"></i>
        <p class="mt-2 mb-0">Start typing to see preview</p>
      </div>
    `;
    return;
  }
  
  const targetBadge = target ? `<span class="badge bg-secondary mb-2">${target}</span>` : '';
  const titleHtml = title ? `<h5 class="fw-bold text-primary">${escapeHtml(title)}</h5>` : '';
  const bodyHtml = body ? `<p class="mb-0">${escapeHtml(body).replace(/\n/g, '<br>')}</p>` : '';
  
  previewContent.innerHTML = `
    ${targetBadge}
    ${titleHtml}
    ${bodyHtml}
    <small class="text-muted d-block mt-2">
      <i class="bi bi-calendar me-1"></i>
      ${new Date().toLocaleDateString()}
    </small>
  `;
}

function showPreview() {
  updatePreview();
  document.getElementById('previewContent').scrollIntoView({ behavior: 'smooth' });
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}

// Form validation
document.getElementById('announcementForm').addEventListener('submit', function(e) {
  const title = document.getElementById('title').value.trim();
  const body = document.getElementById('body').value.trim();
  const target = document.getElementById('target_roles').value;
  
  if (!title || !body || !target) {
    e.preventDefault();
    alert('Please fill in all required fields.');
    return false;
  }
  
  if (title.length > 255) {
    e.preventDefault();
    alert('Title must be 255 characters or less.');
    return false;
  }
  
  // Confirm before publishing
  if (!confirm(`Are you sure you want to publish this announcement to ${target}?`)) {
    e.preventDefault();
    return false;
  }
});

// Initialize preview
updatePreview();
</script>

<?= $this->endSection() ?>
