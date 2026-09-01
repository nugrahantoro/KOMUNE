<?php

class Konten extends Controller
{
    public function index($param1 = "", $param2 = "")
    {
        $data["title"] = "Pusat Konten & Edukasi Keagamaan";
        $data["href"] = "konten";
        $this->view("main/view/header", $data);
        $this->view("konten/view/v_konten", $data);
        $this->view("main/view/footer");
    }

    public function artikel($param1 = "", $param2 = "")
    {
        $data["title"] = "Artikel & Bacaan Edukasi";
        $data["href"] = "konten/artikel";
        $this->view("main/view/header", $data);
        $this->view("konten/view/v_artikel", $data);
        $this->view("main/view/footer");
    }

    public function video($param1 = "", $param2 = "")
    {
        $data["title"] = "Video Kajian & Pembelajaran";
        $data["href"] = "konten/video";
        $this->view("main/view/header", $data);
        $this->view("konten/view/v_video", $data);
        $this->view("main/view/footer");
    }

    public function feed($param1 = "", $param2 = "")
    {
        $data["title"] = "Feed Konten & Kajian Warga";
        $data["href"] = "konten/feed";
        $this->view("main/view/header", $data);
        $this->view("konten/view/v_feed", $data);
        $this->view("main/view/footer");
    }

    public function ex($param1, $params2 = null, $params3 = null)
    {
        $subController = preg_replace('/[^a-zA-Z0-9_\-]/', '', $param1);
        $subFile = "app/module/konten/controller/$subController.php";

        if (file_exists($subFile)) {
            require_once $subFile;
            $instance = new $subController();
            if ($params2 && method_exists($instance, $params2)) {
                return call_user_func_array([$instance, $params2], [$params3]);
            }
        }
    }

    public function js($param1)
    {
        $safeJs = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $param1);
        $this->get_js("konten/js/" . $safeJs);
    }
}
