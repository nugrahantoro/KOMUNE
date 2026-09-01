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
	
</style>
<div class="main-content">

	<div class="page-content">
		<div class="container-fluid">

			<!-- start page title -->
			<div class="row">
				<div class="col-12">
					<div class="page-title-box d-sm-flex align-items-center justify-content-between">
						<h4 class="mb-sm-0 font-size-18">Pengaturan Iuran
							Periode
							<select id='id_ta' class='form-select' style='display:inline-block;width:auto' onchange="cari_semua();">
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
							<select id='id_jenjang' class='form-select' style='display:inline-block;width:auto' onchange="cari_semua_jenjang();">
								<?php
								$sesi = $_SESSION['id'];
								$res = db_query("SELECT data_user_jenjang.id_user,data_user_jenjang.id_jenjang,data_jenjang.nama_jenjang FROM data_user_jenjang INNER JOIN data_jenjang ON data_jenjang.id_jenjang=data_user_jenjang.id_jenjang WHERE data_user_jenjang.id_user='$sesi'");
								while ($dt = mysqli_fetch_array($res)) {
									echo "<option value = '$dt[id_jenjang]'>$dt[nama_jenjang]</option>";
								}
								?>
							</select>
						</h4>
						<div class="page-title-right">
							<ol class="breadcrumb m-0">
								<li class="breadcrumb-item"><a href="javascript: void(0);">Pembayaran Digital</a></li>
								<li class="breadcrumb-item active">Pengaturan Iuran</li>
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
						<li class="nav-item" onclick='tab_siswa_kelas();'>
							<a class="nav-link active" data-bs-toggle="tab" href="#data_kelas_siswa" role="tab" aria-selected="false">
								<span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
								<span class="d-none d-sm-block">Data Warga</span>
							</a>
						</li>
						<li class="nav-item" onclick='tab_mst_transaksi();'>
							<a class="nav-link " data-bs-toggle="tab" href="#mst_jns_transaksi" role="tab" aria-selected="true">
								<span class="d-block d-sm-none"><i class="far fa-user"></i></span>
								<span class="d-none d-sm-block">Jenis Iuran</span>
							</a>
						</li>
						<li class="nav-item" onclick="tab_konfig_bayar();">
							<a class="nav-link" data-bs-toggle="tab" href="#konfig_bayar" role="tab" aria-selected="false">
								<span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
								<span class="d-none d-sm-block">Penetapan Iuran</span>
							</a>
						</li>
					</ul>

					<!-- Tab panes -->
					<div class="tab-content p-3 text-muted" style="border: 1px var(--bs-gray-400) solid;border-top: unset;border-radius: 0px 0px 5px 5px;">
						<div class="tab-pane active" id="data_kelas_siswa" role="tabpanel">
							<div class="row">
								<div class="col-xl-5" id='div_kelas'>
									<div class="card">
										<div class="card-header align-items-center d-flex">
											<h4 class="card-title mb-0 flex-grow-1">Data Wilayah</h4>
											<div class="flex-shrink-0">
												<div class="d-flex flex-wrap gap-2 mb-0 my-n1">
													<button class='btn btn-sm btn-success' onclick='modal_kelas(event);'>
														<i class="bx bx-plus"></i> Tambah Wilayah
													</button>
													<input class="form-control form-control-sm" type="text" id="sk_cari_kelas" placeholder="Cari..." style='width:60pt;' onkeyup='sk_cari_kelas();'>
													<!-- <span>Tipe Wilayah</span>
													<select class="form-select form-select-sm mb-0 my-n1" id='id_level' onchange='sk_load_kelas();' style='width:unset;margin-bottom:unset !important;'></select> -->
												</div>
											</div>
										</div><!-- end card header -->

										<div class="card-body text-muted frozeCol" id='div_list_kelas' style='max-height: 400px;overflow-y: auto;'>

										</div><!-- end card-body -->
									</div><!-- end card -->
								</div><!-- end col -->

								<div class="col-xl-7" id='colom-siswa'>
									<div class="card">
										<div class="card-header align-items-center d-flex">
											<h4 class="card-title mb-0 flex-grow-1">
												<div>Data Warga
													<select class="form-select form-select-sm mb-0 my-n1" id="id_kelas" onchange="change_kelas();">
													</select>
												</div>
											</h4>
											<div class="flex-shrink-0">
												<ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs" role="tablist">
													<li class="nav-item">
														<a class="nav-link active" data-bs-toggle="tab" href="#tab_data_siswa" role="tab" aria-selected="false"  onclick='click_tab_siswa(1);'>
															<span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
															<span class="d-none d-sm-block">Data</span>
														</a>
													</li>
													<li class="nav-item">
														<a class="nav-link " data-bs-toggle="tab" href="#tab_va_dll" role="tab" aria-selected="true" onclick='click_tab_siswa(2);'>
															<span class="d-block d-sm-none">Virtual Account.</span>
															<span class="d-none d-sm-block">Aktivasi Pembayaran</span>
														</a>
													</li>
													<li class="nav-item">
														<a class="nav-link" data-bs-toggle="tab" href="#tab_pindah_naik" role="tab" aria-selected="false"  onclick='click_tab_siswa(3);'>
															<span class="d-block d-sm-none">Pindah Wilayah</i></span>
															<span class="d-none d-sm-block">Pindah Wilayah</span>
														</a>
													</li>
												</ul>
											</div>
										</div><!-- end card header -->

										<div class="card-body pt-1">
											<div class='row'>
												<div class='col-sm-4'>
													<input class="form-control form-control-sm" type="text" id="sk_cari_siswa" placeholder="Cari..." onkeyup="sk_cari_siswa();">
												</div>
												<div class='col-sm-8'>
													<div class='act_tab_siswa' id='act_data_siswa'>
														<span id='act_data_siswa' style='float:right;'>
															<button class='btn btn-sm btn-success' onclick='modal_siswa();'>
																<i class="bx bx-user-plus "></i> Baru
															</button>
															<!-- <button class='btn btn-sm btn-info' onclick='modal_import_siswa();'>
																<i class=" bx bx-import "></i> Import Data
															</button> -->
														</span>
													</div>
													<div class='act_tab_siswa' id='act_va_dll'>
														<ul class="nav nav-tabs nav-justified" role="tablist">
															<li class="nav-item" onclick=''>
																<a class="nav-link active" data-bs-toggle="tab" href="#tab_aktivasi_va" role="tab" aria-selected="false">
																	<span class="d-block d-sm-none">VA</span>
																	<span class="d-none d-sm-block">Aktivasi Akun</span>
																</a>
															</li>
															<!-- <li class="nav-item" onclick=''>
																<a class="nav-link" data-bs-toggle="tab" href="#tab_sound_nama" role="tab" aria-selected="false">
																	<span class="d-block d-sm-none">Sound</span>
																	<span class="d-none d-sm-block">Sound Nama</span>
																</a>
															</li> -->
															<li class="nav-item" onclick=''>
																<a class="nav-link" data-bs-toggle="tab" href="#tab_card_telp" role="tab" aria-selected="false">
																	<span class="d-block d-sm-none">Kartu & WhatsApp</i></span>
																	<span class="d-none d-sm-block">Kartu & WhatsApp</span>
																</a>
															</li>
														</ul>
													</div>
													<div class='act_tab_siswa' id='act_pindah_naik'>
														<ul class="nav nav-tabs nav-justified" role="tablist">
															<li class="nav-item" onclick=''>
																<a class="nav-link active" data-bs-toggle="tab" href="#tab_pindah_kelas" role="tab" aria-selected="false">
																	<span class="d-block d-sm-none">Pindah</span>
																	<span class="d-none d-sm-block">Pindah Wilayah</span>
																</a>
															</li>
															<li class="nav-item" onclick=''>
																<a class="nav-link" data-bs-toggle="tab" href="#tab_naik_kelas" role="tab" aria-selected="false">
																	<span class="d-block d-sm-none">Naik</span>
																	<span class="d-none d-sm-block">Naik Periode</span>
																</a>
															</li>
														</ul>
														
													</div>
												</div>
											</div>
											<!-- Tab panes -->
											<div class="tab-content text-muted">
												<div class="tab-pane active list_siswa frozeCol" id="tab_data_siswa" role="tabpanel">
													data-list
												</div>
												<div class="tab-pane " id="tab_va_dll" role="tabpanel">
													<input type='file' id='file_sound_nama' style='display:none;' accept=".mp3">
													<div class="tab-content text-muted">
														<div class="tab-pane frozeCol active fade show list_siswa" id="tab_aktivasi_va" role="tabpanel">
															
														</div>
														<div class="tab-pane frozeCol fade list_siswa" id="tab_sound_nama" role="tabpanel">
															sound nama
														</div>
														<div class="tab-pane fade" id="tab_card_telp" role="tabpanel">
															<div class="p-2">
																<select id='mode_card_telp' class='form-select form-select-sm mb-0 my-n1' style='width:unset;display:inline;' onchange="$('#card_telp_val').val('');">
																	<option value='telp_one'>WhatsApp</option>
																	<option value='no_kartu'>Kartu</option>
																</select>
																<input class="form-control form-control-sm" type="text" id="card_telp_val" style="display: inline-block;width: unset;">
															</div>
															<div class='frozeCol list_siswa'id='tbl_card_telp'>
																
															</div>
														</div>
													</div>
												</div>
												<div class="tab-pane" id="tab_pindah_naik" role="tabpanel">
													<div class="tab-content text-muted">
														<div class="tab-pane active" id="tab_pindah_kelas" role="tabpanel">
															<div class="p-2">
																<span>&nbsp Wilayah Tujuan</span>
																<select id='id_kelas_move' class='form-select form-select-sm mb-0 my-n1' style='width:unset;display:inline;'>
																</select>
																<span>
																<span id="" style="float:right;">
																	<button type="button" class="btn btn-primary btn-sm" onclick='batch_pindah_siswa();'>Pindahkan <span id='cnt_pindah_siswa' class="badge bg-danger ms-1">0<span></button>
																</span>
															</div>
															<div class='frozeCol list_siswa'id='tbl_pindah_kelas'>
															</div>
															<i style="font-size: smaller;">
																<span style='display:inline-block;width:10px;height:10px;background-color:#d4f0e5;'></span>
																 : Warga Sudah Dinaikkan
															</i>
														</div>
														<div class="tab-pane " id="tab_naik_kelas" role="tabpanel">
															<div class="p-2">
																<span>Ke Periode </span>
																<select id='id_ta_next' class='form-select form-select-sm mb-0 my-n1' style='width:unset;display:inline;'>
																	<option>2023 - 2024</option>
																</select>
																<span>&nbsp Wilayah Tujuan</span>
																<select id='id_kelas_next' class='form-select form-select-sm mb-0 my-n1' style='width:unset;display:inline;'>
																</select>
																<span>
																<span id="" style="float:right;">
																	<div class="btn-group">
																		<button type="button" class="btn btn-primary btn-sm" onclick='batch_naik_siswa();'>Naikkan <span id='cnt_naik_siswa' class="badge bg-danger ms-1">0<span></button>
																		<button type="button" class="btn btn-primary btn-sm dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
																			<i class="mdi mdi-chevron-down"></i>
																		</button>
																		<div class="dropdown-menu" style="">
																			<a class="dropdown-item" href="#" style='color:red;' onclick='event.preventDefault();batch_naik_siswa_batal()'>
																				<i class="bx bxs-x-circle"></i> 
																			Batalkan</a>
																		</div>
																	</div>
																</span>
															</div>
															<div class='frozeCol list_siswa'id='tbl_naik_kelas'>
															</div>
															<i style="font-size: smaller;">
																<span style='display:inline-block;width:10px;height:10px;background-color:#d4f0e5;'></span>
																 : Warga Sudah Dinaikkan
															</i>
														</div>
													</div>
												</div>
											</div>
										</div><!-- end card-body -->
									</div>
								</div><!-- end col -->
							</div>
						</div>
						<div class="tab-pane p-2" id="mst_jns_transaksi" role="tabpanel">
							<div class='row'>
								<div class='col-md-6'>
									<input class="form-control form-control-sm" type="text" id="cari_mst_transaksi" placeholder="Cari..." onkeyup="cari_mst_transaksi();">
								</div>
								<div class='col-md-6 tr'>
									<div class="btn-group" role="group">
										<button type="button" class="btn btn-sm btn-primary waves-effect waves-light w-lg" onclick='modal_master_kategori();'>
											<i class="bx bx-list-ul "></i>  Master Data Iuran
										</button>
										<button type="button" class="btn btn-sm btn-success waves-effect waves-light w-lg" onclick='modal_mst_trans(1);'>
											<i class="bx bxs-file-plus  "></i> Tambah Iuran
										</button>
									</div>
								</div>
								<div id='tbl_mst_transaksi' class='p-3' style='min-height:400px;'>
									
								</div>
							</div>
						</div>
						<div class="tab-pane" id="konfig_bayar" role="tabpanel">
							<div class="row">
								<div class="col-xl-4" id='kb_div_kelas'>
									<div class="card">
										<div class="card-header align-items-center d-flex">
											<h4 class="card-title mb-0 flex-grow-1">Data Wilayah</h4>
											<div class="flex-shrink-0">
												<div class="d-flex flex-wrap gap-2 mb-0 my-n1">
													<input class="form-control form-control-sm" type="text" id="kb_cari_kelas" placeholder="Cari..."  onkeyup='kb_cari_kelas();'>
												</div>
											</div>
										</div><!-- end card header -->

										<div class="card-body text-muted frozeCol pt-0" id='kb_div_list_kelas' style='max-height: 400px;overflow-y: auto;'>

										</div><!-- end card-body -->
									</div><!-- end card -->
								</div><!-- end col -->
								<div class='col-xl-8 card' id='kb_div_trans'>
									<div class='row card-header pb-2' >
										<div class='col-auto'>
											<h5>Wilayah <span id='kb_span_nm_kelas'>-</span></h5> 
										</div>
										<div class='col'>
											<div class="input-group">
											  <button class="btn btn-success btn-primary btn-sm w-md" type="button" onclick='modal_add_trans();'>
												<i class="bx bxs-file-plus  "></i> Tambah Penetapan
												</button>
											  <input type="text" class="form-control form-control-sm" id="kb_cari_trans" onkeyup='kb_cari_trans();' placeholder="Cari...">
											</div>
										</div>
										<div class='col-xl-5'>
											<ul class="nav justify-content-end nav-pills card-header-pills" role="tablist">
												<li class="nav-item">
													<a class="nav-link active" data-bs-toggle="tab" href="#kb_per_siswa" role="tab">
														<span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
														<span class="d-none d-sm-block">Per Warga</span>    
													</a>
												</li>
												<li class="nav-item">
													<a class="nav-link" data-bs-toggle="tab" href="#kb_per_jns_trans" role="tab">
														<span class="d-block d-sm-none"><i class="far fa-user"></i></span>
														<span class="d-none d-sm-block">Per Jenis Iuran</span>    
													</a>
												</li>
											</ul>
										</div>
									</div>
                                        <!-- Tab panes -->
                                        <div class="tab-content p-3 text-muted">
                                            <div class="tab-pane active frozeCol" id="kb_per_siswa" role="tabpanel"  style='max-height: 400px;overflow-y: auto;' >
                                                <p class="mb-0">
                                                    Siswa
                                                </p>
                                            </div>
                                            <div class="tab-pane frozeCol" id="kb_per_jns_trans" role="tabpanel" style='max-height: 400px;overflow-y: auto;' >
                                                <p class="mb-0">
                                                    Jenis Transaki
                                                </p>
                                            </div>
                                        </div>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="settings" role="tabpanel">
							<p class="mb-0">
								Trust fund seitan letterpress, keytar raw denim keffiyeh etsy
								art party before they sold out master cleanse gluten-free squid
								scenester freegan cosby sweater. Fanny pack portland seitan DIY,
								art party locavore wolf cliche high life echo park Austin. Cred
								vinyl keffiyeh DIY salvia PBR, banh mi before they sold out
								farm-to-table VHS.
							</p>
						</div>
					</div>

				</div><!-- end col -->

			</div>

		</div>
		<!-- container-fluid -->
	</div>
	<!-- End Page-content -->
