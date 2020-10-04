<?php

class Candidates_model extends CI_Model {

    public $display_items = 15;

    public function __construct() {
        $this->load->database();
    }

    public function create($data) {
        $this->load->helper('string');

        $data['candidate_id'] = '';
        do {
            $data['candidate_id'] = random_string('alnum', 8);
        } while ($this->exists($data['candidate_id']));

        $this->db->insert('candidates', $data);
        return $data['candidate_id'];
    }

    public function get($candidate_id = NULL, $columns = '*', $params = NULL) {
        $this->db->select($columns)->from('candidates');
        if (isset($params)) {
            $this->db->where($params);
        }
        if (isset($candidate_id)) {
            $this->db->where('candidate_id', $candidate_id);
            return $this->db->get()->row_array(0);
        }
        $this->db->order_by('name');
        $data = $this->db->get()->result_array();
        if (!isset($data)) {
            $data = [];
        }
        return $data;
    }

    public function search($page = 1, $filter = '') {
        if ($page < 1) {
            show_error('Kesalahan parameter', 400);
        }

        $this->db->select('candidate_id,name,description')->from('candidates');
        $count = $this->db->count_all_results('', FALSE);
        $max_page = ceil($count / $this->display_items);
        if ($page > $max_page) {
            show_error('Kesalahan parameter', 400);
        }

        $this->db->like('name', $filter);
        $this->db->limit($this->display_items, ($page - 1) * $this->display_items);
        $this->db->order_by('name');

        $data = $this->db->get()->result_array();
        if (!isset($data)) {
            $data = [];
        }
        return $data;
    }

    public function set($candidate_id, $data) {
        $this->db->where('candidate_id', $candidate_id);
        $this->db->update('candidates', $data);
    }

    public function delete($candidate_id) {
        $this->db->where('candidate_id', $candidate_id);
        $this->db->update('tokens', ['candidate_id' => NULL]);

        $this->db->where('candidate_id', $candidate_id);
        $this->db->delete('candidates');

        if (file_exists($this->ezvote->image_path('candidate_'.$candidate_id))) {
            unlink($this->ezvote->image_path('candidate_'.$candidate_id));
        }
    }

    public function exists($candidate_id) {
        return $this->db->select('candidate_id')->from('candidates')->where('candidate_id', $candidate_id)->count_all_results() != 0;
    }

}

?>
