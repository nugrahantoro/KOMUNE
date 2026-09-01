<style>

</style>
<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Manajemen Officer</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dahsboard Mobile App</a></li>
                                <li class="breadcrumb-item active">Manajemen Officer</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

           <div class="row">
                <hr>
                <div class="col-md-6" id='col-userkeu'>
                    <h5>User</h5>
                    <div class="card">
                        <div class="card-body">
							<div class="d-flex flex-wrap align-items-center mb-4">
                                <div class='col-md-6  tr'>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-success" onclick="modal_user(event,1);">
                                            <i class="bx bx-plus"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                                <div class='col-md-6'>
                                    <input class="form-control form-control-sm" type="text" id="cari_user" placeholder="Cari nama..." onkeyup="cari_user();">
                                </div>
                            </div>
                            <div class="row">
                                <div id='div_data_user' style='max-height: 600px;overflow-y: auto;'></div>
                            </div>
						</div>
                    </div>
                </div><!-- end col -->

                <div class="col-md-6" id='col-userkeu'>
                    <h5>Akses Officer</h5>
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center mb-4">
                                <div class='col-md-6  tr'>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-success" onclick="modal_akses(event,1);">
                                            <i class="bx bx-plus"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                                <div class='col-md-6'>
                                    <input class="form-control form-control-sm" type="text" id="cari_akses" placeholder="Cari user akses..." onkeyup="cari_akses();">
                                </div>
                            </div>
                            <div class="row">
                                <div id='div_data_akses' style='max-height: 400px;overflow-y: auto;'></div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h5>Menu Officer</h5>
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center mb-4">
                                <div class='col-md-6  tr'>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-success" onclick="modal_menu(event,1);">
                                            <i class="bx bx-plus"></i> Tambah
                                        </button>
                                    </div>
                                </div>
                                <div class='col-md-6'>
                                    <input class="form-control form-control-sm" type="text" id="cari_menu" placeholder="Cari menu..." onkeyup="cari_menu();">
                                </div>
                            </div>
                            <div class="row">
                                <div id='div_data_menu' style='max-height: 400px;overflow-y: auto;'></div>
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

