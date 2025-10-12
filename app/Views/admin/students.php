<?php if (!isset($this)) { /* placeholder to ensure file exists */ } ?>
<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3">Manage Students</h1>
  <div>
    <button class="btn btn-primary me-2" onclick="openEnrollStudentModal()">
      <i class="bi bi-plus-circle"></i> Enroll Student
    </button>
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary">Back</a>
  </div>
</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-auto">
    <label class="form-label">Grade</label>
    <select name="grade" class="form-select" onchange="this.form.submit()">
      <option value="">All</option>
      <?php for ($g=7; $g<=10; $g++): ?>
        <option value="<?= $g ?>" <?= ($gradeLevel==$g?'selected':'') ?>>Grade <?= $g ?></option>
      <?php endfor; ?>
    </select>
  </div>
  <div class="col-auto">
    <label class="form-label">Section</label>
    <select name="section" class="form-select" onchange="this.form.submit()">
      <option value="">All Sections</option>
      <?php if (!empty($allSections)): ?>
        <?php foreach ($allSections as $sec): ?>
          <option value="<?= $sec['id'] ?>" <?= ($section == $sec['id'] ? 'selected' : '') ?>>
            <?= esc($sec['section_name']) ?> (Grade <?= $sec['grade_level'] ?>)
          </option>
        <?php endforeach; ?>
      <?php endif; ?>
    </select>
  </div>
  <div class="col-auto">
    <label class="form-label">Search</label>
    <input type="text" name="search" class="form-control" value="<?= esc($search) ?>" placeholder="Name or LRN">
  </div>
  <div class="col-auto align-self-end">
    <button class="btn btn-primary">Filter</button>
    <button type="button" class="btn btn-outline-info ms-2" onclick="showEmergencyContacts()">
      <i class="bi bi-person-lines-fill"></i> Emergency Contacts
    </button>
  </div>
</form>

<div class="card">
  <div class="card-body p-0">
    <?php if (!empty($students)): ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead>
            <tr>
              <th>LRN</th>
              <th>Name</th>
              <th>Grade</th>
              <th>Section</th>
              <th>Status</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($students as $st): ?>
              <tr>
                <td><?= esc($st['lrn'] ?? '—') ?></td>
                <td><?= esc($st['first_name'].' '.$st['last_name']) ?></td>
                <td>Grade <?= esc($st['grade_level']) ?></td>
                <td>
                  <?php if (empty($st['section_name'])): ?>
                    <span class="text-warning">
                      <i class="bi bi-exclamation-triangle me-1"></i>
                      Not assigned
                    </span>
                  <?php else: ?>
                    <?php 
                      // Remove "Grade X - " prefix if it exists
                      $sectionName = $st['section_name'];
                      $sectionName = preg_replace('/^Grade \d+ - /', '', $sectionName);
                      echo esc($sectionName);
                    ?>
                  <?php endif; ?>
                </td>
                <td><span class="badge bg-success">Enrolled</span></td>
                <td class="text-end">
                  <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary" onclick="viewStudent(<?= $st['id'] ?>)" title="View Details">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" onclick="editStudent(<?= $st['id'] ?>)" title="Edit Student">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteStudent(<?= $st['id'] ?>, '<?= esc($st['first_name'] . ' ' . $st['last_name']) ?>')" title="Delete Student">
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
      <div class="p-4 text-center text-muted">No enrolled students found.</div>
    <?php endif; ?>
  </div>
</div>

<!-- Custom Student Details Modal -->
<div id="customStudentModal" class="custom-modal-overlay" style="display: none;">
  <div class="custom-modal-container">
    <div class="custom-modal-header">
      <h3 class="custom-modal-title">Student Details</h3>
      <button type="button" class="custom-modal-close" onclick="closeStudentModal()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="custom-modal-body">
      <div id="customStudentDetails">
        <div class="loading-spinner">
          <div class="spinner"></div>
          <p>Loading student details...</p>
        </div>
      </div>
    </div>
    <div class="custom-modal-footer">
      <button type="button" class="btn btn-secondary" onclick="closeStudentModal()">Close</button>
    </div>
  </div>
