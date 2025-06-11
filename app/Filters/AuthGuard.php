<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // If no session email (not logged in)
        if (!session()->get('email')) {
            // Get the current route/URL
            $currentURL = $request->getUri()->getSegment(1);

            // Check if user is trying to access admin, author, or user area
            if (strpos($currentURL, 'admin') !== false) {
                return redirect()->to(base_url('admin/login'));
            } elseif (strpos($currentURL, 'author') !== false) {
                return redirect()->to(base_url('author'));
            } else {
                // Default login (for general users)
                return redirect()->to(base_url('/login'));
            }
        }

        // Optional: Check user role if needed (e.g., admin trying to access author area)
        // $userRole = session()->get('role');
        // if ($arguments && !in_array($userRole, $arguments)) {
        //     return redirect()->to(base_url('unauthorized'));
        // }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}