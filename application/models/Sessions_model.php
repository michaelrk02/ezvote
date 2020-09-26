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
        $data = $this->db->get()->result_array();
        if (!isset($data)) {
            $data = [];
        }
        return $data;
    }

    public function update($session_id, $data) {
        $this->db->where('session_id', $session_id);
        $this->db->update('sessions', $data);
    }

    public function delete($session_id) {
        $this->load->model('tokensets_model');
        $tokensets = $this->tokensets_model->get(NULL, 'tokenset_id', ['session_id' => $session_id]);
        foreach ($tokensets as $tokenset) {
            $this->tokensets_model->delete($tokenset['tokenset_id']);
        }

        $this->db->where('session_id', $session_id);
        $this->db->delete('sessions');
    }

    public function exists($session_id) {
        return $this->db->select('session_id')->from('sessions')->where('session_id', $session_id)->count_all_results() != 0;
    }

}

?>
