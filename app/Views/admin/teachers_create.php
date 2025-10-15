<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3">Add New Teacher</h1>
  <div>
    <button type="button" class="btn btn-info me-2" onclick="fillDemo()">
      <i class="bi bi-magic"></i> Demo Fill
    </button>
    <a href="<?= base_url('admin/teachers') ?>" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Back to Teachers
    </a>
  </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger">
    <?= session()->getFlashdata('error') ?>
  </div>
<?php endif; ?>

<?php if (isset($validation) && $validation->getErrors()): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($validation->getErrors() as $error): ?>
        <li><?= esc($error) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">Teacher Information</h5>
  </div>
  <div class="card-body">
    <form method="post" action="<?= base_url('admin/teachers/store') ?>">
      <?= csrf_field() ?>
      
      <!-- Account Information -->
      <div class="row mb-4">
        <div class="col-12">
          <h6 class="text-primary border-bottom pb-2 mb-3">Account Information</h6>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="license_number" class="form-label">PRC License Number</label>
            <input type="text" class="form-control" id="license_number" name="license_number" 
                   value="<?= old('license_number') ?>" placeholder="PRC License Number (Optional)" maxlength="20">
            <div class="form-text">Optional - Enter PRC License Number if available</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="email" name="email" 
                   value="<?= old('email') ?>" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-group">
              <input type="password" class="form-control" id="password" name="password" required>
              <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="bi bi-eye" id="toggleIcon"></i>
              </button>
            </div>
            <div class="form-text">Minimum 8 characters</div>
          </div>
        </div>
      </div>

      <!-- Personal Information -->
      <div class="row mb-4">
        <div class="col-12">
          <h6 class="text-primary border-bottom pb-2 mb-3">Personal Information</h6>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="first_name" name="first_name" 
                   value="<?= old('first_name') ?>" required>
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label for="middle_name" class="form-label">Middle Name</label>
            <input type="text" class="form-control" id="middle_name" name="middle_name" 
                   value="<?= old('middle_name') ?>">
          </div>
        </div>
        <div class="col-md-4">
          <div class="mb-3">
            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="last_name" name="last_name" 
                   value="<?= old('last_name') ?>" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
            <select class="form-select" id="gender" name="gender" required>
              <option value="">Select Gender</option>
              <option value="Male" <?= old('gender') === 'Male' ? 'selected' : '' ?>>Male</option>
              <option value="Female" <?= old('gender') === 'Female' ? 'selected' : '' ?>>Female</option>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                   value="<?= old('date_of_birth') ?>" required>
          </div>
        </div>
      </div>

      <!-- Professional Information -->
      <div class="row mb-4">
        <div class="col-12">
          <h6 class="text-primary border-bottom pb-2 mb-3">Professional Information</h6>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="subjects" class="form-label">Subjects</label>
            <select class="form-select" id="subjects" name="subjects">
              <option value="">Select Subject</option>
              <option value="Mathematics" <?= old('subjects') === 'Mathematics' ? 'selected' : '' ?>>Mathematics</option>
              <option value="Science" <?= old('subjects') === 'Science' ? 'selected' : '' ?>>Science</option>
              <option value="English" <?= old('subjects') === 'English' ? 'selected' : '' ?>>English</option>
              <option value="Filipino" <?= old('subjects') === 'Filipino' ? 'selected' : '' ?>>Filipino</option>
              <option value="Araling Panlipunan" <?= old('subjects') === 'Araling Panlipunan' ? 'selected' : '' ?>>Araling Panlipunan</option>
              <option value="MAPEH" <?= old('subjects') === 'MAPEH' ? 'selected' : '' ?>>MAPEH</option>
              <option value="Values Education" <?= old('subjects') === 'Values Education' ? 'selected' : '' ?>>Values Education</option>
              <option value="TLE" <?= old('subjects') === 'TLE' ? 'selected' : '' ?>>TLE</option>
              <option value="TLE FSC" <?= old('subjects') === 'TLE FSC' ? 'selected' : '' ?>>TLE FSC</option>
              <option value="TLE/BPP" <?= old('subjects') === 'TLE/BPP' ? 'selected' : '' ?>>TLE/BPP</option>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="position" class="form-label">Position</label>
            <select class="form-select" id="position" name="position">
              <option value="">Select Position</option>
              <option value="Teacher" <?= old('position') === 'Teacher' ? 'selected' : '' ?>>Teacher</option>
              <option value="Department Head" <?= old('position') === 'Department Head' ? 'selected' : '' ?>>Department Head</option>
              <option value="Principal" <?= old('position') === 'Principal' ? 'selected' : '' ?>>Principal</option>
              <option value="Assistant Principal" <?= old('position') === 'Assistant Principal' ? 'selected' : '' ?>>Assistant Principal</option>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="date_hired" class="form-label">Date Hired <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="date_hired" name="date_hired" 
                   value="<?= old('date_hired') ?>" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="employment_status" class="form-label">Employment Status <span class="text-danger">*</span></label>
            <select class="form-select" id="employment_status" name="employment_status" required>
              <option value="">Select Status</option>
              <option value="active" <?= old('employment_status') === 'active' ? 'selected' : '' ?>>Active</option>
              <option value="inactive" <?= old('employment_status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
              <option value="on_leave" <?= old('employment_status') === 'on_leave' ? 'selected' : '' ?>>On Leave</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Contact Information -->
      <div class="row mb-4">
        <div class="col-12">
          <h6 class="text-primary border-bottom pb-2 mb-3">Contact Information</h6>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="contact_number" class="form-label">Contact Number</label>
            <input type="text" class="form-control" id="contact_number" name="contact_number" 
                   value="<?= old('contact_number') ?>">
          </div>
        </div>
        <div class="col-md-12">
          <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" id="address" name="address" rows="3"><?= old('address') ?></textarea>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-end gap-2">
        <a href="<?= base_url('admin/teachers') ?>" class="btn btn-secondary" style="background-color: #6c757d; border-color: #6c757d;">Cancel</a>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-check-circle"></i> Create Teacher
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function fillDemo() {
    const firstNames = ['Maria', 'Juan', 'Ana', 'Carlos', 'Elena', 'Miguel', 'Sofia', 'Roberto', 'Carmen', 'Diego', 'Isabella', 'Fernando', 'Lucia', 'Antonio', 'Gabriela'];
    const middleNames = ['Cruz', 'Reyes', 'Garcia', 'Lopez', 'Martinez', 'Gonzalez', 'Rodriguez', 'Fernandez', 'Morales', 'Jimenez', 'Herrera', 'Medina', 'Castro', 'Ortiz', 'Ramos'];
    const lastNames = ['Santos', 'Dela Cruz', 'Bautista', 'Villanueva', 'Aquino', 'Mendoza', 'Torres', 'Rivera', 'Flores', 'Perez', 'Valdez', 'Navarro', 'Aguilar', 'Salazar', 'Moreno'];
    const subjects = ['Mathematics', 'Science', 'English', 'Filipino', 'Araling Panlipunan', 'MAPEH', 'Values Education', 'TLE', 'TLE FSC', 'TLE/BPP'];
    const positions = ['Teacher', 'Department Head', 'Assistant Principal'];
    const genders = ['Male', 'Female'];
    const barangays = ['Poblacion', 'San Isidro', 'Magsaysay', 'Rizal', 'Burgos', 'Mabini', 'Bonifacio', 'Luna', 'Del Pilar', 'Jacinto'];
    const cities = ['Tagbilaran City', 'Baclayon', 'Alburquerque', 'Loboc', 'Carmen', 'Loon', 'Calape', 'Tubigon', 'Clarin', 'Inabanga'];
    
    const firstName = firstNames[Math.floor(Math.random() * firstNames.length)];
    const middleName = middleNames[Math.floor(Math.random() * middleNames.length)];
    const lastName = lastNames[Math.floor(Math.random() * lastNames.length)];
    const gender = genders[Math.floor(Math.random() * genders.length)];
    const subject = subjects[Math.floor(Math.random() * subjects.length)];
    const position = positions[Math.floor(Math.random() * positions.length)];
    const barangay = barangays[Math.floor(Math.random() * barangays.length)];
    const city = cities[Math.floor(Math.random() * cities.length)];
    
    const licenseNum = 'PRC' + Math.floor(Math.random() * 9000000 + 1000000);
    const email = firstName.toLowerCase() + '.' + lastName.toLowerCase().replace(' ', '') + '@lphs.edu';
    const password = 'Demo' + Math.floor(Math.random() * 9000 + 1000) + '!';
    const birthYear = Math.floor(Math.random() * 20 + 1970);
    const birthMonth = String(Math.floor(Math.random() * 12 + 1)).padStart(2, '0');
    const birthDay = String(Math.floor(Math.random() * 28 + 1)).padStart(2, '0');
    const hireYear = Math.floor(Math.random() * 10 + 2015);
    const hireMonth = String(Math.floor(Math.random() * 12 + 1)).padStart(2, '0');
    const hireDay = String(Math.floor(Math.random() * 28 + 1)).padStart(2, '0');
    const contactNum = '09' + Math.floor(Math.random() * 900000000 + 100000000);
    
    document.getElementById('license_number').value = licenseNum;
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
    document.getElementById('first_name').value = firstName;
    document.getElementById('middle_name').value = middleName;
    document.getElementById('last_name').value = lastName;
    document.getElementById('gender').value = gender;
    document.getElementById('date_of_birth').value = birthYear + '-' + birthMonth + '-' + birthDay;
    document.getElementById('subjects').value = subject;
    document.getElementById('position').value = position;
    document.getElementById('date_hired').value = hireYear + '-' + hireMonth + '-' + hireDay;
    document.getElementById('employment_status').value = 'active';
    document.getElementById('contact_number').value = contactNum;
    document.getElementById('address').value = 'Purok ' + Math.floor(Math.random() * 7 + 1) + ', Barangay ' + barangay + ', ' + city + ', Bohol';
}

document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordField = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
});
</script>

<?= $this->endSection() ?>
