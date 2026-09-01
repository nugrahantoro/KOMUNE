<style>

</style>
<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Informasi & Pengumuman
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
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Komunitas Digital</a></li>
                                <li class="breadcrumb-item active">Informasi & Pengumuman</li>
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
                                <div class='col-md-6  tr'>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-success" onclick="modal_slide(event);">
                                            <i class="bx bx-plus"></i> Tambah Informasi
                                        </button>
                                    </div>
                                </div>
                                <div class='col-md-6'>
                                    <input class="form-control form-control-sm" type="text" id="cari_slide" placeholder="Cari judul..." onkeyup="cari_slide();">
                                </div>
                            </div>
                            <div class="row">
                                <div id='div_data_slide'></div>
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

<div class="modal fade" id="modal_slide" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-chalkboard "></i> <span id='ket_modal_slide'></span> Informasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id='modal_body1'>
                <form id="form_slide" enctype="multipart/form-data">
    
                    <div class="row mb-4">
                        <label class="col-sm-3 col-form-label">
                            Gambar <span>max. 2 MB</span>
                        </label>

                        <div class="col-sm-9">
                            <input 
                                type="file" 
                                class="form-control" 
                                id="frm_gambar"
                                name="gambar"
                                accept="image/png, image/jpg, image/jpeg"
                            >
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-3 col-form-label">
                            Judul
                        </label>

                        <div class="col-sm-9">
                            <input 
                                type="text" 
                                class="form-control" 
                                id="frm_judul"
                                placeholder="Masukkan judul informasi"
                            >
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label class="col-sm-3 col-form-label">
                            Isi Informasi
                        </label>

                        <div class="col-sm-9">
                            <textarea 
                                class="form-control" 
                                id="frm_informasi"
                                rows="8"
                                placeholder="Tulis informasi warga..."
                            ></textarea>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_save_slide" onclick='save_slide();'><i class='bx bx-save'></i> <span></span></button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id='btn_del_slide' onclick='del_slide();' title='Hapus Informasi'><i class='bx bx-trash'></i></button>
            </div>
        </div>
    </div>
</div>

<script src="mobileapp/js/j_data_mobileapp.js?v=<?php echo uniqid() ?>"></script>