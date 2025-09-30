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

.original-content {
  background: #e3f2fd;
  border-left: 4px solid #2196f3;
  padding: 1rem;
  border-radius: 4px;
}
</style>

<!-- Header Section -->
<div class="dashboard-header mb-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3 fw-bold text-primary mb-1">Edit Announcement</h1>
      <p class="text-muted mb-0 small">Modify and update announcement content</p>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= base_url('admin/announcements/show/' . $announcement['id']) ?>" class="btn btn-outline-info btn-sm">
        <i class="bi bi-eye me-2"></i>View
      </a>
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

<!-- Original Content Reference -->
<div class="card bg-white border-0 shadow-sm rounded-3 mb-4">
  <div class="card-header bg-info text-white">
    <h5 class="card-title mb-0">
      <i class="bi bi-info-circle me-2"></i>Original Announcement
    </h5>
  </div>
  <div class="card-body">
    <div class="original-content">
      <h6 class="fw-bold text-primary"><?= esc($announcement['title']) ?></h6>
      <p class="mb-2"><?= nl2br(esc($announcement['body'])) ?></p>
      <div class="d-flex gap-2">
        <span class="badge bg-secondary"><?= esc($announcement['target_roles']) ?></span>
        <span class="badge bg-success">Published</span>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Form Section -->
  <div class="col-lg-8">
    <div class="card bg-white border-0 shadow-sm rounded-3">
      <div class="card-header bg-warning text-dark">
        <h5 class="card-title mb-0">
          <i class="bi bi-pencil me-2"></i>Edit Announcement
        </h5>
      </div>
      <div class="card-body p-4">
        <form method="post" action="<?= base_url('admin/announcements/update/' . $announcement['id']) ?>" id="editAnnouncementForm">
          <?= csrf_field() ?>
          
          <div class="row g-3">
            <div class="col-12">
              <div class="form-floating">
                <input type="text" class="form-control" id="title" name="title" 
                       placeholder="Enter announcement title" required 
                       value="<?= old('title', $announcement['title']) ?>" maxlength="255">
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
                  <option value="all" <?= old('target_roles', $announcement['target_roles']) === 'all' ? 'selected' : '' ?>>All Users</option>
                  <option value="admin" <?= old('target_roles', $announcement['target_roles']) === 'admin' ? 'selected' : '' ?>>Administrators</option>
                  <option value="teacher" <?= old('target_roles', $announcement['target_roles']) === 'teacher' ? 'selected' : '' ?>>Teachers</option>
                  <option value="student" <?= old('target_roles', $announcement['target_roles']) === 'student' ? 'selected' : '' ?>>Students</option>
                  <option value="parent" <?= old('target_roles', $announcement['target_roles']) === 'parent' ? 'selected' : '' ?>>Parents</option>
                </select>
                <label for="target_roles">Target Audience *</label>
              </div>
            </div>
            

            
            <div class="col-12">
              <div class="form-floating">
                <textarea class="form-control" id="body" name="body" 
                          placeholder="Enter announcement content" required 
                          style="min-height: 150px;"><?= old('body', $announcement['body']) ?></textarea>
                <label for="body">Announcement Content *</label>
              </div>
              <div class="form-text">
                Use clear and concise language. This will be visible to your selected audience.
              </div>
            </div>
          </div>
          
          <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-warning">
              <i class="bi bi-check-circle me-2"></i>Update Announcement
            </button>
            <button type="button" class="btn btn-outline-info" onclick="showPreview()">
              <i class="bi bi-eye me-2"></i>Preview
            </button>
            <a href="<?= base_url('admin/announcements/show/' . $announcement['id']) ?>" class="btn btn-outline-secondary">
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
      <div class="card-header bg-success text-white">
        <h5 class="card-title mb-0">
          <i class="bi bi-eye me-2"></i>Updated Preview
        </h5>
      </div>
      <div class="card-body">
        <div id="previewContent" class="preview-card p-3">
          <!-- Preview will be populated by JavaScript -->
        </div>
        
        <div class="mt-3">
          <h6 class="fw-semibold">Current Status:</h6>
          <div class="d-flex gap-2 mb-3">
            <span class="badge bg-success">Published</span>
            <small class="text-muted align-self-center">
              <?= date('M j, Y', strtotime($announcement['published_at'])) ?>
            </small>
          </div>
          
          <h6 class="fw-semibold">Editing Tips:</h6>
          <ul class="small text-muted">
            <li>Review changes carefully before updating</li>
            <li>Consider the impact on your audience</li>
            <li>Use "Preview" to see how it will look</li>
            <li>Published announcements are immediately visible</li>
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
  
  const targetBadge = target ? `<span class="badge bg-secondary mb-2">${target}</span>` : '';
  const titleHtml = title ? `<h5 class="fw-bold text-primary">${escapeHtml(title)}</h5>` : '<h5 class="text-muted">No title</h5>';
  const bodyHtml = body ? `<p class="mb-0">${escapeHtml(body).replace(/\n/g, '<br>')}</p>` : '<p class="text-muted">No content</p>';
  
  previewContent.innerHTML = `
    ${targetBadge}
    ${titleHtml}
    ${bodyHtml}
    <small class="text-muted d-block mt-2">
      <i class="bi bi-calendar me-1"></i>
      Updated: ${new Date().toLocaleDateString()}
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
document.getElementById('editAnnouncementForm').addEventListener('submit', function(e) {
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
  
  // Confirm before updating
  if (!confirm(`Are you sure you want to update this announcement for ${target}?`)) {
    e.preventDefault();
    return false;
  }
});

// Initialize preview and character counter
document.addEventListener('DOMContentLoaded', function() {
  updatePreview();
  
  // Update character counter
  const titleInput = document.getElementById('title');
  const event = new Event('input');
  titleInput.dispatchEvent(event);
});
</script>

<?= $this->endSection() ?>
