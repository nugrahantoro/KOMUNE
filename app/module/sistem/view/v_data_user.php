<style>

</style>
<div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Manajemen Pengguna</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Sistem</a></li>
                                            <li class="breadcrumb-item active">Manajemen Pengguna</li>
                                        </ol>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                       <div class="row">
                            <div class="col-md-12" id='col-userkeu'>
                                <div class="card">
                                    <div class="card-body">
										<div class="d-flex flex-wrap align-items-center mb-4">
                                                    <h3 class="card-title me-2">Pengguna Sistem</h3>
                                                    <div class="ms-auto">
                                                        <div class="btn-group btn-group-sm ms-auto" role="group" aria-label="Basic example">
															<button type="button" class="btn btn-primary" onclick='modal_user(1);'>
																<i class="bx bx-user-plus"></i>
																Tambah Pengguna</button>
															<button type="button" class="btn btn-primary" onclick='modal_akses_jenjang();'>
																<i class=" bx bx-check-double "></i>
																Akses Wilayah</button>
														</div>
                                                    </div>
                                                </div>
										<div id='div_data_user'></div>
									</div>
                                </div>
                            </div><!-- end col -->

                            <!-- <div class="col-md-6" id='col-userlain'>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex flex-wrap align-items-center mb-4">
                                                    <h3 class="card-title me-2">User App Officer</h3>
                                                    <div class="ms-auto">
                                                        <div class="btn-group btn-group-sm ms-auto" role="group" aria-label="Basic example">
															<button type="button" class="btn btn-primary" onclick='modal_user2(1);'>
																<i class="bx bx-user-plus"></i>
																Pengguna Baru</button>
														</div>
                                                    </div>
                                                </div>
										<div id='div_data_user2'></div>
                                    </div>
                                </div>
                            </div> -->
							<!-- end col -->

                        </div>
					   
                    </div>
                    <!-- container-fluid -->
                </div>
                <!-- End Page-content -->
</div>

<div class="modal fade" id="modal_user" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="bx bx-user"></i> <span id='ket_modal'></span> Pengguna</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class='form' id='modal_body1'>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id='us_kode' placeholder="Kode User">
			  <label for="us_kode">ID Pengguna</label> 
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_nama" placeholder="Nama Lengkap<">
			  <label for="us_nama">Nama Pengguna</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_user" placeholder="Username">
			  <label for="us_user">Username</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_pass" placeholder="Password">
			  <label for="us_pass">Password</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_alamat" placeholder="Alamat">
			  <label for="us_alamat">Alamat</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_jabatan" placeholder="Jabatan">
			  <label for="us_jabatan">Role</label>
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='btn_save_user' onclick='save_user();'><i class='bx bx-save'></i> <span></span></button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-danger" id='btn_del_user' onclick='del_user();' title='Hapus Pengguna'><i class='bx bx-trash'></i></button>
      </div>
    </div>
  </div>
</div>

<!-- <div class="modal fade" id="modal_user2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="bx bx-user"></i> <span id='ket_modal2'></span> User Lain</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id='modal_body2'> 
        <div class='form'>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id='us_kode2' placeholder="Kode User">
			  <label for="us_kode2">Kode User</label> 
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_nama2" placeholder="Nama Lengkap">
			  <label for="us_nama2">Nama Lengkap</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_user2" placeholder="Username">
			  <label for="us_user2">Username</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_pass2" placeholder="Password">
			  <label for="us_pass2">Password</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_alamat2" placeholder="Alamat">
			  <label for="us_alamat2">Alamat</label>
			</div>
			<div class='row'>
				<div class='col-sm-6'>
					<div class="form-floating mb-2">
					  <select class="form-select" id='us_jabatan2' onchange='init_kelas();'>
						<option value='0'>-pilih-</option>
						<?php
							$res = db_query("SELECT * FROM tbl_jabatan WHERE id_jabatan IN ( 2,3,10 ) ORDER BY id_jabatan ASC");
							while($dt=mysqli_fetch_array($res))
							{
								echo '<option value="'.$dt['id_jabatan'].'">'.$dt['nama_jabatan'].'</option>';
							}
						?>
					  </select>
					  <label for="us_jabatan2">Jabatan</label>
					</div>
				</div>
				<div class='col-sm-6'>
					<div class="form-floating mb-2 ">
					  <select class="form-select" id='us_kelas'>
						<option value='0'>-pilih-</option>
						<?php
							$ta = db_get("SELECT id_tahunajar FROM data_tahunajar 
											WHERE aktif='1' LIMIT 1");
							$res = db_query("SELECT * FROM data_kelas 
								WHERE id_sesi='".$ta."'");
							while($dt=mysqli_fetch_array($res))
							{
								echo '<option value="'.$dt['id_kelas'].'">'.$dt['nama_kelas'].'</option>';
							}
						?>
					  </select>
					  <label for="us_kelas">Kelas</label>
					</div>
				</div>
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='btn_save_user2' onclick='save_user2();'><i class='bx bx-save'></i> <span></span></button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-danger" id='btn_del_user2' onclick='del_user();' title='Hapus User'><i class='bx bx-trash'></i></button>
      </div>
    </div>
  </div>
</div> -->

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
                         <label class="form-label" for="formrow-email-input">Nama Pengguna</label>
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

<script src="sistem/js/j_data_user.js?v=<?php echo uniqid() ?>"></script>