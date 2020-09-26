<?php

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->library('session');
    }

    public function index() {
        redirect('admin/dashboard');
    }

    public function login() {
        if (!empty($_SESSION['ezvote_admin'])) {
            redirect('admin');
        }
        if (!empty($this->input->post('submit'))) {
            $password = $this->input->post('password');

            if (password_verify($password, ADMIN_PASSWORD)) {
                $_SESSION['ezvote_admin'] = TRUE;
                redirect('admin');
            } else {
                $this->ezvote->error('Password tidak valid');
            }
        }

        $this->load->helper('form');

        $data = [];
        $data['status'] = $this->ezvote->status();

        $this->load->view('header', ['title' => 'Masuk']);
        $this->load->view('admin/login', $data);
        $this->load->view('footer');
    }

    public function dashboard() {
        $this->login_check();

        $this->load->database();

        $data = [];
        $data['status'] = $this->ezvote->status();
        $data['sessions'] = $this->db->select('session_id')->from('sessions')->count_all_results();
        $data['candidates'] = $this->db->select('candidate_id')->from('candidates')->count_all_results();
        $data['tokensets'] = $this->db->select('tokenset_id')->from('tokensets')->count_all_results();

        $this->load->view('header', ['title' => 'Dashboard']);
        $this->load->view('admin/navbar');
        $this->load->view('admin/dashboard', $data);
        $this->load->view('footer');
    }

    public function session_create() {
        $this->login_check();

        $this->load->helper('form');

        if ($this->session_editor_validate(TRUE)) {
            $this->load->model('sessions_model');

            $data['title'] = $this->input->post('title');
            $data['password'] = password_hash($this->input->post('password'), PASSWORD_BCRYPT, ['cost' => 5]);
            $data['description'] = $this->input->post('description');
            $data['tagline'] = $this->input->post('tagline');

            $session_id = $this->sessions_model->create($data);
            $this->ezvote->success('Sesi <b>'.$data['title'].'</b> berhasil dibuat (kode: '.$session_id.')');
            redirect('admin/session');
        }

        $form = [];
        $form['session_id'] = '(otomatis)';
        $form['title'] = set_value('title');
        $form['password'] = set_value('password');
        $form['description'] = set_value('description');
        $form['tagline'] = set_value('tagline');

        $data = [];
        $data['create'] = TRUE;
        $data['data'] = $form;

        $this->load->view('header', ['title' => 'Buat Sesi']);
        $this->load->view('admin/navbar');
        $this->load->view('admin/session', ['status' => $this->ezvote->status()]);
        $this->load->view('admin/session_editor', $data);
        $this->load->view('footer');
    }

    public function session() {
        $this->load->helper('form');
        $this->load->model('sessions_model');

        $id = $this->input->get('id');
        $action = $this->input->get('action');

        if (!empty($action)) {
            if ($action === 'edit') {
                if (!empty($this->input->post('submit'))) {
                    if ($this->session_editor_validate(FALSE)) {
                        if ($this->sessions_model->exists($id)) {
                            $data = [];
                            $data['title'] = $this->input->post('title');
                            $data['description'] = $this->input->post('description');
                            $data['tagline'] = $this->input->post('tagline');

                            if (!empty($this->input->post('password'))) {
                                $data['password'] = $this->input->post('password');
                                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 5]);
                            }

                            $this->sessions_model->update($id, $data);
                            $this->ezvote->success('Sesi <b>'.$data['title'].'</b> berhasil diperbarui');
                            redirect('admin/session');
                        } else {
                            $this->ezvote->error('Sesi tidak ditemukan');
                            redirect('admin/session');
                        }
                    }
                }
            }
            if ($action === 'delete') {
                if ($this->sessions_model->exists($id)) {
                    $this->sessions_model->delete($id);
                    $this->ezvote->success('Sesi berhasil dihapus');
                } else {
                    $this->ezvote->error('Sesi tidak ditemukan');
                    redirect('admin/session');
                }
            }
        }

        $data = [];

        $sessions = $this->sessions_model->get(NULL, 'session_id,title');

        $this->load->view('header', ['title' => 'Sesi']);
        $this->load->view('admin/navbar');
        $this->load->view('admin/session', ['status' => $this->ezvote->status()]);
        $this->load->view('admin/session_chooser', ['id' => $id, 'sessions' => $sessions]);
        if (!empty($id)) {
            $session = $this->sessions_model->get($id, 'title,description,tagline');
            if (isset($session)) {
                $form = [];
                $form['session_id'] = $id;
                $form['title'] = set_value('title', $session['title']);
                $form['description'] = set_value('description', $session['description']);
                $form['tagline'] = set_value('tagline', $session['tagline']);

                $data['status'] = '';
                $data['create'] = FALSE;
                $data['data'] = $form;

                $this->load->view('admin/session_editor', $data);
            }
        }
        $this->load->view('footer');
    }

    private function session_editor_validate($create) {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $password_rules = ['min_length[8]', 'max_length[72]'];
        if ($create) {
            $password_rules[] = 'required';
        }
        $password_rules = implode('|', $password_rules);

        $this->form_validation->set_rules('title', 'title', 'required|max_length[100]');
        $this->form_validation->set_rules('password', 'password', $password_rules);
        $this->form_validation->set_rules('description', 'description', 'max_length[500]');
        $this->form_validation->set_rules('tagline', 'tagline', 'max_length[100]');

        if ($this->form_validation->run()) {
            return TRUE;
        }
        if (!empty(validation_errors())) {
            $this->ezvote->error(validation_errors());
        }

        return FALSE;
    }

    public function candidate_create() {
        $this->login_check();

        $this->load->helper('form');

        if ($this->candidate_editor_validate(TRUE)) {
            $this->load->model('candidates_model');

            $data['session_id'] = $this->input->post('session_id');
            $data['name'] = $this->input->post('name');
            $data['description'] = $this->input->post('description');

            $candidate_id = $this->candidates_model->create($data);
            $this->ezvote->success('Kandidat <b>'.$data['title'].'</b> berhasil ditambahkan (kode: '.$candidate_id.')');
            redirect('admin/candidate');
        }

        $this->load->model('sessions_model');

        $form = [];
        $form['candidate_id'] = '(otomatis)';
        $form['name'] = set_value('name');
        $form['description'] = set_value('description');

        $data = [];
        $data['create'] = TRUE;
        $data['data'] = $form;
        $data['sessions'] = $this->sessions_model->get(NULL, 'session_id,title');

        $this->load->view('header', ['title' => 'Tambah Kandidat']);
        $this->load->view('admin/navbar');
        $this->load->view('admin/candidate', ['status' => $this->ezvote->status()]);
        $this->load->view('admin/candidate_editor', $data);
        $this->load->view('footer');
    }

    public function candidate() {
        $this->load->helper('form');
        $this->load->model('candidates_model');

        $id = $this->input->get('id');
        $action = $this->input->get('action');

        if (!empty($action)) {
            if ($action === 'edit') {
                if (!empty($this->input->post('submit'))) {
                    if ($this->candidate_editor_validate(FALSE)) 
                        if ($this->candidates_model->exists($id)) {
                            $data = [];
                            $data['session_id'] = $this->input->post('session_id');
                            $data['name'] = $this->input->post('title');
                            $data['description'] = $this->input->post('description');

                            $this->sessions_model->update($id, $data);
                            $this->ezvote->success('Kandidat <b>'.$data['name'].'</b> berhasil diperbarui');
                            redirect('admin/candidate');
                        } else {
                            $this->ezvote->error('Kandidat tidak ditemukan');
                            redirect('admin/candidate');
                        }
                    }
                }
            }
            if ($action === 'delete') {
                if ($this->candidates_model->exists($id)) {
                    $this->candidates_model->delete($id);
                    $this->ezvote->success('Kandidat berhasil dihapus');
                } else {
                    $this->ezvote->error('Kandidat tidak ditemukan');
                    redirect('admin/session');
                }
            }
        }

        $data = [];

        $this->load->database();

        $candidates = $this->db->query('SELECT candidate_id, candidates.session_id AS session_id, sessions.title AS session_title, name FROM candidates INNER JOIN sessions ON candidates.session_id = sessions.session_id')->result_array();
        if (!isset($candidates)) {
            $candidates = [];
        }

        $this->load->view('header', ['title' => 'Sesi']);
        $this->load->view('admin/navbar');
        $this->load->view('admin/candidate', ['status' => $this->ezvote->status()]);
        $this->load->view('admin/candidate_chooser', ['id' => $id, 'candidates' => $candidates]);
        if (!empty($id)) {
            $candidate = $this->candidates_model->get($id, 'session_id,name,description');
            if (isset($candidate)) {
                $form = [];
                $form['candidate_id'] = $id;
                $form['session_id'] = $candidate['session_id'];
                $form['name'] = set_value('name', $candidate['title']);
                $form['description'] = set_value('description', $candidate['description']);

                $data['status'] = '';
                $data['create'] = FALSE;
                $data['data'] = $form;

                $this->load->view('admin/session_editor', $data);
            }
        }
        $this->load->view('footer');
    }

    private function candidate_editor_validate($create) {
        $this->load->helper('from');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('session_id', 'session', 'required');
        $this->form_validation->set_rules('name', 'name', 'required|max_length[100]');
        $this->form_validation->set_rules('description', 'description', 'max_length[500]');

        if ($this->form_validation->run()) {
            return TRUE;
        }
        if (!empty(validation_errors())) {
            $this->ezvote->error(validation_errors());
        }
        return FALSE;
    }

    public function tokenset_create() {
    }

    public function tokenset() {
    }

    private function tokenset_editor_validate($create) {
    }

    public function logout() {
        if (isset($_SESSION['ezvote_admin'])) {
            unset($_SESSION['ezvote_admin']);
            redirect('admin');
        }
    }

    private function login_check() {
        if (empty($_SESSION['ezvote_admin'])) {
            redirect('admin/login');
        }
    }

}

?>
