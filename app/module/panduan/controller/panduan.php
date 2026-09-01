<?php

class Panduan extends Controller
{
    public function index($param1 = "", $param2 = "")
    {
        $data["title"] = "Panduan Penggunaan Sistem";
        $data["href"] = "panduan";
        $this->view("main/view/header", $data);
        $this->view("panduan/view/v_panduan", $data);
        $this->view("main/view/footer");
    }

    public function js($param1)
    {
        $safeJs = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $param1);
        $this->get_js("panduan/js/" . $safeJs);
    }
}