</div>

<!-- Document Viewer Modal -->
<div id="documentViewerModal" class="document-viewer-modal" style="display: none;">
  <div class="document-viewer-overlay" onclick="closeDocumentModal()"></div>
  <div class="document-viewer-content">
    <div class="document-viewer-header">
      <h5 id="documentViewerTitle">Document Viewer</h5>
      <button type="button" class="document-viewer-close" onclick="closeDocumentModal()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="document-viewer-body">
      <img id="documentViewerImage" src="" alt="Document" class="document-viewer-image">
    </div>
  </div>
</div>

<!-- Emergency Contacts Modal -->
<div id="emergencyContactsModal" class="custom-modal-overlay" style="display: none;">
  <div class="custom-modal-container">
    <div class="custom-modal-header">
      <h3 class="custom-modal-title">Emergency Contacts</h3>
      <button type="button" class="custom-modal-close" onclick="closeEmergencyContactsModal()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="custom-modal-body">
      <div id="emergencyContactsList">
        <div class="loading-spinner">
          <div class="spinner"></div>
          <p>Loading emergency contacts...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Student Modal -->
<div id="editStudentModal" class="custom-modal-overlay" style="display: none;">
  <div class="custom-modal-container">
    <div class="custom-modal-header">
      <h3 class="custom-modal-title">Edit Student</h3>
      <button type="button" class="custom-modal-close" onclick="closeEditStudentModal()">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div class="custom-modal-body">
      <form id="editStudentForm">
        <div class="row">
          <div class="col-md-4">
            <div class="mb-3">
              <label class="form-label">LRN</label>
              <input type="text" class="form-control" name="lrn" id="editLrn" required>
            </div>
          </div>
          <div class="col-md-4">
            <div class="mb-3">
              <label class="form-label">Student Type</label>
              <select class="form-select" name="student_type" id="editStudentType">
                <option value="">Select Type</option>
                <option value="New Student">New Student</option>
                <option value="Transferee">Transferee</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" id="editEmail" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label">First Name</label>
              <input type="text" class="form-control" name="first_name" id="editFirstName" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="mb-3">
              <label class="form-label">Last Name</label>
              <input type="text" class="form-control" name="last_name" id="editLastName" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="mb-3">
              <label class="form-label">Grade Level</label>
              <select class="form-select" name="grade_level" id="editGradeLevel" required>
                <option value="7">Grade 7</option>
                <option value="8">Grade 8</option>
                <option value="9">Grade 9</option>
                <option value="10">Grade 10</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="mb-3">
              <label class="form-label">Gender</label>
              <select class="form-select" name="gender" id="editGender" required>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select class="form-select" name="enrollment_status" id="editStatus" required>
                <option value="enrolled">Enrolled</option>
                <option value="suspended">Suspended</option>
                <option value="graduated">Graduated</option>
                <option value="transferred">Transferred</option>
              </select>
            </div>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Contact Number</label>
          <input type="text" class="form-control" name="contact_number" id="editContactNumber">
        </div>
        <div class="mb-3">
          <label class="form-label">Address</label>
          <textarea class="form-control" name="address" id="editAddress" rows="2"></textarea>
        </div>
      </form>
    </div>
    <div class="custom-modal-footer">
      <button type="button" class="btn btn-primary" onclick="saveStudent()">Save Changes</button>
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
  z-index: 99999;
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
  margin: 0;
}

.custom-modal-header h3.custom-modal-title {
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

/* Student Details Styling */
.student-info-section {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  border: 2px solid #e2e8f0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.student-info-title {
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

.student-info-table {
  width: 100%;
  border-collapse: collapse;
}

.student-info-table td {
  padding: 0.75rem 0;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: top;
}

.student-info-table td:first-child {
  font-weight: 600;
  color: #374151;
  width: 35%;
}

.student-info-table td:last-child {
  color: #6b7280;
}

.student-info-table tr:last-child td {
  border-bottom: none;
}

/* Document Viewer Modal */
.document-viewer-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.9);
  z-index: 100000;
  display: flex;
  align-items: center;
  justify-content: center;
}

