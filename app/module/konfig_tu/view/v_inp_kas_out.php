<link rel="stylesheet" href="css/gridstack.min.css">
<link rel="stylesheet" href="css/gridstack-extra.min.css">

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

<style>
.tc { text-align: center; }
.fr { float: right; }

	/* Padding classes */
.padding-0 { padding: 0; }
.padding-1 { padding: 5px; }
.padding-2 { padding: 10px; }
.padding-3 { padding: 15px; }
.padding-4 { padding: 20px; }
.padding-5 { padding: 25px; }

.padding-top-0 { padding-top: 0; }
.padding-top-1 { padding-top: 5px; }
.padding-top-2 { padding-top: 10px; }
.padding-top-3 { padding-top: 15px; }
.padding-top-4 { padding-top: 20px; }
.padding-top-5 { padding-top: 25px; }

.padding-right-0 { padding-right: 0; }
.padding-right-1 { padding-right: 5px; }
.padding-right-2 { padding-right: 10px; }
.padding-right-3 { padding-right: 15px; }
.padding-right-4 { padding-right: 20px; }
.padding-right-5 { padding-right: 25px; }

.padding-bottom-0 { padding-bottom: 0; }
.padding-bottom-1 { padding-bottom: 5px; }
.padding-bottom-2 { padding-bottom: 10px; }
.padding-bottom-3 { padding-bottom: 15px; }
.padding-bottom-4 { padding-bottom: 20px; }
.padding-bottom-5 { padding-bottom: 25px; }

.padding-left-0 { padding-left: 0; }
.padding-left-1 { padding-left: 5px; }
.padding-left-2 { padding-left: 10px; }
.padding-left-3 { padding-left: 15px; }
.padding-left-4 { padding-left: 20px; }
.padding-left-5 { padding-left: 25px; }

/* Common padding classes */
.p-0 { padding: 0; }
.p-1 { padding: 5px; }
.p-2 { padding: 10px; }
.p-3 { padding: 15px; }
.p-4 { padding: 20px; }
.p-5 { padding: 25px; }

/* Padding for specific sides */
.pt-0 { padding-top: 0; }
.pt-1 { padding-top: 5px; }
.pt-2 { padding-top: 10px; }
.pt-3 { padding-top: 15px; }
.pt-4 { padding-top: 20px; }
.pt-5 { padding-top: 25px; }

.pr-0 { padding-right: 0; }
.pr-1 { padding-right: 5px; }
.pr-2 { padding-right: 10px; }
.pr-3 { padding-right: 15px; }
.pr-4 { padding-right: 20px; }
.pr-5 { padding-right: 25px; }

.pb-0 { padding-bottom: 0; }
.pb-1 { padding-bottom: 5px; }
.pb-2 { padding-bottom: 10px; }
.pb-3 { padding-bottom: 15px; }
.pb-4 { padding-bottom: 20px; }
.pb-5 { padding-bottom: 25px; }

.pl-0 { padding-left: 0; }
.pl-1 { padding-left: 5px; }
.pl-2 { padding-left: 10px; }
.pl-3 { padding-left: 15px; }
.pl-4 { padding-left: 20px; }
.pl-5 { padding-left: 25px; }

/* Common margin classes */
.m-0 { margin: 0; }
.m-1 { margin: 5px; }
.m-2 { margin: 10px; }
.m-3 { margin: 15px; }
.m-4 { margin: 20px; }
.m-5 { margin: 25px; }

/* Margin for specific sides */
.mt-0 { margin-top: 0; }
.mt-1 { margin-top: 5px; }
.mt-2 { margin-top: 10px; }
.mt-3 { margin-top: 15px; }
.mt-4 { margin-top: 20px; }
.mt-5 { margin-top: 25px; }

.mr-0 { margin-right: 0; }
.mr-1 { margin-right: 5px; }
.mr-2 { margin-right: 10px; }
.mr-3 { margin-right: 15px; }
.mr-4 { margin-right: 20px; }
.mr-5 { margin-right: 25px; }

.mb-0 { margin-bottom: 0; }
.mb-1 { margin-bottom: 5px; }
.mb-2 { margin-bottom: 10px; }
.mb-3 { margin-bottom: 15px; }
.mb-4 { margin-bottom: 20px; }
.mb-5 { margin-bottom: 25px; }

.ml-0 { margin-left: 0; }
.ml-1 { margin-left: 5px; }
.ml-2 { margin-left: 10px; }
.ml-3 { margin-left: 15px; }
.ml-4 { margin-left: 20px; }
.ml-5 { margin-left: 25px; }

.modal-footer {
	padding: 7px 26px;
	background-color: lightgray;
	color: white;
	
}


