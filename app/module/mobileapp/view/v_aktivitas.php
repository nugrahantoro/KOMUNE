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
						<h4 class="mb-sm-0 font-size-18">Aktivitas Siswa
							Tahun
							<select id='id_ta' class='form-select' style='display:inline-block;width:auto' onchange="ta();">
                                <?php
                                $res = db_query("SELECT *,
                                    IF(DATE(NOW()) BETWEEN tgl_mulai AND tgl_selesai,1,0) as cur
                                FROM data_tahunajar ORDER BY tgl_mulai DESC LIMIT 3");
                                while ($dt = mysqli_fetch_array($res)) {
                                    $selected = '';
                                    if ($dt['cur'] == 1) {
                                        $selected = 'selected';
                                    }
                                    echo "<option $selected value = '$dt[id_tahunajar]'>$dt[nama_tahunajar]</option>";
                                }
                                ?>
                            </select>
							Jenjang
                            <select id='id_jenjang' class='form-select' style='display:inline-block;width:auto' onchange="jenjang();">
                                <?php
                                $sesi = $_SESSION['id'];
                                $res = db_query("
                                    SELECT
                                        data_user_jenjang.id_user,
                                        data_user_jenjang.id_jenjang,
                                        data_jenjang.nama_jenjang 
                                    FROM
                                        data_user_jenjang
                                        INNER JOIN data_jenjang ON data_jenjang.id_jenjang = data_user_jenjang.id_jenjang 
                                    WHERE
                                        data_user_jenjang.id_user = '$sesi'
                                ");
                                while ($dt = mysqli_fetch_array($res)) {
                                    echo "<option value = '$dt[id_jenjang]'>$dt[nama_jenjang]</option>";
                                }
                                ?>
                            </select>
						</h4>
						<div class="page-title-right">
							<ol class="breadcrumb m-0">
								<li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard Mobile App</a></li>
								<li class="breadcrumb-item active">Aktivitas Siswa</li>
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
						<li class="nav-item" onclick='tab_aktivitas();'>
							<a class="nav-link active" data-bs-toggle="tab" href="#data_kelas_siswa" role="tab" aria-selected="false">
								<span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
								<span class="d-none d-sm-block">Data Aktivitas</span>
							</a>
						</li>
						<li class="nav-item" onclick='tab_konfig_aktivitas();'>
							<a class="nav-link" data-bs-toggle="tab" href="#data_konfig" role="tab" aria-selected="false">
								<span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
								<span class="d-none d-sm-block">Konfigurasi Aktivitas</span>
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
											<h4 class="card-title mb-0 flex-grow-1">Data Kelas</h4>
											<div class="flex-shrink-0">
												<div class="d-flex flex-wrap gap-2 mb-0 my-n1">
													<input class="form-control form-control-sm" type="text" id="cari_kelas" placeholder="Cari..." style='width:160pt;' onkeyup='cari_kelas();'>
												</div>
											</div>
										</div><!-- end card header -->

										<div class="card-body text-muted frozeCol" id='div_list_kelas' style='max-height: 400px;overflow-y: auto;'>

										</div><!-- end card-body -->
									</div><!-- end card -->
								</div><!-- end col -->

								<div class="col-xl-7" id='div_siswa'>
									<div class="card">
										<div class="card-header align-items-center d-flex">
											<h4 class="card-title mb-0 flex-grow-1">Data Siswa [<span id="span_nm_kelas"></span>]</h4>
											<div class="flex-shrink-0">
												<div class="d-flex flex-wrap gap-2 mb-0 my-n1">
													<input class="form-control form-control-sm" type="text" id="cari_siswa" placeholder="Cari..." style='width:160pt;' onkeyup='cari_siswa();'>
												</div>
											</div>
										</div><!-- end card header -->

										<div class="card-body text-muted frozeCol" id='div_list_siswa'>
											
										</div><!-- end card-body -->
									</div>
								</div><!-- end col -->
							</div>
						</div>

						<div class="tab-pane p-2" id="data_konfig" role="tabpanel">
							<div class="row">
								<div class="col-xl-5" id='div_item'>
									<div class="card">
										<div class="card-header align-items-center d-flex">
											<h4 class="card-title mb-0 flex-grow-1">Data Item</h4>
											<div class="flex-shrink-0">
												<div class="d-flex flex-wrap gap-2 mb-0 my-n1">
													 <button class='btn btn-sm btn-success' onclick='modal_item(event);'>
                                                                <i class="bx bx-plus"></i> Baru
                                                            </button>
													<input class="form-control form-control-sm" type="text" id="cari_item" placeholder="Cari..." style='width:160pt;' onkeyup='cari_item();'>
												</div>
											</div>
										</div><!-- end card header -->

										<div class="card-body text-muted frozeCol" id='div_list_item' style='max-height: 400px;overflow-y: auto;'>

										</div><!-- end card-body -->
									</div><!-- end card -->
								</div><!-- end col -->

								<div class="col-xl-7" id='div_sub_item'>
									<div class="card">
										<div class="card-header align-items-center d-flex">
											<h4 class="card-title mb-0 flex-grow-1">Data Sub Item [<span id="span_nm_item"></span>]</h4>
											<div class="flex-shrink-0">
												<div class="d-flex flex-wrap gap-2 mb-0 my-n1">
													 <button class='btn btn-sm btn-success' onclick='modal_sub_item(event);'>
                                                                <i class="bx bx-plus"></i> Baru
                                                            </button>
													<input class="form-control form-control-sm" type="text" id="cari_sub_item" placeholder="Cari..." style='width:160pt;' onkeyup='cari_sub_item();'>
												</div>
											</div>
										</div><!-- end card header -->

										<div class="card-body text-muted frozeCol" id='div_list_sub_item'  style='max-height: 300px;overflow-y: auto;'>
											
										</div><!-- end card-body -->
									</div>
								</div><!-- end col -->
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

<div class="modal fade" id="modal_detail_aktivitas" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-chalkboard "></i> <span id='ket_modal_detail_aktivitas'></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id='modal_body1' id="div_aktivitas">
               <table class='table table-sm table-bordered text-sm'>
                    <thead>
                        <tr>
                            <th width="15%">Nama</th>
                            <td id="kh_nama"></td>
                        </tr>
                        <tr>
                            <th>Kelas</th>
                            <td id="kh_kelas"></td>
                        </tr>
                        <tr>
                            <th>Tahun</th>
                            <td id="kh_tahun">
                            	<input type="hidden" id="kh_nis">
                            	<input type="hidden" id="sek_nama">
                            	<input type="hidden" id="sek_kelas">
                            	<?php 
                        		$currentYear = date('Y');
                        		$years = range($currentYear, $currentYear - 3, -1); ?>

                        		<select name="year" id="year" onchange="lembar_aktivitas();">
								<?php 
								foreach ($years as $year) {
								    echo '<option value="' . $year . '">' . $year . '</option>';
								}
								?>
								</select>
                            </td>
                        </tr>
                        <tr>
                            <th>Bulan</th>
                            <td id="kh_bulan">
                            	<?php 
                            	$currentMonth = date("m"); 
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
								$bln = $bulan_data[(int)$currentMonth];
                            	?>
                            	<select name="month" id="month" onchange="lembar_aktivitas();">
                            	<?php
                            		echo "<option value='".$currentMonth."' selected=''>".$bln."</option>";
                            	?>
                            		<option value="1">Januari</option>
                            		<option value="2">Februari</option>
                            		<option value="3">Maret</option>
                            		<option value="4">April</option>
                            		<option value="5">Mei</option>
                            		<option value="6">Juni</option>
                            		<option value="7">Juli</option>
                            		<option value="8">Agustus</option>
                            		<option value="9">September</option>
                            		<option value="10">Oktober</option>
                            		<option value="11">November</option>
                            		<option value="12">Desember</option>
                            	</select>
                            </td>
                        </tr>
                    </thead>
                </table>
                <div id="div_list_aktivitas">
                	
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_item" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-chalkboard "></i> <span id='ket_modal_item'></span> Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id='modal_body1'>
                <form>
                    <div class="row mb-4">
                        <label for="frm_id_item" class="col-sm-3 col-form-label">Nama Item</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="frm_nama_item" placeholder="">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_save_item" onclick='save_item();'><i class='bx bx-save'></i> <span></span></button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id='btn_del_item' onclick='del_item();' title='Hapus Item'><i class='bx bx-trash'></i></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_sub_item" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-chalkboard "></i> <span id='ket_modal_sub_item'></span> Sub Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id='modal_body1'>
                <form>
                    <div class="row mb-4">
                        <label for="frm_id_item" class="col-sm-3 col-form-label">Nama Sub Item</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="frm_nama_sub_item" placeholder="">
                        </div>
                    </div>
                    <div class="row mb-4" id="div_gambar">
                        <label for="frm_id_informasi" class="col-sm-3 col-form-label">Gambar</label>
                        <div class="col-sm-9">
                            <input type="file" class="form-control" id="frm_gambar_item" accept="image/png, image/jpg, image/jpeg">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_save_sub_item" onclick='save_sub_item();'><i class='bx bx-save'></i> <span></span></button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id='btn_del_sub_item' onclick='del_sub_item();' title='Hapus Item'><i class='bx bx-trash'></i></button>
            </div>
        </div>
    </div>
</div>

<script src="mobileapp/js/j_aktivitas.js?v=<?php echo uniqid() ?>"></script>