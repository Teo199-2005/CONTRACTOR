<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1 class="h3">Edit Teacher</h1>
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
    <form method="post" action="<?= base_url('admin/teachers/update/' . $teacher['id']) ?>">
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
                   value="<?= old('teacher_id', $teacher['teacher_id'] ?? '') ?>" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="email" name="email" 
                   value="<?= old('email', $teacher['email']) ?>" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="password" name="password">
            <div class="form-text">Leave blank to keep current password. Minimum 8 characters if changing.</div>
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
                   value="<?= old('first_name', $teacher['first_name']) ?>" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="last_name" name="last_name" 
                   value="<?= old('last_name', $teacher['last_name']) ?>" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
            <select class="form-select" id="gender" name="gender" required>
              <option value="">Select Gender</option>
              <option value="Male" <?= old('gender', $teacher['gender']) === 'Male' ? 'selected' : '' ?>>Male</option>
              <option value="Female" <?= old('gender', $teacher['gender']) === 'Female' ? 'selected' : '' ?>>Female</option>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                   value="<?= old('date_of_birth', $teacher['date_of_birth']) ?>" required>
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
            <label for="department" class="form-label">Department</label>
            <input type="text" class="form-control" id="department" name="department" 
                   value="<?= old('department', $teacher['department']) ?>" placeholder="e.g., Mathematics, Science, English">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="position" class="form-label">Position</label>
            <input type="text" class="form-control" id="position" name="position" 
                   value="<?= old('position', $teacher['position']) ?>" placeholder="e.g., Teacher, Department Head, Principal">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="specialization" class="form-label">Specialization</label>
            <input type="text" class="form-control" id="specialization" name="specialization" 
                   value="<?= old('specialization', $teacher['specialization']) ?>" placeholder="e.g., Algebra, Biology, Literature">
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="hire_date" name="hire_date" 
                   value="<?= old('hire_date', $teacher['hire_date'] ?? '') ?>" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label for="employment_status" class="form-label">Employment Status <span class="text-danger">*</span></label>
            <select class="form-select" id="employment_status" name="employment_status" required>
              <option value="">Select Status</option>
              <option value="active" <?= old('employment_status', $teacher['employment_status']) === 'active' ? 'selected' : '' ?>>Active</option>
              <option value="inactive" <?= old('employment_status', $teacher['employment_status']) === 'inactive' ? 'selected' : '' ?>>Inactive</option>
              <option value="on_leave" <?= old('employment_status', $teacher['employment_status']) === 'on_leave' ? 'selected' : '' ?>>On Leave</option>
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
                   value="<?= old('contact_number', $teacher['contact_number']) ?>">
          </div>
        </div>
        <div class="col-md-12">
          <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" id="address" name="address" rows="3"><?= old('address', $teacher['address']) ?></textarea>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-end gap-2">
        <a href="<?= base_url('admin/teachers') ?>" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-check-circle"></i> Update Teacher
        </button>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>
