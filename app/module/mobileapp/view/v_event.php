<style>

</style>
<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Manajemen Event
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
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dahsboard Mobile App</a></li>
                                <li class="breadcrumb-item active">Manajemen Event</li>
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
                        <li class="nav-item" onclick='tab_event();'>
                            <a class="nav-link active" data-bs-toggle="tab" href="#data_event" role="tab" aria-selected="false">
                                <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                <span class="d-none d-sm-block">Data Event</span>
                            </a>
                        </li>
                        <li class="nav-item" onclick='tab_laporan();'>
                            <a class="nav-link" data-bs-toggle="tab" href="#data_laporan_absen" role="tab" aria-selected="false">
                                <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                <span class="d-none d-sm-block">Laporan Absensi</span>
                            </a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content p-3 text-muted" style="border: 1px var(--bs-gray-400) solid;border-top: unset;border-radius: 0px 0px 5px 5px;">
                        <div class="tab-pane active" id="data_event" role="tabpanel">
                            <div class="row">
                                <div class="col-xl-12" id='div_evnt'>
                                    <div class="card">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0 flex-grow-1">Data Event</h4>
                                            <div class="flex-shrink-0">
                                                <div class="d-flex flex-wrap gap-2 mb-0 my-n1">
                                                    <button class='btn btn-sm btn-success' onclick='modal_event(event);'>
                                                                <i class="bx bx-plus"></i> Baru
                                                            </button>
                                                    <input class="form-control form-control-sm" type="text" id="cari_event" placeholder="Cari kelas/event..." style='width:160pt;' onkeyup='cari_event();'>
                                                </div>
                                            </div>
                                        </div><!-- end card header -->

                                        <div class="card-body text-muted frozeCol" id='div_list_event' style='max-height: 700px;overflow-y: auto;'>

                                        </div><!-- end card-body -->
                                    </div><!-- end card -->
                                </div><!-- end col -->

                            </div>
                        </div>

                        <div class="tab-pane p-2" id="data_laporan_absen" role="tabpanel">
                            <div class="row">
                                <div class="col-xl-5" id='div_item'>
                                    <div class="card">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0 flex-grow-1">Laporan Absensi Event</h4>
                                            <div class="flex-shrink-0">
                                                <div class="d-flex flex-wrap gap-2 mb-0 my-n1">
                                                     
                                                    <input class="form-control form-control-sm" type="text" id="cari_nm_event" placeholder="Cari..." style='width:160pt;' onkeyup='cari_nm_event();'>
                                                </div>
                                            </div>
                                        </div><!-- end card header -->

                                        <div class="card-body text-muted frozeCol" id='div_list_lap_event' style='max-height: 700px;overflow-y: auto;'>

                                        </div><!-- end card-body -->
                                    </div><!-- end card -->
                                </div><!-- end col -->

                                <div class="col-xl-7" id='div_item_absensi'>
                                    <div class="card">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0 flex-grow-1">Detail Laporan Absensi</h4>
                                            <div class="flex-shrink-0">
                                                <div class="d-flex flex-wrap gap-2 mb-0 my-n1">
                                                    <input class="form-control form-control-sm" type="text" id="cari_absensi" placeholder="Cari..." style='width:260pt;' onkeyup='cari_absensi();'>
                                                </div>
                                            </div>
                                        </div><!-- end card header -->

                                        <div class="card-body text-muted frozeCol" id='div_list_absensi' style='max-height: 700px;overflow-y: auto;'>

                                        </div><!-- end card-body -->
                                    </div><!-- end card -->
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

<div class="modal fade" id="modal_event" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-chalkboard "></i> <span id='ket_modal_event'></span> Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id='modal_body1'>
                <form>
                    <div class="row mb-4">
                        <label for="frm_id_event" class="col-sm-3 col-form-label">Pilih Kelas</label>
                        <div class="col-sm-9" id="list_kelas">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_event" class="col-sm-3 col-form-label">Nomor TID Alat</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="frm_tid" placeholder="">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_event" class="col-sm-3 col-form-label">Nama Event</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="frm_nama" placeholder="">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_event" class="col-sm-3 col-form-label">Deskripsi Event</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="frm_keterangan" placeholder="">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_event" class="col-sm-3 col-form-label">Tanggal Mulai</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" id="frm_tgl_mulai" placeholder="">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_event" class="col-sm-3 col-form-label">Jam Mulai</label>
                        <div class="col-sm-9">
                            <input type="time" class="form-control" id="frm_jam_mulai" placeholder="">
                        </div>
                    </div>
                     <div class="row mb-4">
                        <label for="frm_id_event" class="col-sm-3 col-form-label">Tanggal Selesai</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" id="frm_tgl_selesai" placeholder="">
                        </div>
                    </div>
                    <div class="row mb-4">
                        <label for="frm_id_event" class="col-sm-3 col-form-label">Jam Selesai</label>
                        <div class="col-sm-9">
                            <input type="time" class="form-control" id="frm_jam_selesai" placeholder="">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btn_save_event" onclick='save_event();'><i class='bx bx-save'></i> <span></span></button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id='btn_del_event' onclick='del_event();' title='Hapus Item'><i class='bx bx-trash'></i></button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_cetak" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="div_qrcode">
            
        </div>
    </div>
</div>

<script src="mobileapp/js/j_event.js?v=<?php echo uniqid() ?>"></script>