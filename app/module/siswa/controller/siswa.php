<?php

class Siswa extends Controller
{
    public function index($param1 = "", $param2 = "")
    {
        global $basepath;
        header("Location: " . $basepath . "warga");
        exit;
    }

    public function konfig_absensi($param1 = "", $param2 = "")
    {
        global $basepath;
        header("Location: " . $basepath . "warga");
        exit;
    }

    public function rekap_absensi($param1 = "", $param2 = "")
    {
        global $basepath;
        header("Location: " . $basepath . "warga");
        exit;
    }
}
