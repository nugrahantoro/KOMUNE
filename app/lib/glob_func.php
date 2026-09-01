<?php

function get($name) {
    return isset($_GET[$name]) ? $_GET[$name] : null;
}

function post($name) {
    return isset($_POST[$name]) ? $_POST[$name] : null;
}

function rupiah($nom)
{
	return number_format($nom,0,',','.');
}

function tgl_indo($tgl,$show_time=false)
{
	$tgl_full=explode(' ',$tgl);
	$tgl = explode('-',$tgl_full[0]);
	$tgl=$tgl[2].'-'.$tgl[1].'-'.$tgl[0];
	if($show_time==true)
	{
		$tgl.=" ".$tgl_full[1];
	}
	return $tgl;
}

//Laki-laki -> L, Perempuan -> P
function toLP($gender)
{
	if(stripos($gender,"laki") !== false)
	{
		$gender = "L";
	}
	if(stripos($gender,"perempuan") !== false)
	{
		$gender = "P";
	}
	return $gender;
}

function list_bulan_ta($ta)
{
	$dt = db_assoc("SELECT * FROM data_tahunajar WHERE id_tahunajar = $ta");
	$tgl = date_create($dt['tgl_mulai']);
	$namaBulan = array('',"Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
	$data=[];
	for($i=1;$i<=12;$i++)
	{
		$tg['nilai'] = date_format($tgl,"Y-m");
		$tg['value'] = date_format($tgl,"Y-m");
		$tg['val'] = date_format($tgl,"Y-m");
		$bln = $namaBulan[date_format($tgl,"n")];
		$tg['bulan']= $bln.' '.date_format($tgl,"Y")."<br>";
		$tg['month']= $bln.' '.date_format($tgl,"Y")."<br>";
		$tg['bln']= $bln.' '.date_format($tgl,"Y")."<br>";
		array_push($data,$tg);
		$tgl->modify('+1 month');
	}
	//echo json_encode($data);
	return $data;
}


?>