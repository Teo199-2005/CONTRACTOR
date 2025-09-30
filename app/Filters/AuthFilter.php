<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Check if user is authenticated
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $auth = service('auth');
        
        // Check if user is logged in
        if (!$auth->loggedIn()) {
            return redirect()->to(base_url('login'));
        }
        
        // If specific group is required, check it
        if (!empty($arguments) && is_array($arguments)) {
            $requiredGroup = $arguments[0];
            if (!$auth->user()->inGroup($requiredGroup)) {
                return redirect()->to(base_url('/'))->with('error', 'Access denied. ' . ucfirst($requiredGroup) . ' role required.');
            }
        }
        
        return $request;
    }

    /**
     * We don't have anything to do here
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
