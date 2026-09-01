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
	
	.frozeCol tbody tr:hover {
		background-color: ghostwhite;
	}

	#div_list_kelas thead th {
		top: -20px;
	}

	.nav-tabs-custom .nav-item .nav-link::after {
		height: 2px;
	}

	.kelas-item:hover,
	.siswa-item:hover,
	.trans-item:hover,
	#tbl_mst_transaksi tbody tr:hover,
	#modal_add_trans tbody tr:hover,
	#modal_master_kategori tbody tr:hover
	{
		background-color: ghostwhite;
		cursor: pointer;
	}

	.kelas-active {
		background-color: #8184bf !important;
		color: white;
		font-weight: bold;
	}

	#id_kelas {
		width: unset;
		margin-bottom: unset !important;
		display: inline;
		font-size: initial;
		font-weight: bold;
		padding: 1pt 6pt;
		padding-right: 22pt;
		max-width: 90pt;
	}

	.list_siswa {
		max-height: 400px;
		overflow-y: auto;
	}

	#import_siswa_file_avail input[type=checkbox] {
		scale: 1.3;
	}

	#import_siswa_file_avail input[type=checkbox]:hover  {
		cursor: pointer;
	}

	#import_siswa_file_avail tr:hover {
		background-color: ghostwhite;
	}


	#modalSiswa .nav-tabs .nav-link.active .d-none {
		display: inline !important;
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
	
	#div_cari_siswa {
		position: absolute;
		z-index: 999;
		background: white;
		border: lightgray solid 1px;
		padding: 15pt 20pt;
		left: 50%;
		border-radius: 3pt;
		transform: translateX(-50%);
		font-size: initial;
		font-weight: initial;
		text-align: left;
		white-space: nowrap;
	}
	
	#div_cari_siswa thead tr th:last-child {
	  min-width:40pt;
	}
	
	.card-header {
		padding-bottom: 0.5rem;
		padding-top: 0.5rem;
	}
	
	#tabel_tagihan_empty {
		padding-top: 5rem;
		height: 15rem;
		text-align: center;
		font-size: x-large;
		color: darkgray;
	}
	
	.text-r
	{
		text-align:right;
	}
	
	#dt_nama_siswa::before {
	  content: "\00a0";
	  visibility: hidden;
	}
	
