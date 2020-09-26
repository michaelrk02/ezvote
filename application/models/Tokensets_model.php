<?php

class Tokensets_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function create($data) {
        $this->load->helper('text');

        $data['tokenset_id'] = NULL;
        do {
            $data['tokenset_id'] = random_string('alnum', 4);
        } while ($this->exists($data['tokenset_id']));

        $this->db->insert('tokensets', $data);
        return $data['tokenset_id'];
    }

    public function get($tokenset_id = NULL, $columns = '*', $params = NULL) {
        $this->db->select($columns)->from('tokensets');
        if (isset($params)) {
            $this->db->where($params);
        }
        if (isset($tokenset_id)) {
            $this->db->where('tokenset_id', $tokenset_id);
            return $this->db->get()->row_array(0);
        }

        $data = $this->db->get()->result_array();
        if (!isset($data)) {
            $data = [];
        }
        return $data;
    }

    public function set($tokenset_id, $data) {
        $this->db->where('tokenset_id', $tokenset_id);
        $this->db->update('tokensets', $data);
    }

    public function delete($tokenset_id) {
        $this->load->model('tokens_model');

        $this->tokens_model->delete($tokenset_id);

        $this->db->where('tokenset_id', $tokenset_id);
        $this->db->delete('tokensets');
    }

    public function exists($tokenset_id) {
        return $this->db->select('tokenset_id')->from('tokensets')->where('tokenset_id', $tokenset_id)->count_all_results() != 0;
    }

}

?>
