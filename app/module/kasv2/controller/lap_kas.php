<?php

class Lap_Kas extends Controller{
	
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
	
	function get_saldo_akhir($periode,$modif=0)
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
			$total_in += $this->get_grand_total($dt['periode'],'masuk');
			$total_out += $this->get_grand_total($dt['periode'],'keluar');
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


	
	function get_data_trans($periode, $mode)
	{
		$id_jenjang = $_GET['id_jenjang'];
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
					(jns.del_stat = 0 
					OR jns.id_jns_$table IN ( 0 )
					)
					AND id_jenjang = $id_jenjang
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
		
		$que = $this->query_list_transaksi($periode,$mode);
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
		$id_jenjang = $_GET['id_jenjang'];
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
					(
					jns.del_stat = 0 
					OR jns.id_jns_$table IN ( 0 ) 
					)
					AND id_jenjang = $id_jenjang
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
	
	function get_grand_total($periode,$mode='')
	{
		$data = $this->get_data_trans($periode,$mode);
		$total=0;
		foreach($data as $dt)
		{
			$total+=$dt['total_all'];
		}
		return $total;
	}
	
	function get_table_laporan($periode,$mode,$print=0)
	{
		$msk = $this->get_data_trans($periode,$mode);
		///buat table
		$tbody = '';
		$i=0;
		$rspan_all=0;
		
		if($mode=='masuk' || $mode =='in')
		{
			$mode_ket="PEMASUKAN";
			$nomor="1";
			$ket_size = '9pt';
		}
		if($mode=='keluar' || $mode =='out')
		{
			$mode_ket="PENGELUARAN";
			$nomor="2";
			$ket_size = '11pt';
		}
		
		$tr_jns = '';
		$grand_total=0;
		$arr_jns=[];
		foreach($msk as $jns)
		{
			$i+=1;
			$cspan_jns = 1;
			$rspan_jns = 1+($jns['kategori_count']-1)+$jns['subkategori_count'];
			
			$arr_kat = [];
			if(count($jns['kategori'])>0)
			{
				if($jns['total']>0)
				{
					$rspan_jns+=1;
				}
				
				$i_kat = 0;
				foreach($jns['kategori'] as $kat)
				{
					$rspan_kat = 1+ $kat['subkategori_count'];
					
					$tr_skat = [];
					foreach($kat['subkategori'] as $skat)
					{
						$tr = "
							<td>$skat[ref] $skat[nama_subkat]</td>
							<td style='text-align:right;'>".rupiah($skat['total'])."</td>";
						$tr_skat[]=$tr;
					}
					
					$td_style='';
					if($kat['subkategori_count']>0)
					{
						$td_style = "font-weight: bold;";
					}
					
					$tr = "
						<td rowspan = '$rspan_kat' style='text-align:center;'>$kat[ref]</td>
						<td style='$td_style'>$kat[nama_kat] </td>
						<td style='text-align:right;'>".rupiah($kat['total'])."</td>
					";
					
					$br='';
					if($print==1)
					{
						$br=$this->add_br($rspan_jns-2);
					}
					
					if($i_kat==0)
					{
						$tr.="<td style='text-align:right;' rowspan='$rspan_jns'>$br ".rupiah($jns['total_all'])."</td>";
					}
					
					if(count($tr_skat)>0)
					{
						$tr.="</tr><tr>".implode("</tr><tr>",$tr_skat);
					}
					$arr_kat[]=$tr;
					$i_kat++;
				}
				
				if($jns['total']>0)
				{
					$tr = "
						<td></td>
						<td>Lainnya</td>
						<td style='text-align:right;'>".rupiah($jns['total'])."</td>
						";
					$arr_kat[]=$tr;
				}
			}
			else
			{
				$rspan_jns = 1;
				$cspan_jns = 3;
				$tr="
					<td colspan = '2' style='text-align:right;'>".rupiah($jns['total_all'])."</td>";
				$arr_kat[]=$tr;
			}	
			
			$br='';
			if($print==1)
			{
				$br=$this->add_br($rspan_jns-2);
			}
					
			$tr = "
					<td rowspan='$rspan_jns' colspan = '$cspan_jns'>$br $jns[ref] $jns[nama_jns]
					</td>
					";
			if(count($arr_kat)>0)
			{
				$tr.=implode("</tr><tr>",$arr_kat);
			}
			$arr_jns[]=$tr;
			$rspan_all+=$rspan_jns;
			$grand_total+=$jns['total_all'];
		}
		//style='writing-mode: sideways-lr;width: 20pt;'
		$br='';
		$ket_lap="<span style='writing-mode: vertical-lr;rotate:180deg;'><b>$mode_ket</b></span>";
		if($print==1)
		{
			$img_path = "app/lib/$mode_ket.png";
			$br=$this->add_br($rspan_all-4);
			$br_ket=$this->add_br($rspan_all-6);
			$ket_lap ="$br_ket <img src='$img_path' width ='$ket_size'>";
		}
		$tbody = "
				<tr>
					<td rowspan='$rspan_all'  style='vertical-align:middle;text-align:center;' width='4%'>$br <b>$nomor</b></td>
					<td rowspan='$rspan_all' width='4%'  style='vertical-align:middle;text-align:center;' > $ket_lap</td>
				".implode("</tr><tr>",$arr_jns)."</tr>";
		
		
		$tahun_ajar = db_get("SELECT
							nama_tahunajar 
						FROM
							data_tahunajar 
						WHERE
							'$periode-02' BETWEEN tgl_mulai 
							AND tgl_selesai 
							LIMIT 1");
		
		
		$table = "
		<table width='100%' border='0' style=''>
			<tr>
				<td style='text-align:center;font-size:10pt;'><b>LAPORAN $mode_ket BULANAN</b></td>
			</tr>
			<tr>
				<td style='text-align:center;font-size:10pt;'><b> ".strtoupper(SCHOOL_NAME)." </b></td>
			</tr>
			<tr>
				<td style='text-align:center;font-size:10pt;'><b>TAHUN PELAJARAN $tahun_ajar</b></td>
			</tr>
		</table>
		<br>
		<br>
		<table width='100%'>
			<tr>
				<td style='text-align:right;'><b>Bulan : ".strtoupper($this->bulan_tahun($periode))."</b></td>
			</tr>
		</table>
		<table  class='table table-bordered table-sm' border='0.5pt' width='100%' cellpadding='1.5pt'>
				<tr>
					<th colspan='2' style='text-align:center;' width='8%'><b>No</b></th>
					<th style='text-align:center;' width='24%'><b>Akun</b></th>
					<th style='text-align:center;' width='6%'><b>Ref</b></th>
					<th style='text-align:center;' width='34%'><b>Jenis</b></th>
					<th style='text-align:center;' width='14%'><b>Debit (Rp)</b></th>
					<th style='text-align:center;' width='14%'><b>Jumlah (Rp)</b></th>
				</tr>
				$tbody
				<tr>
					<th colspan='5' style='text-align:center;'><b>".strtoupper("JUMLAH $mode_ket SEKOLAH BULAN ".$this->bulan_tahun($periode))."</b></th>
					<th colspan='2' style='text-align:right;'><b>".rupiah($grand_total)."</b></th>
				</tr>
		</table>";
		return $table;
	}
	
	function get_table_neraca($periode,$print=0)
	{
		$id_jenjang = $_GET['id_jenjang'];
		$arr_mode=['masuk','keluar'];
		$tbody_neraca='';
		foreach($arr_mode as $mode_val)
		{
			$mode=$mode_val;
			$msk = $this->get_data_trans($periode,$mode);
			///buat table
			$i=0;
			$rspan_all=0;
			
			if($mode=='masuk' || $mode =='in')
			{
				$mode_ket="PEMASUKAN";
				$nomor="1";
				$ket_size = '8pt';
				$ex_rspan=0;
			}
			if($mode=='keluar' || $mode =='out')
			{
				$mode_ket="PENGELUARAN";
				$nomor="2";
				$ket_size = '11pt';
				$ex_rspan=2;
			}
			
			$tr_jns = '';
			$grand_total[$mode]=0;
			$arr_jns=[];
			//$arr_jns[0]='';
			foreach($msk as $jns)
			{ 
				$i+=1;
				$cspan_jns = 1;
				$rspan_jns = 1;
				$rspan_all+=$rspan_jns;
				$tr = "
						<td>$jns[ref]</td>
						<td>$jns[nama_jns]</td>
						<td  style='text-align:right;'>".rupiah($jns['total_all'])."</td>
						";
				$arr_jns[]=$tr;
				$grand_total[$mode]+=$jns['total_all'];
			}
			
			
			$br='';
			$ket_lap="<span style='writing-mode: vertical-lr;rotate:180deg;'><b>$mode_ket</b></span>";
			if($print==1)
			{
				$img_path = "app/lib/$mode_ket.png";
				$br=$this->add_br($rspan_all-2);
				$br_ket=$this->add_br($rspan_all-4);
				$ket_lap ="$br_ket <img src='$img_path' width ='$ket_size'>";
			}
			if(!isset($arr_jns[0]))
			{
				$tr = "
						<td></td>
						<td></td>
						<td  style='text-align:right;'></td>
						";
				$arr_jns[0]=$tr;
				$rspan_all+=1;
			}
			$arr_jns[0].="<td style='text-align:right;' rowspan='$rspan_all'>$br ".rupiah($grand_total[$mode])."</td>";
			$tbody = "
					<tr>
						<td rowspan='".($rspan_all+$ex_rspan)."'  style='vertical-align:middle;text-align:center;' width='4%'>$br <b>$nomor</b></td>
						<td rowspan='".($rspan_all+$ex_rspan)."' width='4%'  style='vertical-align:middle;text-align:center;' > $ket_lap</td>
					".implode("</tr><tr>",$arr_jns)."</tr>";
					
			$tbody_neraca.=$tbody; 
		}
		
		$chk_periode = db_get("SELECT COUNT(*) FROM kas_periode WHERE periode = '$periode'");
		if($chk_periode==0)
		{
			$this->generate_periode($periode);
		}
		
		$tahun_ajar = db_get("SELECT
							nama_tahunajar 
						FROM
							data_tahunajar 
						WHERE
							'$periode-02' BETWEEN tgl_mulai 
							AND tgl_selesai 
							LIMIT 1");
		
		
		
		//$saldo_awal = db_get("SELECT saldo_awal FROM kas_periode WHERE periode = '$periode'");
		$saldo_awal = $this->get_saldo_akhir($periode,'-1');
		$surplus_defisit = $grand_total['masuk']-$grand_total['keluar'];
		$saldo_akhir = $saldo_awal + $surplus_defisit;
		
		$table = "
		<table width='100%' border='0' style=''>
			<tr>
				<td style='text-align:center;font-size:10pt;'><b>LAPORAN NERACA PERIODE BULANAN</b></td>
			</tr>
			<tr>
				<td style='text-align:center;font-size:10pt;'><b> ".strtoupper(SCHOOL_NAME)." </b></td>
			</tr>
			<tr>
				<td style='text-align:center;font-size:10pt;'><b>TAHUN PELAJARAN $tahun_ajar</b></td>
			</tr>
		</table>
		<br>
		<br>
		<table width='100%'>
			<tr>
				<td style='text-align:right;'><b>Bulan : ".strtoupper($this->bulan_tahun($periode))."</b></td>
			</tr>
		</table>
		<table  class='table table-bordered table-sm' border='0.5px' width='100%' cellpadding='2pt'>
				<tr>
					<th colspan='2' style='text-align:center;' width='8%'><b>No</b></th>
					<th style='text-align:center;' width='6%'><b>Ref</b></th>
					<th style='text-align:center;' width='58%'><b>Jenis</b></th>
					<th style='text-align:center;' width='14%'><b>Debit (Rp)</b></th>
					<th style='text-align:center;' width='14%'><b>Jumlah (Rp)</b></th>
				</tr>
				$tbody_neraca
				<tr>
					<th colspan='2' style='text-align:center;'><b>".strtoupper("SURPLUS / DEFISIT")."</b></th>
					<th colspan='2' style='text-align:right;'><b>".rupiah($surplus_defisit)."</b></th>
				</tr>
				<tr>
					<th colspan='2' style='text-align:center;'><b>Saldo Awal Bulan ".strtoupper($this->bulan_tahun($periode))."</b></th>
					<th colspan='2' style='text-align:right;'><b>".rupiah($saldo_awal)."</b></th>
				</tr>
				<tr>
					<th colspan='4' style='text-align:center;'><b>".strtoupper("SALDO AKHIR BULAN ".$this->bulan_tahun($periode))."</b></th>
					<th colspan='2' style='text-align:right;'><b>".rupiah($saldo_akhir)."</b></th>
				</tr>
		</table>";
		return $table;
	}
	
	function load_lap_masuk()
	{
		//$periode = $_POST['periode']; 
		
		$periode =$_GET['periode']; 
		
		$table=$this->get_table_laporan($periode,'masuk');
		
		echo $table;
	}
	
	function load_lap_keluar()
	{
		//$periode = $_POST['periode']; 
		
		$periode =$_GET['periode']; 
		
		$table=$this->get_table_laporan($periode,'keluar');
		
		echo $table;
	}
	
	function load_neraca()
	{
		$periode =$_GET['periode']; 
		$table=$this->get_table_neraca($periode);
		
		echo $table; 
	}
	
	function query_list_transaksi($periode,$mode='all')
	{
		$id_jenjang = $_GET['id_jenjang'];
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
						id_jns_pemasukan IN ($id_tahunan) 
						AND master_jenis_transaksi.id_sesi = $id_ta
						AND master_jenis_transaksi.id_jenjang = $id_jenjang
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
							AND master_jenis_transaksi.id_jenjang = $id_jenjang
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
							AND master_jenis_transaksi.id_jenjang = $id_jenjang
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
						tipe = $tipe AND id_jenjang = $id_jenjang AND del_stat = 0 AND tanggal >= '$periode-01 00:00:00' 
						AND tanggal <= '$akhir 23:59:59' 
					$ext_que
					";
			array_push($query,$que);
		}
		$query = implode(" UNION ALL ", $query);
		return $query;
	}
	
	function load_jurnal_all($print=0)
	{
		$periode = $_GET['periode'];
		$tgl_awal = tgl_indo($_GET['tgl_awal']);
		$tgl_akhir = tgl_indo($_GET['tgl_akhir']);
		
		$que = $this->query_list_transaksi($periode);
		$que = "SELECT
					tb_trans.*,
					kas_jenis_pemasukan.ref as ref_jns_pemasukan,
					nama_jns_pemasukan,
					kas_kategori_pemasukan.ref as ref_kat_pemasukan,
					nama_kat_pemasukan,
					kas_subkategori_pemasukan.ref as ref_subkat_pemasukan,
					nama_subkat_pemasukan,
					kas_jenis_pengeluaran.ref as ref_jns_pengeluaran,
					nama_jns_pengeluaran,
					kas_kategori_pengeluaran.ref as ref_kat_pengeluaran,
					nama_kat_pengeluaran,
					kas_subkategori_pengeluaran.ref as ref_subkat_pengeluaran,
					nama_subkat_pengeluaran,
					IF(tanggal >= '$tgl_awal 00:00:00' AND tanggal <= '$tgl_akhir 23:59:59',1,0) as show_data
				FROM
					(
						$que
					) AS tb_trans
					LEFT JOIN kas_jenis_pemasukan ON tb_trans.id_jns = kas_jenis_pemasukan.id_jns_pemasukan 
					AND tipe = 1
					LEFT JOIN kas_kategori_pemasukan ON tb_trans.id_kat = kas_kategori_pemasukan.id_kat_pemasukan 
					AND tipe = 1
					LEFT JOIN kas_subkategori_pemasukan ON tb_trans.id_subkat = kas_subkategori_pemasukan.id_subkat_pemasukan 
					AND tipe = 1
					LEFT JOIN kas_jenis_pengeluaran ON tb_trans.id_jns = kas_jenis_pengeluaran.id_jns_pengeluaran 
					AND tipe = 2
					LEFT JOIN kas_kategori_pengeluaran ON tb_trans.id_kat = kas_kategori_pengeluaran.id_kat_pengeluaran 
					AND tipe = 2
					LEFT JOIN kas_subkategori_pengeluaran ON tb_trans.id_subkat = kas_subkategori_pengeluaran.id_subkat_pengeluaran 
					AND tipe = 2 
				ORDER BY
					tanggal, id_transaksi";
		$res = db_query($que);
		$saldo_awal = $this->get_saldo_akhir($periode,'-1');
		$tbody='';
		$saldo = $saldo_awal;
		$saldo_awal_ext = 0;
		$saldo_arr=[];
		$jml_trans=0;
		$jml_trans_in=0;
		$jml_trans_out=0;
		$jml_nominal_in=0;
		$jml_nominal_out=0;
		while($dt=mysqli_fetch_array($res))
		{
			$md=['','pemasukan','pengeluaran'];
			$nm_jns=$dt['ref_jns_'.$md[$dt['tipe']]].' '.$dt['nama_jns_'.$md[$dt['tipe']]];
			$nm_kat=$dt['ref_kat_'.$md[$dt['tipe']]].' '.$dt['nama_kat_'.$md[$dt['tipe']]];
			$nm_subkat=$dt['ref_subkat_'.$md[$dt['tipe']]].' '.$dt['nama_subkat_'.$md[$dt['tipe']]];
			$jns_kat=$nm_jns;
			if(!is_null($nm_kat))
			{
				$jns_kat.="<br>  ".$nm_kat;
				//$jns_kat.="<br> > ".$nm_kat;
				if(!is_null($nm_subkat))
				{
					//$jns_kat.="<br> > ".$nm_subkat;
					$jns_kat.="<br> ".$nm_subkat;
				}
			}
			$nominal_in='';
			$nominal_out='';;
			if($dt['tipe']==1)
			{
				$nominal_in=rupiah($dt['nominal']);
				$saldo+=$dt['nominal'];
			}
			else
			{
				$nominal_out=rupiah($dt['nominal']);
				$saldo-=$dt['nominal'];
			}
			if($dt['show_data']==1)
			{
				$td_ext='';
				if($print==0)
				{
					$td_ext="<td class='cari-q' style='display:none;'>$dt[nominal] $saldo</td>";
				}
				$tbody.= "
					<tr class='cari-item'>
						<td class='cari-q'>$dt[tanggal]</td>
						<td class='cari-q'>$jns_kat</td>
						<td class='cari-q'>$dt[keterangan]</td>
						<td class='cari-q' style='text-align:right;color:green;'>$nominal_in</td>
						<td class='cari-q' style='text-align:right;color:red;'>$nominal_out</td>
						<td class='cari-q' style='text-align:right;font-weight:bold;'>".rupiah($saldo)."</td>
						$td_ext
					</tr>";
				$jml_trans+=1;
				if($dt['tipe']==1)
				{
					$jml_trans_in+=1;
					$jml_nominal_in+=$dt['nominal'];
				}
				else
				{
					$jml_trans_out+=1;
					$jml_nominal_out+=$dt['nominal'];
				}
				array_push($saldo_arr,$saldo);
			}
			else
			{
				if($dt['tipe']==1)
				{
					$saldo_awal_ext+=$dt['nominal'];
				}
				else
				{
					$saldo_awal_ext-=$dt['nominal'];
				}
			}
		}
		
		$border="";
		if($print==0)
		{
			$tbody.="
				<tr class='no_result'>
					<td colspan='6' style='text-align:center;'><i>Tidak Ada Transaksi</i></td>
				</tr>";
		}
		else
		{
			$border="border='1'";
		}
		
		$ket_saldo_awal = "BULAN ".strtoupper($this->bulan_tahun($periode));
		if($periode.'-01'!=$tgl_awal)
		{
			$split = explode('-',$tgl_awal);
			$tgl = $split[2];
			$ket_saldo_awal="TGL. ".$tgl.' '.strtoupper($this->bulan_tahun($periode));
		}
		
		$tbody = "<tr style='font-weight:bold;'>
					<td></td>
					<td></td>
					<td>SALDO AWAL $ket_saldo_awal</td>
					<td style='text-align:right;'></td>
					<td></td>
					<td style='text-align:right;'>".rupiah($saldo_awal+$saldo_awal_ext)."</td>
				</tr> $tbody";
		
		$table = "
			<table $border class='table table-striped  table-sm'>
				<thead>
					<tr>
						<th>Tanggal</th>
						<th>Akun</th>
						<th>Keterangan</th>
						<th>Masuk</th>
						<th>Keluar</th>
						<th>Saldo</th>
					</tr>
				</thead>
				<tbody>
					$tbody
				</tbody>
			</table>";
		
		if(count($saldo_arr)==0)
		{
			$saldo_arr=[0];
		}
		$table_summary="
			<table>
				<tr>
					<td>JUMLAH TRANSAKSI MASUK</td>
					<td>:</td>
					<td style='text-align:right;'>$jml_trans_in</td>
					<td style='text-align:right;padding-left:20pt;font-weight:bold;'>".rupiah($jml_nominal_in)."</td>
				</tr>
				<tr>
					<td>JUMLAH TRANSAKSI KELUAR</td>
					<td>:</td>
					<td style='text-align:right;'>$jml_trans_out</td>
					<td style='text-align:right;padding-left:20pt;font-weight:bold;'>".rupiah($jml_nominal_out)."</td>
				</tr>
				<tr>
					<td>SALDO TERTINGGI</td>
					<td>:</td>
					<td style='text-align:right;'></td>
					<td style='text-align:right;padding-left:20px;font-weight:bold;'>".rupiah(max($saldo_arr))."</td>
				</tr>
				<tr>
					<td>SALDO TERENDAH</td>
					<td>:</td>
					<td style='text-align:right;'></td>
					<td style='text-align:right;padding-left:20px;font-weight:bold;'>".rupiah(min($saldo_arr))."</td>
				</tr>
				<tr>
					<td>SALDO RATA-RATA</td>
					<td>:</td>
					<td style='text-align:right;'></td>
					<td style='text-align:right;padding-left:20px;font-weight:bold;'>".rupiah(array_sum($saldo_arr)/count($saldo_arr))."</td>
				</tr>
			</table>";
		
		$data =$_GET;
		$data['saldo_awal']=$saldo_awal+$saldo_awal_ext;
		$data['saldo_akhir']=$saldo;
		$data['jml_trans']=$jml_trans;
		$data['jml_trans_in']=$jml_trans_in;
		$data['jml_trans_out']=$jml_trans_out;
		$data['jml_nominal_in']=$jml_nominal_in;
		$data['jml_nominal_out']=$jml_nominal_out;
		$data['table_jurnal']=$table;
		$data['table_summary']=$table_summary;
		
		if($print==1)
		{
			return $data;
		}
		
		echo json_encode($data);
		
	}
	
	function load_jurnal_khusus($print=0)
	{
		$id_jenjang = $_GET['id_jenjang'];
		$periode = $_GET['periode'];
		$mode = $_GET['mode'];
		$data = $this->get_data_trans_ext($periode,$mode);
		
		if($mode=='masuk' || $mode =='in' || $mode =='all' )
		{
			$ket_lap = "Pemasukan";
			$dork = "D";
		}
		if($mode=='keluar' || $mode =='out' || $mode =='all')
		{
			$ket_lap = "Pengeluaran";
			$dork = "K";
		}
		
		$border='';
		if($print==1)
		{
			$border="border='1px'";
		}
		
		$table="
		<table width='100%'>
			<tr>
				<td colspan='5' style='font-size:12pt;text-align:center;font-weight:bold;'>LAPORAN JURNAL KHUSUS ".strtoupper($ket_lap)."</td>
			</tr>
			<tr>
				<td colspan='5' style='font-size:12pt;text-align:center;font-weight:bold;'>PERIODE BULAN ".strtoupper($this->bulan_tahun($periode))."</td>
			</tr>
		</table>
		";
		foreach($data as $jns)
		{
			$table .="<b>$jns[ref] $jns[nama_jns]</b>
			<br>
			";
			
			if(count($jns['data_trans'])>0)
			{
				$tbody='';
				$no=1;
				$saldo = 0;
				foreach($jns['data_trans'] as $trn)
				{
					$saldo+=$trn['nominal'];
					$tbody.="
						<tr>
							<td>".$no++."</td>
							<td>".$trn['keterangan']."</td>
							<td>".tgl_indo($trn['tanggal'])."</td>
							<td style='text-align:right;'>".rupiah($trn['nominal'])."</td>
							<td style='text-align:right;'>".rupiah($saldo)."</td>
						</tr>
					";
				}
				
				if($tbody=='')
				{
					$tbody.="
						<tr>
							<td>1</td>
							<td></td>
							<td></td>
							<td style='text-align:right;'>-</td>
							<td style='text-align:right;'>-</td>
						</tr>
						<tr>
							<td colspan='3' style='text-align:center;'><b>JUMLAH</b></td>
							<td style='text-align:right;'>-</td>
							<td style='text-align:right;'>-</td>
						</tr>
					";
				}
				else
				{
					$tbody.="
						<tr>
							<td colspan='3' style='text-align:center;'><b>JUMLAH</b></td>
							<td style='text-align:right;'><b>".rupiah($saldo)."</b></td>
							<td style='text-align:right;'><b>".rupiah($saldo)."</b></td>
						</tr>
					";
				}
				
				$table.="
				<table $border class='table table-bordered table-sm'>
					<thead>
						<tr>
							<th>No.</th>
							<th>Akun $ket_lap</th>
							<th>Tanggal</th>
							<th>$dork</th>
							<th>Saldo</th>
						</tr>
					</thead>
					<tbody>
						$tbody
					</tbody>
				</table>
				<br>
				";
			}
			
			foreach($jns['kategori'] as $kat)
			{
				$tbody='';
				$no=1;
				$saldo = 0;
				foreach($kat['data_trans'] as $trn)
				{
					$saldo+=$trn['nominal'];
					$tbody.="
						<tr>
							<td>".$no++."</td>
							<td>".$trn['keterangan']."</td>
							<td>".tgl_indo($trn['tanggal'])."</td>
							<td style='text-align:right;'>".rupiah($trn['nominal'])."</td>
							<td style='text-align:right;'>".rupiah($saldo)."</td>
						</tr>
					";
				}
				
				if($tbody=='')
				{
					$tbody.="
						<tr>
							<td>1</td>
							<td></td>
							<td></td>
							<td style='text-align:right;'>-</td>
							<td style='text-align:right;'>-</td>
						</tr>
						<tr>
							<td colspan='3' style='text-align:center;'><b>JUMLAH</b></td>
							<td style='text-align:right;'>-</td>
							<td style='text-align:right;'>-</td>
						</tr>
					";
				}
				else
				{
					$tbody.="
						<tr>
							<td colspan='3' style='text-align:center;'><b>JUMLAH</b></td>
							<td style='text-align:right;'><b>".rupiah($saldo)."</b></td>
							<td style='text-align:right;'><b>".rupiah($saldo)."</b></td>
						</tr>
					";
				}
				
				$table.="
				<b>$kat[ref] $kat[nama_kat]</b>
				<table $border class='table table-bordered table-sm'>
					<thead>
						<tr>
							<th>No.</th>
							<th>Akun $ket_lap</th>
							<th>Tanggal</th>
							<th>$dork</th>
							<th>Saldo</th>
						</tr>
					</thead>
					<tbody>
						$tbody
					</tbody>
				</table>
				<br>
				";
			}
		}
		
		echo $table;
	}
	
	function excel_jurnal()
	{
		$_POST = $_GET;
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=Jurnal_Kas_".$_GET['periode'].".xls");
		
		$data = $this->load_jurnal_all(1);
		echo"
		<table>
			<tr>
				<td colspan='6' style='text-align:center;'><h4>JURNAL TRANSAKSI KAS</h4></td>
			</tr>
			<tr>
				<td colspan='6' style='text-align:center;'><h4>".strtoupper(SCHOOL_NAME)."</h4></td>
			</tr>
			<tr>
				<td colspan='6' style='text-align:center;'><h4>PERIODE ".strtoupper($this->bulan_tahun($data['periode']))."</h4></td>
			</tr>
		</table>
		<br>
		";
		$tbl = $data['table_jurnal'];
		$tbl = str_replace('<br>','&#13;&#10;',$data['table_jurnal']);
		echo $tbl;
		echo '<br>';
		echo $data['table_summary'];
	}
	
	function excel_jurnal_khusus()
	{
		$_POST = $_GET;
		header("Content-type: application/vnd-ms-excel");
		header("Content-Disposition: attachment; filename=Jurnal_Kas_$_GET[mode]_".$_GET['periode'].".xls");
		
		$data = $this->load_jurnal_khusus(1);
		$tbl = $data['table_jurnal'];
		//$tbl = str_replace('<br>','&#13;&#10;',$data['table_jurnal']);
		echo $tbl;
	}
	
	function cetak_lap_masuk()
	{
		ob_start();
		$periode =$_GET['periode'];

		$text= $this->get_table_laporan($periode,'masuk',1);
		$data = db_get("SELECT data_cetak FROM kas_periode WHERE periode = '$periode'");
		$data = json_decode($data,true);
		$data = $data['neraca'];
		$text .= "
			<br>
			<br>
			<br>
			<br>
			<div style='font-size:10pt;'>
			<table  border='0px'>
				<tr>
					<td style='text-align:center;'>Mengetahui,</td>
					<td></td>
					<td style='text-align:center;'>$data[tempat], $data[tanggal]</td>
				</tr>
				<tr>
					<td style='text-align:center;'>$data[jabatan_2]</td>
					<td></td>
					<td style='text-align:center;'>$data[jabatan_1]</td>
				</tr>
				<tr>
					<td style='text-align:center;'></td>
					<td>
						<br>
						<br>
						<br>
						<br>
					</td>
					<td></td>
				</tr>
				<tr>
					<td style='text-align:center;'>$data[nama_2]</td>
					<td></td>
					<td style='text-align:center;'>$data[nama_1]</td>
				</tr>
				<tr>
					<td style='text-align:center;'>$data[nip_2]</td>
					<td></td>
					<td style='text-align:center;'>$data[nip_1]</td>
				</tr>
			</table>
			</div>
			";
		$text=str_replace("'",'"',$text);
		
		
            require_once ('app/lib/tcpdf/tcpdf.php');
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetFont('freeserif', '', '10pt', '', true);
            $pdf->SetMargins(10, 5, 10);
            $pdf->SetFooterMargin(0);
            $pdf->SetAutoPageBreak(FALSE, 10);
					$pdf->AddPage('P', array(215, 330));
					$pdf->setCellPaddings("50px", $top = '', $right = '', $bottom = '');
					$pdf->writeHTML($text);
				ob_end_clean();
				$pdf->Output("Laporan Pemasukan Kas $periode.pdf", 'I');
				ob_end_flush();
	}
	
	
	function cetak_lap_keluar()
	{
		ob_start();
		$periode =$_GET['periode'];

		$text= $this->get_table_laporan($periode,'keluar',1);
		$text=str_replace("'",'"',$text);
		//echo $text;die;
		
		
            require_once ('app/lib/tcpdf/tcpdf.php');
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetFont('freeserif', '', '9pt', '', true);
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetFooterMargin(0);
            $pdf->SetAutoPageBreak(FALSE, 10);
					$pdf->AddPage('P', array(215, 330));
					$pdf->setCellPaddings("100px", $top = '', $right = '', $bottom = '');
					$pdf->writeHTML($text);
				ob_end_clean();
				$pdf->Output("Laporan Pengeluaran Kas $periode.pdf", 'I');
				ob_end_flush();
	}
	
	function cetak_neraca()
	{
		ob_start();
		$periode =$_GET['periode'];

		$text= $this->get_table_neraca($periode,1);
		
		$data = db_get("SELECT data_cetak FROM kas_periode WHERE periode = '$periode'");
		$data = json_decode($data,true);
		$data = $data['neraca'];
		$text .= "
			<br>
			<br>
			<br>
			<br>
			<div style='font-size:10pt;'>
			<table  border='0px'>
				<tr>
					<td style='text-align:center;'>Mengetahui,</td>
					<td></td>
					<td style='text-align:center;'>$data[tempat], ".ucwords($data['tanggal'])."</td>
				</tr>
				<tr>
					<td style='text-align:center;'>$data[jabatan_2]</td>
					<td></td>
					<td style='text-align:center;'>$data[jabatan_1]</td>
				</tr>
				<tr>
					<td style='text-align:center;'></td>
					<td>
						<br>
						<br>
						<br>
						<br>
					</td>
					<td></td>
				</tr>
				<tr>
					<td style='text-align:center;'>$data[nama_2]</td>
					<td></td>
					<td style='text-align:center;'>$data[nama_1]</td>
				</tr>
				<tr>
					<td style='text-align:center;'>$data[nip_2]</td>
					<td></td>
					<td style='text-align:center;'>$data[nip_1]</td>
				</tr>
			</table>
			</div>
			";
		$text=str_replace("'",'"',$text);
		
            require_once ('app/lib/tcpdf/tcpdf.php');
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetFont('freeserif', '', '10pt', '', true);
            $pdf->SetMargins(10, 10, 10);
            $pdf->SetFooterMargin(0);
            $pdf->SetAutoPageBreak(FALSE, 10);
					$pdf->AddPage('P', array(215, 330));
					$pdf->setCellPaddings("100px", $top = '', $right = '', $bottom = '');
					$pdf->writeHTML($text);
				ob_end_clean();
				$pdf->Output("Laporan Pengeluaran Kas $periode.pdf", 'I');
				ob_end_flush();
	}
	
	function test($val)
	{
		echo 'sds';
		return $val;
	}
	
	function getLastDayOfMonth($yearMonth) {
		$lastDayOfMonth = date('Y-m-t', strtotime("$yearMonth-01"));
		return $lastDayOfMonth;
	}
	
	
	function datetime_db($inputDate) {
		$dateTime = DateTime::createFromFormat('d-m-Y H:i', $inputDate);

		if ($dateTime instanceof DateTime) {
			return $dateTime->format('Y-m-d H:i');
		} else {
			$dateTime = DateTime::createFromFormat('Y-m-d H:i', $inputDate);

			if ($dateTime instanceof DateTime) {
				return $dateTime->format('d-m-Y H:i');
			} else {
				return "Invalid date format";
			}
		}
	}
	
}

?>