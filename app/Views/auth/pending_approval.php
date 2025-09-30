<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<style>
.pending-container {
  height: calc(100vh - 80px);
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 4rem 1rem;
  position: relative;
  margin: -2rem -15px -2rem -15px;
}

.pending-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%233b82f6" stroke-width="0.5" opacity="0.2"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
  opacity: 1;
}

.pending-card {
  background: rgba(30, 64, 175, 0.95);
  backdrop-filter: blur(25px);
  border: 1px solid rgba(59, 130, 246, 0.3);
  border-radius: 20px;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(59, 130, 246, 0.2);
  width: 100%;
  max-width: 500px;
  overflow: hidden;
  position: relative;
  z-index: 1;
  text-align: center;
}

.pending-header {
  padding: 3rem 2.5rem 2rem;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(147, 197, 253, 0.05) 100%);
  border-bottom: 1px solid rgba(59, 130, 246, 0.1);
}

.pending-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1.5rem;
  box-shadow: 0 8px 24px rgba(251, 191, 36, 0.3);
}

.pending-title {
  font-size: 2rem;
  font-weight: 800;
  color: white;
  margin-bottom: 0.5rem;
  font-family: 'Times New Roman', serif;
}

.pending-subtitle {
  color: rgba(255, 255, 255, 0.8);
  font-size: 1rem;
  margin: 0;
}

.pending-body {
  padding: 2rem 2.5rem;
}

.pending-message {
  color: rgba(255, 255, 255, 0.9);
  font-size: 1.1rem;
  line-height: 1.6;
  margin-bottom: 2rem;
}

.pending-steps {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  text-align: left;
}

.pending-steps h6 {
  color: white;
  font-weight: 600;
  margin-bottom: 1rem;
}

.pending-steps ol {
  color: rgba(255, 255, 255, 0.8);
  margin: 0;
  padding-left: 1.2rem;
}

.pending-steps li {
  margin-bottom: 0.5rem;
}

.back-btn {
  background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
  color: white;
  border: none;
  padding: 0.75rem 2rem;
  border-radius: 12px;
  font-weight: 600;
  text-decoration: none;
  display: inline-block;
  transition: all 0.3s ease;
  box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
}

.back-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(107, 114, 128, 0.4);
  color: white;
  text-decoration: none;
}
</style>

<div class="pending-container">
  <div class="pending-card">
    <div class="pending-header">
      <div class="pending-icon">
        <i class="bi bi-clock-history text-white" style="font-size: 2rem;"></i>
      </div>
      <h1 class="pending-title">Enrollment Pending</h1>
      <p class="pending-subtitle">Your application is under review</p>
    </div>

    <div class="pending-body">
      <p class="pending-message">
        Thank you for submitting your enrollment application to Lourdes Provincial High School. 
        Your application is currently being reviewed by our administration team.
      </p>

      <div class="pending-steps">
        <h6>What happens next?</h6>
        <ol>
          <li>Admin reviews your application and documents</li>
          <li>You'll receive an email notification about the decision</li>
          <li>Once approved, you can access the student portal</li>
          <li>Complete any remaining enrollment requirements</li>
        </ol>
      </div>

      <p class="pending-message">
        <strong>Need help?</strong><br>
        Contact our enrollment office at <strong>info@lphs.edu.ph</strong> or call <strong>+63 38 502 9000</strong>
      </p>

      <a href="<?= base_url('login') ?>" class="back-btn">
        <i class="bi bi-arrow-left me-2"></i>Back to Login
      </a>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
