<?php

class Ezvote {

    public function set_status($severity, $message) {
        $_SESSION['ezvote_status'] = ['severity' => $severity, 'message' => $message];
    }

    public function success($message) {
        $this->set_status('success', $message);
    }

    public function warning($message) {
        $this->set_status('warning', $message);
    }

    public function error($message) {
        $this->set_status('error', $message);
    }

    public function status($keep = FALSE) {
        if (isset($_SESSION['ezvote_status'])) {
            $status = $_SESSION['ezvote_status'];
            if (!$keep) {
                unset($_SESSION['ezvote_status']);
            }
            $str = '';
            $str .= '<div class="toast toast-'.$status['severity'].'">';
            $str .= ' '.$status['message'];
            $str .= '</div>';
            return $str;
        }
        return '<div></div>';
    }

    public function image_path($image_id) {
        return APPPATH.'third_party/data/img_'.md5($image_id).'.png';
    }

}

?>
