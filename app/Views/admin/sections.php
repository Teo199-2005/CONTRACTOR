<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3">Manage Sections & Faculty Assignment</h1>
  <div>
    <button class="btn btn-primary me-2" onclick="showCreateSection()">
      <i class="bi bi-plus-circle"></i> Create Section
    </button>
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary">Back</a>
  </div>
</div>

<!-- Summary Statistics -->
<div class="card mb-4">
  <div class="card-body">
    <div class="row text-center">
      <div class="col-3">
        <div class="d-flex align-items-center justify-content-center">
          <div class="text-primary me-3">
            <i class="bi bi-building fs-2"></i>
          </div>
          <div>
            <h4 class="mb-0"><?= count($sections) ?></h4>
            <small class="text-muted">Total Sections</small>
          </div>
        </div>
      </div>
      <div class="col-3">
        <div class="d-flex align-items-center justify-content-center">
          <div class="text-success me-3">
            <i class="bi bi-person-check fs-2"></i>
          </div>
          <div>
            <h4 class="mb-0"><?= count(array_filter($sections, fn($s) => !empty($s['adviser_name']))) ?></h4>
            <small class="text-muted">With Advisers</small>
          </div>
        </div>
      </div>
      <div class="col-3">
        <div class="d-flex align-items-center justify-content-center">
          <div class="text-warning me-3">
            <i class="bi bi-person-x fs-2"></i>
          </div>
          <div>
            <h4 class="mb-0"><?= count(array_filter($sections, fn($s) => empty($s['adviser_name']))) ?></h4>
            <small class="text-muted">Need Advisers</small>
          </div>
        </div>
      </div>
      <div class="col-3">
        <div class="d-flex align-items-center justify-content-center">
          <div class="text-info me-3">
            <i class="bi bi-people fs-2"></i>
          </div>
          <div>
            <h4 class="mb-0"><?= array_sum(array_column($sections, 'max_capacity')) ?></h4>
            <small class="text-muted">Total Capacity</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Filter Options -->
<form class="row g-2 mb-3" method="get">
  <div class="col-auto">
    <label class="form-label">Grade Level</label>
    <select name="grade" class="form-select" onchange="this.form.submit()">
      <option value="">All Grades</option>
      <?php for ($g = 7; $g <= 12; $g++): ?>
        <option value="<?= $g ?>" <?= (($gradeFilter ?? '') == $g ? 'selected' : '') ?>>Grade <?= $g ?></option>
      <?php endfor; ?>
    </select>
  </div>
  <div class="col-auto">
    <label class="form-label">Adviser Status</label>
    <select name="adviser_status" class="form-select" onchange="this.form.submit()">
      <option value="">All Sections</option>
      <option value="with_adviser" <?= (($adviserFilter ?? '') == 'with_adviser' ? 'selected' : '') ?>>With Adviser</option>
      <option value="no_adviser" <?= (($adviserFilter ?? '') == 'no_adviser' ? 'selected' : '') ?>>Need Adviser</option>
    </select>
  </div>
  <div class="col-auto">
    <label class="form-label">Search</label>
    <input type="text" name="search" class="form-control" value="<?= esc($searchTerm ?? '') ?>" placeholder="Section name or teacher">
  </div>
  <div class="col-auto align-self-end">
    <button class="btn btn-primary">Filter</button>
    <?php if (!empty($gradeFilter) || !empty($adviserFilter) || !empty($searchTerm)): ?>
      <a href="<?= base_url('admin/sections') ?>" class="btn btn-outline-secondary">Clear</a>
    <?php endif; ?>
  </div>
</form>