</div>

<div class="modal fade" id="modal_kelas" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-chalkboard "></i> <span id='ket_modal_kelas'></span> Wilayah</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4" id='modal_body1'>
				<form>
					<div class="row mb-4">
						<label for="frm_id_kelas" class="col-sm-3 col-form-label">Nama Wilayah</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="frm_nama_kelas" placeholder="">
						</div>
					</div>
					<!-- <div class="row mb-4">
						<label for="frm_id_level" class="col-sm-3 col-form-label">Tingkat</label>
						<div class="col-sm-9">
							<select class="form-select" id='frm_id_level'>
								<option value='0'>-pilih-</option>
								<option value='1'>1</option>
								<option value='2'>2</option>
								<option value='3'>3</option>
								<option value='4'>4</option>
								<option value='5'>5</option>
								<option value='6'>6</option>
							</select>
						</div>
					</div> -->
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btn_save_kelas" onclick='save_kelas();'><i class='bx bx-save'></i> <span></span></button>
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
				<button type="button" class="btn btn-danger" id='btn_del_kelas' onclick='del_kelas();' title='Hapus Wilayah'><i class='bx bx-trash'></i></button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_import_siswa" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-chalkboard "></i> Import Data Siswa</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4 pt-2 pb-2" id='modal_body1'>

				<!-- Nav tabs -->
				<ul class="nav nav-pills nav-justified" role="tablist" onclick='import_btn_verify();'>
					<li class="nav-item waves-effect waves-light">
						<a class="nav-link active" data-bs-toggle="tab" href="#tab_excel" role="tab" aria-selected="false">
							<span class="d-block d-sm-none">File Excel</span>
							<span class="d-none d-sm-block">File Excel</span>
						</a>
					</li>
					<li class="nav-item waves-effect waves-light">
						<a class="nav-link" data-bs-toggle="tab" href="#tab_import_siswa" role="tab" id='tab_button_import_siswa' aria-selected="true">
							<span class="d-block d-sm-none">Import Data Siswa</i></span>
							<span class="d-none d-sm-block">Import Data Siswa</span>
						</a>
					</li>
				</ul>

				<!-- Tab panes -->
				<div class="tab-content p-3 text-muted" id='content_tab_import_siswa'>
					<div class="tab-pane active" id="tab_excel" role="tabpanel">
						<div>
							<br>
							<div style='text-align:right;'>
								<a href="konfig_tu/ex/siswa_kelas/import_sample" class="btn btn-light btn-sm">
									<i class="bx bxs-download"></i>
									Download file sampel</a>
							</div>
							<div class="mb-3">
								<label class="form-label" for="formrow-firstname-input">File Excel (.xlsx) </label>
								<input type="file" class="form-control" id="file_excel_siswa" accept=".xlsx">
							</div>

							<div style='text-align:center;'>
								<button class="btn btn-primary" onclick='upload_excel_siswa();'>
									<i class="bx bx-upload "></i> Upload File</button>
							</div>
							<div id='prog_upload_siswa' style='display:none;'>
								<br>
								<div class="progress progress-xl">
									<div id='prog_upload_siswa_bar' class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
								<div class='tc'>
									<h5><span id='prog_perc'></span> % ( <span id='prog_uploaded'></span> kb / <span id='prog_total'></span> kb ) </h5>
									<h5 id='status_upload_excel'></h5>
								</div>
							</div>
							<br>
							<i>*) Pastikan data yang diupload sesuai dengan format yang telah ditentukan.</i>
						</div>
					</div>
					<div class="tab-pane fade" id="tab_import_siswa" role="tabpanel">
						<div id='import_siswa_no_file' class=' p-5 mt-5 ' style='text-align:center;'>
							<div>
								<b>
									Belum ada file excel(.xlsx) yang diupload / tidak valid.
								</b>
							</div>
						</div>
						<div id='import_siswa_file_avail' style='display:none;'>
							<h5>Import Data Siswa TA. <span class='spn_ta'></span> Jenjang <span class='spn_jenjang'></span></h5>
							<div id='tbl_import_siswa' class='frozeCol' style=':max-height: 325px;'></div>
							<i class='mt-1'>*) Tandai nama murid yang akan diimport</i>
						</div>
					</div>
				</div>

			</div>
			<div class="modal-footer">
				<span style='float:left; display:none;' class='btn_import'>
					<b><span class='import_jml_pilih badge rounded-pill bg-primary fs-6'>0</span> Siswa Dipilih</b>
				</span>
				<button type="button" style="display:none;" class="btn btn-success btn_import" onclick='import_siswa_btn();'>Import Siswa</button>
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Data Warga -->
<div class="modal fade" id="modalSiswa" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

            <!-- HEADER -->
            <div class="modal-header border-0 px-4 py-3">
                <h5 class="modal-title fw-bold">
                    <i class="bx bx-user-circle me-1"></i>
                    Tambah Data Warga
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body px-4 pb-4">

				<div id="form_data_siswa">

					<div class="row g-4 align-items-start">

						<!-- FOTO -->
						<div class="col-md-4 border-end">

							<div class="text-center px-3">

								<div class="mb-3">
									<img src="assets/images/users/avatar-1.jpg"
										id="prs_photo_preview"
										class="rounded-circle border"
										style="width:140px;height:140px;object-fit:cover;">
								</div>

								<input type="file"
									id="prs_photo"
									hidden>

								<button class="btn btn-primary btn-sm"
										onclick="$('#prs_photo').trigger('click')">
									Ganti Foto
								</button>

								<div class="mt-4">
									<h4 class="fw-bold mb-1"
										id="txt_nama_siswa">
										Nama Warga
									</h4>

									<span class="badge bg-light text-dark px-3 py-2"
										id="txt_kelas_siswa">
										Wilayah
									</span>
								</div>

							</div>

						</div>

						<!-- FORM -->
						<div class="col-md-8">

							<div class="row g-3">

								<!-- LEFT -->
								<div class="col-md-6">

									<div>
										<label class="form-label fw-semibold">
											Nama Lengkap
										</label>

										<input type="text"
											class="form-control"
											field="nama_siswa">
									</div>

									<div>
										<label class="form-label fw-semibold">
											Nama Panggilan
										</label>

										<input type="text"
											class="form-control"
											field="nama_murid_panggilan">
									</div>

									<div>
										<label class="form-label fw-semibold">
											Gender
										</label>

										<select class="form-select"
												field="gender">
											<option>- Pilih Gender -</option>
											<option value="L">Laki-Laki</option>
											<option value="P">Perempuan</option>
										</select>
									</div>

									<div>
										<label class="form-label fw-semibold">
											Tanggal Lahir
										</label>

										<input type="date"
											class="form-control"
											field="tanggal_lahir">
									</div>

									<div>
										<label class="form-label fw-semibold">
											Nomor WhatsApp
										</label>

										<input type="text"
											class="form-control"
											placeholder="08xxxxxxxxxx"
											field="telp_one">
									</div>

								</div>

								<!-- RIGHT -->
								<div class="col-md-6">

									<div>
										<label class="form-label fw-semibold">
											Alamat
										</label>

										<textarea class="form-control"
												rows="4"
												field="alamat"></textarea>
									</div>

									<div>
										<label class="form-label fw-semibold">
											Kelurahan / Desa
										</label>

										<input type="text"
											class="form-control"
											field="kelurahan">
									</div>

									<div>
										<label class="form-label fw-semibold">
											Kecamatan
										</label>

										<input type="text"
											class="form-control"
											field="kecamatan">
									</div>

									<div>
										<label class="form-label fw-semibold">
											Kabupaten / Kota
										</label>

										<input type="text"
											class="form-control"
											field="kabupatenkota">
									</div>

									<div>
										<label class="form-label fw-semibold">
											Wilayah
										</label>

										<select class="form-select"
												id="sw_kelas"
												field="id_kelass">
											<option>- Pilih Wilayah -</option>
										</select>
									</div>

								</div>

							</div>

						</div>

					</div>

				</div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer border-0 px-4 pb-4">

                <button type="button"
                        class="btn btn-light px-4"
                        data-bs-dismiss="modal">
                    Tutup
                </button>

                <button type="button"
                        class="btn btn-primary px-4"
                        onclick="save_siswa();">
                    <i class="bx bx-save me-1"></i>
                    Simpan Data Warga
                </button>

            </div>

        </div>
    </div>
