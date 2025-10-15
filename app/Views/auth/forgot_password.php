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

.register-link:hover {
  color: #f59e0b;
  transform: translateY(-1px);
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
  <div class="login-card">
    <div class="login-header">
      <h1 class="login-title" style="font-family: 'Times New Roman', serif; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, white 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Lourdes Provincial High School</h1>
      <p class="login-subtitle">Reset Password</p>
      <p style="color: rgba(255, 255, 255, 0.7); font-size: 0.8rem; margin: 0.5rem 0 0 0;">Enter your PRC License Number (teachers) or LRN (students) to verify your identity.</p>
    </div>

    <div class="login-form">
                <?php if ($success = session('success')): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i><?= esc($success) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error = session('error')): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i><?= esc($error) ?>
                    </div>
                <?php endif; ?>

      <form method="post" action="<?= base_url('forgot-password/verify') ?>">
        <?= csrf_field() ?>
        
        <div class="mb-3">
          <input type="text" class="form-control" id="identifier" name="identifier"
                 placeholder="PRC License Number or LRN" required>
          <div class="form-text text-white-50 mt-2">
            Teachers: Enter your PRC License Number<br>
            Students: Enter your LRN (Learning Reference Number)
          </div>
        </div>

        <button type="submit" class="login-btn">
          <i class="bi bi-shield-check me-2"></i>VERIFY IDENTITY
        </button>
      </form>

      <div class="register-section">
        <p class="register-text">
          Remember your password? 
          <a href="<?= base_url('login') ?>" class="register-link">
            <i class="bi bi-arrow-left me-1"></i>Back to Login
          </a>
        </p>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>