.document-viewer-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.document-viewer-content {
  background: white;
  border-radius: 12px;
  max-width: 90vw;
  max-height: 90vh;
  overflow: hidden;
  position: relative;
}

.document-viewer-header {
  background: #1e40af;
  color: white;
  padding: 1rem 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.document-viewer-header h5 {
  margin: 0;
  color: white;
}

.document-viewer-close {
  background: rgba(255, 255, 255, 0.2);
  border: none;
  color: white;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.document-viewer-body {
  padding: 1rem;
  text-align: center;
}

.document-viewer-image {
  max-width: 100%;
  max-height: 70vh;
  object-fit: contain;
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
</style>

<script>
// Store student data for quick access
const studentsData = <?= json_encode($students) ?>;

// View student details in full page
function viewStudent(studentId) {
  window.location.href = `<?= base_url('admin/students/view/') ?>${studentId}`;
}

// Open custom modal
function openStudentModal(studentId) {
  const modal = document.getElementById('customStudentModal');
  const detailsContainer = document.getElementById('customStudentDetails');
  const footer = document.querySelector('.modern-footer');

  // Hide footer and show modal
  if (footer) footer.style.display = 'none';
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden'; // Prevent background scrolling

  // Show loading state
  detailsContainer.innerHTML = `
    <div class="loading-spinner">
      <div class="spinner"></div>
      <p>Loading student details...</p>
    </div>
  `;

  // Simulate loading delay and then show student data
  setTimeout(() => {
    loadStudentDetails(studentId);
  }, 500);
}

// Close custom modal
function closeStudentModal() {
  const modal = document.getElementById('customStudentModal');
  const footer = document.querySelector('.modern-footer');
  
  modal.style.display = 'none';
  if (footer) footer.style.display = 'block';
  document.body.style.overflow = ''; // Restore scrolling
}

// Load and display student details
function loadStudentDetails(studentId) {
  const detailsContainer = document.getElementById('customStudentDetails');

  // Fetch student details with documents from server
  fetch(`<?= base_url('admin/students/details/') ?>${studentId}`)
    .then(response => response.text())
    .then(html => {
      detailsContainer.innerHTML = html;
    })
    .catch(error => {
      console.error('Error loading student details:', error);

      // Fallback to local data if server request fails
      const student = studentsData.find(s => s.id == studentId);

      if (!student) {
        detailsContainer.innerHTML = `
          <div class="student-info-section">
            <div class="alert alert-danger">
              <i class="bi bi-exclamation-triangle"></i>
              Student not found.
            </div>
          </div>
        `;
        return;
      }

      // Show basic student info without documents
      loadBasicStudentDetails(student, detailsContainer);
    });
}

// Fallback function for basic student details
function loadBasicStudentDetails(student, detailsContainer) {

  const detailsHtml = `
    <div class="row">
      <div class="col-md-6">
        <div class="student-info-section">
          <div class="student-info-title">
            <i class="bi bi-person-circle"></i>
            Personal Information
          </div>
          <table class="student-info-table">
            <tr><td>LRN:</td><td>${student.lrn || 'N/A'}</td></tr>
            <tr><td>Full Name:</td><td>${student.first_name} ${student.middle_name || ''} ${student.last_name} ${student.suffix || ''}</td></tr>
            <tr><td>Gender:</td><td>${student.gender || 'N/A'}</td></tr>
            <tr><td>Date of Birth:</td><td>${student.date_of_birth ? new Date(student.date_of_birth).toLocaleDateString() : 'N/A'}</td></tr>
            <tr><td>Place of Birth:</td><td>${student.place_of_birth || 'N/A'}</td></tr>
            <tr><td>Nationality:</td><td>${student.nationality || 'N/A'}</td></tr>
            <tr><td>Religion:</td><td>${student.religion || 'N/A'}</td></tr>
          </table>
        </div>
      </div>
      <div class="col-md-6">
        <div class="student-info-section">
          <div class="student-info-title">
            <i class="bi bi-telephone"></i>
            Contact Information
          </div>
          <table class="student-info-table">
            <tr><td>Email:</td><td>${student.email || 'N/A'}</td></tr>
            <tr><td>Contact Number:</td><td>${student.contact_number || 'N/A'}</td></tr>
            <tr><td>Address:</td><td>${student.address || 'N/A'}</td></tr>
          </table>
        </div>

        <div class="student-info-section">
          <div class="student-info-title">
            <i class="bi bi-shield-exclamation"></i>
            Emergency Contact
          </div>
          <table class="student-info-table">
            <tr><td>Name:</td><td>${student.emergency_contact_name || 'N/A'}</td></tr>
            <tr><td>Number:</td><td>${student.emergency_contact_number || 'N/A'}</td></tr>
            <tr><td>Relationship:</td><td>${student.emergency_contact_relationship || 'N/A'}</td></tr>
          </table>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="student-info-section">
          <div class="student-info-title">
            <i class="bi bi-mortarboard"></i>
            Academic Information
          </div>
          <table class="student-info-table">
            <tr><td>Grade Level:</td><td>Grade ${student.grade_level}</td></tr>
            <tr><td>Section:</td><td>${student.section_name || 'Not assigned'}</td></tr>
            <tr><td>School Year:</td><td>${student.school_year || 'N/A'}</td></tr>
            <tr><td>Status:</td><td><span class="badge bg-success">Enrolled</span></td></tr>
            <tr><td>Enrollment Date:</td><td>${student.created_at ? new Date(student.created_at).toLocaleDateString() : 'N/A'}</td></tr>
          </table>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="student-info-section">
          <div class="student-info-title">
            <i class="bi bi-file-earmark-text"></i>
            Required Documents
          </div>
          <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            Documents are not available in fallback mode. Please refresh the page to load documents.
          </div>
        </div>
      </div>
    </div>
  `;

  detailsContainer.innerHTML = detailsHtml;
}

// Simple image viewer functions (global scope)
function showImageModal(imageUrl, title) {
  // Create modal if it doesn't exist
  let modal = document.getElementById('simpleImageModal');
  if (!modal) {
    modal = document.createElement('div');
    modal.id = 'simpleImageModal';
    modal.innerHTML = `
      <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 10000; display: flex; align-items: center; justify-content: center;" onclick="closeSimpleModal()">
        <div style="background: white; border-radius: 12px; max-width: 90vw; max-height: 90vh; overflow: hidden; position: relative;" onclick="event.stopPropagation()">
          <div style="background: #1e40af; color: white; padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h5 style="margin: 0; color: white;" id="simpleModalTitle">Document Viewer</h5>
            <button onclick="closeSimpleModal()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer;">×</button>
          </div>
          <div style="padding: 1rem; text-align: center;">
            <img id="simpleModalImage" src="" alt="Document" style="max-width: 100%; max-height: 70vh; object-fit: contain;">
          </div>
        </div>
      </div>
    `;
    document.body.appendChild(modal);
  }

  // Set image and title
  const img = modal.querySelector('#simpleModalImage');
  const titleEl = modal.querySelector('#simpleModalTitle');

  if (img) img.src = imageUrl;
  if (titleEl) titleEl.textContent = title;

  // Show modal
  modal.style.display = 'block';
  document.body.style.overflow = 'hidden';
}

function closeSimpleModal() {
  const modal = document.getElementById('simpleImageModal');
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  }
}



// Document viewer functions (global scope) - keeping for compatibility
function openDocumentModal(imageUrl, title) {
  console.log('openDocumentModal called, redirecting to showImageModal');
  showImageModal(imageUrl, title);
}

function closeDocumentModal() {
  console.log('Closing document modal');

  const modal = document.getElementById('documentViewerModal');
  if (modal) {
    modal.style.display = 'none';
    document.body.style.overflow = '';
    console.log('Document modal closed successfully');
  } else {
    console.error('Document viewer modal not found for closing');
  }
}

// Event listeners for the custom modal
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('customStudentModal');

  // Event delegation for clickable documents
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('clickable-document') || e.target.closest('.clickable-document')) {
      e.preventDefault();
      e.stopPropagation();

      const element = e.target.classList.contains('clickable-document') ? e.target : e.target.closest('.clickable-document');
      const imageUrl = element.getAttribute('data-image-url');
      const imageTitle = element.getAttribute('data-image-title');

      console.log('Document clicked via event delegation:', imageUrl, imageTitle);

      if (imageUrl && imageTitle) {
        openDocumentModal(imageUrl, imageTitle);
      }
    }
  });

  // Close modal when clicking outside the modal container
  modal.addEventListener('click', function(e) {
    if (e.target === modal) {
      closeStudentModal();
    }
  });

  // Close modal with Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      if (modal.style.display === 'flex') {
        closeStudentModal();
      }

      // Also close document viewer modal
      const docModal = document.getElementById('documentViewerModal');
      if (docModal && docModal.style.display === 'flex') {
        closeDocumentModal();
      }
    }
  });

  // Prevent modal content clicks from closing the modal
  const modalContainer = modal.querySelector('.custom-modal-container');
  if (modalContainer) {
    modalContainer.addEventListener('click', function(e) {
      e.stopPropagation();
    });
  }
});

