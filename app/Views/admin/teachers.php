<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3">Manage Teachers</h1>
  <div>
    <a href="<?= base_url('admin/teachers/create') ?>" class="btn btn-primary me-2">
      <i class="bi bi-plus-circle"></i> Add New Teacher
    </a>
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary">Back</a>
  </div>
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

<!-- Search Form -->
<form class="row g-2 mb-3" method="get">
  <div class="col-md-6">
    <label class="form-label">Search</label>
    <input type="text" name="search" class="form-control" placeholder="Name, Teacher ID, or Email"
           value="<?= esc($search ?? '') ?>">
  </div>
  <div class="col-auto d-flex align-items-end">
    <button type="submit" class="btn btn-primary me-2">Search</button>
    <a href="<?= base_url('admin/teachers') ?>" class="btn btn-outline-secondary">Clear</a>
  </div>
</form>

<div class="card">
  <div class="card-body p-0">
    <?php if (!empty($teachers)): ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead>
            <tr>
              <th>Teacher ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Department</th>
              <th>Position</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($teachers as $teacher): ?>
              <tr>
                <td><?= esc($teacher['teacher_id'] ?? '—') ?></td>
                <td><?= esc($teacher['first_name'] . ' ' . $teacher['last_name']) ?></td>
                <td><?= esc($teacher['email']) ?></td>
                <td><?= esc($teacher['department'] ?? '—') ?></td>
                <td><?= esc($teacher['position'] ?? '—') ?></td>
                <td>
                  <?php
                  $statusClass = match($teacher['employment_status'] ?? 'active') {
                    'active' => 'bg-success',
                    'inactive' => 'bg-danger',
                    'on_leave' => 'bg-warning',
                    default => 'bg-secondary'
                  };
                  ?>
                  <span class="badge <?= $statusClass ?>">
                    <?= ucfirst(str_replace('_', ' ', $teacher['employment_status'] ?? 'active')) ?>
                  </span>
                </td>
                <td class="text-end">
                  <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="openTeacherModal(<?= $teacher['id'] ?>)" title="View Details">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="openEditTeacherModal(<?= $teacher['id'] ?>)" title="Edit Teacher">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteTeacher(<?= $teacher['id'] ?>, '<?= esc($teacher['first_name'] . ' ' . $teacher['last_name']) ?>')" title="Delete Teacher">
                      <i class="bi bi-trash"></i>
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
        <i class="bi bi-person-x display-1 text-muted"></i>
        <p class="mt-3">No teachers found.</p>
        <a href="<?= base_url('admin/teachers/create') ?>" class="btn btn-primary">
          <i class="bi bi-plus-circle"></i> Add First Teacher
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Edit Teacher Modal -->
<div id="editTeacherModal" class="custom-modal-overlay" style="display: none;">
  <div class="custom-modal-container">
    <div class="custom-modal-header">
      <h3 class="custom-modal-title">Edit Teacher</h3>
      <button type="button" class="custom-modal-close" onclick="closeEditTeacherModal()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <form id="editTeacherForm" method="post">
      <div class="custom-modal-body">
        <?= csrf_field() ?>
        <div id="editTeacherContent">
          <div class="loading-spinner">
            <div class="spinner"></div>
            <p>Loading teacher details...</p>
          </div>
        </div>
      </div>
      <div class="custom-modal-footer">
        <button type="submit" class="btn btn-primary">Update Teacher</button>
      </div>
    </form>
  </div>
</div>

<!-- Custom Teacher Details Modal -->
<div id="customTeacherModal" class="custom-modal-overlay" style="display: none;">
  <div class="custom-modal-container">
    <div class="custom-modal-header">
      <h3 class="custom-modal-title">Teacher Details</h3>
      <button type="button" class="custom-modal-close" onclick="closeTeacherModal()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="custom-modal-body">
      <div id="customTeacherDetails">
        <div class="loading-spinner">
          <div class="spinner"></div>
          <p>Loading teacher details...</p>
        </div>
      </div>
    </div>
    <div class="custom-modal-footer">
      <button type="button" class="btn btn-danger" onclick="closeTeacherModal()">Close</button>
    </div>
  </div>
