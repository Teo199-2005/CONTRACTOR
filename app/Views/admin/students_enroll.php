<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3">Enroll New Student</h1>
  <div>
    <button type="button" class="btn btn-info me-2" onclick="fillDemoData()">Demo Fill</button>
    <a href="<?= base_url('admin/students') ?>" class="btn btn-outline-secondary">Back to Students</a>
  </div>
</div>

<form id="enrollStudentForm" method="post" action="<?= base_url('admin/students/store') ?>" enctype="multipart/form-data">
  <?= csrf_field() ?>
  
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0">Account Information</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">LRN</label>
            <input type="text" class="form-control" name="lrn" placeholder="Auto-generated if empty">
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Email *</label>
            <input type="email" class="form-control" name="email" required>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Password *</label>
            <div style="position: relative;">
              <input type="password" class="form-control" name="password" id="password" required minlength="8">
              <button type="button" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer; color: #6c757d;">
                <i class="bi bi-eye" id="eyeIcon"></i>
              </button>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Confirm Password *</label>
            <div style="position: relative;">
              <input type="password" class="form-control" name="confirm_password" id="confirm_password" required minlength="8">
              <button type="button" id="toggleConfirmPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer; color: #6c757d;">
                <i class="bi bi-eye" id="confirmEyeIcon"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0">Personal Information</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label">First Name *</label>
            <input type="text" class="form-control" name="first_name" required>
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label">Middle Name</label>
            <input type="text" class="form-control" name="middle_name">
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label">Last Name *</label>
            <input type="text" class="form-control" name="last_name" required>
          </div>
        </div>
        <div class="col-md-3">
          <div class="mb-3">
            <label class="form-label">Suffix</label>
            <input type="text" class="form-control" name="suffix" placeholder="Jr., Sr., III">
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
            <label class="form-label">Date of Birth *</label>
            <input type="date" class="form-control" name="date_of_birth" required>
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Place of Birth</label>
            <input type="text" class="form-control" name="place_of_birth">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Nationality</label>
            <input type="text" class="form-control" name="nationality" value="Filipino">
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Religion</label>
            <input type="text" class="form-control" name="religion">
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="text" class="form-control" name="contact_number">
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0">Academic Information</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Grade Level *</label>
            <select class="form-select" name="grade_level" required>
              <option value="">Select Grade</option>
              <option value="7">Grade 7</option>
              <option value="8">Grade 8</option>
              <option value="9">Grade 9</option>
              <option value="10">Grade 10</option>
              <option value="11">Grade 11</option>
              <option value="12">Grade 12</option>
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Student Type *</label>
            <select class="form-select" name="student_type" required>
              <option value="">Select Type</option>
              <option value="New Student">New Student</option>
              <option value="Transferee">Transferee</option>
            </select>
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label class="form-label">Section</label>
            <select class="form-select" name="section_id">
              <option value="">No Section Assigned</option>
            </select>
          </div>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Address</label>
        <textarea class="form-control" name="address" rows="2"></textarea>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0">Required Documents</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Birth Certificate *</label>
            <input type="file" class="form-control" name="birth_certificate" accept=".pdf,.jpg,.jpeg,.png" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Report Card (Form 138) *</label>
            <input type="file" class="form-control" name="report_card" accept=".pdf,.jpg,.jpeg,.png" required>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Good Moral Certificate *</label>
            <input type="file" class="form-control" name="good_moral" accept=".pdf,.jpg,.jpeg,.png" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">2x2 Photo *</label>
            <input type="file" class="form-control" name="photo" accept=".jpg,.jpeg,.png" required>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card mb-4">
    <div class="card-header">
      <h5 class="card-title mb-0">Emergency Contact</h5>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Emergency Contact Name</label>
            <input type="text" class="form-control" name="emergency_contact_name">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label">Emergency Contact Number</label>
            <input type="text" class="form-control" name="emergency_contact_number">
          </div>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Relationship</label>
        <select class="form-select" name="emergency_contact_relationship">
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
  </div>

  <div class="d-flex justify-content-end gap-2 mb-4">
    <a href="<?= base_url('admin/students') ?>" class="btn btn-secondary" style="background-color: #374151; border-color: #374151;">Cancel</a>
    <button type="submit" class="btn btn-primary">Enroll Student</button>
  </div>
</form>

<script>
// Password toggle functionality
document.getElementById('togglePassword').addEventListener('click', function() {
  const passwordInput = document.getElementById('password');
  const eyeIcon = document.getElementById('eyeIcon');
  
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    eyeIcon.className = 'bi bi-eye-slash';
  } else {
    passwordInput.type = 'password';
    eyeIcon.className = 'bi bi-eye';
  }
});

document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
  const confirmPasswordInput = document.getElementById('confirm_password');
  const confirmEyeIcon = document.getElementById('confirmEyeIcon');
  
  if (confirmPasswordInput.type === 'password') {
    confirmPasswordInput.type = 'text';
    confirmEyeIcon.className = 'bi bi-eye-slash';
  } else {
    confirmPasswordInput.type = 'password';
    confirmEyeIcon.className = 'bi bi-eye';
  }
});

// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
  const password = document.getElementById('password').value;
  const confirmPassword = this.value;
  
  if (password !== confirmPassword) {
    this.setCustomValidity('Passwords do not match');
  } else {
    this.setCustomValidity('');
  }
});

// Demo fill function
function fillDemoData() {
  const firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Carlos', 'Sofia', 'Miguel', 'Isabella', 'Luis', 'Carmen'];
  const middleNames = ['Santos', 'Cruz', 'Reyes', 'Garcia', 'Lopez', 'Martinez', 'Gonzalez', 'Rodriguez'];
  const lastNames = ['Dela Cruz', 'Santos', 'Garcia', 'Reyes', 'Lopez', 'Martinez', 'Gonzalez', 'Rodriguez'];
  const genders = ['Male', 'Female'];
  const gradeLevels = ['7', '8', '9', '10', '11', '12'];
  const studentTypes = ['New Student', 'Transferee'];
  const places = ['Tagbilaran City, Bohol', 'Panglao, Bohol', 'Dauis, Bohol', 'Baclayon, Bohol'];
  const religions = ['Catholic', 'Protestant', 'Iglesia ni Cristo', 'Baptist'];
  const relationships = ['Mother', 'Father', 'Guardian', 'Aunt', 'Uncle'];
  
  const getRandom = (arr) => arr[Math.floor(Math.random() * arr.length)];
  const getRandomBirthDate = () => {
    const today = new Date();
    const age = Math.floor(Math.random() * 7) + 11;
    const birthYear = today.getFullYear() - age;
    const birthMonth = Math.floor(Math.random() * 12) + 1;
    const birthDay = Math.floor(Math.random() * 28) + 1;
    return `${birthYear}-${birthMonth.toString().padStart(2, '0')}-${birthDay.toString().padStart(2, '0')}`;
  };
  const getRandomPhone = () => {
    const prefixes = ['0917', '0918', '0919', '0920', '0921'];
    return getRandom(prefixes) + Math.floor(Math.random() * 10000000).toString().padStart(7, '0');
  };
  
  const firstName = getRandom(firstNames);
  const lastName = getRandom(lastNames);
  const email = (firstName + lastName).toLowerCase().replace(/\s+/g, '') + Math.floor(Math.random() * 999) + '@gmail.com';
  
  document.querySelector('[name="lrn"]').value = '999' + Date.now().toString().slice(-9);
  document.querySelector('[name="email"]').value = email;
  document.querySelector('[name="password"]').value = 'Demo123!';
  document.querySelector('[name="confirm_password"]').value = 'Demo123!';
  document.querySelector('[name="first_name"]').value = firstName;
  document.querySelector('[name="middle_name"]').value = getRandom(middleNames);
  document.querySelector('[name="last_name"]').value = lastName;
  document.querySelector('[name="gender"]').value = getRandom(genders);
  document.querySelector('[name="date_of_birth"]').value = getRandomBirthDate();
  document.querySelector('[name="place_of_birth"]').value = getRandom(places);
  document.querySelector('[name="nationality"]').value = 'Filipino';
  document.querySelector('[name="religion"]').value = getRandom(religions);
  document.querySelector('[name="contact_number"]').value = getRandomPhone();
  document.querySelector('[name="grade_level"]').value = getRandom(gradeLevels);
  document.querySelector('[name="student_type"]').value = getRandom(studentTypes);
  document.querySelector('[name="address"]').value = `Purok ${Math.floor(Math.random() * 10) + 1}, Barangay Poblacion, Panglao, Bohol`;
  document.querySelector('[name="emergency_contact_name"]').value = getRandom(firstNames) + ' ' + getRandom(lastNames);
  document.querySelector('[name="emergency_contact_number"]').value = getRandomPhone();
  document.querySelector('[name="emergency_contact_relationship"]').value = getRandom(relationships);
}

// Form submission
document.getElementById('enrollStudentForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  
  fetch(this.action, {
    method: 'POST',
    body: formData
  })
  .then(response => {
    if (!response.ok) {
      return response.text().then(text => {
        throw new Error(`HTTP ${response.status}: ${text}`);
      });
    }
    return response.text();
  })
  .then(text => {
    if (text.includes('<!doctype html>') || text.includes('<html')) {
      alert('Student enrolled successfully!');
      window.location.href = '<?= base_url('admin/students') ?>';
      return;
    }
    
    try {
      const data = JSON.parse(text);
      if (data.success) {
        alert('Student enrolled successfully!');
        window.location.href = '<?= base_url('admin/students') ?>';
      } else {
        alert('Error: ' + (data.error || data.message || 'Failed to enroll student'));
      }
    } catch (e) {
      alert('Error: Unable to process server response');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Network Error: ' + error.message);
  });
});
</script>

<?= $this->endSection() ?>