<?php

class rekap_semua extends Controller
{

    public function index($param1 = "", $param2 = "")
    {
        $data["title"] = "rekapsemua";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("rekap_semua/view/v_rekapsemua");
        $this->view("main/view/footer");
    }

    public function rekapsemua($param1 = "", $param2 = "")
    {
        $data["title"] = "rekapsemua";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("rekap_semua/view/v_rekapsemua");
        $this->view("main/view/footer");
    }
	public function ex($param1,$params2)
	{
		$ex_controller = '';
		if (file_exists("app/module/rekap_semua/controller/$param1.php"))
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

	function laporan_absensi_semua(){
        global $con;
        $awal=isset($_GET['a'])? $_GET['a']:"";
        $akhir=isset($_GET['b'])? $_GET['b']:date('Y-m-d') . " 23:23:59";
        $a=$awal;
        $b=$akhir;

        

        $cek = substr($awal, 0, 4);
        $pecah_bulan = substr($a, 5,2);
        if (substr($a, 5,1) == '1') {
            $x = $pecah_bulan;
        }
        else{
             $x = substr($a, 6,1);
        }
        $pecah_tgl_awal = substr($a, 8,2);
        $pecah_tgl_akhir = substr($b, 8,2);

        // echo $pecah_tgl_akhir;

        $sql= "
            SELECT
                users_log.nama_us,
                users_log.id 
            FROM
                users_log 
            WHERE
                jenis_user = '2'"
        ;
        
        $bulan = date("m");
        $tahun = date("Y");
        $result = mysqli_query($con,$sql);
        echo "
		<button onclick='cetak_semua();' class='btn btn-light waves-effect waves-light' style='border:1px;padding:5px;margin:4px;'><img src='images/logo/excel.png' style='width:15px;height:15px;'> <span style='font-size:11px;'>Download excel</span></button>
		";
        $bln = '';
        $d = $pecah_tgl_akhir;
        $bulan_data = array (
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        );
        $bln = $bulan_data[(int)$x];

        echo '
		<div class="col-md-12">
			<div class="table-responsive">
						<table class="table mb-0" id="tabsesur" style="font-size: 12px; margin-left:10px">
							<thead class="table-light">
								<tr>
									<th width="30px" rowspan="2">No</th>
									<th width="80px" rowspan="2">Nama</th>
									<th width="80px" rowspan="2">Masuk/Pulang</th>
									<th colspan="'.$d.'"><center>Tanggal</center></th>
								
								</tr>
						';
							
		echo '<tr>';

        for ($i=$pecah_tgl_awal; $i <= $d; $i++) { 
                $tanggal_where = $cek."-".$x."-".$i;
                if (strlen($i)=="1") {
                    $i = "0".$i;
                }
                echo '
                    <td align="right">'.$i.'</td>
                ';
        }
        echo '</thead></tr>';
        $no=1;
        while($dt=mysqli_fetch_array($result))
        {
            $id_karyawan = $dt['id'];
           
            echo '
                <tr>
                <td rowspan="3">' . $no++ . '</td>
                <td rowspan="3">'.$dt['nama_us'].'</td>
            ';

            echo '</tr>';
            echo '
                <tr>
                <td align="center" style="background: green; color: white;">Jam Masuk</td>
            ';
            for ($i=$pecah_tgl_awal; $i <= $d; $i++) { 
                $tanggal_where = $cek."-".$x."-".$i;

                $quer_masuk = "
                    SELECT
                        SUBSTR(modified_on,11,6) AS masuk

                    FROM
                        tbl_absensi_karyawan 
                    WHERE
                        id_user_karyawan = '$id_karyawan'
                        AND jenis_task = '1'
                        AND DATE ( cretaed_at ) = '$tanggal_where.'LIMIT 1
                ";
                $result_waktu1 = mysqli_query($con,$quer_masuk);
                if (mysqli_num_rows($result_waktu1)>0) {
                     while($rowe1=mysqli_fetch_array($result_waktu1)){
                        echo '<td align="center"> '.$rowe1['masuk'] .'</td>';
                    }
                }
                else{
                    echo '<td></td>';
                }
            }
            echo '</tr>';
            echo '
                <tr>
                <td align="center" style="background: red; color: white;">Jam Pulang</td>
            ';
            for ($i=$pecah_tgl_awal; $i <= $d; $i++) { 
                $tanggal_where = $cek."-".$x."-".$i;

                $quer_pulang = "
                    SELECT
                        SUBSTR(modified_on,11,6) AS pulang
                    FROM
                        tbl_absensi_karyawan 
                    WHERE
                        id_user_karyawan = '$id_karyawan'
                        AND jenis_task = '4'
                        AND DATE ( cretaed_at ) = '$tanggal_where.'LIMIT 1
                ";
                $result_waktu1 = mysqli_query($con,$quer_pulang);
                if (mysqli_num_rows($result_waktu1)>0) {
                    while($rowe1=mysqli_fetch_array($result_waktu1)){
                        echo '<td align="center"> '.$rowe1['pulang'] .'</td>';
                    }
                }
                else{
                    echo '<td></td>';
                }

                
            }
            echo '</tr>';
        }
        echo '</table';
		echo '	
			</div>
		</div>  ';
    }
    function rekap_semua_default(){
        global $con;
        $awal = date('Y-m-d H:i:s');
        $akhir=isset($_GET['b'])? $_GET['b']:date('Y-m-d') . " 23:23:59";
        $a=$awal;
        $b=$akhir;

        $cek = substr($awal,0,4);
        $pecah_bulan = substr($a, 5,2);

        if (substr($a, 5,1) == '1') {
            $x = $pecah_bulan;
        }
        else{
             $x = substr($a, 6,1);
        }
        $pecah_tgl_awal = 01;
        $pecah_tgl_akhir = substr($b, 8,2);

        $sql= "
            SELECT
                users_log.nama_us,
                users_log.id 
            FROM
                users_log 
            WHERE
                jenis_user = '2'"
        ;
        
        $bulan = date("m");
        $tahun = date("Y");
        $result = mysqli_query($con,$sql);
        echo "
		<button onclick='cetak_semua();' class='btn btn-light waves-effect waves-light' style='border:1px;padding:5px;margin:4px;'><img src='images/logo/excel.png' style='width:15px;height:15px;'> <span style='font-size:11px;'>Download excel</span></button>
		";
        $bln = '';
        $d = $pecah_tgl_akhir;
        $bulan_data = array (
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        );
        $bln = $bulan;

        echo '
		<div class="col-md-12">
			<div class="table-responsive">
						<table class="table mb-0" id="tabsesur" style="font-size: 12px; margin-left:10px">
							<thead class="table-light">
								<tr>
									<th width="30px" rowspan="2">No</th>
									<th width="80px" rowspan="2">Nama</th>
									<th width="80px" rowspan="2">Masuk/Pulang</th>
									<th colspan="'.$d.'"><center>Tanggal</center></th>
								
								</tr>
						';
							
		echo '<tr>';

        for ($i=$pecah_tgl_awal; $i <= $d; $i++) { 
                $tanggal_where = $cek."-".$x."-".$i;
                if (strlen($i)=="1") {
                    $i = "0".$i;
                }
                echo '
                    <td align="right">'.$i.'</td>
                ';
        }
        echo '</thead></tr>';
        $no=1;
        while($dt=mysqli_fetch_array($result))
        {
            $id_karyawan = $dt['id'];
           
            echo '
                <tr>
                <td rowspan="3">' . $no++ . '</td>
                <td rowspan="3">'.$dt['nama_us'].'</td>
            ';

            echo '</tr>';
            echo '
                <tr>
                <td align="center" style="background: green; color: white;">Jam Masuk</td>
            ';
            for ($i=$pecah_tgl_awal; $i <= $d; $i++) { 
                $tanggal_where = $cek."-".$x."-".$i;

                $quer_masuk = "
                    SELECT
                        SUBSTR(modified_on,11,6) AS masuk

                    FROM
                        tbl_absensi_karyawan 
                    WHERE
                        id_user_karyawan = '$id_karyawan'
                        AND jenis_task = '1'
                        AND DATE ( cretaed_at ) = '$tanggal_where.'LIMIT 1
                ";
                $result_waktu1 = mysqli_query($con,$quer_masuk);
                if (mysqli_num_rows($result_waktu1)>0) {
                     while($rowe1=mysqli_fetch_array($result_waktu1)){
                        echo '<td align="center"> '.$rowe1['masuk'] .'</td>';
                    }
                }
                else{
                    echo '<td></td>';
                }
            }
            echo '</tr>';
            echo '
                <tr>
                <td align="center" style="background: red; color: white;">Jam Pulang</td>
            ';
            for ($i=$pecah_tgl_awal; $i <= $d; $i++) { 
                $tanggal_where = $cek."-".$x."-".$i;

                $quer_pulang = "
                    SELECT
                        SUBSTR(modified_on,11,6) AS pulang
                    FROM
                        tbl_absensi_karyawan 
                    WHERE
                        id_user_karyawan = '$id_karyawan'
                        AND jenis_task = '4'
                        AND DATE ( cretaed_at ) = '$tanggal_where.'LIMIT 1
                ";
                $result_waktu1 = mysqli_query($con,$quer_pulang);
                if (mysqli_num_rows($result_waktu1)>0) {
                    while($rowe1=mysqli_fetch_array($result_waktu1)){
                        echo '<td align="center"> '.$rowe1['pulang'] .'</td>';
                    }
                }
                else{
                    echo '<td></td>';
                }

                
            }
            echo '</tr>';
        }
        echo '</table';
		echo '	
			</div>
		</div>  ';
    }


    public function js($param1)
    {
        $this->get_js("rekap_semua/js/" .$param1);
    }
}
?>