</div>

<div id='modal_sound_nama' class="modal fade" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Sound Nama '<b id='sound_nama_siswa'></b>'</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table>
			<tr>
				<td>Nama File</td>
				<td>:</td>
				<td><b id='snd_name'></b></td>
			</tr>
				<td>Ukuran</td>
				<td>:</td>
				<td><b id='snd_size'></b></td>
			</tr>
				<td>Durasi</td>
				<td>:</td>
				<td ><b id='snd_duration'></b></td>
			</tr>
			</tr>
				<td></td>
				<td></td>
				<td>
					<button type="button" onclick="play_sound_nama_local();" class="btn btn-primary btn-sm">
							<i class=" bx bx-play-circle "></i>
							Play
						</button>
					<button type="button" onclick="dialog_sound_nama(glob_id_siswa);" class="btn btn-dark btn-sm">
							<i class=" bx bx-revision "></i>
							Ubah File
						</button>
				</td>
			</tr>
		</table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick='upload_sound_nama();'><i class="bx bx-upload"></i> Upload</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<div id='modal_master_kategori' class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> <i class="bx bx-list-ul "></i> Master Data Iuran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
			<div class='row'>
				<div class='col-md-4' id='div_mst_kat_transaksi'>
					<div class="card">
                                <div class="card-header pb-0">
                                    <h4 class="card-title">Jenis Iuran</h4>
                                    <p class="card-title-desc">
										<div class='row'>
											<div class='col-8'>
												<input type="text" class="form-control form-control-sm" id="inp_mst_kat_transaksi" placeholder='Nama Item'>
											</div>
											<div class='col-4'>
												<button type="button" class="btn btn-sm btn-primary" onclick='save_mst_kat_transaksi(1)'>
													<i class="bx bx-plus"></i> Tambah
												</button>
											</div>
										</div>
									</p>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" id='mst_kat_tbl_transaksi'></div>
                                </div>
                                <!-- end card body -->
                            </div>
				</div>
				<div class='col-md-4' id='div_mst_kat_laporan'>
					<div class="card">
                                <div class="card-header pb-0">
                                    <h4 class="card-title">Alokasi Dana</h4>
                                    <p class="card-title-desc">
										<div class='row'>
											<div class='col-8'>
												<input type="text" class="form-control form-control-sm" id="inp_mst_kat_laporan" placeholder='Nama Item'>
											</div>
											<div class='col-4'>
												<button type="button" class="btn btn-sm btn-primary" onclick='save_mst_kat_laporan(1)'>
													<i class="bx bx-plus"></i> Tambah
												</button>
											</div>
										</div>
									</p>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" id='mst_kat_tbl_laporan'></div>
                                </div>
                                <!-- end card body -->
                            </div>
				</div>
				<div class='col-md-4' id='div_mst_kat_pengeluaran'>
					<div class="card">
                                <div class="card-header pb-0">
                                    <h4 class="card-title">Pos Kas</h4>
                                    <p class="card-title-desc">
										<div class='row'>
											<div class='col-8'>
												<input type="text" class="form-control form-control-sm" id="inp_mst_kat_pengeluaran" placeholder='Nama Item'>
											</div>
											<div class='col-4'>
												<button type="button" class="btn btn-sm btn-primary" onclick='save_mst_kat_pengeluaran(1)'>
													<i class="bx bx-plus"></i> Tambah
												</button>
											</div>
										</div>
									</p>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" id='mst_kat_tbl_pengeluaran'></div>
                                </div>
                                <!-- end card body -->
                            </div>
				</div>
			</div>
		</div>
    </div>
  </div>
