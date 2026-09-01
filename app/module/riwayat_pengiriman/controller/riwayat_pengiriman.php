<?php

class riwayat_pengiriman extends Controller
{

    public function index($param1 = "", $param2 = "")
    {
        $data["title"] = "riwayatpengiriman";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("riwayat_pengiriman/view/v_riwayat_pengiriman");
        $this->view("main/view/footer");
    }

    public function riwayatpengiriman($param1 = "", $param2 = "")
    {
        $data["title"] = "riwayatpengiriman";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("riwayat_pengiriman/view/v_riwayat_pengiriman");
        $this->view("main/view/footer");
    }

    public function ex($param1,$params2)
	{
		$ex_controller = '';
		if (file_exists("app/module/riwayat_pengiriman/controller/$param1.php"))
		{
			$ex_controller=$param1;
		}
		if($ex_controller != '')
		{
			require_once "$param1.php";
			$ex_controller = new $ex_controller;
			//Method
			if(isset($params2))
			{
				if (method_exists($ex_controller,$params2))
				{
				//Run Controller & Method. Send Paramaters if exists
				call_user_func_array([$ex_controller,$params2],[]);
				}
			}
		}
	}


  
    function tahun() {
        
        global $con;

        $namaBulan = array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
        $idBulan = array("01","02","03","04","05","06","07","08","09","10","11","12");
        $jml=count($namaBulan);

        echo '<select id="myselects_th" style="border: 1px solid #E1E1E1;" onChange="myNewFunction();">';
        for($i = 0; $i < $jml; $i++){
            $tahunsk=date("m-Y");
            $sele=$idBulan[$i]."-".date('Y');
            if($sele == $tahunsk){
                $sc="selected=selected";
            }
            echo '<option '.$sc.' value="'.date('Y').'-'.$idBulan[$i].'" >'.$namaBulan[$i].' - '.date('Y').'</option>';
            
            $sc="";
        }
        echo '</select>';
        echo '<hr>';
    }

    function total_harga_perbulan() {
        global $con;
        $periode = isset($_GET['periode'])? $_GET['periode']:"";
        $tahun = substr($periode, 0, 4);
        $bulan = substr($periode, 5, 2);

        $sql = "
            SELECT
                SUM(harga) as total_harga_perbulan
            FROM
                ref_log_wa_gw
            WHERE
                MONTH(created) = '$bulan'
                AND YEAR(created) = '$tahun'
        ";
        $result = mysqli_query($con, $sql);
        $total_harga_perbulan = 0;
        while($r = mysqli_fetch_array($result)) {
            $total_harga_perbulan = $r['total_harga_perbulan'];
        }

        echo 'Rp ' . number_format($total_harga_perbulan);
    }

    function total_harga_perhari() {
        global $con;

        $hari_ini_awal = date('Y-m-d') . ' 00:00:00';
        $hari_ini_akhir = date('Y-m-d') . ' 23:59:59';
        $sql = "
            SELECT
                SUM( harga ) AS total_harga_perbulan 
            FROM
                ref_log_wa_gw 
            WHERE
                created >= '$hari_ini_awal' 
                AND created <= '$hari_ini_akhir'
        ";

        $result = mysqli_query($con, $sql);
        $total_harga_perbulan = 0;
        while($r = mysqli_fetch_array($result)) {
            $total_harga_perbulan = $r['total_harga_perbulan'];
        }

        echo 'Rp ' . number_format($total_harga_perbulan);
    }
    
    function total_berhasil_perbulan() {
        global $con;
        $periode = isset($_GET['periode'])? $_GET['periode']:"";
        $tahun = substr($periode, 0, 4);
        $bulan = substr($periode, 5, 2);

        $sql = "
            SELECT
                COUNT(id_user) as jumlah_berhasil
            FROM
                ref_log_wa_gw
            WHERE
                MONTH(created) = '$bulan'
                AND YEAR(created) = '$tahun'
                AND code = 200
        ";
        $result = mysqli_query($con, $sql);
        $jumlah_berhasil = 0;
        while($r = mysqli_fetch_array($result)) {
            $jumlah_berhasil = $r['jumlah_berhasil'];
        }

        echo number_format($jumlah_berhasil);
    }

