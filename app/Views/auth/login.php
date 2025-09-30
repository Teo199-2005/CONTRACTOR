<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<style>
.login-container {
  height: calc(100vh - 80px);
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem 1rem;
  position: relative;
  margin: -2rem -15px -2rem -15px;
}

.login-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%233b82f6" stroke-width="0.5" opacity="0.2"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
  opacity: 1;
}

.login-card {
  background: rgba(30, 64, 175, 0.95);
  backdrop-filter: blur(25px);
  border: 1px solid rgba(59, 130, 246, 0.3);
  border-radius: 20px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(59, 130, 246, 0.2);
  width: 100%;
  max-width: 440px;
  overflow: hidden;
  position: relative;
  z-index: 1;
}

.login-header {
  text-align: center;
  padding: 3rem 2.5rem 2rem;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(147, 197, 253, 0.05) 100%);
  border-bottom: 1px solid rgba(59, 130, 246, 0.1);
}

.login-title {
  font-size: 2.25rem;
  font-weight: 800;
  color: white;
  margin-bottom: 0.75rem;
  letter-spacing: -0.025em;
}

.login-subtitle {
  color: rgba(255, 255, 255, 0.9);
  font-size: 1rem;
  font-weight: 500;
  margin: 0;
}

.login-form {
  padding: 2rem 2.5rem 2.5rem;
}

.form-floating {
  margin-bottom: 1.5rem;
}

.form-floating > .form-control {
  border: 2px solid #e2e8f0;
  border-radius: 14px;
  padding: 1.125rem 1rem;
  font-size: 1rem;
  background: #f8fafc;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-weight: 500;
}

.form-floating > .form-control:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
  background: white;
  transform: translateY(-1px);
}

.form-control {
  border: 2px solid #e2e8f0;
  border-radius: 14px;
  padding: 1.25rem 1rem;
  font-size: 1rem;
  background: #f8fafc;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-weight: 500;
  height: auto;
}

.form-control:focus {
  border-color: #3b82f6;
  box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
  background: white;
  transform: translateY(-1px);
  outline: none;
}

.form-floating > label {
  color: #000000;
  font-weight: 600;
  font-size: 0.95rem;
}

.remember-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 2.5rem;
}

.forgot-password-section {
  display: flex;
  align-items: center;
}

.form-check {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.form-check-input {
  width: 1.2rem;
  height: 1.2rem;
  border-radius: 6px;
  border: 2px solid #d1d5db;
  transition: all 0.2s ease;
}

.form-check-input:checked {
  background-color: #3b82f6;
  border-color: #3b82f6;
  transform: scale(1.05);
}

.form-check-label {
  color: white;
  font-size: 0.95rem;
  font-weight: 600;
  margin: 0;
  cursor: pointer;
}

.forgot-link {
  color: #3b82f6;
  text-decoration: none;
  font-size: 0.9rem;
  font-weight: 500;
  transition: color 0.2s ease;
}

.forgot-link:hover {
  color: #fbbf24;
  text-decoration: underline;
}

.login-btn {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%);
  border: none;
  border-radius: 14px;
  color: white;
  font-weight: 700;
  font-size: 1.05rem;
  letter-spacing: 0.025em;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  margin-bottom: 2rem;
  position: relative;
  overflow: hidden;
}

.login-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 15px 35px rgba(251, 191, 36, 0.4);
  background: linear-gradient(135deg, #f59e0b 0%, #ea580c 50%, #dc2626 100%);
}

.login-btn:active {
  transform: translateY(0);
  box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
}

.register-section {
  text-align: center;
  padding-top: 2rem;
  border-top: 1px solid rgba(59, 130, 246, 0.15);
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.02) 0%, rgba(147, 197, 253, 0.02) 100%);
  margin: 0 -2.5rem -2.5rem;
  padding-left: 2.5rem;
  padding-right: 2.5rem;
  padding-bottom: 2.5rem;
}