</div>

<div id='modal_mst_trans' class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> <i class="bx bxs-file "></i> <span id='titel_modal_mst_trans'></span> Jenis Iuran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
		<div class='p-4 pt-1 pb-1' >
			<div class="row mb-3">
				<label class="col-sm-3 col-form-label">Nama Iuran</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id='msjns_nama_transaksi'>
				</div>
			</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-form-label">Batas Pembayaran</label>
				<div class="col-sm-9">
					<select class="form-select" id='msjns_jatuh_tempo'>
                     </select>
				</div>
			</div>
			<div class="row mb-3">
				<label class="col-sm-3 col-form-label">Status Iuran</label>
				<div class="col-sm-9">
					<select class="form-select" id='msjns_status' onchange='change_mst_status_trans();'>
                        <option value='-'>-pilih-</option>
                        <option value='0'>Non Kelompok</option>
                        <option value='1'>Kelompok</option>
                     </select>
				</div>
			</div>
			<div class="row mb-3" id='div_msjns_kelompok_trans'>
				<label class="col-sm-3 col-form-label">Jenis Iuran</label>
				<div class="col-sm-9">
					<select class="form-select" id='msjns_kelompok_trans'>
                     </select>
				</div>
			</div>
			<div class="row mb-3" id='div_msjns_cicilan'>
				<label  class="col-sm-3 col-form-label">Bisa Dicicil</label>
				<div class="col-sm-9">
					<select class="form-select" id='msjns_cicilan'>
                        <option value='-'>-pilih-</option>
                        <option value='null'>Tidak</option>
                        <option value='1'>Ya</option>
                     </select>
				</div>
			</div>
			<div class="row mb-3" id='div_msjns_cicilan_khusus'>
				<label  class="col-sm-3 col-form-label">Cicilan Iuran (K)</label>
				<div class="col-sm-9">
					<select class="form-select" id='msjns_cicilan_khusus'>
                        <option value='-'>-pilih-</option>
                        <option value='biasa'>Tidak</option>
                        <option value='popup'>Ya</option>
                     </select>
				</div>
			</div>
			<div class="row mb-3" style=''>
				<label  class="col-sm-3 col-form-label"> Alokasi Dana</label>
				<div class="col-sm-9">
					<select class="form-select" id='msjns_kelompok_laporan'>
                     </select>
				</div>
			</div>
			<div class="row mb-3"  style=''>
				<label  class="col-sm-3 col-form-label"> Pos Kas</label>
				<div class="col-sm-9">
					<select class="form-select"id='msjns_kelompok_pengeluaran'>
                        <option>Select</option>
                        <option>Large select</option>
                        <option>Small select</option>
                     </select>
				</div>
			</div>
			
			<!-- <div class="row mb-3" style=''>
				<label  class="col-sm-3 col-form-label"> Kategori Pemasukan Kas</label>
				<div class="col-sm-9">
					<select class="form-select" id='msjns_kategori_pemasukan' onchange='list_subkat();'>
                     </select>
				</div>
			</div>
			<div class="row mb-3 row_subkat" style=''>
				<label  class="col-sm-3 col-form-label"> Sub Kategori Kas</label>
				<div class="col-sm-9">
					<select class="form-select" id='msjns_subkategori_pemasukan'>
                     </select>
				</div>
			</div> -->
         </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick='save_mst_trans();'><i class="bx bx-save"></i> Simpan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<div id='modal_show_trans' class="modal fade" tabindex="-1" data-bs-focus="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> <i class="bx bxs-file "></i>Daftar Penetapan Iuran <span class='mode_modal'></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 pr-5 pl-5">
		<div class='row'>
			<div class='col  ps-3'>
				<div class='row'>
					<div class='col-8'>
						<input class="form-control form-control-sm mb-3" type="text" id="kb_cari_trans_view" placeholder="Cari..." onkeyup='kb_cari_trans_view();'>
					</div>
					<div class='col' style='text-align:right;'>
						<button class='btn btn-sm btn-danger' onclick='kb_del_trans_multi()'> 
								<i class='bx bx-trash'></i> Hapus Yg Ditandai 
								<span id='kb_cnt_trans_view_del' class="badge bg-dark" style="font-size: inherit;">0</span> 
						</button>
					</div>
				</div>
				<div id='kb_trans_view' class='frozeCol' style='max-height:400px;'></div>
			</div>
			<div class='col-md-4 ps-3' id='kb_trans_edit'>
				<div>
					<div class="accordion" id="accordionExample">
					  <div class="accordion-item">
						<h2 class="accordion-header" id="headingOne">
						  <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
							<b>Edit Iuran Yg Dipilih</b>
						  </button>
						</h2>
						<div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
						  <div class="accordion-body">
								<div class="mb-2">
								<label class="form-label" for="default-input">Nama Warga</label>
								<input id='kb_edit_nama_siswa' class="form-control form-control-sm" type="text" readonly>
							</div>
							<div class="mb-2">
								<label class="form-label" for="default-input">Nama Iuran</label>
								<input id='kb_edit_nama_transaksi' class="form-control form-control-sm" type="text" readonly>
							</div>
							<div class="mb-2">
								<label class="form-label" for="default-input">Nominal Iuran</label>
									<input id='kb_edit_nominal' class="form-control form-control-sm number" type="text">
							</div>
							<div class="mb-2">
								<label class="form-label" for="default-input">Batas Pembayaran</label>
								<div >
									<input id='kb_edit_tgl_jth' class="form-control form-control-sm" style='width:100%;' type="text">
								</div>
							</div>
							<div class="d-grid gap-2 mt-3 mb-2">
                                 <button type="button" class="btn btn-primary waves-effect waves-light" onclick='kb_save_edit_trans(1);' >Simpan</button>
                            </div>
						</div>
						</div>
					  </div>
					  <div class="accordion-item">
						<h2 class="accordion-header" id="headingTwo">
						  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
							<b>Edit Massal Iuran</b>
						  </button>
						</h2>
						<div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
						  <div class="accordion-body">
							<div class='row'>
								<div class='col-sm-1 tc'>
									<input class='mt-3' id='kb_chk_edit_nominal' type='checkbox' style="scale: 1.4;">
								</div>
								<div class='col'>
									<div class="mb-2">
										<label class="form-label" for="default-input">Nominal Iuran</label>
										<input class="form-control form-control-sm number" type="text" id="kb_chk_edit_nominal_val">
									</div>
								</div>
							</div>
							<div class='row'>
								<div class='col-sm-1 tc'>
									<input  class='mt-3' id='kb_chk_edit_tgl_jth' type='checkbox' style="scale: 1.4;">
								</div>
								<div class='col'>
									<div class="mb-2">
										<label class="form-label" for="default-input">Batas Pembayaran</label>
										<input class="form-control form-control-sm" type="text" id="kb_chk_edit_tgl_jth_val">
									</div>
								</div>
								<div class="d-grid gap-2 mt-3 mb-2">
									 <button type="button" class="btn btn-primary waves-effect waves-light" onclick='kb_save_edit_trans(2);'>Simpan Yg Ditandai <span id='kb_cnt_trans_view' class="badge bg-danger ms-1">0</span></button>
								</div>
							</div>
						  </div>
						</div>
					  </div>
					</div>
				</div>
			</div>
		</div>
      </div>
    </div>
  </div>