    function total_berhasil_perhari() {
        global $con;

        $hari_ini_awal = date('Y-m-d') . ' 00:00:00';
        $hari_ini_akhir = date('Y-m-d') . ' 23:59:59';
        $sql = "
            SELECT
                COUNT(id_user) as jumlah_berhasil
            FROM
                ref_log_wa_gw 
            WHERE
                created >= '$hari_ini_awal' 
                AND created <= '$hari_ini_akhir'
                AND code = 200
        ";

        $result = mysqli_query($con, $sql);
        $jumlah_berhasil = 0;
        while($r = mysqli_fetch_array($result)) {
            $jumlah_berhasil = $r['jumlah_berhasil'];
        }

        echo number_format($jumlah_berhasil);
    }

    function total_gagal_perbulan() {
        global $con;
        $periode = isset($_GET['periode'])? $_GET['periode']:"";
        $tahun = substr($periode, 0, 4);
        $bulan = substr($periode, 5, 2);

        $sql = "
            SELECT
                COUNT(id_user) as jumlah_gagal
            FROM
                ref_log_wa_gw
            WHERE
                MONTH(created) = '$bulan'
                AND YEAR(created) = '$tahun'
                AND code != 200
        ";
        $result = mysqli_query($con, $sql);
        $jumlah_gagal = 0;
        while($r = mysqli_fetch_array($result)) {
            $jumlah_gagal = $r['jumlah_gagal'];
        }

        echo number_format($jumlah_gagal);
    }

    function total_gagal_perhari() {
        global $con;

        $hari_ini_awal = date('Y-m-d') . ' 00:00:00';
        $hari_ini_akhir = date('Y-m-d') . ' 23:59:59';
        $sql = "
            SELECT
                COUNT(id_user) as jumlah_gagal
            FROM
                ref_log_wa_gw 
            WHERE
                created >= '$hari_ini_awal' 
                AND created <= '$hari_ini_akhir'
                AND code != 200
        ";

        $result = mysqli_query($con, $sql);
        $jumlah_gagal = 0;
        while($r = mysqli_fetch_array($result)) {
            $jumlah_gagal = $r['jumlah_gagal'];
        }

        echo number_format($jumlah_gagal);
    }

