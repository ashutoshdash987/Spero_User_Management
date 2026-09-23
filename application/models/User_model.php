<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    protected $table = 'users';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get user by email for authentication
     */
    public function get_by_email($email) {
        return $this->db->get_where($this->table, ['email' => $email])->row_array();
    }

    /**
     * Get user by ID (excludes password hash for safety)
     */
    public function get_by_id($id) {
        $this->db->select('id, name, email, role, status, created_at, updated_at');
        return $this->db->get_where($this->table, ['id' => $id])->row_array();
    }

    /**
     * Count total users with search/filter criteria (For Pagination)
     */
    public function count_users($search = '', $role = '', $status = '') {
        $this->_apply_filters($search, $role, $status);
        return $this->db->count_all_results($this->table);
    }

    /**
     * Get filtered & paginated users list
     */
    public function get_users($limit, $offset, $search = '', $role = '', $status = '') {
        $this->db->select('id, name, email, role, status, created_at, updated_at');
        $this->_apply_filters($search, $role, $status);
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $offset);
        return $this->db->get($this->table)->result_array();
    }

    /**
     * Internal helper to apply filters to query
     */
    private function _apply_filters($search = '', $role = '', $status = '') {
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('email', $search);
            $this->db->group_end();
        }

        if (!empty($role)) {
            $this->db->where('role', $role);
        }

        if (!empty($status)) {
            $this->db->where('status', $status);
        }
    }

    /**
     * Insert new user
     */
    public function insert_user($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update user details
     */
    public function update_user($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Update user status (for AJAX toggle)
     */
    public function update_status($id, $status) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Delete user
     */
    public function delete_user($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    /**
     * Check if email is unique excluding current user ID
     */
    public function is_email_unique_for_user($email, $id) {
        $this->db->where('email', $email);
        $this->db->where('id !=', $id);
        $query = $this->db->get($this->table);
        return $query->num_rows() === 0;
    }
}