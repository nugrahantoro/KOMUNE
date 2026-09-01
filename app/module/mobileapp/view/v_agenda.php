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
						<h4 class="mb-sm-0 font-size-18">Agenda Warga
							Periode
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
                            Wilayah
                            <select id='id_jenjang' class='form-select' style='display:inline-block;width:auto' onchange="jenjang();">
                                <?php
                                $sesi = $_SESSION['id'] ?? '';
                                $res = db_query("
                                    SELECT DISTINCT data_jenjang.id_jenjang, data_jenjang.nama_jenjang 
                                    FROM data_jenjang 
                                    LEFT JOIN data_user_jenjang ON data_jenjang.id_jenjang = data_user_jenjang.id_jenjang AND data_user_jenjang.id_user = '$sesi'
                                    ORDER BY data_jenjang.id_jenjang ASC
                                ");
                                if ($res) {
                                    while ($dt = mysqli_fetch_array($res)) {
                                        echo "<option value = '$dt[id_jenjang]'>$dt[nama_jenjang]</option>";
                                    }
                                }
                                ?>
                            </select>
						</h4>
						<div class="page-title-right">
							<ol class="breadcrumb m-0">
								<li class="breadcrumb-item active">Agenda Warga</li>
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
									<h4 class="card-title mb-0 flex-grow-1"></h4>
									<div class="flex-shrink-0">
										<div class="d-flex flex-wrap gap-2 mb-0 my-n1">
											<button class='btn btn-sm btn-success' onclick='modal_agenda(event);'>
												<i class="bx bx-plus"></i> Tambah Agenda
											</button>
											<input class="form-control form-control-sm" type="text" id="cari_agenda" placeholder="Cari agenda warga..." style='width:260pt;' onkeyup='cari_agenda();'>
										</div>
									</div>
								</div><!-- end card header -->

								<div class="card-body text-muted frozeCol" id='div_list_agenda' style='max-height: 400px;overflow-y: auto;'>

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

<div class="modal fade" id="modal_agenda" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content border-0 shadow-lg">

			<div class="modal-header border-0 pb-0 px-4 pt-4">
				<div>
					<h4 class="modal-title fw-bold mb-1">
						<i class="bx bx-calendar-event text-primary"></i>
						<span id="ket_modal_kelas"></span> Agenda Warga
					</h4>
					<small class="text-muted">
						Kelola agenda dan kegiatan komunitas warga
					</small>
				</div>

				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>

			<div class="modal-body px-4 py-4" id="modal_body1">

				<form autocomplete="off">

					<div class="row g-4">

						<div class="col-md-8">
							<label class="form-label fw-semibold">
								Judul Agenda
							</label>

							<input 
								type="text" 
								class="form-control form-control-lg" 
								id="frm_judul"
								placeholder="Contoh : Kerja Bakti Lingkungan"
							>
						</div>

						<div class="col-md-4">
							<label class="form-label fw-semibold">
								Kategori
							</label>

							<select class="form-select form-select-lg" id="frm_kategori">
								<option value="">Pilih kategori</option>
								<option value="Kerja Bakti">Kerja Bakti</option>
								<option value="Rapat Warga">Rapat Warga</option>
								<option value="Keagamaan">Keagamaan</option>
								<option value="Keamanan">Keamanan</option>
								<option value="Sosial">Sosial</option>
								<option value="Event">Event</option>
							</select>
						</div>

						<div class="col-md-6">
							<label class="form-label fw-semibold">
								Tanggal Agenda
							</label>

							<input 
								type="date" 
								class="form-control form-control-lg"
								id="frm_tanggal"
							>
						</div>

						<div class="col-md-6">
							<label class="form-label fw-semibold">
								Lokasi
							</label>

							<input 
								type="text" 
								class="form-control form-control-lg"
								id="frm_lokasi"
								placeholder="Contoh : Balai Warga RT 01"
							>
						</div>

						<div class="col-md-12">
							<label class="form-label fw-semibold">
								Deskripsi Kegiatan
							</label>

							<textarea 
								class="form-control"
								id="frm_kegiatan"
								rows="5"
								placeholder="Tulis detail kegiatan agenda warga..."
							></textarea>
						</div>

						<div class="col-md-6">
							<label class="form-label fw-semibold">
								Status Agenda
							</label>

							<select class="form-select form-select-lg" id="frm_status">
								<option value="draft">Draft</option>
								<option value="aktif">Aktif</option>
								<option value="selesai">Selesai</option>
							</select>
						</div>

					</div>

				</form>

			</div>

			<div class="modal-footer border-0 px-4 pb-4">

				<button 
					type="button" 
					class="btn btn-light border px-4"
					data-bs-dismiss="modal"
				>
					Tutup
				</button>

				<button 
					type="button" 
					class="btn btn-danger px-4"
					id="btn_del_agenda"
					onclick="del_agenda();"
					title="Hapus Agenda"
				>
					<i class='bx bx-trash'></i>
				</button>

				<button 
					type="button" 
					class="btn btn-primary px-4"
					id="btn_save_agenda"
					onclick="save_agenda();"
				>
					<i class='bx bx-save'></i>
					<span>Simpan Agenda</span>
				</button>

			</div>

		</div>
	</div>
</div>

<script src="mobileapp/js/j_agenda.js?v=<?php echo uniqid() ?>"></script>