</div>

<div id='modal_add_trans' class="modal fade" tabindex="-1" data-bs-focus="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> <i class="bx bxs-file "></i>Tambah Penetapan Iuran </h5>
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
			<div class='col-md-6 ps-3'>
				<div class='row'>
					<div class='col-auto'>
						<strong>Warga Wilayah </strong>
						<select class="form-select form-select-sm" id="kb_add_trans_siswa_kls" onchange="kb_add_siswa_list();" style="display:inline;width:unset;margin-bottom:unset !important;">
							
						</select>
					</div>
					<div class='col'>
						<input class="form-control form-control-sm mb-3" type="text" id="kb_cari_add_siswa" placeholder="Cari..." onkeyup='kb_cari_add_siswa();'>
					</div>
				</div>
				<div id='kb_add_siswa_list' class='frozeCol' style='max-height:350px;'></div>
				<div class='mt-3 row'>
					<div class='col-auto'>
						Tampilkan
						<select class="form-select form-select-sm" id="kb_cari_add_siswa_chk" onchange="kb_cari_add_siswa();" style="display:inline;width:unset;margin-bottom:unset !important;">
							<option value='all'>Semua</option>
							<option value='check'>Yg Ditandai</option>
							<option value='uncheck'> Yg Tidak Ditandai</option>
						</select>
					</div>
					<div class='col' style='text-align:right;'>
						<strong>
							<span class="badge rounded-pill bg-primary"  style="font-size: small;" id='kb_add_siswa_list_cnt'>0</span> Ditandai
						</strong>
					</div>
				</div>
			</div>
		</div>
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick='add_ketetapan();'><i class="bx bx-save"></i> Terapkan Iuran</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<div id='modal_pin_siswa' class="modal fade" tabindex="-1" data-bs-focus="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"> <i class="bx bx-health "></i> <span class='modal_title'></span> PIN </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-6 pr-5 pl-5">
			<form>
					<div class="row mb-4">
						<label for="pin_nomor" class="col-sm-3 col-form-label">PIN</label>
						<div class="col-sm-9">
							<input type="text" class="form-control number" id="pin_nomor" placeholder="">
						</div>
					</div>
					<div id='col_pin_keterangan' class="row mb-4">
						<label for="pin_keterangan" class="col-sm-3 col-form-label">Keterangan</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="pin_keterangan" placeholder="">
						</div>
					</div>
				</form>
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick='pin_siswa_save();'><i class="bx bx-save"></i> Simpan </button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
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

<script src="konfig_tu/js/j_siswa_kelas.js?v=<?php echo uniqid() ?>"></script>
<script src="konfig_tu/js/j_mst_transaksi.js?v=<?php echo uniqid() ?>"></script>
<script src="konfig_tu/js/j_konfig_bayar.js?v=<?php echo uniqid() ?>"></script>
<script>
	glob_dbn="<?php echo $data['dbn'] ?>";
</script>