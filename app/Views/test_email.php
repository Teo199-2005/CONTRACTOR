<!DOCTYPE html>
<html>
<head>
    <title>Email Test - LPHS SMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Gmail SMTP Test</h3>
                    </div>
                    <div class="card-body">
                        <button id="testBtn" class="btn btn-primary">Send Test Email</button>
                        <div id="result" class="mt-3"></div>
                        
                        <hr>
                        <h5>Gmail Setup Instructions:</h5>
                        <ol>
                            <li>Go to your Gmail account settings</li>
                            <li>Enable 2-Factor Authentication</li>
                            <li>Go to Security → App passwords</li>
                            <li>Generate a new app password for "Mail"</li>
                            <li>Copy the 16-character password (no spaces)</li>
                            <li>Update your .env file: GMAIL_APP_PASSWORD=your16charpassword</li>
                        </ol>
                        
                        <div class="alert alert-info">
                            <strong>Current Gmail Password Status:</strong>
                            <div id="configStatus">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('testBtn').addEventListener('click', function() {
            const btn = this;
            const result = document.getElementById('result');
            
            btn.disabled = true;
            btn.textContent = 'Sending...';
            result.innerHTML = '<div class="alert alert-info">Sending test email...</div>';
            
            fetch('/test-email/send')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        result.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                    } else {
                        let debugHtml = '';
                        if (data.debug) {
                            debugHtml = '<hr><small><strong>Debug Info:</strong><br><pre style="font-size:10px;">' + data.debug + '</pre></small>';
                        }
                        result.innerHTML = '<div class="alert alert-danger">' + 
                            '<strong>Error:</strong> ' + data.message + 
                            '<br><small>Gmail Password Set: ' + (data.config.gmail_password_set ? 'Yes' : 'No') + 
                            '<br>Password Length: ' + data.config.gmail_password_length + '</small>' +
                            debugHtml +
                            '</div>';
                    }
                })
                .catch(error => {
                    result.innerHTML = '<div class="alert alert-danger">Network error: ' + error + '</div>';
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.textContent = 'Send Test Email';
                });
        });
        
        // Check config status on load
        fetch('/test-email/send')
            .then(response => response.json())
            .then(data => {
                const status = document.getElementById('configStatus');
                if (data.config) {
                    status.innerHTML = 'Gmail Password Set: ' + (data.config.gmail_password_set ? 'Yes ✓' : 'No ✗') + 
                                     '<br>Password Length: ' + data.config.gmail_password_length + ' characters';
                }
            });
    </script>
</body>
</html>