    function data_histori_pengiriman() {
        global $con;
        $periode = isset($_GET['periode'])? $_GET['periode']:"";
        $tahun = substr($periode, 0, 4);
        $bulan = substr($periode, 5, 2);

        $limit = 10;
        $pages = $_GET["page"];
        $start = ($pages - 1) * 10;

        $sql="
            SELECT
                ref_log_wa_gw.*,
                users_log.*,
                ref_template_wa.template_name
            FROM
                ref_log_wa_gw
                JOIN users_log ON ref_log_wa_gw.id_user = users_log.id 
                JOIN ref_template_wa ON ref_log_wa_gw.id_template_wa = ref_template_wa.id
            WHERE
                MONTH(ref_log_wa_gw.created) = '$bulan'
                AND YEAR(ref_log_wa_gw.created) = '$tahun'
            ORDER BY
                ref_log_wa_gw.created DESC
            LIMIT $start, $limit
        ";
        $result = mysqli_query($con,$sql);

        $sql_page="
            SELECT
                ref_log_wa_gw.*,
                users_log.*,
                ref_template_wa.template_name
            FROM
                ref_log_wa_gw
                JOIN users_log ON ref_log_wa_gw.id_user = users_log.id 
                JOIN ref_template_wa ON ref_log_wa_gw.id_template_wa = ref_template_wa.id
            WHERE
                MONTH(ref_log_wa_gw.created) = '$bulan'
                AND YEAR(ref_log_wa_gw.created) = '$tahun'
        ";
        $result_page = mysqli_query($con,$sql_page);
        $count = mysqli_num_rows($result_page);
        $page = ceil($count / $limit);

        echo '<select id="myselects_thal" style="border: 1px solid #E1E1E1;" onChange="myNewFunctionPage(this);">';
        echo '<option>Page</option>';
        for($i=1; $i<=$page; $i++){
            echo '<option value="'.$i.'" id="'.$i.'">'.$i.'</option>';
        }
        echo '</select><span style="font-family:tahoma;font-size:11px;margin-left:10px;">halaman '.$pages.' dari '.$count.' data</span>
            <br>
            <br>
            <button onclick="cetak_histori_pengiriman_excel();" style="border-radius:3px;border:0px;padding:5px;margin:3px;"><img src="images/logo/excel.png" style="width:15px;height:15px;"><span style="font-size:11px;">Download excel</span></button>
            <br>
            <br>
        ';
        echo '
        <div class="col-md-12">
            <div class="table-responsive">
            <table class="table mb-0" id="tabsesur" style="font-size: 12px;">
            <thead class="table-light">
                <tr>
                    <th><center>No</center></th>
                    <th><center>Tanggal</center></th>
                    <th><center>Reff</center></th>
                    <th><center>No. Whatsapp</center></th>
                    <th><center>Pesan</center></th>
                    <th><center>Status</center></th>
                    <th><center>Harga</center></th>
                    <th><center>User</center></th>
                    <th><center>Nama Template</center></th>
                </tr>
            </thead>
        ';
        $no = 1;
        while($hs = mysqli_fetch_array($result)){
            if ($hs['code'] == '200') {
                $status = "Berhasil";
            } else {
                $status = "Gagal";
            }
            
            echo '
                <tr>
                    <td><center>'.$no.'</center></td>
                    <td><center>'.$hs['created'].'</center></td>
                    <td><center>'.$hs['reff'].'</center></td>
                    <td><center>'.$hs['phone'].'</center></td>
                    <td>'.$hs['pesan'].'</td>
                    <td><center>'.$status.'<center></td>
                    <td><center>Rp ' . $hs['harga'] . '</center></td>
                    <td><center>'.$hs['nama_us'].'<center></td>
                    <td>'.$hs['template_name'].'</td>
                </tr>
            ';
            $no++;
        }
        echo '
            </table>
        ';
        echo'
        </div>
        ';
    echo'
    </div>
    ';

    }

    function cetak_histori_pengiriman_excel() {
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=histori_pengiriman.xls"); 
        global $con;

        $periode = isset($_GET['periode'])? $_GET['periode']:"";
        $tahun = substr($periode, 0, 4);
        $bulan = substr($periode, 5, 2);

        $sql="
            SELECT
                * 
            FROM
                ref_log_wa_gw
                JOIN users_log ON ref_log_wa_gw.id_user = users_log.id 
            WHERE
                MONTH(ref_log_wa_gw.created) = '$bulan'
                AND YEAR(ref_log_wa_gw.created) = '$tahun'
            ORDER BY
                ref_log_wa_gw.created DESC
        ";

        $result = mysqli_query($con,$sql);

        echo '
            <table id="tabsesur" width="100%" style="font-size: 10px;">
                <tr>
                    <th><center>No</center></th>
                    <th><center>Tanggal</center></th>
                    <th><center>Reff</center></th>
                    <th><center>No. Whatsapp</center></th>
                    <th><center>Pesan</center></th>
                    <th><center>Status</center></th>
                    <th><center>Harga</center></th>
                    <th><center>User</center></th>
                </tr>
        ';
        $no = 1;
        while($hs = mysqli_fetch_array($result)){
            if ($hs['code'] == '200') {
                $status = "Berhasil";
            } else {
                $status = "Gagal";
            }
            
            echo '
                <tr>
                    <td><center>'.$no.'</center></td>
                    <td><center>'.$hs['created'].'</center></td>
                    <td><center>'.$hs['reff'].'</center></td>
                    <td><center>'.$hs['phone'].'</center></td>
                    <td>'.$hs['pesan'].'</td>
                    <td><center>'.$status.'<center></td>
                    <td>Rp ' . $hs['harga'] . '</td>
                    <td><center>'.$hs['nama_us'].'<center></td>
                </tr>
            ';
            $no++;
        }
        echo '
            </table>
        ';
    }

    public function js($param1)
    {
        $this->get_js("riwayat_pengiriman/js/" .$param1);
    }

}
?>