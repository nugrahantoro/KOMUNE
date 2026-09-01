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
	
	/* Style the switch */
	  .toggle-switch {
	    position: relative;
	    display: inline-block;
	    width: 60px;
	    height: 34px;
	  }

	  /* Hide the default HTML checkbox */
	  .toggle-switch input {
	    opacity: 0;
	    width: 0;
	    height: 0;
	  }

	  /* The slider */
	  .toggle-switch .slider {
	    position: absolute;
	    cursor: pointer;
	    top: 0;
	    left: 0;
	    right: 0;
	    bottom: 0;
	    background-color: #ccc;
	    -webkit-transition: .4s;
	    transition: .4s;
	    border-radius: 34px;
	  }

	  /* Rounded sliders */
	  .toggle-switch .slider:before {
	    position: absolute;
	    content: "";
	    height: 26px;
	    width: 26px;
	    left: 4px;
	    bottom: 4px;
	    background-color: white;
	    -webkit-transition: .4s;
	    transition: .4s;
	    border-radius: 50%;
	  }

	  /* Move the slider when toggled */
	  .toggle-switch input:checked + .slider {
	    background-color: #2196F3;
	  }

	  /* Move the circle when toggled */
	  .toggle-switch input:checked + .slider:before {
	    -webkit-transform: translateX(26px);
	    -ms-transform: translateX(26px);
	    transform: translateX(26px);
	  }

	  /* Style the text */
	  .toggle-switch .slider-text {
	    color: white;
	    font-family: Arial, sans-serif;
	    position: absolute;
	    top: 50%;
	    left: 50%;
	    transform: translate(-50%, -50%);
	    z-index: 1;
  }
</style>
<div class="main-content">

	<div class="page-content">
		<div class="container-fluid">

			<!-- start page title -->
			<div class="row">
				<div class="col-12">
					<div class="page-title-box d-sm-flex align-items-center justify-content-between">
						<h4 class="mb-sm-0 font-size-18">Pembayaran Warga
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
								<input type="text" class="form-control" id="txt_cari_siswa" onkeyup='cari_siswa();' style='width:unset;display:inline;' placeholder="Cari Nama Warga">
								<div id='div_cari_siswa' class='' style="display:none;">
									<button type="button" class="btn btn-xs btn-outline-dark waves-effect waves-light" style="position: absolute;z-index: 99;right: 30pt;" onclick='hide_list_siswa_cari();'>X</button>
									<div id='list_siswa_cari' class = 'frozeCol' style="max-height:300px;"></div>
								</div>
							</div>
						</h4>
						
						<div class="page-title-right">
							<ol class="breadcrumb m-0">
								<li class="breadcrumb-item"><a href="javascript: void(0);">Pembayaran Digital</a></li>
								<li class="breadcrumb-item active">Pembayaran Warga</li>
							</ol>
						</div>

					</div>
				</div>
			</div>
			<!-- end page title -->
			
			<div class='row p-3 pt-0'>
				<div class="col-md-12" >
					<button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick='simpan_transaksi()'>
                         <i class="bx bx-save font-size-16 align-middle me-2"></i> Simpan
                    </button>
					<button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick='cetak_first();'>
                         <i class="bx bx-printer font-size-16 align-middle me-2"></i> Cetak Bukti
                    </button>
					<button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick='cetak_nota();'>
                         <i class="bx bx-printer font-size-16 align-middle me-2"></i> Cetak Transaksi
                    </button>
					<button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick='cetak_again();'>
                         <i class="bx bx-printer font-size-16 align-middle me-2"></i> Cetak Ulang
                    </button>
					<button type="button" class="btn btn-sm btn-primary waves-effect waves-light"  onclick='modal_verifikasi();'>
                         <i class="bx bx-check font-size-16 align-middle me-2"></i> Riwayat Pembayaran
                    </button>
					<button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick='modal_add_trans();'>
                         <i class="bx bx-receipt font-size-16 align-middle me-2"></i> Penyesuaian Iuran
                    </button>
					<button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick='modal_uang_saku();'>
                         <i class="bx bxs-wallet-alt font-size-16 align-middle me-2"></i> Dompet Digital
                    </button>
					<!-- <button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick="modal_tabungan()">
                         <i class="bx bx-money font-size-16 align-middle me-2"></i> Tabungan
                    </button> -->
					<button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick='rincian_tagihan();'>
                         <i class="bx bxs-file font-size-16 align-middle me-2"></i> Rincian Tagihan
                    </button>
                    <!-- <button type="button" class="btn btn-sm btn-primary waves-effect waves-light" onclick='modal_emoney();'>
                         <i class="bx bxs-wallet-alt font-size-16 align-middle me-2"></i> E-Money
                    </button> -->
                    <div style="float: right;" id="toggle"></div>
				</div>
			</div>
			
			<div class="row">
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Data Warga</h4>
                                                    <p class="card-title-desc">Dompet Digital : <b class='saku_saldo'>0</b></p>
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
												<h4 class="card-title">Data Iuran</h4>
												<div id='blink_tagihan'></div>
											</div>
											<div class='col-5'>
												<div class='row'>
													<label for="frm_id_kelas" class="col-sm-5 col-form-label">Total Iuran</label>
													<div class="col">
														<input type="text" class="form-control text-r" id="total_tagihan" readonly="">
													</div>
												</div>
											</div>
										</div>
                                    </div><!-- end card header -->

                                    <div class="card-body frozeCol pt-0">
                                        <div id='tabel_tagihan'>
                                        </div>
										<div id='tabel_tagihan_empty' onclick='show_list_siswa_cari();'>Pilih Data Warga</div>
                                    </div><!-- end card-body -->
                                </div><!-- end card -->
                            </div><!-- end col -->
                        </div>
						
			<div class='row'>
				<div class='col-12'>
					<div id='transaksi_sementara'></div>
				</div>
			</div>

		</div>
		<!-- container-fluid -->
	</div>
	<!-- End Page-content -->
