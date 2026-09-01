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
	
</style>
<div class="main-content">

	<div class="page-content">
		<div class="container-fluid">

			<!-- start page title -->
			<div class="row">
				<div class="col-12">
					<div class="page-title-box d-sm-flex align-items-center justify-content-between">
						<h4 class="mb-sm-0 font-size-18">Daftar Libur
							TA
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
                                $res = db_query("SELECT * FROM data_jenjang LIMIT 4");
                                while ($dt = mysqli_fetch_array($res)) {
                                    echo "<option value = '$dt[id_jenjang]'>$dt[nama_jenjang]</option>";
                                }
                                ?>
                            </select>
						</h4>
						<div class="page-title-right">
							<ol class="breadcrumb m-0">
								<li class="breadcrumb-item active">Daftar Libur</li>
							</ol>
						</div>

					</div>
				</div>
			</div>
			<!-- end page title -->

			<div class="row">
				<div class="col-md-12" id='col-userkeu'>

					<div class="row">
						<div class="col-xl-12" id='div_kelas'>
							<div class="card">
								<div class="card-header align-items-center d-flex">
									<h4 class="card-title mb-0 flex-grow-1">Data Libur</h4>
									<div class="flex-shrink-0">
										<div class="d-flex flex-wrap gap-2 mb-0 my-n1">
											<button class='btn btn-sm btn-success' onclick='modal_libur(event);'>
												<i class="bx bx-plus"></i> Baru
											</button>
											<input class="form-control form-control-sm" type="text" id="cari_libur" placeholder="Cari tanggal / keterangan..." style='width:260pt;' onkeyup='cari_libur();'>
										</div>
									</div>
								</div><!-- end card header -->

								<div class="card-body text-muted frozeCol" id='div_list_libur' style='max-height: 550px;overflow-y: auto;'>

								</div><!-- end card-body -->
							</div><!-- end card -->
						</div><!-- end col -->
					</div>

				</div><!-- end col -->

			</div>

		</div>
		<!-- container-fluid -->
	</div>
	<!-- End Page-content -->
</div>

<div class="modal fade" id="modal_libur" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-chalkboard "></i> <span id='ket_modal_kelas'></span> Hari Libur</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4" id='modal_body1'>
				<form>
					<div class="row mb-4">
						<label for="frm_id_libur" class="col-sm-3 col-form-label">Tanggal</label>
						<div class="col-sm-9">
							<input type="date" class="form-control" id="frm_tanggal" placeholder="">
						</div>
					</div>
					<div class="row mb-4">
						<label for="frm_id_libur" class="col-sm-3 col-form-label">Keterangan</label>
						<div class="col-sm-9">
							<textarea  class="form-control" id="frm_keterangan"></textarea>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btn_save_libur" onclick='save_libur();'><i class='bx bx-save'></i> <span></span></button>
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
				<button type="button" class="btn btn-danger" id='btn_del_libur' onclick='del_libur();' title='Hapus Agenda'><i class='bx bx-trash'></i></button>
			</div>
		</div>
	</div>
</div>

<script src="mobileapp/js/j_daftar_libur.js?v=<?php echo uniqid() ?>"></script>