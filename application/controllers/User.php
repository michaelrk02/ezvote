<?php

class User extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        redirect('user/main');
    }

    public function main() {
        $this->load->view('header', ['title' => 'Halaman Utama']);
        $this->load->view('footer');
    }

}

?>
