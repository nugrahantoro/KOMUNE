<style>
	.fr {
		float: right;
	}
	
	.tr {
		text-align: right;
	}

	.frozeCol thead th {
		background: white;
		top: 0px;
		text-align: center;
		z-index: 9;
	}

	#div_list_kelas thead th {
		top: -20px;
	}

	.nav-tabs-custom .nav-item .nav-link::after {
		height: 2px;
	}

	

	#prs_photo_preview {
		width: 150px;
		height: 150px;
		display: inline-block;
		background-size: contain;
		background-position: center;
		background-repeat: no-repeat;
	}

	#tsiswa_ortu .card-body {
		padding: 0.5rem 1rem;
	}

	.col-form-label.required:after {
		content: "*";
		color: red;
	}
	
	.table .act_item {
		opacity: 0.0;
		transition: opacity 0.5s ease;
		white-space: nowrap;
	}
	
	.table tr:hover .act_item {
		 opacity: 1;
	}
	
	#modal_add_trans table input[type="checkbox"] {
		scale: 1.3;
	}
	
	.filter_tanggal input 
	{
		background-color:white !important;
	}
	
	#div_jurnal {
		border: 1px solid lightgray;
		padding: 1rem;
		padding-top: 0;
		max-height: 400px;
	}
	
	#div_jurnal_pemasukan th,	#div_jurnal_pengeluaran th {
		text-align:center;
	}
	
</style>

<?php
	function make_list_periode()
	{
		// Array of Indonesian month names
		$month_names_id = [
			1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
			7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
		];

		// Set the starting month and year
		$starting_month = 7; // July
		$starting_year = 2023;

		// Get the current month and year
		$current_month = date('n');
		$current_year = date('Y');

		$opts='';
		// Loop through the months and years
		for ($year = $current_year; $year >= $starting_year; $year--) {
			$start = ($year === $starting_year) ? $starting_month : 1;
			$end = ($year === $current_year) ? $current_month : 12;

			for ($month = $end; $month >= $start; $month--) {
				$month_name = $month_names_id[$month];
				$formatted_month = sprintf("%02d", $month);
				$formatted_year = $year;
				
				$formatted_date = "$formatted_year-$formatted_month";
				$display_date = "<option value='$formatted_date'>$month_name $formatted_year</option>";
				$opts.= $display_date . "";
			}
		}
		
		return $opts;
	}
?>

