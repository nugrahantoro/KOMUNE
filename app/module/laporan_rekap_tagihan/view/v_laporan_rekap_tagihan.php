<?php
$hari_ini = date("Y-m-d");
$tgl_pertama = date('Y-m-01', strtotime($hari_ini));
$tgl_terakhir = date('Y-m-t', strtotime($hari_ini));
?>

<!-- KONTEN -->
<div class="main-content">
  <div style="min-height: 1000px;" class="page-content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-xl-12">
          <div class="card">
            <!-- start page title -->
            <div style="border-bottom: none;" class="card-header">
              <h4 class="card-title">Reminder Tagihan</h4>
            </div>
            <div class="row">
				<div  class="col-md-12">
					<div class="row align-items-end g-3 px-4">

            <div class="col-lg-2">
                <label class="form-label fw-semibold mb-2">
                    Periode
                </label>

                <div id="tampil_tahun">
                    <!-- TAHUN -->
                </div>
            </div>

            <div class="col-lg-2">
                <label class="form-label fw-semibold mb-2">
                    Wilayah
                </label>

                <div id="tampil_jenjang">
                    <!-- JENJANG -->
                </div>
            </div>

            <div class="col-lg-2">
                <label class="form-label fw-semibold mb-2">
                    Wilayah / Grup
                </label>

                <div id="tampil_kelas">
                    <!-- KELAS -->
                </div>
            </div>

            <div class="col-lg-2">
                <label class="form-label fw-semibold mb-2">
                    Tanggal Awal
                </label>

                <input 
                    type="text"
                    class="form-control"
                    id="tanggal_awal"
                    placeholder="<?php echo $tgl_pertama; ?>"
                >
            </div>

            <div class="col-lg-2">
                <label class="form-label fw-semibold mb-2">
                    Tanggal Akhir
                </label>

                <input 
                    type="text"
                    class="form-control"
                    id="tanggal_akhir"
                    placeholder="<?php echo $tgl_terakhir; ?>"
                >
            </div>

            <div class="col-lg-2">
                <label class="form-label fw-semibold mb-2 text-white">
                    Action
                </label>

                <button 
                    class="btn btn-primary w-100"
                    onclick="proses_tagihan()"
                >
                    <i class="bx bx-search-alt"></i>
                    Proses
                </button>
            </div>

            <hr>
            <div class="col-lg-2">
                <button 
                    class="btn btn-success w-100"
                    onclick="modal_format_surat()">
                    <i class="bx bx-file"></i>
                    Edit Format Surat
                </button>
            </div>
            <hr>

        </div>
            </div>
            <!-- REKAP -->
            <div class="col-xl-12">
              <!-- card -->
              <div class="card mt-2">
                <!-- HEAD -->
                <div class="card-header">
                  <div class="d-flex flex-wrap align-items-center">
                    <h5 class="card-title me-2">HASIL REKAP TAGIHAN</h5>
                    <!-- <div class="ms-auto">
                        <div>
                          <button type="button" class="btn btn-soft-secondary btn-sm">
                            EXCEL
                          </button>
                        </div>
                      </div> -->
                  </div>

                  <!-- BODY -->
                  <div class="table-responsive">
                    <div id="hasil_rekap">
                      <!-- HASIL -->
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- KONTEN END -->


<!-- Modal Madul -->

<div class="modal fade" id="modal_format_surat" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="bx bx-file"></i> <span id='ket_modal2'></span> Format Surat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id='modal_body2'> 
        <div class='row'>
			<div class="mb-3 col-6">
                <label for="example-text-input" class="form-label">Nomor Surat</label>
                <input class="form-control" type="text" value="" id="srt_no_surat">
            </div>
			<div class="mb-3 col-3">
                <label for="example-text-input" class="form-label">Tempat</label>
                <input class="form-control" type="text" value="" id="srt_tempat">
            </div>
			<div class="mb-3 col-3">
                <label for="example-text-input" class="form-label">Tanggal</label>
                <input class="form-control" type="text" value="" id="srt_tanggal">
            </div>
		</div>
        <div class='row'>
			<div class="mb-3 col-6">
                <label for="example-text-input" class="form-label">Lampiran</label>
                <input class="form-control" type="text" value="" id="srt_lampiran">
            </div>
			<div class="mb-3 col-6">
                <label for="example-text-input" class="form-label">Perihal</label>
                <input class="form-control" type="text" value="" id="srt_perihal">
            </div>
		</div>
        <div class='row'>
			<div class="mb-3 col-12">
                <label for="example-text-input" class="form-label">Paragraf 1</label>
                <textarea class="form-control" id="srt_paragraf_1"></textarea>
            </div>
		</div>
        <div class='row'>
			<div class="mb-3 col-12">
                <label for="example-text-input" class="form-label">Paragraf 2</label>
                <textarea class="form-control" id="srt_paragraf_2"></textarea>
            </div>
		</div>
        <div class='row'>
			<div class="mb-3 col-12">
                <label for="example-text-input" class="form-label">Paragraf 3</label>
                <div id="srt_paragraf_3"></div>
            </div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='btn_save_user2' onclick='save_format_surat()'><i class='bx bx-save'></i> Simpan Perubahan</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal End --> 

<script src="laporan_rekap_tagihan/js/j_laporan_rekap_tagihan.js?v=<?php echo uniqid(); ?>"></script>

<!-- ckeditor -->
<script src="libs/@ckeditor/ckeditor5-build-classic/build/ckeditor.js"></script>
