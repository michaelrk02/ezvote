<?php

class Candidates_model extends CI_Model {

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
    }

    public function exists($candidate_id) {
        return $this->db->select('candidate_id')->from('candidates')->count_all_results() != 0;
    }

}

?>