.register-text {
  color: rgba(255, 255, 255, 0.8);
  font-size: 0.95rem;
  font-weight: 500;
  margin: 0;
}

.register-link {
  color: #fbbf24;
  text-decoration: none;
  font-weight: 700;
  transition: all 0.2s ease;
  position: relative;
}

.register-link::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 0;
  height: 2px;
  background: linear-gradient(135deg, #fbbf24, #f59e0b);
  transition: width 0.3s ease;
}

.register-link:hover {
  color: #f59e0b;
  transform: translateY(-1px);
}

.register-link:hover::after {
  width: 100%;
}

.demo-dropdown {
  position: fixed;
  top: 5.5rem;
  right: 2rem;
  z-index: 1000;
}

.demo-btn {
  background: #3b82f6;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  color: white;
  font-weight: 500;
  padding: 0.5rem 1rem;
  font-size: 0.875rem;
  transition: all 0.3s ease;
}

.demo-btn:hover {
  background: #2563eb;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.demo-dropdown .dropdown-menu {
  background: #3b82f6;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.demo-dropdown .dropdown-item {
  color: white;
}

.demo-dropdown .dropdown-item:hover {
  background: #2563eb;
  color: white;
}

.alert {
  border: none;
  border-radius: 12px;
  padding: 1rem;
  margin-bottom: 1.5rem;
  font-size: 0.9rem;
}

.alert-danger {
  background: #fef2f2;
  color: #dc2626;
  border-left: 4px solid #dc2626;
}

.alert-success {
  background: #f0fdf4;
  color: #16a34a;
  border-left: 4px solid #16a34a;
}
</style>

<div class="login-container">
  <!-- Demo Login Dropdown -->
  <div class="demo-dropdown">
    <div class="dropdown">
      <button class="demo-btn dropdown-toggle" type="button" id="demoLoginMenu" data-bs-toggle="dropdown">
        <i class="bi bi-person-check me-1"></i> Demo Login
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="border-radius: 12px;">
        <li><a class="dropdown-item" href="<?= base_url('login/demo/admin') ?>">Login as Admin</a></li>
        <li><a class="dropdown-item" href="<?= base_url('login/demo/teacher') ?>">Login as Teacher</a></li>
        <li><a class="dropdown-item" href="<?= base_url('login/demo/newstudent') ?>">Login as New Student</a></li>
      </ul>
    </div>
  </div>

  <div class="login-card">
    <div class="login-header">
      <h1 class="login-title" style="font-family: 'Times New Roman', serif; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, white 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Lourdes Provincial High School</h1>
      <p class="login-subtitle">Student Management System</p>
      <p style="color: rgba(255, 255, 255, 0.7); font-size: 0.8rem; margin: 0.5rem 0 0 0;">Version 2.1.0 | Secure Portal</p>
    </div>

    <div class="login-form">
      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
          <?= session()->getFlashdata('error') ?>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
          <?= session()->getFlashdata('success') ?>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
          <ul class="mb-0 ps-3">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
              <li><?= esc($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="<?= base_url('login') ?>">
        <?= csrf_field() ?>
        
        <div class="mb-3">
          <input type="email" class="form-control" id="email" name="email"
                 placeholder="name@example.com" value="<?= old('email') ?>" required>
        </div>

        <div class="mb-3">
          <input type="password" class="form-control" id="password" name="password"
                 placeholder="Password" required>
        </div>

        <div class="remember-section">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
            <label class="form-check-label" for="remember">Keep me signed in</label>
          </div>
          <div class="forgot-password-section">
            <a href="<?= base_url('forgot-password') ?>" class="forgot-link">Forgot Password?</a>
          </div>
        </div>

        <button type="submit" class="login-btn">ACCESS SYSTEM</button>
      </form>

      <div class="register-section">
        <p class="register-text">
          New student? 
          <a href="<?= base_url('register') ?>" class="register-link">Create Account</a>
        </p>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