</div>

<style>
/* Custom Modal Styles */
.custom-modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  backdrop-filter: blur(4px);
  animation: fadeIn 0.3s ease-out;
}

.custom-modal-container {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
  max-width: 900px;
  width: 90%;
  max-height: 90vh;
  overflow: hidden;
  position: relative;
  animation: slideIn 0.3s ease-out;
}

.custom-modal-header {
  background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
  color: #ffffff !important;
  padding: 1.5rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 3px solid #1e40af;
}

.custom-modal-header * {
  color: #ffffff !important;
}

.custom-modal-title {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 700;
  color: #ffffff !important;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
}

.custom-modal-header h3 {
  color: #ffffff !important;
}

.custom-modal-close {
  background: rgba(255, 255, 255, 0.2);
  border: 2px solid rgba(255, 255, 255, 0.3);
  color: #ffffff !important;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 1.2rem;
}

.custom-modal-close:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: rgba(255, 255, 255, 0.5);
  color: #ffffff !important;
  transform: scale(1.1);
}

.custom-modal-close i {
  color: #ffffff !important;
}

.custom-modal-body {
  padding: 2rem;
  max-height: 60vh;
  overflow-y: auto;
  background: #f8fafc;
}

.custom-modal-footer {
  background: #f1f5f9;
  padding: 1.5rem 2rem;
  border-top: 2px solid #e2e8f0;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

/* Loading Spinner */
.loading-spinner {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem;
  color: #6b7280;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e5e7eb;
  border-top: 4px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

/* Animations */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-50px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
  .custom-modal-container {
    width: 95%;
    margin: 1rem;
  }

  .custom-modal-header,
  .custom-modal-body,
  .custom-modal-footer {
    padding: 1rem;
  }

  .custom-modal-title {
    font-size: 1.2rem;
  }
}

/* Teacher Info Styles */
.teacher-info-section {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  border: 2px solid #e2e8f0;
}

.teacher-info-title {
  color: #1e40af;
  font-size: 1.1rem;
  font-weight: 700;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #e2e8f0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.teacher-info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.teacher-info-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.teacher-info-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
}

.teacher-info-value {
  font-size: 1rem;
  color: #111827;
  font-weight: 500;
}

.status-badge {
  display: inline-flex;
  padding: 0.375rem 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
}

.status-active {
  background-color: #d1fae5;
  color: #065f46;
}
</style>

<script>
// Store teacher data for fallback
const teachersData = <?= json_encode($teachers ?? []) ?>;

// Open custom teacher modal
function openTeacherModal(teacherId) {
  const modal = document.getElementById('customTeacherModal');
  const detailsContainer = document.getElementById('customTeacherDetails');

  // Show modal
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';

  // Show loading state
  detailsContainer.innerHTML = `
    <div class="loading-spinner">
      <div class="spinner"></div>
      <p>Loading teacher details...</p>
    </div>
  `;

  // Try to load from server, fallback to local data
  const url = `<?= base_url('admin/teachers') ?>/details/${teacherId}`;
  console.log('Fetching teacher details from:', url);
  
  fetch(url)
    .then(response => {
      console.log('Response status:', response.status);
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }
      return response.text();
    })
    .then(html => {
      console.log('Received HTML response');
      detailsContainer.innerHTML = html;
    })
    .catch(error => {
      console.error('Fetch error:', error);
      // Fallback to local teacher data
      const teacher = teachersData.find(t => t.id == teacherId);
      if (teacher) {
        showBasicTeacherDetails(teacher, detailsContainer);
      } else {
        detailsContainer.innerHTML = `
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i>
            Teacher not found. Please try again.
          </div>
        `;
      }
    });
}