</style>
<div class="main-content">

	<div class="page-content">
		<div class="container-fluid">

			<!-- start page title -->
			<div class="row">
				<div class="col-12">
					<div class="page-title-box d-sm-flex align-items-center justify-content-between">
						<h4 class="mb-sm-0 font-size-18">Dompet Digital Warga
							Periode
							<select id='id_ta' class='form-select' style='display:inline-block;width:auto'  onchange="change_ta_jenjang();">
								<?php
								$res = db_query(" SELECT *,
																		IF(DATE(NOW()) BETWEEN tgl_mulai AND tgl_selesai,1,0) as cur
																	FROM data_tahunajar ORDER BY tgl_mulai DESC");
								while ($dt = mysqli_fetch_array($res)) {
									$selected = '';
									if ($dt['cur'] == 1) {
										$selected = 'selected';
									}
									echo "<option $selected value = '$dt[id_tahunajar]'>$dt[nama_tahunajar]</option>";
								}
								?>
							</select>
							Wilayah
							<select id='id_jenjang' class='form-select' style='display:inline-block;width:auto' onchange="change_ta_jenjang();">
								<?php
								$sesi = $_SESSION['id'];
								$res = db_query("SELECT data_user_jenjang.id_user,data_user_jenjang.id_jenjang,data_jenjang.nama_jenjang FROM data_user_jenjang INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_user_jenjang.id_jenjang WHERE data_user_jenjang.id_user='$sesi'");
								while ($dt = mysqli_fetch_array($res)) {
									echo "<option value = '$dt[id_jenjang]'>$dt[nama_jenjang]</option>";
								}
								?>
							</select>
							<div style=" display:inline-block;text-align:center;position:relative;">
								<input type="text" class="form-control" id="txt_cari_siswa" onkeyup='cari_siswa();' style='width:unset;display:inline;' placeholder="Nama Warga">
								<div id='div_cari_siswa' class='' style="display:none;">
									<button type="button" class="btn btn-xs btn-outline-dark waves-effect waves-light" style="position: absolute;z-index: 99;right: 30pt;" onclick='hide_list_siswa_cari();'>X</button>
									<div id='list_siswa_cari' class = 'frozeCol' style="max-height:300px;"></div>
								</div>
							</div>
						</h4>
						
						<div class="page-title-right">
							<ol class="breadcrumb m-0">
								<li class="breadcrumb-item"><a href="javascript: void(0);">Pembayaran Digital</a></li>
								<li class="breadcrumb-item active">Dompet Digital Warga</li>
							</ol>
						</div>

					</div>
				</div>
			</div>
			<!-- end page title -->
			
			<div class='row p-3 pt-0'>
				<div class="col-md-12" >
					<button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick='modal_uang_saku();'>
                         <i class="bx bxs-wallet-alt font-size-16 align-middle me-2"></i> Isi Saldo
                    </button>
				</div>
			</div>
			
			<div class="row">
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Data Warga</h4>
                                                    <p class="card-title-desc">Saldo Dompet Digital : <b class='saku_saldo'>0</b></p>
                                    </div><!-- end card header -->

                                    <div class="card-body" id='card_siswa'>
                                        <div>
											<div class="row mb-2">
												<label for="frm_id_kelas" class="col-sm-4 col-form-label">ID Warga</label>
												<div class="col">
													<input type="text" class="form-control" id="dt_nis_id" readonly>
												</div>
											</div>
											<div class="row mb-2">
												<label for="frm_id_kelas" class="col-sm-4 col-form-label">Nama Warga</label>
												<div class="col">
													<div id='dt_nama_siswa' class='nama_siswa form-control '  readonly></div>
												</div>
											</div>
											<div class="row mb-2">
												<label for="frm_id_kelas" class="col-sm-4 col-form-label">Tanggal Lahir</label>
												<div class="col">
													<input type="text" class="form-control" id="dt_tgl_lahir" readonly>
												</div>
											</div>
											<div class="row mb-2">
												<label for="frm_id_kelas" class="col-sm-4 col-form-label">Wilayah</label>
												<div class="col">
													<input type="text" class="form-control " id="dt_kelas" readonly>
												</div>
											</div>
										</div>
                                    </div><!-- end card-body -->
                                </div><!-- end card -->
                            </div><!-- end col -->

                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-header">
										<div class='row'>
											<div class='col'>
												<h4 class="card-title">Riwayat Transaksi</h4>
												<div id='blink_tagihan'></div>
											</div>
											<div class='col-5'>
												<div class='row'>
													<label for="frm_id_kelas" class="col-sm-5 col-form-label">Saldo Dompet Digital</label>
													<div class="col">
														<input type="text" class="form-control text-r" id="total_tagihan" readonly="">
													</div>
												</div>
											</div>
										</div>
                                    </div><!-- end card header -->

                                    <div class="card-body frozeCol pt-0" style="max-height:500px;">
                                        <div id='tabel_tagihan'>
                                        </div>
										<div id='tabel_tagihan_empty' onclick='show_list_siswa_cari();'>Pilih Data Warga</div>
                                    </div><!-- end card-body -->
                                </div><!-- end card -->
                            </div><!-- end col -->
                        </div>

		</div>
		<!-- container-fluid -->
	</div>
	<!-- End Page-content -->
</div>

<div id='modal_uang_saku' class="modal fade" tabindex="-1" data-bs-focus="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-bottom py-3">
        <h5 class="modal-title font-size-16 fw-semibold text-dark"> <i class="bx bxs-wallet-alt text-danger me-1"></i> Isi Saldo Dompet Digital: <span class='nama_siswa fw-bold text-danger'></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="mb-3">
          <label class="form-label font-size-13 text-muted">Nominal Top Up (Rp) <span class="text-danger">*</span></label>
          <input id='nominal_saku_topup' class="form-control form-control-lg number" type="text" placeholder="Contoh: 50000" required>
        </div>
        <div class="mb-3">
          <label class="form-label font-size-13 text-muted">Keterangan Transaksi</label>
          <input id='keterangan_saku_topup' class="form-control" type="text" value="Isi Saldo Dompet Digital" placeholder="Keterangan pengisian saldo">
        </div>
      </div>
	  <div class="modal-footer border-top py-2 px-3">
        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary btn-sm px-4" onclick='saku_topup();'>Simpan & Isi Saldo</button>
      </div>
    </div>
  </div>
</div>

<script src="konfig_tu/js/j_transaksi_emoney.js?v=<?php echo uniqid() ?>"></script>