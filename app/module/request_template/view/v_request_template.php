<?php  ?>
<div class="main-content">
     <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                                <!-- end col -->
                <div class="col-xl-12">
                            <!-- card -->
                            <div class="card card-h-100">
                                <!-- card body -->
                                <div class="card-body">


                                    <div class="row">
                                        <div class="box col-md-12">
                                                <div class="box-inner">
                                                            <div class="box-header well">
                                                                    <h3><i class="glyphicon glyphicon-tasks"></i> Request Template</h3>
                                                                    <div class="box-icon">
                                                                            <a href="#" class="btn btn-minimize btn-round btn-default">
                                                                            <i class="glyphicon glyphicon-chevron-up"></i></a>
                                                                    </div>
                                                            </div>
                                                            <div class="box-content row">
                                                                    <div class="col-md-12">
                                                                            <div id="tam_null"></div>
                                                                            <div id="kelas"></div>
                                                                            <div style="float:right;">
                                                                            <input id="sech" type="hidden" style="width:250px; display: inline-block; border: 1px solid #ccc; border-radius:4px;box-sizing: border-box;height:25px;" placeholder=" Tuliskan"/>
                                                                                <!-- <div id="sech2"></div> -->
                                                                                <!-- <button class="btn btn-primary btn-xs" style="margin-left:5px;margin-top:5px;margin-bottom:5px;" onclick="cari();" ><i class="glyphicon glyphicon-search"></i> Cari</button> -->
                                                                            </div>
                                                                            <div  id="tampil_data"></div>
                                                                    </div>
                                                            </div>
                                                </div>
                                        </div>
                                    </div>

                                    <div class="card-header align-items-center d-flex">
                                        <div class="flex-shrink">
                                            <div class="d-flex flex-wrap gap-2 mb-0 my-n1">
                                                <span>Page</span>
                                                <select class="form-select form-select-sm mb-0 my-n1" id='id_level' onchange='sk_load_kelas();' style='width:unset;margin-bottom:unset !important;'></select>
                                                <button class='btn btn-sm btn-success' onclick='tampil_modal_tambah_request_template();'>
                                                    <i class="bx bx-plus"></i> Tambah
                                                </button>
                                            </div>
                                        </div>
                                    </div><!-- end card header -->

                                        <div  id="data_request_template"></div>
                               
                                </div>
                            </div>
                </div>
            </div>
        </div>
     </div>
</div>



<div class="modal fade" id="modal_kelas" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-chalkboard "></i> <span id='ket_modal_kelas'></span> Tambah Request Template</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4" id='modal_body1'>
				<form>
					<div class="row mb-4">
						<label for="frm_id_kelas" class="col-sm-3 col-form-label">Pesan</label>
						<div class="col-sm-9">
                        <textarea name="pesan" class="form-control" id="pesan" rows="1"></textarea>
						</div>
					</div>
					<div class="row mb-4">
						<label for="frm_id_kelas" class="col-sm-3 col-form-label">Nama Template</label>
						<div class="col-sm-9">
                        <textarea name="pesan" class="form-control" id="pesan" rows="1"></textarea>
						</div>
					</div>
					<div class="row mb-4">
						<label for="frm_id_kelas" class="col-sm-3 col-form-label">Client ID</label>
						<div class="col-sm-9">
                        <textarea name="pesan" class="form-control" id="pesan" rows="1"></textarea>
						</div>
					</div>
					<div class="row mb-4">
						<label for="frm_id_kelas" class="col-sm-3 col-form-label">Project ID</label>
						<div class="col-sm-9">
                        <textarea name="pesan" class="form-control" id="pesan" rows="1"></textarea>
						</div>
					</div>

				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick='btn_simpan_request_template();'><i class='bx bx-save'></i> <span>Simpan</span></button>
			</div>
		</div>
	</div>
</div>

<div id="modal_edit_request_template" class="modal">
<div class="modal-dialog">
	<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-chalkboard "></i> <span id='ket_modal_kelas'></span> Edit Request Template</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
		<div class="modal-body">
			<form style="margin-bottom: 15px;">
				<div class="form-group" hidden>
				    <label for="id_edit">ID</label>
				    <input type="text" class="form-control" name="id_edit" id="id_edit">
				</div>
				<div class="form-group" <?= $_SESSION['jabat'] == 'Super Admin' ? '' : 'hidden' ?>>
				    <label for="nama_template_edit">Pesan</label>
				    <input type="text" class="form-control" name="pesan_edit" id="pesan_edit">
				</div>
				<div class="form-group" <?= $_SESSION['jabat'] == 'Super Admin' ? '' : 'hidden' ?>>
				    <label for="status_edit">Status</label>
				    <select class="form-control" id="status_template_edit" name="status_template_edit">
					    <option value="0">Submit</option>
					    <option value="1">On Process</option>
					    <option value="2">Approved</option>
					    <option value="3">Rejected</option>
				    </select>
				</div>
				<div class="form-group" <?= $_SESSION['jabat'] == 'Super Admin' ? '' : 'hidden' ?>>
				    <label for="nama_template_edit">Nama Template</label>
				    <input type="text" class="form-control" name="nama_template_edit" id="nama_template_edit">
				</div>
				<div class="form-group" <?= $_SESSION['jabat'] == 'Super Admin' ? '' : 'hidden' ?>>
				    <label for="client_id_edit">Client ID</label>
				    <input type="text" class="form-control" name="client_id_edit" id="client_id_edit">
				</div>
				<div class="form-group" <?= $_SESSION['jabat'] == 'Super Admin' ? '' : 'hidden' ?>>
				    <label for="project_id_edit">Project ID</label>
				    <input type="text" class="form-control" name="project_id_edit" id="project_id_edit">
				</div>
			 	<button type="button" onclick="btn_edit_request_template()" class="btn btn-primary">Submit</button>
			</form>
		</div>
	</div>
</div>
</div>



<script src="request_template/js/request_template.js?v=<?php echo uniqid() ?>"></script> 