// Fallback function to show basic teacher details
function showBasicTeacherDetails(teacher, container) {
  const html = `
    <div class="teacher-info-section">
      <div class="teacher-info-title">
        <i class="bi bi-person"></i>
        Personal Information
      </div>
      <div class="teacher-info-grid">
        <div class="teacher-info-item">
          <div class="teacher-info-label">Full Name</div>
          <div class="teacher-info-value">${teacher.first_name} ${teacher.last_name}</div>
        </div>
        <div class="teacher-info-item">
          <div class="teacher-info-label">Teacher ID</div>
          <div class="teacher-info-value">${teacher.teacher_id || 'N/A'}</div>
        </div>
        <div class="teacher-info-item">
          <div class="teacher-info-label">Email</div>
          <div class="teacher-info-value">${teacher.email || 'N/A'}</div>
        </div>
        <div class="teacher-info-item">
          <div class="teacher-info-label">Department</div>
          <div class="teacher-info-value">${teacher.department || 'N/A'}</div>
        </div>
        <div class="teacher-info-item">
          <div class="teacher-info-label">Position</div>
          <div class="teacher-info-value">${teacher.position || 'N/A'}</div>
        </div>
        <div class="teacher-info-item">
          <div class="teacher-info-label">Status</div>
          <div class="teacher-info-value">
            <span class="status-badge status-${teacher.employment_status || 'active'}">
              ${(teacher.employment_status || 'active').replace('_', ' ').toUpperCase()}
            </span>
          </div>
        </div>
      </div>
    </div>
  `;
  container.innerHTML = html;
}

// Close custom teacher modal
function closeTeacherModal() {
  const modal = document.getElementById('customTeacherModal');
  modal.style.display = 'none';
  document.body.style.overflow = ''; // Restore scrolling
}

// Open edit teacher modal
function openEditTeacherModal(teacherId) {
  const modal = document.getElementById('editTeacherModal');
  const form = document.getElementById('editTeacherForm');
  const content = document.getElementById('editTeacherContent');
  
  // Set form action
  form.action = `<?= base_url('admin/teachers/update') ?>/${teacherId}`;
  
  // Show modal
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
  
  // Show loading
  content.innerHTML = `
    <div class="loading-spinner">
      <div class="spinner"></div>
      <p>Loading teacher details...</p>
    </div>
  `;
  
  // Load teacher data
  fetch(`<?= base_url('admin/teachers/edit-form') ?>/${teacherId}`)
    .then(response => response.text())
    .then(html => {
      content.innerHTML = html;
    })
    .catch(error => {
      console.error('Error:', error);
      content.innerHTML = '<div class="alert alert-danger">Failed to load teacher data</div>';
    });
}

// Close edit teacher modal
function closeEditTeacherModal() {
  const modal = document.getElementById('editTeacherModal');
  modal.style.display = 'none';
  document.body.style.overflow = '';
}



// Handle edit form submission
document.addEventListener('DOMContentLoaded', function() {
  const editForm = document.getElementById('editTeacherForm');
  editForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        closeEditTeacherModal();
        location.reload();
      } else {
        // Show validation errors
        if (data.errors) {
          let errorHtml = '<div class="alert alert-danger"><ul class="mb-0">';
          for (let field in data.errors) {
            errorHtml += `<li>${data.errors[field]}</li>`;
          }
          errorHtml += '</ul></div>';
          document.getElementById('editTeacherContent').insertAdjacentHTML('afterbegin', errorHtml);
        }
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to update teacher');
    });
  });
});

// Delete teacher function
function deleteTeacher(teacherId, teacherName) {
  if (confirm(`Are you sure you want to delete teacher "${teacherName}"? This action cannot be undone.`)) {
    fetch(`<?= base_url('admin/teachers/delete') ?>/${teacherId}`, {
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
      alert('Failed to delete teacher');
    });
  }
}
</script>

<?= $this->endSection() ?>