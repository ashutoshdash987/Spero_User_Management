<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Base Controller: Auth_Controller
 * Enforces that user is logged in.
 */
class Auth_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();

        // Check if user is authenticated
        if (!$this->session->userdata('logged_in')) {
            $this->session->set_flashdata('error', 'Please login to access this page.');
            redirect('login');
        }
    }

    /**
     * Check if currently logged in user is Admin
     */
    protected function is_admin() {
        return $this->session->userdata('user_role') === 'ADMIN';
    }

    /**
     * Server-side hard restriction for Admin-only routes
     */
    protected function require_admin() {
        if (!$this->is_admin()) {
            if ($this->input->is_ajax_request()) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(403)
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Unauthorized action. Admin permission required.'
                    ]));
                exit;
            }

            $this->session->set_flashdata('error', 'Unauthorized! Admin access required.');
            redirect('users');
        }
    }
}