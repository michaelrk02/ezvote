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
            $data['participants'] = $this->input->post('participants');
            $data['tagline'] = $this->input->post('tagline');
            $data['locked'] = !empty($this->input->post('locked'));

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
        $form['locked'] = set_value('locked');

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
        $this->login_check();

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
                            $data['participants'] = $this->input->post('participants');
                            $data['tagline'] = $this->input->post('tagline');
                            $data['locked'] = !empty($this->input->post('locked'));

                            if (!empty($this->input->post('password'))) {
                                $data['password'] = $this->input->post('password');
                                $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 5]);
                            }

                            $this->sessions_model->set($id, $data);
                            $this->ezvote->success('Sesi <b>'.$data['title'].'</b> berhasil diperbarui');
                        } else {
                            $this->ezvote->error('Sesi tidak ditemukan');
                        }
                    }
                }
            }
            if ($action === 'delete') {
                if ($this->sessions_model->exists($id)) {
                    if ($this->sessions_model->delete($id)) {
                        $this->ezvote->success('Sesi berhasil dihapus');
                    } else {
                        $this->ezvote->error('Sesi gagal dihapus. Pastikan semua kandidat dan tokenset yang terkait sudah dihapus');
                    }
                } else {
                    $this->ezvote->error('Sesi tidak ditemukan');
                }
            }
            redirect('admin/session');
        }

        $data = [];

        $sessions = $this->sessions_model->get(NULL, 'session_id,title');

        $this->load->view('header', ['title' => 'Sesi']);
        $this->load->view('admin/navbar');
        $this->load->view('admin/session', ['status' => $this->ezvote->status()]);
        $this->load->view('admin/session_chooser', ['id' => $id, 'sessions' => $sessions]);
        if (!empty($id)) {
            $session = $this->sessions_model->get($id, 'title,description,participants,tagline,locked');
            if (isset($session)) {
                $form = [];
                $form['session_id'] = $id;
                $form['title'] = set_value('title', $session['title']);
                $form['description'] = set_value('description', $session['description']);
                $form['participants'] = set_value('participants', $session['participants']);
                $form['tagline'] = set_value('tagline', $session['tagline']);
                $form['locked'] = set_value('locked', $session['locked']);

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
        $this->form_validation->set_rules('participants', 'participants', 'is_natural');
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
            if ($this->candidate_upload_photo($candidate_id)) {
                $this->ezvote->success('Kandidat <b>'.$data['name'].'</b> berhasil ditambahkan (kode: '.$candidate_id.')');
            }
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
        $this->login_check();

        $this->load->helper('form');
        $this->load->model('candidates_model');

        $id = $this->input->get('id');
        $action = $this->input->get('action');

        if (!empty($action)) {
            if ($action === 'edit') {
                if (!empty($this->input->post('submit'))) {
                    if ($this->candidate_editor_validate(FALSE)) {
                        if ($this->candidates_model->exists($id)) {
                            $data = [];
                            $data['session_id'] = $this->input->post('session_id');
                            $data['name'] = $this->input->post('name');
                            $data['description'] = $this->input->post('description');

                            $this->candidates_model->set($id, $data);
                            if ($this->candidate_upload_photo($id)) {
                                $this->ezvote->success('Kandidat <b>'.$data['name'].'</b> berhasil diperbarui');
                            }
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
                    redirect('admin/candidate');
                } else {
                    $this->ezvote->error('Kandidat tidak ditemukan');
                    redirect('admin/candidate');
                }
            }
            if ($action === 'deletephoto') {
                if (!empty($id)) {
                    if (file_exists($this->ezvote->image_path('candidate_'.$id))) {
                        unlink($this->ezvote->image_path('candidate_'.$id));
                    }
                    $this->ezvote->success('Foto kandidat berhasil dihapus');
                    redirect('admin/candidate');
                }
            }
        }

        $data = [];

        $this->load->database();

        $candidates = $this->db->query('SELECT candidate_id, candidates.session_id AS session_id, sessions.title AS session_title, name FROM candidates INNER JOIN sessions ON candidates.session_id = sessions.session_id ORDER BY session_title, name')->result_array();
        if (!isset($candidates)) {
            $candidates = [];
        }

        $this->load->view('header', ['title' => 'Kandidat']);
        $this->load->view('admin/navbar');
        $this->load->view('admin/candidate', ['status' => $this->ezvote->status()]);
        $this->load->view('admin/candidate_chooser', ['id' => $id, 'candidates' => $candidates]);
        if (!empty($id)) {
            $candidate = $this->candidates_model->get($id, 'session_id,name,description');
            if (isset($candidate)) {
                $this->load->model('sessions_model');

                $form = [];
                $form['candidate_id'] = $id;
                $form['session_id'] = set_value('session_id', $candidate['session_id']);
                $form['name'] = set_value('name', $candidate['name']);
                $form['description'] = set_value('description', $candidate['description']);

                $data['sessions'] = $this->sessions_model->get(NULL, 'session_id,title');
                $data['status'] = '';
                $data['create'] = FALSE;
                $data['data'] = $form;
                $data['photo_url'] = $this->candidate_photo_url($id);

                $this->load->view('admin/candidate_editor', $data);
            }
        }
        $this->load->view('footer');
    }

    private function candidate_editor_validate($create) {
        $this->load->helper('form');
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

    private function candidate_upload_photo($candidate_id) {
        $success = TRUE;
        if ($_FILES['photo']['size'] != 0) {
            $success = FALSE;

            $tmp_image_id = 'candidate_'.$candidate_id.'_tmp';
            $tmp_path = $this->ezvote->image_path($tmp_image_id);

            $this->load->library('upload', [
                'upload_path' => dirname($tmp_path),
                'allowed_types' => 'jpeg|jpg|png',
                'file_name' => basename($tmp_path, '.png'),
                'file_ext_tolower' => TRUE,
                'overwrite' => TRUE
            ]);

            if ($this->upload->do_upload('photo')) {
                $tmp_path = $this->upload->data('full_path');

                $image_id = 'candidate_'.$candidate_id;
                $path = $this->ezvote->image_path($image_id);

                $img = NULL;
                $mime = mime_content_type($this->upload->data('full_path'));
                if (($mime === 'image/jpg') || ($mime === 'image/jpeg')) {
                    $img = imagecreatefromjpeg($tmp_path);
                } else if ($mime === 'image/png') {
                    $img = imagecreatefrompng($tmp_path);
                }
                if ($img !== FALSE) {
                    $target = imagecreatetruecolor(512, 512);
                    imagefill($target, 0, 0, imagecolorallocate($target, 0, 0, 0));

                    $src_x = 0;
                    $src_y = 0;
                    $src_w = imagesx($img);
                    $src_h = imagesy($img);

                    $factor = 1.0;
                    if ($src_w > $src_h) {
                        $factor = 512.0 / $src_w;
                    } else if ($src_w < $src_h) {
                        $factor = 512.0 / $src_h;
                    }

                    $dst_w = $factor * $src_w;
                    $dst_h = $factor * $src_h;
                    $dst_x = 256 - ($dst_w / 2);
                    $dst_y = 256 - ($dst_h / 2);

                    imagecopyresized($target, $img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
                    imagepng($target, $path);
                    imagedestroy($target);
                    imagedestroy($img);

                    $success = TRUE;
                } else {
                    $this->ezvote->error('Gagal dalam memproses foto kandidat');
                }
                unlink($tmp_path);
            } else {
                $this->ezvote->error('Tidak dapat mengunggah file: '.$this->upload->display_errors().'. Mohon coba lagi');
            }
        }
        return $success;
    }

    private function candidate_photo_url($id) {
        if (file_exists($this->ezvote->image_path('candidate_'.$id))) {
            return site_url('content/data_img/candidate_'.$id);
        }
        return NULL;
    }

    public function tokenset_create() {
        $this->login_check();

        $this->load->helper('form');

        if ($this->tokenset_editor_validate(TRUE)) {
            $this->load->model('tokensets_model');

            $data['session_id'] = $this->input->post('session_id');
            $data['name'] = $this->input->post('name');

            $tokens = $this->input->post('tokens');

            $tokenset_id = $this->tokensets_model->create($data, $tokens);
            $this->ezvote->success('Tokenset <b>'.$data['name'].'</b> berhasil dibuat (kode: '.$tokenset_id.')');
            redirect('admin/tokenset');
        }

        $this->load->model('sessions_model');

        $form = [];
        $form['tokenset_id'] = '(otomatis)';
        $form['name'] = set_value('name');
        $form['tokens'] = set_value('tokens', 1);

        $data = [];
        $data['create'] = TRUE;
        $data['data'] = $form;
        $data['sessions'] = $this->sessions_model->get(NULL, 'session_id,title');

        $this->load->view('header', ['title' => 'Tambah Kandidat']);
        $this->load->view('admin/navbar');
        $this->load->view('admin/tokenset', ['status' => $this->ezvote->status()]);
        $this->load->view('admin/tokenset_editor', $data);
        $this->load->view('footer');
    }

    public function tokenset() {
        $this->login_check();

        $this->load->helper('form');
        $this->load->model('tokensets_model');

        $id = $this->input->get('id');
        $action = $this->input->get('action');

        if (!empty($action)) {
            if ($action === 'edit') {
                if (!empty($this->input->post('submit'))) {
                    if ($this->tokenset_editor_validate(FALSE)) {
                        if ($this->tokensets_model->exists($id)) {
                            $data = [];
                            $data['session_id'] = $this->input->post('session_id');
                            $data['name'] = $this->input->post('name');

                            $this->tokensets_model->set($id, $data);
                            $this->ezvote->success('Tokenset <b>'.$data['name'].'</b> berhasil diperbarui');
                            redirect('admin/tokenset');
                        } else {
                            $this->ezvote->error('Tokenset tidak ditemukan');
                            redirect('admin/tokenset');
                        }
                    }
                }
            }
            if ($action === 'delete') {
                if ($this->tokensets_model->exists($id)) {
                    $this->tokensets_model->delete($id);
                    $this->ezvote->success('Tokenset berhasil dihapus');
                } else {
                    $this->ezvote->error('Tokenset tidak ditemukan');
                    redirect('admin/tokenset');
                }
            }
        }

        $data = [];

        $this->load->database();

        $tokensets = $this->db->query('SELECT tokenset_id, tokensets.session_id AS session_id, sessions.title AS session_title, name FROM tokensets INNER JOIN sessions ON tokensets.session_id = sessions.session_id ORDER BY session_title, name')->result_array();
        if (!isset($tokensets)) {
            $tokensets = [];
        }

        $this->load->view('header', ['title' => 'Tokenset']);
        $this->load->view('admin/navbar');
        $this->load->view('admin/tokenset', ['status' => $this->ezvote->status()]);
        $this->load->view('admin/tokenset_chooser', ['id' => $id, 'tokensets' => $tokensets]);
        if (!empty($id)) {
            $tokenset = $this->tokensets_model->get($id, 'session_id,name');
            if (isset($tokenset)) {
                $this->load->database();
                $this->load->model('sessions_model');

                $form = [];
                $form['tokenset_id'] = $id;
                $form['session_id'] = set_value('session_id', $tokenset['session_id']);
                $form['name'] = set_value('name', $tokenset['name']);
                $form['tokens'] = $this->db->select('token')->from('tokens')->where('tokenset_id', $id)->count_all_results();

                $data['sessions'] = $this->sessions_model->get(NULL, 'session_id,title');
                $data['status'] = '';
                $data['create'] = FALSE;
                $data['data'] = $form;

                $this->load->view('admin/tokenset_editor', $data);
            }
        }
        $this->load->view('footer');
    }

    private function tokenset_editor_validate($create) {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $this->form_validation->set_rules('session_id', 'session', 'required');
        $this->form_validation->set_rules('name', 'name', 'required|max_length[100]');
        $this->form_validation->set_rules('tokens', 'tokens', 'required|is_natural');

        if ($this->form_validation->run()) {
            return TRUE;
        }
        if (!empty(validation_errors())) {
            $this->ezvote->error(validation_errors());
        }
        return FALSE;
    }

    public function tokens() {
        $this->login_check();

        $this->load->model(['tokens_model', 'tokensets_model']);

        $tokens = NULL;
        $tokenset_id = $this->input->get('tokenset');
        $tokenset = $this->tokensets_model->get($tokenset_id, 'name');
        if (isset($tokenset)) {
            $tokens = $this->tokens_model->get(NULL, 'token,candidate_id', ['tokenset_id' => $tokenset_id]);
            if (!isset($tokens)) {
                $tokens = [];
            }
        } else {
            show_404();
        }

        $data = [];
        $data['tokens'] = $tokens;

        $this->load->view('header', ['title' => 'Daftar Token ('.$tokenset['name'].')']);
        $this->load->view('admin/tokens', $data);
        $this->load->view('footer');
    }

    public function tokens_csv() {
        $this->login_check();

        $this->load->model(['sessions_model', 'tokens_model', 'tokensets_model']);

        $tokens = NULL;
        $tokenset_id = $this->input->get('tokenset');
        $tokenset = $this->tokensets_model->get($tokenset_id, 'session_id,name');
        if (isset($tokenset)) {
            $tokens = $this->tokens_model->get(NULL, 'token', ['tokenset_id' => $tokenset_id]);
            if (!isset($tokens)) {
                $tokens = [];
            }
        } else {
            show_404();
        }

        $session = $this->sessions_model->get($tokenset['session_id'], 'title');

        $this->output->set_content_type('text/csv');
        $this->output->set_header('Content-Disposition: attachment; filename="tokens_'.url_title($session['title']).'_'.url_title($tokenset['name']).'.csv"');
        echo 'ListToken_'.url_title($session['title']).'_'.url_title($tokenset['name']).','.'FormatPesanWhatsApp'.PHP_EOL;
        foreach ($tokens as $token) {
            echo $token['token'].','.'Token: ```'.$token['token'].'```'.PHP_EOL;
        }
    }

    public function live_count() {
        $this->login_check();

        $this->load->model('sessions_model');

        $data = [];
        $data['current'] = 0;
        $data['total'] = 0;
        $data['percentage'] = 0.00;

        $session_id = $this->input->get('session');
        if (!empty($session_id)) {
            $session = $this->sessions_model->get($session_id, 'title,participants,tagline');
            if (isset($session)) {
                $this->load->database();
                $this->load->model('tokensets_model');

                $data['total'] = $session['participants'];

                $tokensets = $this->tokensets_model->get(NULL, 'tokenset_id', ['session_id' => $session_id]);
                foreach ($tokensets as $tokenset) {
                    $cnt = $this->db->query('SELECT COUNT(token) AS cnt FROM tokens WHERE tokenset_id = ? AND candidate_id IS NOT NULL', [$tokenset['tokenset_id']])->row_array(0);
                    if (isset($cnt)) {
                        $data['current'] += $cnt['cnt'];
                    }
                }

                $data['session'] = $session;
                if ($session['participants'] > 0) {
                    $data['percentage'] = round((double)$data['current'] / $data['total'] * 100.00, 2);
                } else {
                    $data['total'] = $data['current'];
                    $data['percentage'] = 100.00;
                }
            } else {
                show_404();
            }
        } else {
            show_error('Kesalahan parameter', 400);
        }

        $this->load->view('header', ['title' => 'Live Count']);
        $this->load->view('admin/live_count', $data);
        $this->load->view('footer');
    }

    public function result() {
        $this->login_check();

        $this->load->helper('form');
        $this->load->model(['sessions_model', 'tokensets_model']);

        $session_id = $this->input->get('session');
        $dramatic = !empty($this->input->get('dramatic'));

        $data = [];
        $data['dramatic'] = $dramatic;

        $sessions = $this->sessions_model->get(NULL, 'session_id,title');

        $this->load->view('header', ['title' => 'Sesi']);
        $this->load->view('admin/navbar');
        $this->load->view('admin/result_chooser', ['session_id' => $session_id, 'sessions' => $sessions, 'dramatic' => $dramatic]);
        if (!empty($session_id) && $this->sessions_model->exists($session_id)) {
            $this->load->database();
            $this->load->model('candidates_model');

            if (!$dramatic) {
                $raw_data = $this->db->query('SELECT candidate_id FROM tokens WHERE candidate_id IS NOT NULL')->result_array();
                if (!isset($raw_data)) {
                    $raw_data = [];
                }
                $voters_map = [];
                foreach ($raw_data as $entry) {
                    $id = $entry['candidate_id'];
                    if (!isset($voters_map[$id])) {
                        $voters_map[$id] = 0;
                    }
                    $voters_map[$id]++;
                }

                $winner = NULL;
                foreach ($voters_map as $id => $voters) {
                    if (isset($winner)) {
                        if ($voters > $voters_map[$winner]) {
                            $winner = $id;
                        }
                    } else {
                        $winner = $id;
                    }
                }

                $data['winner'] = $this->candidates_model->get($winner, 'name')['name'];
                $data['voters'] = $voters_map[$winner];

                $categories = $this->tokensets_model->get(NULL, 'tokenset_id,name');
                foreach ($categories as &$category) {
                    $raw_data = $this->db->query('SELECT candidate_id FROM tokens WHERE candidate_id IS NOT NULL AND tokenset_id = ?', [$category['tokenset_id']])->result_array();
                    if (!isset($raw_data)) {
                        $raw_data = [];
                    }
                    $voters_map = [];
                    foreach ($raw_data as $entry) {
                        $id = $entry['candidate_id'];
                        if (!isset($voters_map[$id])) {
                            $voters_map[$id] = 0;
                        }
                        $voters_map[$id]++;
                    }
                    $category['winner'] = NULL;
                    $category['voters'] = 0;
                    foreach ($voters_map as $id => $voters) {
                        if (isset($category['winner'])) {
                            if ($voters > $voters_map[$category['winner']]) {
                                $category['winner'] = $id;
                            }
                        } else {
                            $category['winner'] = $id;
                        }
                    }
                    if (isset($category['winner'])) {
                        $category['voters'] = $voters_map[$category['winner']];
                        $category['winner'] = $this->candidates_model->get($category['winner'], 'name')['name'];
                    }
                }

                $data['categories'] = $categories;
            } else {
                $candidates = $this->candidates_model->get(NULL, 'candidate_id,name', ['session_id' => $session_id]);
                if (!((count($candidates) == 1) || (count($candidates) > 3))) {
                    foreach ($candidates as &$candidate) {
                        $candidate['voters'] = $this->db->select('token')->from('tokens')->where('candidate_id', $candidate['candidate_id'])->count_all_results();
                    }
                } else {
                    $this->ezvote->error('Jumlah kandidat pada sesi tersebut kurang dari 2 atau lebih dari 3');
                    redirect('admin/result');
                }
                $data['candidates'] = $candidates;
            }
            $data['status'] = $this->ezvote->status();

            $this->load->view('admin/result', $data);
        }
        $this->load->view('footer');
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
