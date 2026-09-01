<?php

class Warga extends Controller
{
    public function index($param1 = "", $param2 = "")
    {
        $data["title"] = "Data Warga & Anggota Komunitas";
        $data["href"] = "warga";
        $this->view("main/view/header", $data);
        $this->view("warga/view/v_warga", $data);
        $this->view("main/view/footer");
    }

    public function kelompok($param1 = "", $param2 = "")
    {
        $data["title"] = "Kelompok Warga & RT/RW";
        $data["href"] = "warga/kelompok";
        $this->view("main/view/header", $data);
        $this->view("warga/view/v_kelompok_warga", $data);
        $this->view("main/view/footer");
    }

    public function ex($param1, $params2 = null, $params3 = null)
    {
        $subController = preg_replace('/[^a-zA-Z0-9_\-]/', '', $param1);
        $subFile = "app/module/warga/controller/$subController.php";

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
        $this->get_js("warga/js/" . $safeJs);
    }
}
