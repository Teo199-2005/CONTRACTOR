<?= $this->extend('dashboard_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4 mb-0">Manage Users</h1>
  <div>
    <button type="button" class="btn btn-primary me-2" onclick="openAddUserModal()"><i class="bi bi-plus-lg me-2"></i>Add User</button>
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
  </div>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <?php if (empty($users)): ?>
      <p class="text-muted mb-0">No users found.</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>ID</th>
              <th>Email</th>
              <th>Role</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
              <td><?= esc($u->id) ?></td>
              <td><?= esc($u->email) ?></td>
              <td>
                <?php $groups = $u->getGroups(); ?>
                <?= !empty($groups) ? esc($groups[0]) : 'No Role' ?>
              </td>
              <td><?= esc($u->created_at) ?></td>
              <td>
                <a href="<?= base_url('admin/users/edit/' . $u->id) ?>" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i></a>
                <a href="<?= base_url('admin/users/delete/' . $u->id) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user?')"><i class="bi bi-trash"></i></a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
function buildAddUserModal() {
  const existing = document.getElementById('addUserModal');
  if (existing) existing.remove();
  const html = `
  <div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="post" action="<?= base_url('admin/users/create') ?>">
        <?= str_replace(["\n","\r"], '', csrf_field()) ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" minlength="8" required>
            <div class="form-text">Minimum 8 characters.</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
              <option value="">Select Role</option>
              <option value="admin">Admin</option>
              <option value="teacher">Teacher</option>
              <option value="student">Student</option>
              <option value="parent">Parent</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> Create User</button>
        </div>
      </form>
    </div></div>
  </div>`;
  document.body.insertAdjacentHTML('beforeend', html);
  return document.getElementById('addUserModal');
}

function openAddUserModal() {
  const el = buildAddUserModal();
  new bootstrap.Modal(el, { backdrop: true, keyboard: true, focus: true }).show();
}
</script>


<?= $this->endSection() ?>


</script>