</div>

<div class="modal fade" id="modal_edit_trans" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-edit "></i> Edit Nominal Ketetapan</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4">
				<form>
					<div class="row mb-3">
						<label for="frm_id_kelas" class="col-sm-4 col-form-label">Nama Iuran</label>
						<div class="col">
							<input type="text" class="form-control" id="edt_nama_trans" readonly>
						</div>
					</div>
					<div class="row mb-3">
						<label for="frm_id_kelas" class="col-sm-4 col-form-label">Nominal Iuran</label>
						<div class="col">
							<input type="text" class="form-control" id="edt_nominal" readonly>
						</div>
					</div>
					<div class="row mb-3">
						<label for="frm_id_kelas" class="col-sm-4 col-form-label">Terbayarkan</label>
						<div class="col">
							<input type="text" class="form-control" id="edt_terbayarkan" readonly>
						</div>
					</div>
					<div class="row mb-3">
						<label for="frm_id_kelas" class="col-sm-4 col-form-label">Sisa Tagihan</label>
						<div class="col">
							<input type="text" class="form-control" id="edt_kekurangan" readonly>
						</div>
					</div>
					<div class="row mb-0">
						<label for="frm_id_kelas" class="col-sm-4 col-form-label">Nominal Baru</label>
						<div class="col">
							<input type="text" class="form-control number" id="edt_nominal_baru">
							<span style="color: red;font-style: italic;font-size: small;">
								<b>Perhatian :</b> Edit nominal ketetapan tidak boleh kurang dari <b>TERBAYARKAN</b>.
								Kalau kurang dari nominal itu, anda harus menghapus salah satu transaksi sebelumnya
							</span>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btn_save_kelas" onclick='edit_trans_save();'><i class='bx bx-save'></i> Simpan</button>
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_bayar_trans" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-money "></i> Input Nominal Pembayaran</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4">
				<form>
					<div class="row mb-2">
						<label for="frm_id_kelas" class="col-sm-4 col-form-label">Nama Iuran</label>
						<div class="col">
							<input type="text" class="form-control" id="byr_nama_trans" readonly>
						</div>
					</div>
					<div class="row mb-2">
						<label for="frm_id_kelas" class="col-sm-4 col-form-label">Nominal Iuran</label>
						<div class="col">
							<input type="text" class="form-control" id="byr_nominal" readonly>
						</div>
					</div>
					<div class="row mb-2">
						<label for="frm_id_kelas" class="col-sm-4 col-form-label">Terbayarkan</label>
						<div class="col">
							<input type="text" class="form-control" id="byr_terbayarkan" readonly>
						</div>
					</div>
					<div class="row mb-2">
						<label for="frm_id_kelas" class="col-sm-4 col-form-label">Kekurangan</label>
						<div class="col">
							<input type="text" class="form-control" id="byr_kekurangan" readonly>
						</div>
					</div>
					<div class="row mb-2">
						<label for="frm_id_kelas" class="col-sm-4 col-form-label">Metode Bayar</label>
						<div class="col">
							<select onchange="valid_nominal_bayar(1);" class = "form-select " id="byr_metode">
								<option value="teller">Tunai</option>
								<option value="Uang Saku">Dompet Digital</option>
								<option value="Ikhlas">Seikhlasnya Tunai</option>
								<option value="Ikhlas Saku">Seikhlasnya Dompet Digital</option>
								<option value="Bank BRI">Bank BRI</option>
								<option value="Bank BNI">Bank BNI</option>
								<option value="Bank Mandiri">Bank Mandiri</option>
								<option value="Bank BCA">Bank BCA</option>
								<option value="kartu">Kartu</option> -->
							</select>
						</div>
					</div>
					<div class="row mb-2">
						<label for="frm_id_kelas" class="col-sm-4 col-form-label">Nominal Bayar</label>
						<div class="col">
							<input type="text" class="form-control number" onkeyup='valid_nominal_bayar();' id="byr_nominal_bayar">
						</div>
					</div>
					<div class="row mb-0">
						<label for="frm_id_kelas" class="col-sm-4 col-form-label">Donasi</label>
						<div class="col">
							<input type="text" class="form-control number" id="byr_infaq">
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btn_save_kelas" onclick='bayar_trans();'><i class='bx bx-save'></i> Simpan</button>
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade " id="modal_verifikasi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-money "></i> Riwayat Pembayaran Warga Periode <span id='ta_verifikasi'></span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4 ">
				<div id='data_verifikasi'>
					
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<div id='modal_add_trans' class="modal fade" tabindex="-1" data-bs-focus="false">
  <div class="modal-dialog modal-lg"> 
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> <i class="bx bxs-file "></i>Tambah Ketetapan Pembayaran </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-6 pr-5 pl-5">
		<div class='row'>
			<div class='col  ps-3'>
				<div class='row'>
					<div class='col-auto'>
						<strong>Data Jenis Iuran</strong>
					</div>
					<div class='col'>
						<input class="form-control form-control-sm mb-3" type="text" id="kb_cari_jenis_trans" placeholder="Cari..." onkeyup='kb_cari_jenis_trans();'>
					</div>
				</div>
				<div id='kb_add_trans_list' class='frozeCol' style='max-height:350px;'></div>
				<div class='mt-3 row'>
					<div class='col-auto'>
						Tampilkan
						<select class="form-select form-select-sm" id="kb_jenis_trans_tampil_chk" onchange="kb_cari_jenis_trans();" style="display:inline;width:unset;margin-bottom:unset !important;">
							<option value='all'>Semua</option>
							<option value='check'>Yg Ditandai</option>
							<option value='uncheck'> Yg Tidak Ditandai</option>
						</select>
					</div>
					<div class='col' style='text-align:right;'>
						<strong>
							<span class="badge rounded-pill bg-primary"  style="font-size: small;" id='kb_add_trans_list_cnt'>0</span> Ditandai
						</strong>
					</div>
				</div>
			</div>
		</div>
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick='add_ketetapan();'><i class="bx bx-save"></i> Simpan Ketetapan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<div id='modal_uang_saku' class="modal fade" tabindex="-1" data-bs-focus="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> <i class="bx bxs-wallet-alt "></i>Dompet Digital - <span class='nama_siswa'></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-6 pr-5 pl-5">
		<div class='row'>
			<div class='col  ps-3'>
				<div>
					<div class="accordion" id="accor_uang_saku">
					  <div class="accordion-item">
						<h2 class="accordion-header" id="headingOne">
						  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#saku_topup" aria-expanded="true" aria-controls="saku_topup">
							<b>Isi Saldo</b>
						  </button>
						</h2>
						<div id="saku_topup" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accor_uang_saku">
						  <div class="accordion-body">
							<div class="mb-2">
								<label class="form-label" for="default-input">Nominal</label>
									<input id='nominal_saku_topup' class="form-control form-control-sm number" type="text">
							</div>
							<div class="d-grid gap-2 mt-3 mb-2">
                                 <button type="button" class="btn btn-primary waves-effect waves-light" onclick='saku_topup();' >Isi Saldo</button>
                            </div>
						</div>
						</div>
					  </div>
					  <div class="accordion-item">
						<h2 class="accordion-header" id="headingTwo">
						  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#saku_tarik" aria-expanded="false" aria-controls="saku_tarik">
							<b>Tarik Saldo</b>
						  </button>
						</h2>
						<div id="saku_tarik" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accor_uang_saku">
						  <div class="accordion-body">
							<div class="mb-2">
								<label class="form-label" for="default-input">Nominal</label>
									<input id='nominal_saku_tarik' class="form-control form-control-sm number" type="text">
							</div>
							<div class="mb-2">
								<label class="form-label" for="default-input">Deskripsi</label>
									<input id='nominal_saku_tarik_ket' class="form-control form-control-sm" type="text">
							</div>
							<div class="d-grid gap-2 mt-3 mb-2">
                                 <button type="button" class="btn btn-primary waves-effect waves-light" onclick='saku_tarik();' >Tarik Saldo</button>
                            </div>
						  </div>
						</div>
					  </div>
					  
					  <!-- <div class="accordion-item">
						<h2 class="accordion-header" id="headingTwo">
						  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#saku_emoney" aria-expanded="false" aria-controls="saku_tarik">
							<b> Top Up E-Money</b>
						  </button>
						</h2>
						<div id="saku_emoney" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accor_uang_saku">
						  <div class="accordion-body">
							<div class="mb-2">
								<label class="form-label" for="default-input">Nominal</label>
									<input id='nominal_saku_emoney' class="form-control form-control-sm number" type="text">
							</div>
							<div class="d-grid gap-2 mt-3 mb-2">
                                 <button type="button" class="btn btn-primary waves-effect waves-light" onclick='saku_emoney(1);' >Simpan</button>
                            </div>
						  </div>
						</div>
					  </div> -->
					</div>
				</div>
			</div>
			<div class='col-8  ps-3' id='div_history_uang_saku'>
				<div id='tbl_history_uang_saku' class='frozeCol' style='max-height:350px;'></div>
			</div>
		</div>
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<div id='modal_emoney' class="modal fade" tabindex="-1" data-bs-focus="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> <i class="bx bxs-wallet-alt "></i>Data E-Money <span class='nama_siswa'></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-6 pr-5 pl-5">
		<div class='row'>
			<div class='col  ps-3'>
				<div>
					<div class="accordion" id="accor_emoney">
					  <div class="accordion-item">
						<h2 class="accordion-header" id="headingTwo">
						  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#saku_emoney" aria-expanded="true" aria-controls="saku_tarik">
							<b> Top Up E-Money</b>
						  </button>
						</h2>
						<div id="saku_emoney" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accor_emoney">
						  <div class="accordion-body">
							<div class="mb-2">
								<label class="form-label" for="default-input">Nominal</label>
									<input id='nominal_emoney' class="form-control form-control-sm number" type="text">
							</div>
							<div class="d-grid gap-2 mt-3 mb-2">
                                 <button type="button" class="btn btn-primary waves-effect waves-light" onclick='emoney_topup(1);' >Simpan</button>
                            </div>
						  </div>
						</div>
					  </div>
					</div>
				</div>
			</div>
			<div class='col-8  ps-3' id='div_history_emoney'>
				<div id='tbl_history_emoney' class='frozeCol' style='max-height:350px;'></div>
			</div>
		</div>
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<div id='modal_tabungan' class="modal fade" tabindex="-1" data-bs-focus="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> <i class="bx bxs-wallet-alt "></i>Data Tabungan <span class='nama_siswa'></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-6 pr-5 pl-5">
		<div class='row'>
			<div class='col  ps-3'>
				<div>
					<div class="accordion" id="accor_tabungan">
					  <div class="accordion-item">
						<h2 class="accordion-header" id="headingOne">
						  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#tabungan_topup" aria-expanded="true" aria-controls="tabungan_topup">
							<b>Top Up</b>
						  </button>
						</h2>
						<div id="tabungan_topup" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accor_uang_saku">
						  <div class="accordion-body">
							<div class="mb-2">
								<label class="form-label" for="default-input">Nominal</label>
									<input id='nominal_tabungan_topup' class="form-control form-control-sm number" type="text">
							</div>
							<div class="d-grid gap-2 mt-3 mb-2">
                                 <button type="button" class="btn btn-primary waves-effect waves-light" onclick='tabungan_topup();' >Simpan</button>
                            </div>
						</div>
						</div>
					  </div>
					  <div class="accordion-item">
						<h2 class="accordion-header" id="headingTwo">
						  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#tabungan_tarik" aria-expanded="false" aria-controls="tabungan_tarik">
							<b>Penarikan</b>
						  </button>
						</h2>
						<div id="tabungan_tarik" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accor_tabungan">
						  <div class="accordion-body">
							<div class="mb-2">
								<label class="form-label" for="default-input">Nominal Penarikan</label>
									<input id='nominal_tabungan_tarik' class="form-control form-control-sm number" type="text">
							</div>
							<div class="mb-2" style="display:none;">
								<label class="form-label" for="default-input">Keterangan</label>
									<input id='nominal_tabungan_tarik_ket' class="form-control form-control-sm" type="text">
							</div>
							<div class="d-grid gap-2 mt-3 mb-2">
                                 <button type="button" class="btn btn-primary waves-effect waves-light" onclick='tabungan_tarik();' >Simpan</button>
                            </div>
						  </div>
						</div>
					  </div>
					</div>
				</div>
			</div>
			<div class='col-8  ps-3' id='div_history_tabungan'>
				<div id='tbl_history_tabungan' class='frozeCol' style='max-height:350px;'></div>
			</div>
		</div>
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_wa_web" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-edit "></i> Notif WA</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4">
				<p>Apakah ingin kirim notifkasi WA mandiri?</p>
			</div>
			<div class="modal-footer" id="btn_wa_web">
				
			</div>
		</div>
	</div>
</div>

<script src="konfig_tu/js/j_transaksi_bayar.js?v=<?php echo uniqid() ?>"></script>