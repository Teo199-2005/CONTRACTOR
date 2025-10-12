<?php

namespace App\Libraries;

class SupabaseEmailService
{
    private $supabaseUrl;
    private $supabaseKey;
    private $fromEmail;
    
    public function __construct()
    {
        $this->supabaseUrl = 'https://qycsakbshrdgwxbpusbg.supabase.co';
        $this->supabaseKey = env('SUPABASE_ANON_KEY', ''); // You'll set this in .env
        $this->fromEmail = 'noreply@lphs.edu.ph';
    }
    
    public function sendVerificationEmail($toEmail, $studentName, $lrn, $tempPassword = null)
    {
        $subject = 'LPHS - Your Enrollment Application Has Been Approved';
        
        $message = $this->getEmailTemplate($studentName, $lrn, $tempPassword);
        
        $result = $this->sendEmail($toEmail, $subject, $message);
        log_message('info', 'Email processing completed for: ' . $toEmail);
        return $result;
    }
    
    private function sendEmail($to, $subject, $htmlContent)
    {
        // Try Supabase Auth email first
        if ($this->sendViaSupabase($to, $subject, $htmlContent)) {
            return true;
        }
        
        // Fallback to direct SMTP
        return $this->sendViaSMTP($to, $subject, $htmlContent);
    }
    
    private function sendViaSupabase($to, $subject, $htmlContent)
    {
        try {
            $supabaseUrl = 'https://qycsakbshrdgwxbpusbg.supabase.co';
            $supabaseKey = $this->supabaseKey;
            
            if (empty($supabaseKey)) {
                log_message('info', 'Supabase key not configured, skipping Supabase email');
                return false;
            }
            
            // Use Supabase Auth to send email
            $data = [
                'email' => $to,
                'data' => [
                    'subject' => $subject,
                    'html_content' => $htmlContent
                ]
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $supabaseUrl . '/auth/v1/admin/users');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $supabaseKey,
                'apikey: ' . $supabaseKey
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 || $httpCode === 201) {
                log_message('info', 'Email sent via Supabase to: ' . $to);
                return true;
            } else {
                log_message('info', 'Supabase email failed, trying SMTP fallback');
                return false;
            }
        } catch (Exception $e) {
            log_message('error', 'Supabase email error: ' . $e->getMessage());
            return false;
        }
    }
    
    private function sendViaSMTP($to, $subject, $htmlContent)
    {
        $email = \Config\Services::email();
        $gmailPass = env('GMAIL_APP_PASSWORD', '');
        
        if (empty($gmailPass)) {
            log_message('info', 'GMAIL_APP_PASSWORD not configured - Email sending disabled');
            log_message('info', 'MANUAL EMAIL NEEDED - Send to: ' . $to . ' | Subject: ' . $subject);
            return false; // Return false to indicate email wasn't sent
        }
        
        $config = [
            'protocol' => 'smtp',
            'SMTPHost' => 'smtp.gmail.com',
            'SMTPUser' => 'lphscodenectars@gmail.com',
            'SMTPPass' => $gmailPass,
            'SMTPPort' => 587,
            'SMTPCrypto' => 'tls',
            'mailType' => 'html',
            'charset' => 'utf-8'
        ];
        
        $email->initialize($config);
        $email->setFrom('lphscodenectars@gmail.com', 'LPHS School System');
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($htmlContent);
        
        $result = $email->send();
        
        if (!$result) {
            log_message('error', 'SMTP Email failed: ' . $email->printDebugger());
            log_message('info', 'MANUAL EMAIL NEEDED - Send to: ' . $to . ' | Subject: ' . $subject);
            return false; // Return false when email fails
        } else {
            log_message('info', 'Email sent via SMTP to: ' . $to);
            return true;
        }
        
        return $result;
    }
    
    private function getEmailTemplate($studentName, $lrn, $tempPassword = null)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #1e40af; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .button { background: #fbbf24; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0; }
                .credentials { background: white; padding: 15px; border-left: 4px solid #fbbf24; margin: 15px 0; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎓 LPHS Enrollment Approved!</h1>
                    <p>Lourdes Provincial High School</p>
                </div>
                
                <div class='content'>
                    <h2>Congratulations, {$studentName}!</h2>
                    
                    <p>Your enrollment application has been <strong>approved</strong> by our school administrators.</p>
                    
                    <div class='credentials'>
                        <h3>📧 Your Login Credentials:</h3>
                        <p><strong>LRN (Learner Reference Number):</strong> <code>{$lrn}</code></p>
                        <p><strong>Password:</strong> <code>{$tempPassword}</code></p>
                    </div>
                    
                    <p>You can now access the student portal using these credentials:</p>
                    
                    <a href='" . base_url('login') . "' class='button'>Login to Student Portal</a>
                    
                    <h3>⚠️ Important:</h3>
                    <ul>
                        <li>Use your LRN (not email) as your username to login</li>
                        <li>Please change your password after first login</li>
                        <li>Keep your login credentials secure</li>
                        <li>Contact the school office if you have any issues</li>
                    </ul>
                    
                    <p>Welcome to Lourdes Provincial High School! We look forward to your academic journey with us.</p>
                </div>
                
                <div class='footer'>
                    <p>Lourdes Provincial High School<br>
                    Barangay Lourdes, Panglao Town, Bohol, Philippines<br>
                    📞 +63 38 502 9000 | 📧 info@lphs.edu.ph</p>
                </div>
            </div>
        </body>
        </html>";
    }
}