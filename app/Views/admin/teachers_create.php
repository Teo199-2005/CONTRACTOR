<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3">Add New Teacher</h1>
  <a href="<?= base_url('admin/teachers') ?>" class="btn btn-outline-secondary">
    <i class="bi bi-arrow-left"></i> Back to Teachers
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
            <label for="teacher_id" class="form-label">Teacher ID <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="teacher_id" name="teacher_id" 
                   value="<?= old('teacher_id') ?>" readonly placeholder="Auto-generated">
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
            <label for="hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="hire_date" name="hire_date" 
                   value="<?= old('hire_date') ?>" required>
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
        <a href="<?= base_url('admin/teachers') ?>" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-check-circle"></i> Create Teacher
        </button>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>