<!-- Sections Table -->
<?php if (!empty($sections)): ?>
  <?php
  // Group sections by grade level
  $sectionsByGrade = [];
  foreach ($sections as $section) {
    $sectionsByGrade[$section['grade_level']][] = $section;
  }
  ksort($sectionsByGrade);
  ?>

  <?php foreach ($sectionsByGrade as $gradeLevel => $gradeSections): ?>
    <div class="card mb-4">
      <div class="card-header">
        <h6 class="card-title mb-0 fw-semibold">
          <i class="bi bi-mortarboard"></i> Grade <?= $gradeLevel ?> Sections
          <span class="badge bg-secondary ms-2"><?= count($gradeSections) ?> sections</span>
        </h6>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover mb-0">
            <thead>
              <tr>
                <th>Section Name</th>
                <th>Enrollment</th>
                <th>Capacity</th>
                <th>Section Adviser</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($gradeSections as $section): ?>
                <tr>
                  <td>
                    <div class="fw-semibold"><?= esc($section['section_name']) ?></div>
                    <small class="text-muted">School Year: <?= esc($section['school_year']) ?></small>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <span class="me-2"><?= $section['current_enrollment'] ?>/<?= $section['max_capacity'] ?></span>
                      <div class="progress" style="width: 60px; height: 8px;">
                        <?php
                        $percentage = $section['max_capacity'] > 0 ? ($section['current_enrollment'] / $section['max_capacity']) * 100 : 0;
                        $progressClass = $percentage >= 90 ? 'bg-danger' : ($percentage >= 70 ? 'bg-warning' : 'bg-success');
                        ?>
                        <div class="progress-bar <?= $progressClass ?>" style="width: <?= $percentage ?>%"></div>
                      </div>
                    </div>
                  </td>
                  <td><?= $section['max_capacity'] ?> students</td>
                  <td>
                    <?php if (!empty($section['adviser_name'])): ?>
                      <div class="fw-semibold text-success"><?= esc($section['adviser_name']) ?></div>
                      <?php if (!empty($section['adviser_email'])): ?>
                        <small class="text-muted"><?= esc($section['adviser_email']) ?></small>
                      <?php endif; ?>
                    <?php else: ?>
                      <span class="text-warning">
                        <i class="bi bi-exclamation-triangle"></i> No Adviser
                      </span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge bg-<?= $section['is_active'] ? 'success' : 'secondary' ?>">
                      <?= $section['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm" role="group">
                      <?php if (empty($section['adviser_name'])): ?>
                        <button class="btn btn-outline-success" onclick="assignAdviser(<?= $section['id'] ?>, '<?= esc($section['section_name']) ?>', <?= $gradeLevel ?>)" title="Assign Teacher">
                          <i class="bi bi-person-plus"></i>
                        </button>
                      <?php else: ?>
                        <button class="btn btn-outline-danger" onclick="removeAdviser(<?= $section['id'] ?>)" title="Remove Adviser">
                          <i class="bi bi-person-x"></i>
                        </button>
                      <?php endif; ?>
                      <button class="btn btn-outline-primary" onclick="viewSectionStudents(<?= $section['id'] ?>)" title="View Students">
                        <i class="bi bi-people"></i>
                      </button>
                      <button class="btn btn-outline-info" onclick="assignStudents(<?= $section['id'] ?>, '<?= esc($section['section_name']) ?>', <?= $section['grade_level'] ?>)" title="Assign Students">
                        <i class="bi bi-person-plus-fill"></i>
                      </button>
                      <button class="btn btn-outline-secondary" onclick="editSection(<?= $section['id'] ?>)" title="Edit Section">
                        <i class="bi bi-pencil"></i>
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
  <?php endforeach; ?>
<?php else: ?>
  <div class="card">
    <div class="card-body text-center py-5">
      <i class="bi bi-building fs-1 text-muted mb-3"></i>
      <h5 class="text-muted">No sections found</h5>
      <p class="text-muted">Create your first section to get started with class management.</p>
      <button class="btn btn-primary" onclick="showCreateSection()">
        <i class="bi bi-plus-circle"></i> Create First Section
      </button>
    </div>
  </div>
<?php endif; ?>

<!-- Assign Adviser Modal is generated dynamically and appended to <body> by JS -->

<!-- Section Students Modal is generated dynamically and appended to <body> by JS -->

<style>
/* Custom styling for assign students modal close button */
#assignStudentsModal .modal-footer .btn-danger {
  background-color: #dc3545 !important;
  border-color: #dc3545 !important;
  color: white !important;
}

