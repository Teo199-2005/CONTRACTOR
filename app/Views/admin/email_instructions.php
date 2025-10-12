<?= $this->extend('dashboard_layout') ?>

<?= $this->section('title') ?>Email Setup Instructions<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📧 Email System Setup Instructions</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Current Status</h5>
                        The email system is configured but requires proper SMTP credentials to send emails automatically.
                        Students are being approved successfully, but emails need to be sent manually.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Option 1: Gmail SMTP (Recommended)</h3>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Go to your Google Account settings</li>
                                        <li>Navigate to <strong>Security</strong></li>
                                        <li>Enable <strong>2-Step Verification</strong> if not already enabled</li>
                                        <li>Go to <strong>App passwords</strong></li>
                                        <li>Generate a new app password for "Mail"</li>
                                        <li>Copy the 16-character password</li>
                                        <li>Update your <code>.env</code> file:</li>
                                    </ol>
                                    <pre class="bg-light p-2">GMAIL_APP_PASSWORD = your_16_character_password</pre>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title">Option 2: Mailtrap (For Testing)</h3>
                                </div>
                                <div class="card-body">
                                    <ol>
                                        <li>Sign up at <a href="https://mailtrap.io" target="_blank">mailtrap.io</a></li>
                                        <li>Create a new inbox</li>
                                        <li>Get your SMTP credentials</li>
                                        <li>Update your <code>.env</code> file:</li>
                                    </ol>
                                    <pre class="bg-light p-2">MAILTRAP_USERNAME = your_username
MAILTRAP_PASSWORD = your_password</pre>
                                    <div class="alert alert-warning">
                                        <small><strong>Note:</strong> Mailtrap captures emails for testing - they won't reach real inboxes.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Manual Email Template</h3>
                        </div>
                        <div class="card-body">
                            <p>Until email is configured, use this template to manually send approval emails:</p>
                            <div class="bg-light p-3">
                                <strong>Subject:</strong> LPHS - Your Enrollment Application Has Been Approved<br><br>
                                <strong>Message:</strong><br>
                                Dear [Student Name],<br><br>
                                Congratulations! Your enrollment application has been approved.<br><br>
                                <strong>Login Credentials:</strong><br>
                                Email: [Student Email]<br>
                                Temporary Password: [Generated Password]<br><br>
                                Please login at: <?= base_url('login') ?><br>
                                Change your password after first login.<br><br>
                                Welcome to LPHS!<br>
                                School Administration
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="<?= base_url('admin/students/pending') ?>" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to Pending Applications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>