<div class="modal fade" id="modal_user" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-chalkboard "></i> <span id='ket_modal_user'></span> User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id='modal_body1'>
                <form>
                    <div class="row mb-4">
                        <label for="frm_id_tipe" class="col-sm-3 col-form-label">Mode</label>
                        <div class="col-sm-9">
                            <select id="frm_mode" class="form-select" onchange="mode_user();">
                                <option value='0'>-pilih-</option>
                                <option value="1">Standar</option>
                                <option value="2">Wali Kelas</option>
                                <option value="3">Absen</option>
                                <option value="4">Jurnal Guru</option>
                                <option value="5">Kantin</option>
                                <option value="6">Absen / Wali Kelas</option>
                                <option value="7">Absen / Wali Kelas / Jurnal Guru</option>
                            </select>
                        </div>
                    </div>
                     <div class="row mb-4">
                        <label for="frm_id_tipe" class="col-sm-3 col-form-label">Jenjang</label>
                        <div class="col-sm-9">
                            <select id="us_jenjang" class="form-select" onchange="ubah();">
                                <option value=''>-pilih-</option>
                                <?php
                                    $res = db_query("SELECT * FROM data_jenjang");
                                    while($dt=mysqli_fetch_array($res))
                                    {
                                        echo '<option value="'.$dt['id_jenjang'].'">'.$dt['nama_jenjang'].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_tipe" class="col-sm-3 col-form-label">Jenis User</label>
                        <div class="col-sm-9">
                            <select id="us_jabatan2" class="form-select" onchange="ubah();">
                                <option value='0'>-pilih-</option>
                                <option value="2">Guru</option>
                                <option value="3">Kepala Sekolah</option>
                                <option value="7">Karyawan</option>
                                <option value="11">BK</option>
                                <option value="10">Kantin</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_tipe" class="col-sm-3 col-form-label">Kode User</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="us_kode2" placeholder="Kode User (tanpa ex)">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_tipe" class="col-sm-3 col-form-label">Nama Lengkap</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="us_nama2" placeholder="Nama Lengkap">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_tipe" class="col-sm-3 col-form-label">Username</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="us_user2" placeholder="Username">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_tipe" class="col-sm-3 col-form-label">Password</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="us_pass2" placeholder="Password">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_tipe" class="col-sm-3 col-form-label">Alamat</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="us_alamat2" placeholder="Alamat">
                        </div>
                    </div>
                    <div id="walikelas">
                        <div class="row mb-4">
                            <label for="frm_id_tipe" class="col-sm-3 col-form-label">Kelas</label>
                            <div class="col-sm-9">
                                <select class="form-select" id='us_kelas'>
                                    
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="absen">
                        <div class="row mb-4">
                            <label for="frm_id_tipe" class="col-sm-3 col-form-label">ID Kartu</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="us_no_kartu" placeholder="ID Kartu">
                            </div>
                        </div>
                    </div>
                    <div id="jurnalguru">
                        <div class="row mb-4">
                            <label for="frm_id_mapel" class="col-sm-3 col-form-label">Mata Pelajaran</label>
                            <div class="col-sm-9" id="div_mata_pelajaran">
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="frm_id_kelas_ampu" class="col-sm-3 col-form-label">Kelas diampu</label>
                            <div class="col-sm-9" id="div_kelas_ampu">
                            </div>
                        </div>
                    </div>
                    <div id="kantin">
                        <div class="row mb-4">
                            <label for="frm_id_tipe" class="col-sm-3 col-form-label">Kode Alat</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="us_kode_alat" placeholder="Kode Alat (contoh jika alat > 1 isikan seperti berikut: 'ABC','DEF')">
                            </div>
                        </div> 
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_save_user" onclick='save_user();'><i class='bx bx-save'></i> <span></span></button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id='btn_del_user' onclick='del_user();' title='Hapus Informasi'><i class='bx bx-trash'></i></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_akses" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-chalkboard "></i> <span id='ket_modal_akses'></span> User Akses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id='modal_body1'>
                <form>
                    <div class="row mb-4">
                        <label for="frm_id_user_akses" class="col-sm-3 col-form-label">Pilih User</label>
                        <div class="col-sm-9">
                            <select id="frm_user" class="form-select">
                                <option value="0">-- pilih --</option>
                                <?php
                                    $query = "
                                        SELECT
                                            id,
                                            nama_us
                                        FROM
                                            users_log_all
                                        WHERE
                                            jenis_user IN ( 2, 3, 4, 5, 10, 11 ) 
                                            AND db_name = '".ABS."'
                                        ORDER BY
                                            id_user_log_all DESC
                                    ";
                                    $res = db_query_core($query);
                                    while($dtx=mysqli_fetch_array($res)){ ?>
                                        <option value="<?php echo $dtx['id']; ?>"><?php echo $dtx['nama_us']; ?></option>
                                    <?php }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_user_akses" class="col-sm-3 col-form-label">Pilihan</label>
                        <div class="col-sm-9">
                            <select id="frm_user_pilihan" class="form-select" onchange="ubah_pilihan();">
                                <option value="0">-- pilih --</option>
                                <option value="1">Default</option>
                                <option value="2">Custom</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4" id="menu_default">
                        <label for="frm_id_menu_user_default" class="col-sm-3 col-form-label">Pilih Menu Default</label>
                        <div class="col-sm-9">
                            <select id="frm_menu_user" class="form-select">
                                <option value="0">-- pilih --</option>
                                <?php
                                    $query = "SELECT tipe FROM tbl_menu_app GROUP BY tipe";
                                    $res = db_query_core($query);
                                    while($dt=mysqli_fetch_array($res))
                                    { ?>
                                        <option value="<?php echo $dt['tipe']; ?>"><?php echo $dt['tipe']; ?></option>
                                    <?php }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4" id="menu_custom">
                        <label for="frm_id_menu_user" class="col-sm-3 col-form-label">Pilih Menu Custom</label>
                        <div class="col-sm-9">
                            <select id="frm_menu_user" class="form-select">
                                <option value="0">-- pilih --</option>
                                <?php
                                    $query = "SELECT * FROM tbl_menu_app ORDER By tipe";
                                    $res = db_query_core($query);
                                    while($dt=mysqli_fetch_array($res))
                                    { ?>
                                        <option value="<?php echo $dt['id_menu_app']; ?>"><?php echo $dt['nama_menu']." (".$dt['tipe'].")"; ?></option>
                                    <?php }
                                ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_save_menu" onclick='save_akses();'><i class='bx bx-save'></i> <span></span></button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_menu" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-chalkboard "></i> <span id='ket_modal_menu'></span> Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id='modal_body1'>
                <form>
                	<div class="row mb-4">
                        <label for="frm_id_nama" class="col-sm-3 col-form-label">Nama Menu</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="frm_nama_menu" placeholder="">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_urutan" class="col-sm-3 col-form-label">Urutan</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="frm_urutan" placeholder="">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_tipe" class="col-sm-3 col-form-label">Tipe</label>
                        <div class="col-sm-9">
                            <select id="frm_tipe" class="form-select">
                            	<option value="guru">Guru</option>
                            	<option value="kepala sekolah">Kepala Sekolah</option>
                            	<option value="kantin">Kantin</option>
                            	<option value="bk">BK</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_tipe" class="col-sm-3 col-form-label">Aktif</label>
                        <div class="col-sm-9">
                            <select id="frm_aktif" class="form-select">
                            	<option value="ya">Ya</option>
                            	<option value="tidak">Tidak</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_menu" class="col-sm-3 col-form-label">Icon</label>
                        <div class="col-sm-9">
                            <input type="file" class="form-control" id="frm_icon" accept="image/png, image/jpg, image/jpeg">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_save_menu" onclick='save_menu();'><i class='bx bx-save'></i> <span></span></button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id='btn_del_menu' onclick='del_menu();' title='Hapus Informasi'><i class='bx bx-trash'></i></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_detail_user" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-chalkboard "></i> <span id='ket_modal_detail_user'></span> Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id='modal_body1'>
                <form>
                    <div id="div_kode">
                        <span>Mode: <input type="text" class="form-control" id="res_mode" disabled=""></span><hr>
                    </div>
                    <div class="row mb-4">
                        <label for="res_id_kode" class="col-sm-4 col-form-label">Kode User</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="res_kode" disabled="">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="res_id_nama" class="col-sm-4 col-form-label">Nama Lengkap</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="res_nama" disabled="">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="res_id_username" class="col-sm-4 col-form-label">Username</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="res_username" disabled="">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="res_id_password" class="col-sm-4 col-form-label">Password</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="res_password" disabled="">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_alamat" class="col-sm-4 col-form-label">Alamat</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="res_alamat" disabled="">
                        </div>
                    </div>
                    <div id="detail_walikelas">
                        <div class="row mb-4">
                            <label for="frm_id_kelas" class="col-sm-4 col-form-label">Kelas</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="res_kelas" disabled="">
                            </div>
                        </div>
                    </div>
                    <div id="detail_absen">
                        <div class="row mb-4">
                            <label for="frm_id_kartu" class="col-sm-4 col-form-label">ID Kartu</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="res_kartu" disabled="">
                            </div>
                        </div>
                    </div>
                    <div id="detail_jurnalguru">
                        <div class="row mb-4">
                            <label for="res_id_mapel" class="col-sm-4 col-form-label">Mata Pelajaran</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="res_mapel" disabled="">
                            </div>
                        </div>
                        <div class="row mb-4">
                            <label for="res_id_kelas_ampu" class="col-sm-4 col-form-label">Kelas diampu</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="res_ampu" disabled="">
                            </div>
                        </div>
                    </div>
                    <div id="detail_kantin">
                        <div class="row mb-4">
                            <label for="res_id_alat" class="col-sm-4 col-form-label">Kode Alat</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="res_kode_alat" disabled="">
                            </div>
                        </div> 
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="mobileapp/js/j_officer.js?v=<?php echo uniqid() ?>"></script>