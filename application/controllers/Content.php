<?php

class Content extends CI_Controller {

    public function lib() {
        $name = $this->input->get('name');
        $type = $this->input->get('type');
        if (!empty($name) && !empty($type)) {
            $name = basename($name);
            $path = APPPATH.'third_party/lib/'.$name;
            if (file_exists($path)) {
                $this->output->set_status_header(200);
                $this->output->set_header('Cache-Control: max-age=86400');
                $this->output->set_content_type($type);
                $this->output->set_output(file_get_contents($path));
            } else {
                $this->output->set_status_header(404);
            }
        } else {
            $this->output->set_status_header(400);
        }
    }

    public function data_img($id) {
        if (!empty($id)) {
            $path = $this->ezvote->image_path($id);
            if (file_exists($path)) {
                $this->output->set_status_header(200);
                $this->output->set_content_type('image/png');
                $this->output->set_header('Cache-Control: max-age=86400');
                $this->output->set_output(file_get_contents($path));
            } else {
                $this->output->set_status_header(404);
            }
        } else {
            $this->output->set_status_header(400);
        }
    }

}

?>
