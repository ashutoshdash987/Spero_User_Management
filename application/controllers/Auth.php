<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    /**
     * Handle Login View & Submission
     */
    public function login()
    {
        // If already logged in, redirect to users list
        if ($this->session->userdata('logged_in')) {
            redirect('users');
        }

        // Set Validation Rules
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('auth/login');
        } else {
            $email = $this->input->post('email', TRUE);
            $password = $this->input->post('password');

            $user = $this->User_model->get_by_email($email);

            if ($user) {
                // Verify password hash
                if (password_verify($password, $user['password'])) {
                    // Check if user status is ACTIVE
                    if ($user['status'] === 'ACTIVE') {
                        // Set session data as per requirements
                        $session_data = [
                            'user_id'   => $user['id'],
                            'user_name' => $user['name'],
                            'user_role' => $user['role'],
                            'logged_in' => TRUE
                        ];
                        $this->session->set_userdata($session_data);
                        $this->session->set_flashdata('success', 'Welcome back, ' . $user['name'] . '!');
                        redirect('users');
                    } else {
                        $this->session->set_flashdata('error', 'Your account is INACTIVE. Please contact administrator.');
                        redirect('login');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Invalid email or password.');
                    redirect('login');
                }
            } else {
                $this->session->set_flashdata('error', 'Invalid email or password.');
                redirect('login');
            }
        }
    }

    /**
     * Handle Logout
     */
    public function logout()
    {
        $this->session->unset_userdata(['user_id', 'user_name', 'user_role', 'logged_in']);
        $this->session->sess_destroy();
        redirect('login');
    }
}
