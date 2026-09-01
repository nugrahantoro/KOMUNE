<style>

</style>
<div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Periode & Wilayah</h4>
                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Sistem</a></li>
                                            <li class="breadcrumb-item active">Periode & Wilayah</li>
                                        </ol>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                       <div class="row">
                            <div class="col-md-6" id='col-userkeu'>
                                <div class="card">
                                    <div class="card-body">
										<div class="d-flex flex-wrap align-items-center mb-4">
                                                    <h3 class="card-title me-2">Periode Operasional</h3>
                                                    <div class="ms-auto">
                                                        <div class="btn-group btn-group-sm ms-auto" role="group" aria-label="Basic example">
															<button type="button" class="btn btn-primary" onclick='modal_ta(1);'>
																<i class="bx bx-user-plus"></i>
																Tambah Periode</button>
														</div>
                                                    </div>
                                                </div>
										<div id='div_data_ta'></div>
									</div>
                                </div>
                            </div><!-- end col -->

                            <div class="col-md-6" id='col-userlain'>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap align-items-center mb-4">
                                                    <h3 class="card-title me-2">Wilayah Komunitas</h3>
                                                    <div class="ms-auto">
                                                        <div class="btn-group btn-group-sm ms-auto" role="group" aria-label="Basic example">
															<button type="button" class="btn btn-primary" onclick='modal_jenjang(1);'>
																<i class="bx bx-user-plus"></i>
																Tambah Wilayah
															</button>
														</div>
                                                    </div>
                                                </div> 
										<div id='div_data_jenjang'></div>
                                    </div>
                                </div> 
                            </div><!-- end col -->

                        </div>
					   
                    </div>
                    <!-- container-fluid -->
                </div>
                <!-- End Page-content -->
</div>

<div class="modal fade" id="modal_ta" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">
          <i class="bx bx-user"></i>
          <span id='ket_modal'></span> Periode
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class='form' id='modal_body1'>
          <div class="mb-3">
            <label class="form-label" for="formrow-firstname-input">Nama Periode</label>
            <input type="text" class="form-control" id="nm_ta" placeholder="">
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3" id='div_tgl_mulai_ta'>
                <label class="form-label" for="formrow-email-input">Tanggal Mulai</label>
                <input type="text" class="form-control" id="tgl_mulai_ta" placeholder="">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="formrow-password-input">Tanggal Selesai</label>
                <input type="text" class="form-control" id="tgl_selesai_ta" placeholder ="">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id='btn_save_ta' onclick='save_ta();'>
            <i class='bx bx-save'></i>
            <span></span>
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="button" class="btn btn-danger" id='btn_del_ta' onclick='del_ta();' title='Hapus Periode'>
            <i class='bx bx-trash'></i>
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_jenjang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="bx bx-user"></i> <span id='ket_modal2'></span> Wilayah</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id='modal_body2'> 
        <div class='form'>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="nama_jenjang" placeholder="Nama Wilayah">
			  <label for="nama_jenjang">Nama Wilayah</label>
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='btn_save_jenjang' onclick='save_jenjang();'><i class='bx bx-save'></i> <span></span></button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-danger" id='btn_del_jenjang' onclick='del_jenjang();' title='Hapus Wilayah'><i class='bx bx-trash'></i></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_akses_jenjang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-check-double "></i> Akses Wilayah</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class='form continer'>
			<div class='row'>                                 
                 <div class="col-md-6">
                    <div class="mb-3">
                         <label class="form-label" for="formrow-email-input">Periode</label>
                         <select class="form-select" id='aj_kode' onchange='data_akses_jenjang();'></select>
                     </div>
                </div>                     
                 <div class="col-md-6">
                    <div class="mb-3">
                         <label class="form-label" for="formrow-email-input3">Wilayah</label>
                         <select class="form-select" id='aj_jenjang'></select>
                     </div>
                </div>
			</div>
			<div class="mb-3 tc">
				<button id='save_akses_jenjang' class='btn btn-success btn-sm w-lg' onclick='save_akses_jenjang();'>
					<i class="bx bx-add-to-queue"></i> Tambah Akses
				</button>
			</div>
			<div id='div_data_jenjang' class='frozeCol'></div>
		</div>
		<div>
		
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" style='width:100%;' data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script src="sistem/js/j_ta_jenjang.js?v=<?php echo uniqid() ?>"></script>