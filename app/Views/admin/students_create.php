<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3">Add New Student</h1>
  <a href="<?= base_url('admin/students') ?>" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Back to Students
  </a>
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
    <h5 class="card-title mb-0">Student Information</h5>
  </div>
  <div class="card-body">
    <form method="post" action="<?= base_url('admin/students/store') ?>">
      <?= csrf_field() ?>
      
      <!-- Account Information -->
      <div class="row mb-4">
        <div class="col-12">
          <h6 class="text-primary border-bottom pb-2 mb-3">Account Information</h6>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="student_id" class="form-label">Student ID <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="student_id" name="student_id" 
                   value="<?= old('student_id') ?>" required>
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
            <input type="password" class="form-control" id="password" name="password" required>
            <div class="form-text">Minimum 8 characters</div>
          </div>
        </div>
      </div>

      <!-- Personal Information -->
      <div class="row mb-4">
        <div class="col-12">
          <h6 class="text-primary border-bottom pb-2 mb-3">Personal Information</h6>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="first_name" name="first_name" 
                   value="<?= old('first_name') ?>" required>
          </div>
        </div>
        <div class="col-md-6">
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
        <div class="col-md-6">
          <div class="mb-3">
            <label for="place_of_birth" class="form-label">Place of Birth</label>
            <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" 
                   value="<?= old('place_of_birth') ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="nationality" class="form-label">Nationality</label>
            <input type="text" class="form-control" id="nationality" name="nationality" 
                   value="<?= old('nationality', 'Filipino') ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="religion" class="form-label">Religion</label>
            <input type="text" class="form-control" id="religion" name="religion" 
                   value="<?= old('religion') ?>">
          </div>
        </div>
      </div>

      <!-- Academic Information -->
      <div class="row mb-4">
        <div class="col-12">
          <h6 class="text-primary border-bottom pb-2 mb-3">Academic Information</h6>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="grade_level" class="form-label">Grade Level <span class="text-danger">*</span></label>
            <select class="form-select" id="grade_level" name="grade_level" required>
              <option value="">Select Grade Level</option>
              <option value="Grade 7" <?= old('grade_level') === 'Grade 7' ? 'selected' : '' ?>>Grade 7</option>
              <option value="Grade 8" <?= old('grade_level') === 'Grade 8' ? 'selected' : '' ?>>Grade 8</option>
              <option value="Grade 9" <?= old('grade_level') === 'Grade 9' ? 'selected' : '' ?>>Grade 9</option>
              <option value="Grade 10" <?= old('grade_level') === 'Grade 10' ? 'selected' : '' ?>>Grade 10</option>
              <option value="Grade 11" <?= old('grade_level') === 'Grade 11' ? 'selected' : '' ?>>Grade 11</option>
              <option value="Grade 12" <?= old('grade_level') === 'Grade 12' ? 'selected' : '' ?>>Grade 12</option>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="section_id" class="form-label">Section</label>
            <select class="form-select" id="section_id" name="section_id">
              <option value="">No Section Assigned</option>
              <?php foreach ($sections as $section): ?>
                <option value="<?= $section['id'] ?>" <?= old('section_id') == $section['id'] ? 'selected' : '' ?>>
                  <?= esc($section['section_name']) ?>
                </option>
              <?php endforeach; ?>
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

      <!-- Emergency Contact -->
      <div class="row mb-4">
        <div class="col-12">
          <h6 class="text-primary border-bottom pb-2 mb-3">Emergency Contact</h6>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="emergency_contact_name" class="form-label">Contact Name</label>
            <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" 
                   value="<?= old('emergency_contact_name') ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="emergency_contact_number" class="form-label">Contact Number</label>
            <input type="text" class="form-control" id="emergency_contact_number" name="emergency_contact_number" 
                   value="<?= old('emergency_contact_number') ?>">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="emergency_contact_relationship" class="form-label">Relationship</label>
            <input type="text" class="form-control" id="emergency_contact_relationship" name="emergency_contact_relationship" 
                   value="<?= old('emergency_contact_relationship') ?>" placeholder="e.g., Parent, Guardian, Sibling">
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-end gap-2">
        <a href="<?= base_url('admin/students') ?>" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-check-circle"></i> Create Student
        </button>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>
