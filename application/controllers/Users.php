<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends Auth_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    /**
     * User List with Search, Filter & Pagination (Admin & Operator)
     */
    public function index()
    {
        $search = $this->input->get('search', TRUE) ?? '';
        $role   = $this->input->get('role', TRUE) ?? '';
        $status = $this->input->get('status', TRUE) ?? '';

        // Pagination Configuration
        $config['base_url'] = site_url('users');
        $config['total_rows'] = $this->User_model->count_users($search, $role, $status);
        $config['per_page'] = 5;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['reuse_query_string'] = TRUE;

        // Bootstrap 5 Pagination Markup
        $config['full_tag_open'] = '<ul class="pagination pagination-sm justify-content-end mb-0">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = '&laquo; First';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Last &raquo;';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '&rsaquo;';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '&lsaquo;';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close'] = '</span></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $page = (int) ($this->input->get('page') ?? 0);
        $data['users'] = $this->User_model->get_users($config['per_page'], $page, $search, $role, $status);
        $data['pagination'] = $this->pagination->create_links();
        $data['search'] = $search;
        $data['selected_role'] = $role;
        $data['selected_status'] = $status;
        $data['total_rows'] = $config['total_rows'];
        $data['is_admin'] = $this->is_admin();

        $this->load->view('layouts/header', ['title' => 'User Management']);
        $this->load->view('users/index', $data);
        $this->load->view('layouts/footer');
    }

    /**
     * Create User (Admin Only)
     */
    public function create()
    {
        $this->require_admin(); // Server-side security check

        $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[password]');
        $this->form_validation->set_rules('role', 'Role', 'required|in_list[ADMIN,OPERATOR]');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[ACTIVE,INACTIVE]');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('layouts/header', ['title' => 'Add New User']);
            $this->load->view('users/create');
            $this->load->view('layouts/footer');
        } else {
            $userData = [
                'name'     => $this->input->post('name', TRUE),
                'email'    => $this->input->post('email', TRUE),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'role'     => $this->input->post('role', TRUE),
                'status'   => $this->input->post('status', TRUE)
            ];

            if ($this->User_model->insert_user($userData)) {
                $this->session->set_flashdata('success', 'User created successfully.');
            } else {
                $this->session->set_flashdata('error', 'Failed to create user.');
            }
            redirect('users');
        }
    }

    /**
     * Edit User (Admin & Operator have different field permissions)
     */
    public function edit($id)
    {
        $user = $this->User_model->get_by_id($id);
        if (!$user) {
            $this->session->set_flashdata('error', 'User not found.');
            redirect('users');
        }

        $is_admin = $this->is_admin();

        $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_check_unique_email[' . $id . ']');

        // Role & Status are only validated & modifiable if user is ADMIN
        if ($is_admin) {
            $this->form_validation->set_rules('role', 'Role', 'required|in_list[ADMIN,OPERATOR]');
            $this->form_validation->set_rules('status', 'Status', 'required|in_list[ACTIVE,INACTIVE]');
        }

        if ($this->form_validation->run() === FALSE) {
            $data['user'] = $user;
            $data['is_admin'] = $is_admin;
            $this->load->view('layouts/header', ['title' => 'Edit User']);
            $this->load->view('users/edit', $data);
            $this->load->view('layouts/footer');
        } else {
            $updateData = [
                'name'  => $this->input->post('name', TRUE),
                'email' => $this->input->post('email', TRUE)
            ];

            // Server-side enforcement: Operator cannot modify Role or Status
            if ($is_admin) {
                $updateData['role'] = $this->input->post('role', TRUE);
                $updateData['status'] = $this->input->post('status', TRUE);
            }

            if ($this->User_model->update_user($id, $updateData)) {
                $this->session->set_flashdata('success', 'User updated successfully.');
            } else {
                $this->session->set_flashdata('error', 'Failed to update user.');
            }
            redirect('users');
        }
    }

    /**
     * Delete/Deactivate User (Admin Only)
     */
    public function delete($id)
    {
        $this->require_admin(); // Server-side enforcement

        // Prevent admin from deleting themselves
        if ($id == $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'You cannot delete your own account.');
            redirect('users');
        }

        if ($this->User_model->delete_user($id)) {
            $this->session->set_flashdata('success', 'User deleted successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete user.');
        }
        redirect('users');
    }

    /**
     * AJAX Endpoint: Update Status (Admin Only)
     */
    public function update_status_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        // Check Admin privilege
        if (!$this->is_admin()) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Unauthorized. Only admins can toggle user status.'
                ]));
        }

        $id = $this->input->post('id', TRUE);
        $status = $this->input->post('status', TRUE);

        if (!$id || !in_array($status, ['ACTIVE', 'INACTIVE'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Invalid parameters provided.'
                ]));
        }

        // Prevent admin from deactivating themselves
        if ($id == $this->session->userdata('user_id') && $status === 'INACTIVE') {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'You cannot deactivate your own active session.'
                ]));
        }

        $updated = $this->User_model->update_status($id, $status);

        if ($updated) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => true,
                    'message' => 'User status updated successfully'
                ]));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => false,
                    'message' => 'Unable to update user status'
                ]));
        }
    }

    /**
     * Custom Validation Callback for Unique Email on Edit
     */
    public function check_unique_email($email, $id)
    {
        if (!$this->User_model->is_email_unique_for_user($email, $id)) {
            $this->form_validation->set_message('check_unique_email', 'The {field} is already in use by another user.');
            return FALSE;
        }
        return TRUE;
    }
}