.frozeCol {
  max-height: 60vh;
  overflow: auto;
}


/* Use position: sticky to have it stick to the edge
 * and top, right, or left to choose which edge to stick to: */

.frozeCol thead th {
  position: -webkit-sticky; /* for Safari */
  position: sticky;
  top: 0;
}

.frozeCol thead .th_1 {
  position: -webkit-sticky; /* for Safari */
  position: sticky;
  top: 22px;
}

.frozeCol tbody th {
  position: -webkit-sticky; /* for Safari */
  position: sticky;
  left: 0;
}


.frozeCol thead tr:first-child th:first-child {
	position: -webkit-sticky; /* for Safari */
  position: sticky;
  left: 0 !important;
  top: 0 !important;
  z-index: 10 !important;
}

.frozeCol thead th {
	background:white;
}


.modal {
	background:rgba(0, 0, 0, 0.5);
}	

.modal-backdrop {
	opacity:0 !important;
}

#div_list_jenis .cari-item:hover, #div_list_kat .cari-item:hover {
	background:#f2f2fa;
	cursor:pointer;
}

#div_list_jenis .cari-item.active, #div_list_kat .cari-item.active {
	background:#8184bf;
	color:white;
	font-weight:bold;
}

.tooltip-inner {
    text-align: left;
	max-width:400px;
}

#div_list_trans tr.active td{
	
	background-color: #5156be;
	--bs-table-accent-bg: unset;
	color: white;
	font-weight: bold;
}

</style>
<div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Pengeluaran Kas
										Periode
			                            <select id='periode_kas' class='form-select' style='display:inline-block;width:auto' onchange="change_periode();">
			                               <?php
												 echo make_list_periode();
										   ?>
			                            </select>
									</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);"> </a></li>
                                            <li class="breadcrumb-item active"> </li>
                                        </ol>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                       <div class="row">
							 <div class="col-md-5" id='col-form-input'>
                                <div class="card">
                                    <div class="card-body">
										<div class="d-flex flex-wrap align-items-center mb-4">
                                                    <h3 class="card-title me-2"><span id='spn_mode'>Input</span> Data Pengeluaran</h3>
                                                    <div class="ms-auto">
                                                        <div class="btn-group btn-group-sm ms-auto" role="group" aria-label="Basic example">
															<button type="button" class="btn btn-primary" onclick='modal_master_jenis_kat(1);'>
																<i class="bx bx-table"></i> 
															Master Kategori</button>
														</div>
                                                    </div>
                                         </div>
										<div id='frm_pengeluaran'>
											<div class="row mb-2">
												<label for="" class="col-sm-4 control-label">Jenis Pengeluaran</label>
												<div class="col-sm-8">
													<select class="form-select" id="in_jenis" onchange='list_kategori();'>
														<option value="0">-pilih-</option>
													</select>
												</div>
											</div>
											<div class="row mb-2 row_kat">
												<label for="" class="col-sm-4 control-label">Kategori</label>
												<div class="col-sm-8">
													<select class="form-select" id="in_kategori" onchange='list_subkat();'>
														<option value="0">-pilih-</option>
													</select>
												</div>
											</div>
											<div class="row mb-2 row_subkat">
												<label for="" class="col-sm-4 control-label">Sub Kategori</label>
												<div class="col-sm-8">
													<select class="form-select" id="in_subkategori">
														<option value="0">-pilih-</option>
													</select>
												</div>
											</div>
											<div class="row mb-2">
												<label for="" class="col-sm-4 control-label">Tanggal</label>
												<div class="col-sm-8">
													 <input type="text" id='in_tanggal' class="form-control">
												</div>
											</div>
											<div class="row mb-2">
												<label for="" class="col-sm-4 control-label">Keterangan</label>
												<div class="col-sm-8">
													 <input type="text" id='in_pengeluaran' class="form-control">
												</div>
											</div>
											<div class="row mb-2">
												<label for="" class="col-sm-4 control-label">Nominal</label>
												<div class="col-sm-8">
													 <div class="input-group">
                                                            <div class="input-group-text">Rp</div>
                                                            <input type="text" id='in_nominal' class="form-control number_sep text-end">
                                                        </div>
												</div>
											</div>
											<div class='mode_add'>
												<div class="d-grid gap-2 mt-4 ">
													<button type="button" class="btn btn-primary waves-effect waves-light" onclick='save_pengeluaran();'>Simpan Pengeluaran</button>
												</div>
											</div>
											<div class='mode_edit' style='display:none;'>
												<div class="d-grid gap-2 mt-4 ">
													<button type="button" class="btn btn-primary waves-effect waves-light" onclick='save_pengeluaran();'>Edit Pengeluaran</button>
												</div>
													<div class='row'>
														<div class='col-10'>
															<div class="d-grid mt-2">
																<button type="button" class="btn btn-secondary waves-effect waves-light" onclick='edit_close();'>Tutup Mode Edit</button>
															</div>
														</div>
														<div class='col-2'>
															<div class="d-grid mt-2">
																<button type="button" class="btn btn-danger waves-effect waves-light" onclick='del_transaksi();' title='Hapus Data'>
																	<i class="bx bx-trash"></i>
																</button>
															</div>
														</div>
													</div>
											</div>
										</div>
									</div>
                                </div>
                            </div><!-- end col -->
							 <div class="col-md-7" id='col-history'>
                                <div class="card">
                                    <div class="card-body">
										<div class="d-flex flex-wrap align-items-center mb-1">
                                                    <h3 class="card-title me-2">Data Transaksi Pengeluaran</h3>
                                                    <div class="ms-auto">
                                                        <div class="btn-group btn-group-sm ms-auto" role="group" aria-label="Basic example">
															<!--<button type="button" class="btn btn-primary" onclick='modal_jadwal(1);'>
																<i class="bx bx-plus"></i> 
															-</button>-->
														</div>
                                                    </div>
													
                                         </div>
										 <div class="row mb-2">
														<label for="" class="col-sm-1 control-label">Tgl.</label>
														<div class="col-sm-5">
															<div class="input-group input-group-sm">
															  <input type="text" id='tgl_awal' class="form-control form-control-sm" onchange='load_list_trans();'>
															  <span class="input-group-text">s/d</span>
															  <input type="text" id='tgl_akhir' class="form-control form-control-sm" onchange='load_list_trans();'>
															</div>
														</div>
														<div class="col-sm-2"></div>
														<div class="col-sm-4">
															<div class="input-group input-group-sm">
																<input type="text" id='cari_transaksi' class="form-control form-control-sm" onkeyup='cari_transaksi();' placeholder="Cari ...">
																<button class="btn btn-xs btn-success" onclick="download_excel();" title="Download Data Excel (*.xls)"> 
																		<i class="bx bx-download"></i> .xls
																</button>
															</div>
														</div>
													</div>
										<div class="row mb-2">
											<div id='div_list_trans' class='frozeCol col-12' style='max-height:400px;'>
												
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

