<?php

class Tokens_model extends CI_Model {

    public function __construct() {
        $this->load->database();
    }

    public function generate($tokenset_id, $count) {
        $this->load->helper('string');

        $tokenset_exists = $this->db->select('token')->from('tokens')->where('tokenset_id', $tokenset_id)->count_all_results() != 0;
        if (!$tokenset_exists) {
            for ($i = 1; $i <= $count; $i++) {
                $num = $i % $count;

                $prefix = sprintf('%s-%04d', $tokenset_id, $num);

                $data = [];
                $data['token'] = NULL;
                do {
                    $data['token'] = sprintf('%s-%s', $prefix, random_string('alnum', 8));
                } while ($this->exists($data['token']));

                $data['tokenset_id'] = $tokenset_id;
                $data['used'] = 0;
                $data['candidate_id'] = NULL;

                $this->db->insert('tokens', $data);
            }
            return TRUE;
        }
        return FALSE;
    }

    public function get($token = NULL, $columns = '*', $params = NULL) {
        $this->db->select($columns)->from('tokens');
        if (isset($params)) {
            $this->db->where($params);
        }
        if (isset($token)) {
            $this->db->where('token', $token);
            return $this->db->get()->row_array(0);
        }

        $data = $this->db->get()->result_array();
        if (!isset($data)) {
            $data = [];
        }
        return $data;
    }

    public function set($token, $data) {
        $this->db->where('token', $token);
        $this->db->update('tokens', $data);
    }

    public function delete($tokenset_id) {
        $this->db->where('tokenset_id', $tokenset_id);
        $this->db->delete('tokens');
    }

    public function exists($token) {
        return $this->db->select('token')->from('tokens')->where('token', $token)->count_all_results() != 0;
    }

}

?>