#assignStudentsModal .modal-footer .btn-danger:hover {
  background-color: #c82333 !important;
  border-color: #c82333 !important;
  color: white !important;
}
</style>

<script>
// Store sections data for quick access
const sectionsData = <?= json_encode($sections) ?>;
const availableTeachers = <?= json_encode($availableTeachers ?? []) ?>;

function buildAssignAdviserModal({ sectionId, sectionName, gradeLevel }) {
  // Remove any previous instance
  const existing = document.getElementById('assignAdviserModal');
  if (existing) existing.remove();

  // Build modal HTML (Bootstrap 5)
  const modalHtml = `
  <div class="modal fade" id="assignAdviserModal" tabindex="-1" aria-labelledby="assignAdviserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="assignAdviserModalLabel">Assign Section Adviser</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="assignAdviserForm" method="post" action="<?= base_url('admin/sections/assign-adviser/') ?>${sectionId}">
          <?= csrf_field() ?>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-semibold">Section Information</label>
              <div class="p-3 bg-light rounded">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h6 class="mb-1">${sectionName}</h6>
                    <small class="text-muted">Grade ${gradeLevel} • School Year <?= date('Y') . '-' . (date('Y') + 1) ?></small>
                  </div>
                  <span class="badge bg-warning">No Adviser</span>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="teacherSelect" class="form-label fw-semibold">Select Teacher</label>
              <select name="adviser_id" id="teacherSelect" class="form-select" required>
                <option value="">Choose a teacher...</option>
                ${ (availableTeachers || []).map(t => `<option value="${t.id}">${t.first_name} ${t.last_name}${t.email ? ` (${t.email})` : ''}</option>`).join('') }
              </select>
              <div class="form-text">
                <i class="bi bi-info-circle"></i> Only teachers without current section assignments are shown.
              </div>
            </div>

            <div class="alert alert-info">
              <i class="bi bi-lightbulb"></i>
              <strong>Note:</strong> The assigned teacher will become the section adviser and will have access to manage students in this section.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-success">
              <i class="bi bi-person-check"></i> Assign Adviser
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>`;

  document.body.insertAdjacentHTML('beforeend', modalHtml);
  return document.getElementById('assignAdviserModal');
}

function assignAdviser(sectionId, sectionName, gradeLevel) {
  const modalEl = buildAssignAdviserModal({ sectionId, sectionName, gradeLevel });
  const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
  modal.show();
}


function removeAdviser(sectionId) {
  const section = sectionsData.find(s => s.id == sectionId);
  if (!section) {
    alert('Section not found.');
    return;
  }
  
  const modalEl = buildRemoveAdviserModal(section);
  const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
  modal.show();
}

function buildRemoveAdviserModal(section) {
  const existing = document.getElementById('removeAdviserModal');
  if (existing) existing.remove();

  const html = `
  <div class="modal fade" id="removeAdviserModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Remove Section Adviser</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Warning:</strong> This action will remove the adviser from this section.
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Section Information</label>
            <div class="p-3 bg-light rounded">
              <h6 class="mb-1">${section.section_name}</h6>
              <small class="text-muted">Grade ${section.grade_level} • ${section.school_year}</small>
              <br><small class="text-success">Current Adviser: ${section.adviser_name || 'Unknown'}</small>
            </div>
          </div>
          <p>Are you sure you want to remove the adviser from this section?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" onclick="confirmRemoveAdviser(${section.id})">
            <i class="bi bi-person-x me-1"></i> Remove Adviser
          </button>
        </div>
      </div>
    </div>
  </div>`;
  
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('removeAdviserModal');
}

function confirmRemoveAdviser(sectionId) {
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = `<?= base_url('admin/sections/remove-adviser/') ?>${sectionId}`;

  const csrfInput = document.createElement('input');
  csrfInput.type = 'hidden';
  csrfInput.name = '<?= csrf_token() ?>';
  csrfInput.value = '<?= csrf_hash() ?>';
  form.appendChild(csrfInput);

  document.body.appendChild(form);
  form.submit();
  
  bootstrap.Modal.getInstance(document.getElementById('removeAdviserModal')).hide();
}



