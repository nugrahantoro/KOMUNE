<?php
class Kasv2 extends Controller
{
    var $content;

    public function index($param1 = "", $param2 = "")
    {
		global $database;
        $data["title"] = "Data Jenis Kas";
        $data["href"] = "";
        $data["dbn"] = $database;
        $this->view("main/view/header", $data);
        $this->view("kasv2/view/v_data_kas",$data);
        $this->view("main/view/footer");
    }
	
	public function pemasukan_kas($param1 = "", $param2 = "")
    {
        $data["title"] = "Pemasukan Kas";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("kasv2/view/v_inp_kas_in");
        $this->view("main/view/footer");
    }
	
	public function pengeluaran_kas($param1 = "", $param2 = "")
    {
        $data["title"] = "Pengeluaran Kas";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("kasv2/view/v_inp_kas_out");
        $this->view("main/view/footer");
    }
	
	public function lap_kas($param1 = "", $param2 = "")
    {
        $data["title"] = "Laporan Arus Kas";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("kasv2/view/v_lap_kas");
        $this->view("main/view/footer");
    }

	public function laporan_kas($param1 = "", $param2 = "")
    {
        $data["title"] = "Laporan Arus Kas";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("kasv2/view/v_lap_kas");
        $this->view("main/view/footer");
    }
	
	public function laporan_kas_dev($param1 = "", $param2 = "")
    {
        $data["title"] = "Laporan Kas";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("kasv2/view/v_lap_kas_dev");
        $this->view("main/view/footer");
    }
	