// Emergency Contacts Modal Functions
function showEmergencyContacts() {
  const modal = document.getElementById('emergencyContactsModal');
  const contactsList = document.getElementById('emergencyContactsList');
  const footer = document.querySelector('.modern-footer');
  
  if (footer) footer.style.display = 'none';
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
  
  // Show loading state
  contactsList.innerHTML = `
    <div class="loading-spinner">
      <div class="spinner"></div>
      <p>Loading emergency contacts...</p>
    </div>
  `;
  
  // Load emergency contacts data
  setTimeout(() => {
    loadEmergencyContacts();
  }, 500);
}

function closeEmergencyContactsModal() {
  const modal = document.getElementById('emergencyContactsModal');
  const footer = document.querySelector('.modern-footer');
  
  modal.style.display = 'none';
  if (footer) footer.style.display = 'block';
  document.body.style.overflow = '';
}

function loadEmergencyContacts() {
  const contactsList = document.getElementById('emergencyContactsList');
  
  // Generate emergency contacts list from students data
  let contactsHtml = '<div class="table-responsive">';
  contactsHtml += '<table class="table table-striped">';
  contactsHtml += '<thead><tr><th>Student</th><th>Emergency Contact</th></tr></thead>';
  contactsHtml += '<tbody>';
  
  studentsData.forEach(student => {
    contactsHtml += `
      <tr>
        <td>
          <strong>${student.first_name} ${student.last_name}</strong><br>
          <small class="text-muted">${student.lrn || 'N/A'} - Grade ${student.grade_level}</small>
        </td>
        <td>
          <strong>Name:</strong> ${student.emergency_contact_name || 'N/A'}<br>
          <strong>Number:</strong> ${student.emergency_contact_number || 'N/A'}<br>
          <strong>Relationship:</strong> ${student.emergency_contact_relationship || 'N/A'}
        </td>
      </tr>
    `;
  });
  
  contactsHtml += '</tbody></table></div>';
  
  if (studentsData.length === 0) {
    contactsHtml = '<div class="alert alert-info"><i class="bi bi-info-circle"></i> No students found.</div>';
  }
  
  contactsList.innerHTML = contactsHtml;
}

