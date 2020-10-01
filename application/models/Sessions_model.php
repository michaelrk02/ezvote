<?php

class Sessions_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function create($data) {
        $this->load->helper('string');

        $data['session_id'] = NULL;
        do {
            $data['session_id'] = random_string('alnum', 8);
        } while ($this->exists($data['session_id']));

        $this->db->insert('sessions', $data);
        return $data['session_id'];
    }

    public function get($session_id = NULL, $columns = '*', $param = NULL) {
        $this->db->select($columns)->from('sessions');
        if (isset($param)) {
            $this->db->where($param);
        }
        if (isset($session_id)) {
            $this->db->where('session_id', $session_id);
            return $this->db->get()->row_array(0);
        }
        $this->db->order_by('title');
        $data = $this->db->get()->result_array();
        if (!isset($data)) {
            $data = [];
        }
        return $data;
    }

    public function set($session_id, $data) {
        $this->db->where('session_id', $session_id);
        $this->db->update('sessions', $data);
    }

    public function delete($session_id) {
        $candidates_exist = $this->db->select('candidate_id')->from('candidates')->where('session_id', $session_id)->count_all_results() != 0;
        $tokensets_exist = $this->db->select('tokenset_id')->from('tokensets')->where('session_id', $session_id)->count_all_results() != 0;

        if (!$candidates_exist && !$tokensets_exist) {
            $this->db->where('session_id', $session_id);
            $this->db->delete('sessions');

            return TRUE;
        }
        return FALSE;
    }

    public function exists($session_id) {
        return $this->db->select('session_id')->from('sessions')->where('session_id', $session_id)->count_all_results() != 0;
    }

}

?>