	public function ex($param1,$params2,$params3=null,$params4=null,$params5=null,$params6=null)
	{
		$ex_controller = '';
		if (file_exists("app/module/kasv2/controller/$param1.php"))
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
				return call_user_func_array([$ex_controller,$params2],[$params3,$params4,$params5,$params6]);
				}
			}
		}
	}
	
	function get_periode($periode='',$modif=0) {
		if($periode=='')
		{
			$periode = date('Y-m');
		}
		
		if($modif==0)
		{
			return $periode;
		}
		
		$timestamp = strtotime($periode);

		$periode_sel_obj = strtotime($modif.' month', $timestamp);

		$periode_sel = date('Y-m', $periode_sel_obj);

		return $periode_sel;
	}
	
	function generate_periode($periode='')
	{ 
		if($periode=='')
		{
			$periode=$this->get_periode();
		}
		$chk_periode = db_get("SELECT COUNT(*) FROM kas_periode WHERE periode = '$periode'");
		if($chk_periode >0)
		{
			die;
		}
		
		$last_periode = $this->get_periode($periode,'-1');
		$chk_periode = db_get("SELECT COUNT(*) FROM kas_periode WHERE periode = '$last_periode'");
		if($chk_periode =0)
		{
			$last_day = $this->getLastDayOfMonth($last_periode);
			$chk_trans = db_get("SELECT COUNT(*) FROM kas_transaksi WHERE del_stat = 0
								AND tanggal BETWEEN '$last_periode-01 00:00:00' AND '$last_day 23:59:59'");
			if($chk_trans>0)
			{
				$this->generate_periode($last_periode);
				die;
			}
			else
			{
				$last_periode = 'default';
			}
			
		}
		
		$saldo_awal=$this->get_saldo_akhir($last_periode);
		$data = db_assoc("SELECT data_cetak FROM kas_periode WHERE periode = '$last_periode'");
		$last_day = ucwords($this->getLastDayOfMonth($periode));
		$arr = json_decode($data['data_cetak'],true);
		$arr['lap_masuk']['tanggal']=$this->bulan_tahun($last_day);
		$arr['lap_keluar']['tanggal']=$this->bulan_tahun($last_day);
		$arr['neraca']['tanggal']=$this->bulan_tahun($last_day);
		$arr['jurnal_masuk']['tanggal']=$this->bulan_tahun($last_day);
		$arr['jurnal_keluar']['tanggal']=$this->bulan_tahun($last_day);
		$data_cetak=json_encode($arr,JSON_PRETTY_PRINT);
		$que = "INSERT INTO kas_periode(
					periode,
					saldo_awal,
					data_cetak
				)
				VALUES (
					'$periode',
					$saldo_awal,
					'$data_cetak'
				)";
		db_query($que);
		$next_periode = $this->get_periode($periode,'+1');
		$this->update_saldo_awal($next_periode);
	}
	
	function update_saldo_awal($periode)
	{
		$chk_periode = db_get("SELECT COUNT(*) FROM kas_periode WHERE periode = '$periode'");
		if($chk_periode ==0)
		{
			$chk_periode = db_get("SELECT COUNT(*) FROM kas_periode WHERE periode = '$periode'");
			if($chk_periode >0)
			{
				$last_day = $this->getLastDayOfMonth($periode);
				$chk_trans = db_get("SELECT COUNT(*) FROM kas_transaksi WHERE del_stat = 0
									AND tanggal BETWEEN '$periode-01 00:00:00' AND '$last_day 23:59:59'");
				if($chk_trans>0)
				{
					$this->generate_periode($periode);
				}				
			}
		}
		else
		{
			$last_periode = $this->get_periode($periode,'-1');
			$saldo_awal = $this->get_saldo_akhir($last_periode);
			$que = "UPDATE kas_periode SET saldo_awal = $saldo_awal WHERE periode = '$periode'";
			db_query($que);
			$next_periode = $this->get_periode($periode,'+1');
			$this->update_saldo_awal($next_periode);
		}
	}
	
	function get_saldo_akhir($periode,$id_jns_kas='',$modif=0)
	{
		$periode = $this->get_periode($periode,$modif);
		$que ="SELECT
					* 
				FROM
					kas_periode 
				WHERE
					periode != 'default' AND DATE(CONCAT(periode,'-1')) <= DATE(CONCAT('$periode','-1'))
				ORDER BY
					periode";
		$res = db_query($que);
		$total_in=0;
		$total_out=0;
		while($dt=mysqli_fetch_array($res)) {
			//echo $dt['periode']."<br>";
			$total_in += $this->get_grand_total($dt['periode'],'masuk',$id_jns_kas);
			$total_out += $this->get_grand_total($dt['periode'],'keluar',$id_jns_kas);
		}
		$saldo_akhir=$total_in-$total_out;
		return $saldo_akhir;
	}
	
	function get_saldo_akhir_old($periode)
	{
		$saldo_awal=db_get("SELECT saldo_awal FROM kas_periode WHERE periode = '$periode'");
		$akhir = $this->getLastDayOfMonth($periode);
		$total_in = $this->get_grand_total($periode,'masuk');
		$total_out = $this->get_grand_total($periode,'keluar');
		$selisih=$total_in-$total_out;
		$saldo_akhir = $saldo_awal+$selisih;
		return $saldo_akhir;
	}
	
	function add_br($jml=1)
	{
		if($jml<0)
		{
			$jml=0;
		}
		$t='';
		for($i=0;$i<$jml;$i++)
		{
			$t.="<br>";
		}
		return $t;
	}

	function bulan_tahun($input) {
		// Explode the input into year, month, and day
		$dateComponents = explode('-', $input);

		// Check the length of the exploded array
		if (count($dateComponents) == 2) {
			// 'yyyy-mm' format
			list($year, $month) = $dateComponents;
			$day = ''; // No day provided, so leave it empty
		} elseif (count($dateComponents) == 3) {
			// 'yyyy-mm-dd' format
			list($year, $month, $day) = $dateComponents;
		} else {
			// Invalid format
			return 'Invalid date format';
		}

		// Array of Indonesian month names
		$monthNames = [
			'januari', 'februari', 'maret',
			'april', 'mei', 'juni',
			'juli', 'agustus', 'september',
			'oktober', 'november', 'desember'
		];

		// Get the Indonesian month name
		$indonesianMonth = $monthNames[intval($month) - 1];

		// Return the formatted result
		return ($day ? $day . ' ' : '') . $indonesianMonth . ' ' . $year;
	}


	
	function get_data_trans($periode, $mode, $id_jns_kas='')
	{
		if($mode=='masuk' || $mode =='in')
		{
			$table="pemasukan";
			$inout="in";
			$tipe = 1;
		}
		if($mode=='keluar' || $mode =='out')
		{
			$table="pengeluaran";
			$inout="out";
			$tipe = 2;
		}
		
		$que = "SELECT
					jns.id_jns_$table as id_jns,
					jns.ref AS ref_jns,
					nama_jns_$table as nama_jns,
					kp.id_kat_$table as id_kat,
					kp.ref AS ref_kp,
					kp.nama_kat_$table as nama_kat,
					id_subkat_$table as id_subkat,
					skp.ref AS ref_skp,
					skp.nama_subkat_$table as  nama_subkat
				FROM
					kas_jenis_$table  AS jns
					LEFT JOIN kas_kategori_$table  AS kp ON kp.id_jns_$table = jns.id_jns_$table AND (
						kp.del_stat = 0 
					OR id_kat_$table IN ( 0 ))
					LEFT JOIN kas_subkategori_$table  AS skp ON skp.id_kat_$table = kp.id_kat_$table AND (
						skp.del_stat = 0 
					OR id_subkat_$table IN ( 0 )) 
				WHERE
					jns.del_stat = 0 
					OR jns.id_jns_$table IN ( 0 ) 
				ORDER BY
					LENGTH( ref_jns ),
					ref_jns,
					LENGTH( ref_kp ),
					ref_kp,
					LENGTH( ref_skp ),
					ref_skp,
					id_subkat_$table ";
		$res = db_query($que);
		$msk = [];
		while($dt = mysqli_fetch_array($res))
		{
			if(!isset($msk[$dt['id_jns']]))
			{
				$msk[$dt['id_jns']]=array("id_jns"=>$dt['id_jns'],
												"ref"=>$dt['ref_jns'],
												"nama_jns"=>$dt['nama_jns'],
												"total_all"=>0,
												"total"=>0,
												"kategori"=>[],
												"kategori_count"=>0,
												"subkategori_count"=>0
												);
			}
			
			$kat = $msk[$dt['id_jns']]['kategori'];
			if(!is_null($dt['id_kat']))
			{
				if(!isset($kat[$dt['id_kat']]))
				{
					$kat[$dt['id_kat']]=array("id_kat"=>$dt['id_kat'],
													"ref"=>$dt['ref_kp'],
													"nama_kat"=>$dt['nama_kat'],
													"total_all"=>0,
													"total"=>0,
													"subkategori"=>[],
													"subkategori_count"=>0
													);
					$msk[$dt['id_jns']]['kategori_count']+=1;
				}
				$skat=$kat[$dt['id_kat']]['subkategori'];
				if(!is_null($dt['id_subkat']))
				{
					if(!isset($skat[$dt['id_subkat']]))
					{
						$skat[$dt['id_subkat']]=array("id_subkat"=>$dt['id_subkat'],
														"ref"=>$dt['ref_skp'],
														"nama_subkat"=>$dt['nama_subkat'],
														"total"=>0);
						$msk[$dt['id_jns']]['subkategori_count']+=1;
						$kat[$dt['id_kat']]['subkategori_count']+=1;
					}
				}
				
				$kat[$dt['id_kat']]['subkategori']=$skat;
			}
			
			$msk[$dt['id_jns']]['kategori']=$kat;
		}
		
		$que = $this->query_list_transaksi($periode,$mode, $id_jns_kas);
		$res = db_query($que);
		while($dt = mysqli_fetch_array($res))
		{
			if($dt['id_kat']==0)
			{
				$dt['id_kat']=null;
			}
			if($dt['id_subkat']==0)
			{
				$dt['id_subkat']=null;
			}
			
			if(!isset($msk[$dt['id_jns']]))
			{
				$dt['id_jns']=0;
				if( !isset($msk[0]))
				{
					$msk[0]=array("id_jns"=>0,
												"ref"=>'',
												"nama_jns"=>'Lainnya',
												"total_all"=>0,
												"total"=>0,
												"kategori"=>[],
												"kategori_count"=>0,
												"subkategori_count"=>0
												);
				}
			}
			
			$msk[$dt['id_jns']]['total_all']+=$dt['nominal'];
			
			if(!is_null($dt['id_kat']))
			{
				$msk[$dt['id_jns']]['kategori'][$dt['id_kat']]['total_all']+=$dt['nominal'];
			}
			else
			{
				$msk[$dt['id_jns']]['total']+=$dt['nominal'];
			}
			
			if(!is_null($dt['id_subkat']))
			{
				$msk[$dt['id_jns']]['kategori'][$dt['id_kat']]['subkategori'][$dt['id_subkat']]['total']+=$dt['nominal'];
			}
			else if (!is_null($dt['id_kat']))
			{
				$msk[$dt['id_jns']]['kategori'][$dt['id_kat']]['total']+=$dt['nominal'];
			}
		}
		
		return $msk;
	}
	
	function get_data_trans_ext($periode, $mode)
	{
		if($mode=='masuk' || $mode =='in')
		{
			$table="pemasukan";
			$inout="in";
			$tipe = 1;
		}
		if($mode=='keluar' || $mode =='out')
		{
			$table="pengeluaran";
			$inout="out";
			$tipe = 2;
		}
		$que = "SELECT
					jns.id_jns_$table as id_jns,
					jns.ref AS ref_jns,
					nama_jns_$table as nama_jns,
					kp.id_kat_$table as id_kat,
					kp.ref AS ref_kp,
					kp.nama_kat_$table as nama_kat,
					id_subkat_$table as id_subkat,
					skp.ref AS ref_skp,
					skp.nama_subkat_$table as  nama_subkat
				FROM
					kas_jenis_$table  AS jns
					LEFT JOIN kas_kategori_$table  AS kp ON kp.id_jns_$table = jns.id_jns_$table AND (
						kp.del_stat = 0 
					OR id_kat_$table IN ( 0 ))
					LEFT JOIN kas_subkategori_$table  AS skp ON skp.id_kat_$table = kp.id_kat_$table AND (
						skp.del_stat = 0 
					OR id_subkat_$table IN ( 0 )) 
				WHERE
					jns.del_stat = 0 
					OR jns.id_jns_$table IN ( 0 ) 
				ORDER BY
					LENGTH( ref_jns ),
					ref_jns,
					LENGTH( ref_kp ),
					ref_kp,
					LENGTH( ref_skp ),
					ref_skp,
					id_subkat_$table ";
		$res = db_query($que);
		$msk = [];
		while($dt = mysqli_fetch_array($res))
		{
			if(!isset($msk[$dt['id_jns']]))
			{
				$msk[$dt['id_jns']]=array("id_jns"=>$dt['id_jns'],
												"ref"=>$dt['ref_jns'],
												"nama_jns"=>$dt['nama_jns'],
												"total_all"=>0,
												"total"=>0,
												"kategori"=>[],
												"kategori_count"=>0,
												"subkategori_count"=>0,
												"data_trans"=>[]
												);
			}
			
			$kat = $msk[$dt['id_jns']]['kategori'];
			if(!is_null($dt['id_kat']))
			{
				if(!isset($kat[$dt['id_kat']]))
				{
					$kat[$dt['id_kat']]=array("id_kat"=>$dt['id_kat'],
													"ref"=>$dt['ref_kp'],
													"nama_kat"=>$dt['nama_kat'],
													"total_all"=>0,
													"total"=>0,
													"subkategori"=>[],
													"subkategori_count"=>0,
													"data_trans"=>[]
													);
					$msk[$dt['id_jns']]['kategori_count']+=1;
				}
				$skat=$kat[$dt['id_kat']]['subkategori'];
				if(!is_null($dt['id_subkat']))
				{
					if(!isset($skat[$dt['id_subkat']]))
					{
						$skat[$dt['id_subkat']]=array("id_subkat"=>$dt['id_subkat'],
														"ref"=>$dt['ref_skp'],
														"nama_subkat"=>$dt['nama_subkat'],
														"total"=>0);
						$msk[$dt['id_jns']]['subkategori_count']+=1;
						$kat[$dt['id_kat']]['subkategori_count']+=1;
					}
				}
				
				$kat[$dt['id_kat']]['subkategori']=$skat;
			}
			
			$msk[$dt['id_jns']]['kategori']=$kat;
		}
		
		$que = $this->query_list_transaksi($periode,$mode);
		$que = "SELECT * FROM ($que) as tb_pulupulu ORDER BY tanggal";
		$res = db_query($que);
		while($dt = mysqli_fetch_assoc($res))
		{
			if($dt['id_kat']==0)
			{
				$dt['id_kat']=null;
			}
			if($dt['id_subkat']==0)
			{
				$dt['id_subkat']=null;
			}
			
			if(!isset($msk[$dt['id_jns']]))
			{
				$dt['id_jns']=0;
				if( !isset($msk[0]))
				{
					$msk[0]=array("id_jns"=>0,
												"ref"=>'',
												"nama_jns"=>'Lainnya',
												"total_all"=>0,
												"total"=>0,
												"kategori"=>[],
												"kategori_count"=>0,
												"subkategori_count"=>0,
												"data_trans"=>[]
												);
				}
			}
			
			$msk[$dt['id_jns']]['total_all']+=$dt['nominal'];
			
			if(!is_null($dt['id_kat']))
			{
				$msk[$dt['id_jns']]['kategori'][$dt['id_kat']]['total_all']+=$dt['nominal'];
			}
			else
			{
				$msk[$dt['id_jns']]['total']+=$dt['nominal'];
			}
			
			if(!is_null($dt['id_subkat']))
			{
				$msk[$dt['id_jns']]['kategori'][$dt['id_kat']]['subkategori'][$dt['id_subkat']]['total']+=$dt['nominal'];
			}
			else if (!is_null($dt['id_kat']))
			{
				$msk[$dt['id_jns']]['kategori'][$dt['id_kat']]['total']+=$dt['nominal'];
			}
			
			
			///DATA ENTRY INTO ARRAY
			
			$dt['keterangan']=str_replace('<br>','',$dt['keterangan']);
			$dt['keterangan']=str_replace('<br>','',$dt['keterangan']);
			
			if(!is_null($dt['id_kat']))
			{
				if(!is_null($dt['id_subkat']))
				{
					if(!isset($msk[$dt['id_jns']]['kategori'][$dt['id_kat']]['data_trans'][$dt['id_subkat']]))
					{
						$tmp = $dt;
						$tmp['keterangan']=$msk[$dt['id_jns']]['kategori'][$dt['id_kat']]['subkategori'][$dt['id_subkat']]['nama_subkat'];
						$tmp['nominal']=0;
						$msk[$dt['id_jns']]['kategori'][$dt['id_kat']]['data_trans'][$dt['id_subkat']]=$tmp;
					}
					$msk[$dt['id_jns']]['kategori'][$dt['id_kat']]['data_trans'][$dt['id_subkat']]['nominal']+=$dt['nominal'];
				}
				else
				{
					array_push($msk[$dt['id_jns']]['kategori'][$dt['id_kat']]['data_trans'],$dt);
				}
			}
			else
			{
				array_push($msk[$dt['id_jns']]['data_trans'],$dt);
			}
		}
		
		return $msk;
	}
	
	function get_grand_total($periode,$mode='',$id_jns_kas='')
	{
		$data = $this->get_data_trans($periode,$mode,$id_jns_kas);
		$total=0;
		foreach($data as $dt)
		{
			$total+=$dt['total_all'];
		}
		return $total;
	}
	
	function query_list_transaksi($periode, $mode = 'all', $id_jns_kas = 0)
	{
		$arr=[];
		if($mode=='masuk' || $mode =='in' || $mode =='all' )
		{
			$dt['table']="pemasukan";
			$dt['inout']="in";
			$dt['tipe'] = 1;
			array_push($arr,$dt);
		}
		if($mode=='keluar' || $mode =='out' || $mode =='all')
		{
			$dt['table']="pengeluaran";
			$dt['inout']="out";
			$dt['tipe'] = 2;
			array_push($arr,$dt);
		}
		
		$wh_id_jns = "";
		if($id_jns_kas!='')
		{
			$wh_id_jns = " id_jns_kas = $id_jns_kas AND";
		}
		
		$akhir = $this->getLastDayOfMonth($periode);
		$query = [];
		foreach($arr as $dt)
		{
			$table=$dt['table'];
			$inout=$dt['inout'];
			$tipe = $dt['tipe'];
			
			$ext_que='';
			if($table=='pemasukan')
			{
				///ID JENIS PEMASUKAN FIXED
				$id_tahunan = "13,3";
				$id_spp = 14;
				$id_kelas=[0,13,14,15];
				$id_lain=3;
				$id_lain_kat='13,14';
				$id_tunggakan=8;
				$id_ta = db_get("SELECT
									id_tahunajar 
								FROM
									data_tahunajar 
								WHERE
									'$periode-05' BETWEEN tgl_mulai 
									AND tgl_selesai 
									LIMIT 1");
				if(is_null($id_ta))
				{
					$id_ta=0;
				}
				$ext_que = "
					UNION ALL 
					SELECT
						id_transaksi,
						CONVERT ( CONCAT( nama_transaksi, '<br> (', nama_siswa, ' - ', nama_kelas, ')' ) USING utf8 ) AS keterangan,
						transaksi_siswa.tgl AS tanggal,
						id_jns_pemasukan AS id_jns,
						id_kat_pemasukan AS id_kat,
						id_subkat_pemasukan AS id_subkat,
						1 as tipe,
						nominal_dibayarkan AS nominal
					FROM
						master_jenis_transaksi
						INNER JOIN transaksi_siswa ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi 
						INNER JOIN data_siswa ON transaksi_siswa.nis_from_idsiswa = data_siswa.id_siswa
						INNER JOIN data_kelas ON transaksi_siswa.id_kelas = data_kelas.id_kelas 
					WHERE
						id_jns_pemasukan IN ($id_tahunan) AND master_jenis_transaksi.id_sesi = $id_ta
						AND transaksi_siswa.tgl >= '$periode-01 00:00:00' 
						AND transaksi_siswa.tgl <= '$akhir 23:59:59' ";
				
				///QUERY OPERASIONAL SPP PER KELAS
				$case= " ";
				for($i=1;$i<count($id_kelas);$i++)
				{
					$case.="WHEN `level` = $i THEN $id_kelas[$i] ";
				}
				$case.=" ELSE id_subkat_pemasukan ";
				$ext_que.="
						UNION ALL
						SELECT
							id_transaksi,
							CONVERT ( CONCAT( nama_transaksi, '<br> (', nama_siswa, ' - ', nama_kelas, ')' ) USING utf8 ) AS keterangan,
							transaksi_siswa.tgl AS tanggal,
							id_jns_pemasukan AS id_jns,
							id_kat_pemasukan AS id_kat,
							(CASE
								$case
							END) AS id_subkat,
							1 as tipe,
							nominal_dibayarkan AS nominal 
						FROM
							master_jenis_transaksi
							INNER JOIN transaksi_siswa ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
							INNER JOIN data_siswa ON transaksi_siswa.nis_from_idsiswa = data_siswa.id_siswa
							INNER JOIN data_kelas ON transaksi_siswa.id_kelas = data_kelas.id_kelas 
						WHERE
							id_jns_pemasukan IN ( $id_spp ) 
							AND master_jenis_transaksi.id_sesi = $id_ta 
							AND transaksi_siswa.tgl >= '$periode-01 00:00:00' 
							AND transaksi_siswa.tgl <= '$akhir 23:59:59' ";
							
				///QUERY TUNGGAKAN
				$ext_que .= "
						UNION ALL
						SELECT
							id_transaksi,
							CONVERT ( CONCAT( nama_transaksi, '<br> (', nama_siswa, ' - ', nama_kelas, ')' ) USING utf8 ) AS keterangan,
							transaksi_siswa.tgl AS tanggal,
							$id_lain AS id_jns,
							$id_tunggakan AS id_kat,
							0 AS id_subkat,
							1 as tipe,
							nominal_dibayarkan AS nominal 
						FROM
							master_jenis_transaksi
							INNER JOIN transaksi_siswa ON master_jenis_transaksi.id_jenis_transaksi = transaksi_siswa.id_jenis_transaksi
							INNER JOIN data_siswa ON transaksi_siswa.nis_from_idsiswa = data_siswa.id_siswa
							INNER JOIN data_kelas ON transaksi_siswa.id_kelas = data_kelas.id_kelas 
						WHERE
							id_jns_pemasukan IN ( $id_lain_kat ) 
							AND master_jenis_transaksi.id_sesi != $id_ta
							AND transaksi_siswa.tgl >= '$periode-01 00:00:00' 
							AND transaksi_siswa.tgl <= '$akhir 23:59:59' ";
			}
			$que="SELECT 
						id_transaksi,
						keterangan,
						tanggal,
						id_jns_$table as id_jns,
						id_kat_$table as id_kat,
						id_subkat_$table as id_subkat,
						tipe,
						nominal_$inout as nominal
					FROM 
						kas_transaksi 
					WHERE 
						$wh_id_jns 
						tipe = $tipe AND del_stat = 0 AND tanggal >= '$periode-01 00:00:00' 
						AND tanggal <= '$akhir 23:59:59' 
					$ext_que
					";
			array_push($query,$que);
		}
		$query = implode(" UNION ALL ", $query);
		return $query;
	}
	
	public function js($param1)
    {
        $this->get_js("kasv2/js/" .$param1);
    }
}

?>