// Edit Student Modal Functions
function editStudent(studentId) {
  const student = studentsData.find(s => s.id == studentId);
  if (!student) {
    alert('Student not found.');
    return;
  }
  
  // Populate form fields
  document.getElementById('editLrn').value = student.lrn || '';
  document.getElementById('editStudentType').value = student.student_type || '';
  document.getElementById('editEmail').value = student.email || '';
  document.getElementById('editFirstName').value = student.first_name || '';
  document.getElementById('editLastName').value = student.last_name || '';
  document.getElementById('editGradeLevel').value = student.grade_level || '7';
  document.getElementById('editGender').value = student.gender || 'Male';
  document.getElementById('editStatus').value = student.enrollment_status || 'enrolled';
  document.getElementById('editContactNumber').value = student.contact_number || '';
  document.getElementById('editAddress').value = student.address || '';
  
  // Store student ID for saving
  document.getElementById('editStudentForm').dataset.studentId = studentId;
  
  // Show modal
  const footer = document.querySelector('.modern-footer');
  if (footer) footer.style.display = 'none';
  document.getElementById('editStudentModal').style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeEditStudentModal() {
  const footer = document.querySelector('.modern-footer');
  
  document.getElementById('editStudentModal').style.display = 'none';
  if (footer) footer.style.display = 'block';
  document.body.style.overflow = '';
}

function saveStudent() {
  const form = document.getElementById('editStudentForm');
  const studentId = form.dataset.studentId;
  const formData = new FormData(form);
  
  fetch(`<?= base_url('admin/students/update') ?>/${studentId}`, {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('Student updated successfully!');
      location.reload();
    } else {
      alert('Error: ' + (data.error || 'Failed to update student'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Failed to update student');
  });
}

// Enroll Student Modal Functions
function buildEnrollStudentModal() {
  const existing = document.getElementById('enrollStudentModal');
  if (existing) existing.remove();

  const html = `
  <div class="modal fade" id="enrollStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Enroll New Student</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="enrollStudentForm" method="post" action="<?= base_url('admin/students/store') ?>">
        <?= str_replace(["\n","\r"], '', csrf_field()) ?>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">First Name *</label>
                <input type="text" class="form-control" name="first_name" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Last Name *</label>
                <input type="text" class="form-control" name="last_name" required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label">Gender *</label>
                <select class="form-select" name="gender" required>
                  <option value="">Select Gender</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label">Grade Level *</label>
                <select class="form-select" name="grade_level" required>
                  <option value="">Select Grade</option>
                  <option value="7">Grade 7</option>
                  <option value="8">Grade 8</option>
                  <option value="9">Grade 9</option>
                  <option value="10">Grade 10</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label">Date of Birth *</label>
                <input type="date" class="form-control" name="date_of_birth" required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" class="form-control" name="contact_number">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea class="form-control" name="address" rows="2"></textarea>
          </div>
          <hr>
          <h6 class="text-primary mb-3">Emergency Contact Information</h6>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Emergency Contact Name *</label>
                <input type="text" class="form-control" name="emergency_contact_name" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Emergency Contact Number *</label>
                <input type="text" class="form-control" name="emergency_contact_number" required>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Relationship *</label>
            <select class="form-select" name="emergency_contact_relationship" required>
              <option value="">Select Relationship</option>
              <option value="Father">Father</option>
              <option value="Mother">Mother</option>
              <option value="Guardian">Guardian</option>
              <option value="Grandfather">Grandfather</option>
              <option value="Grandmother">Grandmother</option>
              <option value="Uncle">Uncle</option>
              <option value="Aunt">Aunt</option>
              <option value="Other">Other</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Enroll Student</button>
        </div>
      </form>
    </div></div>
  </div>`;
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('enrollStudentModal');
}

function openEnrollStudentModal() {
  const modalEl = buildEnrollStudentModal();
  const modal = new bootstrap.Modal(modalEl, { backdrop: true, keyboard: true, focus: true });
  modal.show();
}

// Handle enrollment form submission
document.addEventListener('submit', function(e) {
  if (e.target.id === 'enrollStudentForm') {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    fetch(e.target.action, {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        bootstrap.Modal.getInstance(document.getElementById('enrollStudentModal')).hide();
        alert('Student enrolled successfully!');
        location.reload();
      } else {
        alert('Error: ' + (data.error || 'Failed to enroll student'));
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Failed to enroll student');
    });
  }
});

// Delete student function
function deleteStudent(studentId, studentName) {
  if (confirm(`Are you sure you want to delete student "${studentName}"? This action cannot be undone.`)) {
    fetch(`<?= base_url('admin/students/delete') ?>/${studentId}`, {
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
      alert('Failed to delete student');
    });
  }
}
</script>

<?= $this->endSection() ?>