<div class="modal fade" id="modal_master_jenis_kat" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-table "></i><span class='ket'></span> Master Jenis & Kategori</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
			<div class='row'>
				 <div class="col-md-4" id='col-history'>
                                <div class="card">
                                    <div class="card-body">
										<div class="d-flex flex-wrap align-items-center mb-1">
                                                    <h3 class="card-title me-2">Master Jenis</h3>
                                                    <div class="ms-auto col-6">
                                                        <div class="input-group input-group-sm">
															<input type="text" id="cari_jenis" class="form-control form-control-sm" onkeyup="cari_jenis();" placeholder="Cari ...">
															<button class="btn btn-xs btn-success" onclick="modal_jenis(1);" title="Tambah Data">
															<i class="bx bx-plus"></i> Tambah
															</button>
														</div>
                                                    </div>
													
                                         </div>
										<div class="row mb-2">
											<div id='div_list_jenis' class='frozeCol col-12' style='max-height:400px;'>
												colom jenis
											</div>
										</div>
									</div>
                                </div>
                            </div><!-- end col -->
				 <div class="col-md-4" id='col-history'>
                                <div class="card">
                                    <div class="card-body">
										<div class="d-flex flex-wrap align-items-center mb-1">
                                                    <h3 class="card-title me-2">Master Kategori</h3>
                                                    <div class="ms-auto col-6">
                                                        <div class="input-group input-group-sm">
															<input type="text" id="cari_kat" class="form-control form-control-sm" onkeyup="cari_kat();" placeholder="Cari ...">
															<button class="btn btn-xs btn-success" onclick="modal_kat(1);" title="Tambah Data">
															<i class="bx bx-plus"></i> Tambah
															</button>
														</div>
                                                    </div>
													
                                         </div>
										<div class="row mb-2">
											<div id='div_list_kat' class='frozeCol col-12' style='max-height:400px;'>
												 <div class='tc'>Pilih Item Jenis untuk memunculkan daftar</div>
											</div>
											<div id='div_default_kat' class='frozeCol col-12' style='max-height:400px;'>
												 <div class='tc'>Pilih Item Jenis untuk memunculkan daftar</div>
											</div>
										</div>
									</div>
                                </div>
                            </div><!-- end col -->
				 <div class="col-md-4" id='col-history'>
                                <div class="card">
                                    <div class="card-body">
										<div class="d-flex flex-wrap align-items-center mb-1">
                                                    <h3 class="card-title me-2">Master Sub Kategori</h3>
                                                    <div class="ms-auto col-6">
                                                        <div class="input-group input-group-sm">
															<input type="text" id="cari_subkat" class="form-control form-control-sm" onkeyup="cari_subkat();" placeholder="Cari ...">
															<button class="btn btn-xs btn-success" onclick="modal_subkat(1);" title="Tambah Data">
															<i class="bx bx-plus"></i> Tambah
															</button>
														</div>
                                                    </div>
													
                                         </div>
										<div class="row mb-2">
											<div id='div_list_subkat' class='frozeCol col-12' style='max-height:400px;'>
												 <div class='tc'>Pilih Item Kategori untuk memunculkan daftar</div>
											</div>
											<div id='div_default_subkat' class='frozeCol col-12' style='max-height:400px;'>
												 <div class='tc'>Pilih Item Kategori untuk memunculkan daftar</div>
											</div>
										</div>
									</div>
                                </div>
                            </div><!-- end col -->
			</div>
	   </div>
	   
	   <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_jenis" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-check-double "></i><span class='ket_modal'></span> Jenis<h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
			<div class="row mb-2">
				<label for="" class="col-sm-3 control-label">Ref</label>
				<div class="col-sm-9">
					<input type="text" id="jns_ref" class="form-control">
				</div>
			</div>
			<div class="row mb-2">
				<label for="" class="col-sm-3 control-label">Nama</label>
				<div class="col-sm-9">
					<input type="text" id="jns_nama" class="form-control">
				</div>
			</div>
	   </div>
	   
	   <div class="modal-footer ">
			<button type="button" class="btn btn-primary" id='btn_save_user' onclick='save_jenis();'><i class='bx bx-save'></i> <span class='ket_modal'></span> Jenis</button>
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
			<button type="button" class="btn btn-danger btn_hapus" onclick='del_jenis();' title='Hapus Data'><i class='bx bx-trash'></i></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_kat" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-check-double "></i><span class='ket_modal'></span> Kategori<h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
			<div class="row mb-2">
				<label for="" class="col-sm-3 control-label">Jenis</label>
				<div class="col-sm-9">
					<input type="text" id="kat_jenis" class="form-control" disabled>
				</div>
			</div>
			<div class="row mb-2">
				<label for="" class="col-sm-3 control-label">Ref</label>
				<div class="col-sm-9">
					<input type="text" id="kat_ref" class="form-control">
				</div>
			</div>
			<div class="row mb-2">
				<label for="" class="col-sm-3 control-label">Nama</label>
				<div class="col-sm-9">
					<input type="text" id="kat_nama" class="form-control">
				</div>
			</div>
	   </div>
	   
	   <div class="modal-footer ">
			<button type="button" class="btn btn-primary" id='btn_save_user' onclick='save_kat();'><i class='bx bx-save'></i> <span class='ket_modal'></span> Kategori</button>
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
			<button type="button" class="btn btn-danger btn_hapus" onclick='del_kat();' title='Hapus Data'><i class='bx bx-trash'></i></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_subkat" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-check-double "></i><span class='ket_modal'></span> Kategori<h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
			<div class="row mb-2">
				<label for="" class="col-sm-3 control-label">Jenis</label>
				<div class="col-sm-9">
					<input type="text" id="subkat_jenis" class="form-control" disabled>
				</div>
			</div>
			<div class="row mb-2">
				<label for="" class="col-sm-3 control-label">Kategori</label>
				<div class="col-sm-9">
					<input type="text" id="subkat_kat" class="form-control" disabled>
				</div>
			</div>
			<div class="row mb-2">
				<label for="" class="col-sm-3 control-label">Ref</label>
				<div class="col-sm-9">
					<input type="text" id="subkat_ref" class="form-control">
				</div>
			</div>
			<div class="row mb-2">
				<label for="" class="col-sm-3 control-label">Nama</label>
				<div class="col-sm-9">
					<input type="text" id="subkat_nama" class="form-control">
				</div>
			</div>
	   </div>
	   
	   <div class="modal-footer ">
			<button type="button" class="btn btn-primary" id='btn_save_user' onclick='save_subkat();'><i class='bx bx-save'></i> <span class='ket_modal'></span> Kategori</button>
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
			<button type="button" class="btn btn-danger btn_hapus" onclick='del_subkat();' title='Hapus Data'><i class='bx bx-trash'></i></button>
      </div>
    </div>
  </div>
</div>


<script src="konfig_tu/js/j_inp_kas_out.js?v=<?php echo uniqid() ?>"></script>
<script src="js/gridstack-all.js"></script>