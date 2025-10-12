<?php

namespace App\Controllers;

class TestEmail extends BaseController
{
    public function index()
    {
        return view('test_email');
    }
    
    public function send()
    {
        $this->response->setContentType('application/json');
        
        $email = \Config\Services::email();
        $gmailPass = env('GMAIL_APP_PASSWORD', '');
        
        $config = [
            'protocol' => 'smtp',
            'SMTPHost' => 'smtp.gmail.com',
            'SMTPUser' => 'teofiloharry69@gmail.com',
            'SMTPPass' => $gmailPass,
            'SMTPPort' => 587,
            'SMTPCrypto' => 'tls',
            'mailType' => 'html',
            'charset' => 'utf-8'
        ];
        
        $email->initialize($config);
        $email->setFrom('teofiloharry69@gmail.com', 'LPHS Test');
        $email->setTo('harrypogi200519@gmail.com');
        $email->setSubject('LPHS Email Test');
        $email->setMessage('<h1>Test Email</h1><p>If you receive this, Gmail SMTP is working!</p>');
        
        if ($email->send()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Test email sent successfully!']);
        } else {
            $debugInfo = $email->printDebugger(['headers']);
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Email failed',
                'debug' => $debugInfo,
                'config' => [
                    'gmail_password_set' => !empty($gmailPass),
                    'gmail_password_length' => strlen($gmailPass),
                    'smtp_host' => 'smtp.gmail.com',
                    'smtp_port' => 587,
                    'smtp_user' => 'lpnationalhighschool@gmail.com'
                ]
            ]);
        }
    }
}