<div class="main-content">

	<div class="page-content">
		<div class="container-fluid">

			<!-- start page title -->
			<div class="row">
				<div class="col-12">
					<div class="page-title-box d-sm-flex align-items-center justify-content-between">
						<h4 class="mb-sm-0 font-size-18">Laporan Kas Periode 
							<select id='periode_kas' class='form-select' style='display:inline-block;width:auto' onchange="change_periode();">
			                               <?php
												 echo make_list_periode();
										   ?>
							</select>
							
			                             Jenjang  
			                            <select id='id_jenjang'  class='form-select' style='display:inline-block;width:auto' onchange="change_periode();">
			                            <?php
			                            	$que = "SELECT
														* 
													FROM
														data_jenjang";
											$res = db_query($que);
											while($dt = mysqli_fetch_array($res))
											{
												$selected = '';
												if($dt['cur_ta']==1)
												{
													$selected = "selected";
												}
												echo "<option value = '$dt[id_jenjang]' $selected>$dt[nama_jenjang]</option>";
											}
			                            ?>
			                            </select>
						</h4>
						<div class="page-title-right">
							<ol class="breadcrumb m-0">
								<li class="breadcrumb-item"><a href="javascript: void(0);">TU</a></li>
								<li class="breadcrumb-item active">Konfigurasi Pembayaran</li>
							</ol>
						</div>

					</div>
				</div>
			</div>
			<!-- end page title -->

			<div class="row">
				<div class="col-md-12" id='col-userkeu'>

					<!-- Nav tabs -->
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item" onclic='tab_siswa_kelas();'>
							<a class="nav-link active" data-bs-toggle="tab" href="#tab_lap_masuk" role="tab" aria-selected="false">
								<span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
								<span class="d-none d-sm-block">Laporan Pemasukan</span>
							</a> 
						</li>
						<li class="nav-item" onclic='tab_mst_transaksi();'>
							<a class="nav-link " data-bs-toggle="tab" href="#tab_lap_keluar" role="tab" aria-selected="true">
								<span class="d-block d-sm-none"><i class="far fa-user"></i></span>
								<span class="d-none d-sm-block">Laporan Pengeluaran</span>
							</a>
						</li>
						<li class="nav-item" onclic="tab_konfig_bayar();">
							<a class="nav-link" data-bs-toggle="tab" href="#tab_neraca" role="tab" aria-selected="false">
								<span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
								<span class="d-none d-sm-block">Neraca</span>
							</a>
						</li>
						<li class="nav-item" onclic="tab_konfig_bayar();">
							<a class="nav-link" data-bs-toggle="tab" href="#tab_jurnal" role="tab" aria-selected="false">
								<span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
								<span class="d-none d-sm-block">Jurnal</span>
							</a>
						</li>
						<li class="nav-item" onclic="tab_konfig_bayar();">
							<a class="nav-link" data-bs-toggle="tab" href="#tab_jurnal_masuk" role="tab" aria-selected="false">
								<span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
								<span class="d-none d-sm-block">Jurnal Khusus Pemasukan</span>
							</a>
						</li>
						<li class="nav-item" onclic="tab_konfig_bayar();">
							<a class="nav-link" data-bs-toggle="tab" href="#tab_jurnal_keluar" role="tab" aria-selected="false">
								<span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
								<span class="d-none d-sm-block">Jurnal Khusus Pengeluaran</span>
							</a>
						</li>
						<!-- <li class="nav-item">
							<a class="nav-link" data-bs-toggle="tab" href="#settings" role="tab" aria-selected="false">
								<span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
								<span class="d-none d-sm-block">Settings</span>
							</a>
						</li> -->
					</ul>

					<!-- Tab panes -->
					
					<div class="tab-content p-3 text-muted" style="border: 1px var(--bs-gray-400) solid;border-top: unset;border-radius: 0px 0px 5px 5px;">
						<div class="tab-pane active fade show" id="tab_lap_masuk" role="tabpanel">
								<div style="position: absolute;right: 5%;z-index: 9;">
									<button class='btn btn-sm btn-success' onclick='cetak_lap_masuk();'>
										<i class=' bx bx-printer '></i> Cetak
									</button>
								</div>
								<div class='row'>
									<div class='col-11'>
										<div id='div_lap_masuk'></div>
									</div>
								</div>
						</div>
						<div class="tab-pane fade" id="tab_lap_keluar" role="tabpanel"> 
								<div style="position: absolute;right: 5%;z-index: 9;">
									<button class='btn btn-sm btn-success' onclick='cetak_lap_keluar();'>
										<i class=' bx bx-printer '></i> Cetak
									</button>
								</div>
								<div class='row'>
									<div class='col-11'>
										<div id='div_lap_keluar'></div>
									</div>
								</div>
						</div>
						<div class="tab-pane fade" id="tab_neraca" role="tabpanel">
								<div style="position: absolute;right: 5%;z-index: 9;">
									<button class='btn btn-sm btn-success' onclick='cetak_neraca();'>
										<i class=' bx bx-printer '></i> Cetak
									</button>
								</div>
								<div class='row'>
									<div class='col-11'>
										<div id='div_neraca'></div>
									</div>
								</div>
						</div>
						
						<div class="tab-pane fade" id="tab_jurnal_masuk" role="tabpanel">
								<div style="position: absolute;right: 5%;z-index: 9;">
									<button class='btn btn-sm btn-success' onclick="excel_jurnal_khusus('masuk');">
										<i class=' bx bx-download '></i> .xls
									</button>
								</div>
								<div class='row'>
									<div class='col-11'>
										<div id='div_jurnal_pemasukan'></div>
									</div>
								</div>
						</div>
						
						<div class="tab-pane fade" id="tab_jurnal_keluar" role="tabpanel">
								<div style="position: absolute;right: 5%;z-index: 9;">
									<button class='btn btn-sm btn-success' onclick="excel_jurnal_khusus('keluar');">
										<i class=' bx bx-download '></i> .xls
									</button>
								</div>
								<div class='row'>
									<div class='col-11'>
										<div id='div_jurnal_pengeluaran'></div>
									</div>
								</div>
						</div>
						<div class="tab-pane fade" id="tab_jurnal" role="tabpanel">
										 <div class="row mb-2">
														<label for="" class="col-sm-1 control-label p-1  text-end">Tanggal</label>
														<div class="col-sm-3 filter_tanggal" >
															<div class="input-group input-group-sm">
															  <input type="text" id='jrn_tgl_awal' class="form-control form-control-sm text-center" onchange='load_tab_jurnal_all();'>
															  <span class="input-group-text">s/d</span>
															  <input type="text" id='jrn_tgl_akhir' class="form-control form-control-sm text-center" onchange='load_tab_jurnal_all();'>
															</div>
														</div>
														<div class="col-sm-2 p-1">
															<span><span id='jml_jrn_trans'>0</span> Transaksi <span>
															<!--(<span id='jml_jrn_trans_rinc'></span>)-->
														</div>
														
														<div class="col-sm-3 p-1 text-end">
															<i id='spn_cari_jurnal'><span id='jml_cari_jurnal'>0</span> Data Ditemukan </i>
															<!--(<span id='jml_jrn_trans_rinc'></span>)-->
														</div>
														<div class="col-sm-3">
															<div class="input-group input-group-sm">
																<input type="text" id='cari_jurnal' class="form-control form-control-sm" onkeyup='cari_jurnal();' placeholder="Cari ...">
																<button class="btn btn-xs btn-success" onclick="excel_jurnal();" title="Download Data Excel (*.xls)"> 
																		<i class="bx bx-download"></i> .xls
																</button>
															</div>
														</div>
													</div>
								<div class='row'>
									<div class='col-12'>
										<div id='div_jurnal' class='frozeCol'></div>
										<div id='div_jurnal_summary' class='mt-3'></div>
									</div>
								</div>
						</div>
					</div>
					

				</div><!-- end col -->

			</div>

		</div>
		<!-- container-fluid -->
	</div>
	<!-- End Page-content -->
</div>


<div id='modal_kb_lap_edit' class="modal fade" tabindex="-1" data-bs-focus="false">
  <div class="modal-dialog  modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> Data Report Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-6 pr-5 pl-5">
			<div id='div_lap_edit' style="min-height: 65vh;"></div>
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script src="kasv2/js/j_lap_kas.js?v=<?php echo uniqid() ?>"></script>