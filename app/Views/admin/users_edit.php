<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Edit User</h1>
  <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <?= form_open('admin/users/edit/' . $user->id) ?>
      <?= csrf_field() ?>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control <?= isset($validation) && $validation->hasError('email') ? 'is-invalid' : '' ?>" 
               id="email" name="email" value="<?= old('email', $user->email) ?>" required>
        <?php if (isset($validation) && $validation->hasError('email')): ?>
          <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
        <?php endif; ?>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password <small class="text-muted">(leave blank to keep current)</small></label>
        <input type="password" class="form-control <?= isset($validation) && $validation->hasError('password') ? 'is-invalid' : '' ?>" 
               id="password" name="password">
        <?php if (isset($validation) && $validation->hasError('password')): ?>
          <div class="invalid-feedback"><?= $validation->getError('password') ?></div>
        <?php endif; ?>
      </div>

      <div class="mb-3">
        <label for="role" class="form-label">Role</label>
        <?php $userGroups = $user->getGroups(); $currentRole = !empty($userGroups) ? $userGroups[0] : ''; ?>
        <select class="form-select <?= isset($validation) && $validation->hasError('role') ? 'is-invalid' : '' ?>" 
                id="role" name="role" required>
          <option value="">Select Role</option>
          <option value="admin" <?= old('role', $currentRole) === 'admin' ? 'selected' : '' ?>>Admin</option>
          <option value="teacher" <?= old('role', $currentRole) === 'teacher' ? 'selected' : '' ?>>Teacher</option>
          <option value="student" <?= old('role', $currentRole) === 'student' ? 'selected' : '' ?>>Student</option>
          <option value="parent" <?= old('role', $currentRole) === 'parent' ? 'selected' : '' ?>>Parent</option>
        </select>
        <?php if (isset($validation) && $validation->hasError('role')): ?>
          <div class="invalid-feedback"><?= $validation->getError('role') ?></div>
        <?php endif; ?>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Cancel</a>
      </div>
    <?= form_close() ?>
  </div>
</div>

<?= $this->endSection() ?>