function viewSectionStudents(sectionId) {
  const section = sectionsData.find(s => s.id == sectionId);

  if (!section) {
    alert('Section not found.');
    return;
  }

  // Build and show modal dynamically
  const modalEl = buildSectionStudentsModal(section.section_name);
  const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });

  // Show loading
  const listEl = modalEl.querySelector('#sectionStudentsList');
  listEl.innerHTML = `
    <div class="text-center">
      <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
      <p class="mt-2">Loading students...</p>
    </div>
  `;

  modal.show();

  // Fetch students via AJAX
  fetch(`<?= base_url('admin/sections/students/') ?>${sectionId}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        displaySectionStudents(data.students);
      } else {
        document.getElementById('sectionStudentsList').innerHTML = `
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i> ${data.message || 'Failed to load students'}
          </div>
        `;
      }
    })
    .catch(error => {
      document.getElementById('sectionStudentsList').innerHTML = `
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-triangle"></i> Error loading students: ${error.message}
        </div>
      `;
    });
}

function buildSectionStudentsModal(sectionName) {
  const existing = document.getElementById('sectionStudentsModal');
  if (existing) existing.remove();

  const html = `
  <div class="modal fade" id="sectionStudentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">${sectionName} Students</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="sectionStudentsList"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>
    </div></div>
  </div>`;
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('sectionStudentsModal');
}

function displaySectionStudents(students) {
  if (students.length === 0) {
    document.getElementById('sectionStudentsList').innerHTML = `
      <div class="text-center py-4">
        <i class="bi bi-people fs-1 text-muted mb-3"></i>
        <h6 class="text-muted">No students enrolled</h6>
        <p class="text-muted">This section doesn't have any students yet.</p>
      </div>
    `;
    return;
  }

  let studentsHtml = `
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Status</th>
            <th>Enrolled Date</th>
          </tr>
        </thead>
        <tbody>
  `;

  students.forEach(student => {
    studentsHtml += `
      <tr>
        <td>${student.student_id || 'N/A'}</td>
        <td>${student.first_name} ${student.last_name}</td>
        <td><span class="badge bg-success">Enrolled</span></td>
        <td>${student.created_at ? new Date(student.created_at).toLocaleDateString() : 'N/A'}</td>
      </tr>
    `;
  });

  studentsHtml += `
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      <small class="text-muted">Total: ${students.length} students</small>
    </div>
  `;

  document.getElementById('sectionStudentsList').innerHTML = studentsHtml;
}

function buildEditSectionModal(section) {
  const existing = document.getElementById('editSectionModal');
  if (existing) existing.remove();

  const html = `
  <div class="modal fade" id="editSectionModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Section</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" action="<?= base_url('admin/sections/update/') ?>${section.id}">
        <?= str_replace(["\n","\r"], '', csrf_field()) ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-semibold">Section Name</label>
            <input type="text" name="section_name" class="form-control" required value="${section.section_name || ''}">
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">Grade Level</label>
              <input type="number" class="form-control" value="${section.grade_level}" readonly>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold">School Year</label>
              <input type="text" class="form-control" value="${section.school_year}" readonly>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Max Capacity</label>
            <input type="number" name="max_capacity" class="form-control" min="1" required value="${section.max_capacity || 40}">
            <div class="form-text">Current: ${section.current_enrollment || 0} students</div>
          </div>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="isActiveSwitch" name="is_active" ${section.is_active ? 'checked' : ''}>
            <label class="form-check-label" for="isActiveSwitch">Active</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Changes</button>
        </div>
      </form>
    </div></div>
  </div>`;
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('editSectionModal');
}

function editSection(sectionId) {
  const section = sectionsData.find(s => s.id == sectionId);
  if (!section) { alert('Section not found.'); return; }
  const modalEl = buildEditSectionModal(section);
  const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
  modal.show();
}

// Handle assign adviser form submission
document.addEventListener('submit', function(e) {
  if (e.target.id === 'assignAdviserForm') {
    e.preventDefault();
    
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    const adviserId = form.querySelector('#teacherSelect').value;
    
    if (!adviserId) {
      alert('Please select a teacher to assign as adviser.');
      return;
    }
    
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Assigning...';
    submitBtn.disabled = true;
    
    const formData = new FormData(form);
    
    fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }
      return response.text();
    })
    .then(html => {
      // Check if response contains success or error message
      if (html.includes('alert-success') || html.includes('successfully')) {
        bootstrap.Modal.getInstance(document.getElementById('assignAdviserModal')).hide();
        showNotification('Teacher successfully assigned as section adviser!', 'success');
        setTimeout(() => location.reload(), 1000);
      } else if (html.includes('alert-danger') || html.includes('error')) {
        const errorMatch = html.match(/alert-danger[^>]*>([^<]+)</i);
        const errorMsg = errorMatch ? errorMatch[1].trim() : 'Failed to assign teacher as adviser.';
        showNotification(errorMsg, 'error');
      } else {
        // Fallback - reload page to show server response
        location.reload();
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showNotification('Network error occurred. Please try again.', 'error');
    })
    .finally(() => {
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    });
  }
});

// Show notification function
function showNotification(message, type = 'info') {
  const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
  const iconClass = type === 'success' ? 'bi-check-circle' : type === 'error' ? 'bi-exclamation-triangle' : 'bi-info-circle';
  
  const notification = document.createElement('div');
  notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
  notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
  notification.innerHTML = `
    <i class="bi ${iconClass} me-2"></i>${message}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  `;
  
  document.body.appendChild(notification);
  
  // Auto-remove after 5 seconds
  setTimeout(() => {
    if (notification.parentNode) {
      notification.remove();
    }
  }, 5000);
}

function assignStudents(sectionId, sectionName, gradeLevel) {
  const modalEl = buildAssignStudentsModal({ sectionId, sectionName, gradeLevel });
  const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
  
  // Show loading
  const listEl = modalEl.querySelector('#unassignedStudentsList');
  listEl.innerHTML = `
    <div class="text-center">
      <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
      <p class="mt-2">Loading unassigned students...</p>
    </div>
  `;
  
  modal.show();
  
  // Fetch unassigned students for this grade level
  fetch(`<?= base_url('admin/sections/unassigned-students/') ?>${gradeLevel}`)
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        displayUnassignedStudents(data.students, sectionId);
      } else {
        listEl.innerHTML = `
          <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> ${data.message || 'No unassigned students found for this grade level'}
          </div>
        `;
      }
    })
    .catch(error => {
      listEl.innerHTML = `
        <div class="alert alert-danger">
          <i class="bi bi-exclamation-triangle"></i> Error loading students: ${error.message}
        </div>
      `;
    });
}

function buildAssignStudentsModal({ sectionId, sectionName, gradeLevel }) {
  const existing = document.getElementById('assignStudentsModal');
  if (existing) existing.remove();
  
  const html = `
  <div class="modal fade" id="assignStudentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Assign Students to ${sectionName}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <div class="p-3 bg-light rounded">
            <h6 class="mb-1">${sectionName}</h6>
            <small class="text-muted">Grade ${gradeLevel} • Unassigned students only</small>
          </div>
        </div>
        <div id="unassignedStudentsList"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="assignSelectedStudents(${sectionId})">
          <i class="bi bi-person-check"></i> Assign Selected
        </button>
      </div>
    </div></div>
  </div>`;
  
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('assignStudentsModal');
}

function displayUnassignedStudents(students, sectionId) {
  const listEl = document.getElementById('unassignedStudentsList');
  
  if (students.length === 0) {
    listEl.innerHTML = `
      <div class="text-center py-4">
        <i class="bi bi-people fs-1 text-muted mb-3"></i>
        <h6 class="text-muted">No unassigned students</h6>
        <p class="text-muted">All students in this grade level are already assigned to sections.</p>
      </div>
    `;
    return;
  }
  
  let studentsHtml = `
    <div class="mb-3">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="selectAllStudents" onchange="toggleAllStudents()">
        <label class="form-check-label fw-semibold" for="selectAllStudents">
          Select All (${students.length} students)
        </label>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th width="50">Select</th>
            <th>LRN</th>
            <th>Name</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
  `;
  
  students.forEach(student => {
    studentsHtml += `
      <tr>
        <td>
          <div class="form-check">
            <input class="form-check-input student-checkbox" type="checkbox" value="${student.id}" id="student_${student.id}">
          </div>
        </td>
        <td>${student.lrn || 'N/A'}</td>
        <td>${student.first_name} ${student.last_name}</td>
        <td><span class="badge bg-warning">Unassigned</span></td>
      </tr>
    `;
  });
  
  studentsHtml += `
        </tbody>
      </table>
    </div>
  `;
  
  listEl.innerHTML = studentsHtml;
}

function toggleAllStudents() {
  const selectAll = document.getElementById('selectAllStudents');
  const checkboxes = document.querySelectorAll('.student-checkbox');
  
  checkboxes.forEach(checkbox => {
    checkbox.checked = selectAll.checked;
  });
}

function assignSelectedStudents(sectionId) {
  const selectedStudents = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb => cb.value);
  
  if (selectedStudents.length === 0) {
    showStyledAlert('Please select at least one student to assign.', 'warning');
    return;
  }
  
  showConfirmationModal(
    'Confirm Assignment',
    `Are you sure you want to assign ${selectedStudents.length} student(s) to this section?`,
    () => {
      // Send assignment request
      fetch(`<?= base_url('admin/sections/assign-students/') ?>${sectionId}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        },
        body: JSON.stringify({ student_ids: selectedStudents })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          bootstrap.Modal.getInstance(document.getElementById('assignStudentsModal')).hide();
          showNotification(data.message || 'Students assigned successfully!', 'success');
          setTimeout(() => location.reload(), 1000);
        } else {
          showNotification(data.message || 'Failed to assign students', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showNotification('Network error occurred. Please try again.', 'error');
      });
    }
  );
}

