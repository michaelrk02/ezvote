<?php

class User extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('session');
    }

    public function index() {
        redirect('user/choose_session');
    }

    public function choose_session() {
        if (!empty($_SESSION['ezvote_user_session'])) {
            redirect('user/token');
        }

        $this->load->helper('form');
        $this->load->model('sessions_model');

        if (!empty($this->input->post('submit'))) {
            $this->load->model('sessions_model');

            $session_id = $this->input->post('session_id');
            $automatic = !empty($this->input->post('automatic'));

            if ($this->sessions_model->exists($session_id)) {
                $_SESSION['ezvote_user_session'] = $session_id;
                $_SESSION['ezvote_user_automatic'] = $automatic;
                redirect('user/token');
            } else {
                $this->ezvote->error('Sesi tidak ditemukan');
            }
        }

        $data = [];
        $data['status'] = $this->ezvote->status();
        $data['sessions'] = $this->sessions_model->get(NULL, 'session_id,title');

        $this->load->view('header', ['title' => 'Pilih Sesi']);
        $this->load->view('user/choose_session', $data);
        $this->load->view('footer');
    }

    public function token() {
        $this->session_check();
        if (!empty($_SESSION['ezvote_user_token'])) {
            redirect('user/vote');
        }

        $automatic = $_SESSION['ezvote_user_automatic'];
        if ($automatic) {
            $this->token_automatic();
        } else {
            $this->token_manual();
        }
    }

    private function token_manual() {
        $this->load->helper('form');
        $this->load->model('sessions_model');

        if (!empty($this->input->post('submit'))) {
            $this->load->model('tokens_model');

            $token_id = $this->input->post('token');

            $token = $this->tokens_model->get($token_id, 'tokenset_id,candidate_id');
            if (isset($token)) {
                if (!isset($token['candidate_id'])) {
                    $this->load->model('tokensets_model');

                    $tokenset = $this->tokensets_model->get($token['tokenset_id'], 'session_id');
                    if (isset($tokenset)) {
                        $_SESSION['ezvote_user_token'] = $token_id;
                        redirect('user/vote');
                    } else {
                        $this->ezvote->error('Token tersebut tidak terdaftar pada sesi yang anda pilih');
                    }
                } else {
                    $this->ezvote->error('Token tersebut telah terpakai. Silakan untuk memilih token yang lain');
                }
            } else {
                $this->ezvote->error('Token tidak valid. Mohon cek penulisan sekali lagi');
            }
        }

        $data = [];
        $data['session'] = $this->sessions_model->get($_SESSION['ezvote_user_session'], 'session_id,title,description,tagline');
        $data['status'] = $this->ezvote->status();

        $this->load->view('header', ['title' => 'Masukkan Token']);
        $this->load->view('user/token_manual', $data);
        $this->load->view('footer');
    }

    private function token_automatic() {
        $this->load->view('header', ['title' => 'Pilih Tokenset']);
        $this->load->view('user/token_automatic', ['status' => $this->ezvote->status()]);
        $this->load->view('footer');
    }

    public function vote() {
        $this->session_check();
        $this->token_check();

        $this->load->database();
        $this->load->helper('form');
        $this->load->model('candidates_model');

        if (!empty($this->input->post('submit'))) {
            $candidate_id = $this->input->post('candidate_id');
            if ($this->candidates_model->exists($candidate_id)) {
                $this->load->model('tokens_model');

                if ($this->tokens_model->exists($_SESSION['ezvote_user_token'])) {
                    $this->tokens_model->set($_SESSION['ezvote_user_token'], ['candidate_id' => $candidate_id]);
                    redirect('user/token_logout');
                } else {
                    $this->ezvote->error('Token tidak ditemukan');
                    unset($_SESSION['ezvote_user_token']);
                    redirect('user/token');
                }
            } else {
                $this->ezvote->error('Kandidat tidak ditemukan');
            }
        }

        $page = $this->input->get('page');
        if (!isset($page)) {
            $page = 1;
        }

        $query = $this->input->get('query');
        if (!isset($query)) {
            $query = '';
        }

        $search_data = $this->db->query('SELECT candidate_id, name, SUBSTRING(description, 1, 30) AS description FROM candidates WHERE session_id = ?', [$_SESSION['ezvote_user_session']])->result_array();
        if (!isset($search_data)) {
            $search_data = [];
        }

        $max_count = count($search_data);
        $max_page = ceil($max_count / $this->candidates_model->display_items);

        $data = [];
        $data['candidates'] = $this->candidates_model->search($page, $query);
        $data['pages'] = $max_page;
        $data['previous'] = $page == 1 ? NULL : site_url(uri_string()).'?query='.urlencode($query).'&page='.($page - 1);
        $data['next'] = $page == $max_page ? NULL : site_url(uri_string()).'?query='.urlencode($query).'&page='.($page + 1);
        $data['query'] = $query;
        $data['page'] = $page;
        $data['search_data'] = $search_data;
        $data['status'] = $this->ezvote->status();

        $this->load->view('header', ['title' => 'Pilih Kandidat']);
        $this->load->view('user/vote', $data);
        $this->load->view('footer');
    }

    public function finish() {
        $this->load->view('header', ['title' => 'Terima Kasih']);
        $this->load->view('user/finish');
        $this->load->view('footer');
    }

    public function session_logout() {
        if (isset($_SESSION['ezvote_user_session'])) {
            unset($_SESSION['ezvote_user_session']);
        }
        redirect('user');
    }

    public function token_logout() {
        $this->session_check();
        if (isset($_SESSION['ezvote_user_token'])) {
            unset($_SESSION['ezvote_user_token']);
        }
        redirect('user/finish');
    }

    private function session_check() {
        if (empty($_SESSION['ezvote_user_session'])) {
            redirect('user/choose_session');
        }
        $this->load->model('sessions_model');
        $session = $this->sessions_model->get($_SESSION['ezvote_user_session'], 'locked');
        $fail = FALSE;
        if (isset($session)) {
            if (!empty($session['locked'])) {
                $fail = TRUE;
                $this->ezvote->error('Sesi terkunci');
            }
        } else {
            $fail = TRUE;
            $this->ezvote->error('Sesi tidak ditemukan. Silakan login kembali');
        }
        if ($fail) {
            unset($_SESSION['ezvote_user_session']);
            unset($_SESSION['ezvote_user_token']);
            redirect('user/choose_session');
        }
    }

    private function token_check() {
        if (empty($_SESSION['ezvote_user_token'])) {
            redirect('user/token');
        }
    }

}

?>
