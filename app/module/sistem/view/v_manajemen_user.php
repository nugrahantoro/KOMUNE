<style>
.tooltip-inner {
    text-align: left;
}
</style>
<div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Manajemen User</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Sistem</a></li>
                                            <li class="breadcrumb-item active">Manajemen User</li>
                                        </ol>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                       <div class="d-lg-flex">
                            <div class="chat-leftsidebar card">
                                <div class="p-3 px-4 border-bottom">
                                    <div class="d-flex align-items-start ">
                                        <div class="flex-grow-1">
                                            <h5 class="font-size-16 mb-1">
											<i class="bx bx-user"></i>
											<a href="#" class="text-dark">Data User </a></h5>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-2">
                                    <div class="search-box position-relative">
                                        <input id='q_user' type="text" class="form-control rounded border" placeholder="Cari..." onkeyup='cari_user()'>
                                        <i class="bx bx-search search-icon"></i>
                                    </div>
                                </div>

                                <div class="chat-leftsidebar-nav">
                                    <div class="tab-content">
                                        <div class="tab-pane show active" id="chat">
                                            <div class="chat-message-list" data-simplebar="init" style='height:45vh;'>
												<div class="simplebar-wrapper" style="margin: 0px;">
													<div class="simplebar-height-auto-observer-wrapper">
														<div class="simplebar-height-auto-observer"></div>
													</div>
													<div class="simplebar-mask">
														<div class="simplebar-offset" style="right: -20px; bottom: 0px;">
															<div class="simplebar-content-wrapper" style="height: 100%; padding-right: 20px; padding-bottom: 0px; overflow: hidden scroll;">
															<div class="simplebar-content" style="padding: 0px;">
																<div class="pt-3">
																	<ul id='col_data_user' class="list-unstyled chat-list">
																		<?php
																			$sql = "SELECT
																						id,
																						nama_us,
																						username,
																						`password`,
																						alamat,
																						IF
																						( jenis_user = 2, CONCAT( jabat, ' ', IFNULL( nama_kelas, '' )), jabat ) AS jabat, tas 
																					FROM
																						users_log
																						LEFT JOIN data_kelas ON akses_kelas = id_kelas 
																					WHERE
																						jenis_user IN (1,3,10,11)
																					ORDER BY
																						nama_us";
																			$res = db_query($sql);
																			while($dt=mysqli_fetch_array($res))
																			{
																				echo '
																					<li class="cari-item item_user" id_user="'.$dt['id'].'" onclick="user_click(this);">
																						<a href="javascript: void(0);">
																							<div class="d-flex align-items-start">
																								
																								<div class="flex-shrink-0 user-img align-self-center me-3 cari-q">
																									'.$dt['id'].'
																								</div>
																								
																								<div class="flex-grow-1 overflow-hidden">
																									<h5 class="text-truncate font-size-14 mb-1 cari-q">'.$dt['nama_us'].'</h5>
																									<p class="text-truncate mb-0 cari-q">'.$dt['jabat'].'</p>
																								</div>
																							</div>
																						</a>
																					</li>
																				';
																			}
																		?> 
																		<li class="user_no_result" style='display:none;'>
																			<a href="javascript: void(0);">
																				<div class="d-flex align-items-start">
																					<div class="flex-shrink-0 user-img align-self-center me-3 cari-q"></div>
																					<div class="flex-grow-1 overflow-hidden">
																						<p class="text-truncate mb-0 cari-q">Tidak ada data</p>
																					</div>
																				</div>
																			</a>
																		</li>
																	</ul>
																</div>
															</div>
														</div>
														</div>
													</div>
													<div class="simplebar-placeholder" style="width: auto; height: 683px;"></div>
												</div>
												<div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
													<div class="simplebar-scrollbar" style="transform: translate3d(0px, 0px, 0px); display: none;"></div>
												</div>
												<div class="simplebar-track simplebar-vertical" style="visibility: visible;">
													<div class="simplebar-scrollbar" style="height: 25px; transform: translate3d(0px, -2px, 0px); display: block;"></div>
												</div>
											</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- end chat-leftsidebar -->

                            <div id='root_akses' class="w-100 user-chat mt-4 mt-sm-0 ms-lg-1">
                                <div class="card">
                                    <div class="p-3 px-lg-4 border-bottom">
                                        <div class="row">
                                            <div class="col-xl-7 col-7">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1">
                                                        <h5 class="font-size-16 mb-1">
														<i class="bx bx-check-double"></i>
														<a href="#" class="text-dark">Akses Menu </a> <span id='cur_user'></span></h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
									<div class='p-2'>
										<div class="search-box position-relative  akses-elem" style="display:none;">
											<input id='q_akses' type="text" class="form-control rounded border" placeholder="Cari..." onkeyup='cari_akses()'>
											<i class="bx bx-search search-icon"></i>
										</div>
									</div>
                                    <div class="chat-conversation p-3 px-2" data-simplebar="init">
										<div id ='div_akses' class='p-2 pl-5' style="padding-left: 2rem !important;">
											<div class='tc p-5' style='font-size: initial;'>
												Klik nama User untuk mengubah akses menu
											</div>
										</div>
									</div>
									<div class='p-3 akses-elem'  style="display:none;">
										<button type="button" class="btn btn-primary waves-effect waves-light w-lg" onclick='save_akses();'>
                                             <i class="bx bx-save font-size-16 align-middle me-2"></i> Simpan Akses Menu
                                         </button>
									</div>
                                </div>
                            </div>
                            <!-- end user chat -->
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
        <h5 class="modal-title" id="exampleModalLabel"><i class="bx bx-user"></i> <span id='ket_modal'></span> User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class='form'>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id='us_kode' placeholder="Kode User">
			  <label for="us_kode">Kode User</label> 
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_nama" placeholder="">
			  <label for="us_nama">Nama Lengkap</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_user" placeholder="">
			  <label for="us_user">Username</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_pass" placeholder="">
			  <label for="us_pass">Password</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_alamat" placeholder="">
			  <label for="us_alamat">Alamat</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_jabatan" placeholder="">
			  <label for="us_jabatan">Jabatan</label>
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='btn_save_user' onclick='save_user();'><i class='bx bx-save'></i> <span></span></button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-danger" id='btn_del_user' onclick='del_user();' title='Hapus User'><i class='bx bx-trash'></i></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_user2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="bx bx-user"></i> <span id='ket_modal2'></span> User Lain</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class='form'>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id='us_kode2' placeholder="Kode User">
			  <label for="us_kode2">Kode User</label> 
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_nama2" placeholder="">
			  <label for="us_nama2">Nama Lengkap</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_user2" placeholder="">
			  <label for="us_user2">Username</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_pass2" placeholder="">
			  <label for="us_pass2">Password</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="us_alamat2" placeholder="">
			  <label for="us_alamat2">Alamat</label>
			</div>
			<div class='row'>
				<div class='col-sm-6'>
					<div class="form-floating mb-2">
					  <select class="form-select" id='us_jabatan2' onchange='init_kelas();'>
						<option value='0'>-pilih-</option>
						<?php
							$res = db_query("SELECT * FROM tbl_jabatan ORDER BY id_jabatan ASC");
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
</div>

<div class="modal fade" id="modal_akses_jenjang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-check-double "></i> Akses Jenjang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class='form continer'>
			<div class='row'>                                 
                 <div class="col-md-6">
                    <div class="mb-3">
                         <label class="form-label" for="formrow-email-input">Nama User</label>
                         <select class="form-select" id='aj_kode' onchange='data_akses_jenjang();'></select>
                     </div>
                </div>                     
                 <div class="col-md-6">
                    <div class="mb-3">
                         <label class="form-label" for="formrow-email-input3">Jenjang</label>
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

<script src="sistem/js/j_manajemen_user.js?v=<?php echo uniqid() ?>"></script>