function showStyledAlert(message, type = 'info') {
  const alertClass = type === 'warning' ? 'alert-warning' : type === 'error' ? 'alert-danger' : 'alert-info';
  const iconClass = type === 'warning' ? 'bi-exclamation-triangle' : type === 'error' ? 'bi-x-circle' : 'bi-info-circle';
  
  const modalHtml = `
  <div class="modal fade" id="styledAlertModal" tabindex="-1" style="z-index: 999999;">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-body text-center p-4">
          <div class="${alertClass} border-0 mb-3">
            <i class="${iconClass} fs-2 mb-2"></i>
            <p class="mb-0">${message}</p>
          </div>
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>`;
  
  const existing = document.getElementById('styledAlertModal');
  if (existing) existing.remove();
  
  document.body.insertAdjacentHTML('beforeend', modalHtml);
  const modal = new bootstrap.Modal(document.getElementById('styledAlertModal'));
  modal.show();
  document.body.style.overflow = 'hidden';
}

function showConfirmationModal(title, message, onConfirm) {
  const modalHtml = `
  <div class="modal fade" id="confirmationModal" tabindex="-1" style="z-index: 999999;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">${title}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning border-0">
            <i class="bi bi-question-circle fs-2 mb-2 d-block text-center"></i>
            <p class="text-center mb-0">${message}</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="confirmAction()">Yes, Assign</button>
        </div>
      </div>
    </div>
  </div>`;
  
  const existing = document.getElementById('confirmationModal');
  if (existing) existing.remove();
  
  document.body.insertAdjacentHTML('beforeend', modalHtml);
  
  window.confirmAction = () => {
    bootstrap.Modal.getInstance(document.getElementById('confirmationModal')).hide();
    document.body.style.overflow = '';
    onConfirm();
  };
  
  const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
  modal.show();
  document.body.style.overflow = 'hidden';
  
  // Handle modal close events
  document.getElementById('confirmationModal').addEventListener('hidden.bs.modal', () => {
    document.body.style.overflow = '';
  });
}

function showCreateSection() {
  // Placeholder for create section functionality
  alert('Create section functionality will be implemented soon.');
}
</script>

<?= $this->endSection() ?>