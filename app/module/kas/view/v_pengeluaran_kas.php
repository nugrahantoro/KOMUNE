<div class="main-content">
  <div class="page-content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-xl-12">
          <div class="card">
            <!-- start page title -->
            <div style="border-bottom: none;" class="card-header">
              <h4 class="card-title">Pengeluaran Kas</h4>
            </div>
            <div class="row">
              <div class="row">
                <div class="col-lg-2">
                  <div id="tahun"></div> <br>
                </div>
                <div class="col-lg-2">
                  <div id="jenjang"></div><br>
                </div>
              </div>
            </div>
            <div id="tampil_data" class="row">
              
            </div>
            
            <div class="card">
            <!-- start page title -->
            <div style="border-bottom: none;" class="card-header">
              <!-- <h4 class="card-title">Laporan Saldo Kas</h4> -->
            </div>
            <div class="row">
              <div class="col-lg-2">
                <label>&nbsp;&nbsp;&nbsp;&nbsp;Pengambilan Dari</label>
              </div>
              <div class="col-lg-2" id="ambil_dari_select">
                <select class="btn btn-light btn-lg" id="ambil_dari" style="border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:170px;height:40px;margin-left:10px;">
                  <option value="0">Pilih</option>
                </select>
              </div>
              <div class="col-lg-8">
                
              </div>

              <!-- ================================== -->

              <div class="col-lg-2" style="margin-top: 1%;">
                <label>&nbsp;&nbsp;&nbsp;&nbsp;Jenis Pengeluaran</label>
              </div>
              <div class="col-lg-2" style="margin-top: 1%;" id="ambil_jenis_select">
                <select class="btn btn-light btn-lg" id="ambil_jenis" style="border:1px solid #E1E1E1;font-family:tahoma;font-size:13px;width:170px;height:40px;margin-left:10px;">
                  <option value="0">Pilih</option>
                </select>
              </div>
              <div class="col-lg-8" style="margin-top: 1%;">
                <button class="btn btn-primary" onclick="tamtag();">Tambah</button>
              </div>

              <!-- ================================== -->

              <div class="col-lg-2" style="margin-top: 1%;">
                <label>&nbsp;&nbsp;&nbsp;&nbsp;Nama Pengeluaran</label>
              </div>
              <div class="col-lg-6" style="margin-top: 1%;">
                <input type="text" name="nama_pengeluaran" id="nama_pengeluaran" class="chk_item" style="width:100%;margin-left:10px;">
              </div>
              <div class="col-lg-4" style="margin-top: 1%;">
                
              </div>

              <!-- ================================== -->

              <div class="col-lg-2" style="margin-top: 1%;">
                <label>&nbsp;&nbsp;&nbsp;&nbsp;Deskripsi / Catatan</label>
              </div>
              <div class="col-lg-6" style="margin-top: 1%;">
                <input type="text" name="deskripsi" id="deskripsi" class="chk_item" style="width:100%;margin-left:10px;">
              </div>
              <div class="col-lg-4" style="margin-top: 1%;">
                
              </div>

              <!-- ================================== -->

              <div class="col-lg-2" style="margin-top: 1%;">
                <label>&nbsp;&nbsp;&nbsp;&nbsp;Pemasukan</label>
              </div>
              <div class="col-lg-3" style="margin-top: 1%;">
                <input type="text" name="pemasukan" id="pemasukan" class="chk_item" style="width:100%;margin-left:10px;background-color: #E1E1E1;" readonly>
              </div>
              <div class="col-lg-7" style="margin-top: 1%;">
                
              </div>

              <!-- ================================== -->

              <div class="col-lg-2" style="margin-top: 1%;">
                <label>&nbsp;&nbsp;&nbsp;&nbsp;Pengeluaran</label>
              </div>
              <div class="col-lg-3" style="margin-top: 1%;">
                <input type="text" name="pengeluaran" id="pengeluaran" class="chk_item" style="width:100%;margin-left:10px;background-color: #E1E1E1;" readonly>
              </div>
              <div class="col-lg-7" style="margin-top: 1%;">
                
              </div>

              <!-- ================================== -->

              <div class="col-lg-2" style="margin-top: 1%;">
                <label>&nbsp;&nbsp;&nbsp;&nbsp;Saldo</label>
              </div>
              <div class="col-lg-3" style="margin-top: 1%;">
                <input type="text" name="saldo" id="saldo" class="chk_item" style="width:100%;margin-left:10px;background-color: #E1E1E1;" readonly>
              </div>
              <div class="col-lg-7" style="margin-top: 1%;">
                
              </div>

              <!-- ================================== -->

              <div class="col-lg-2" style="margin-top: 1%;">
                <label>&nbsp;&nbsp;&nbsp;&nbsp;Nominal Dikeluarkan</label>
              </div>
              <div class="col-lg-3" style="margin-top: 1%;">
                <input type="hidden" name="id_pengeluaran" id="id_pengeluaran" class="chk_item" style="width:100%;margin-left:10px;">
                <input type="text" name="nominal" id="nominal" class="chk_item" style="width:100%;margin-left:10px;">
                <input type="hidden" name="nominal_hitung" id="nominal_hitung" class="chk_item" style="width:100%;margin-left:10px;">
              </div>
              <div class="col-lg-7" style="margin-top: 1%;">
                
              </div>

              <!-- ================================== -->

              <div class="col-lg-2" style="margin-top: 1%;">
                
              </div>
              <div class="col-lg-3" style="margin-top: 1%;margin-bottom: 1%;">
                &nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-primary" id="simpan" onclick="simpan_pengeluaran();">Simpan</button> 
                <button class="btn btn-primary" id="baru" onclick="baru_pengeluaran();">Baru</button> 
                <button class="btn btn-primary" id="edit" onclick="edit_pengeluaran();">Edit</button> 
                <button class="btn btn-primary" id="hapus" onclick="hapus_pengeluaran();">Hapus</button>
              </div>
              <div class="col-lg-7" style="margin-top: 1%;">
                
              </div>

              <!-- ================================== -->
              <div class="col-lg-2" style="margin-bottom: 250px;">
                
              </div>
              <div class="col-lg-2" style="margin-bottom: 250px;">
                <input type="text" class="form-control" id="e" style='border:1px solid #E1E1E1;font-family:tahoma;font-size:14px;width:185px;height:40px;margin-left:10px;'placeholder="Pilih tanggal awal">
              </div>
              <div class="col-lg-2" style="margin-bottom: 250px;">
                <input type="text" class="form-control" id="ee" style='border:1px solid #E1E1E1;font-family:tahoma;font-size:14px;width:185px;height:40px;margin-left:5px;'placeholder="Pilih tanggal akhir">
              </div>
              <div class="col-lg-2" style="margin-bottom: 250px;">
                <button class="btn btn-primary" id="tampilkan_tanggal_saja" onclick="tampil_tanggal();">Tampilkan Tanggal</button>
              </div>
              <div class="col-lg-2" style="margin-bottom: 250px;">
                <button class="btn btn-primary" id="tampilkan_keseluruhan" onclick="tampil_semua();">Tampilkan Semua</button>
              </div>
              <div class="col-lg-2" style="margin-bottom: 250px;">
                
              </div>

              <!-- ================================== -->

              <div class="col-lg-12">
                <div id="tampilkan_data"></div>
              </div>

            </div>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="tag_pem" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Form Tambah Jenis Pengeluaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div>
          <div class="row">
            <div class="col-lg-12">
              <input type="hidden" name="id_jenis_pengeluaran" id="id_jenis_pengeluaran">
              <input style="font-family:tahoma;font-size:12px;width:100%;" type="text" id="nama_jns_pengeluaran"/><br><br>
              <button class="btn btn-primary btn-xs" id="add" onclick="simpan_kat();"><i class="glyphicon glyphicon glyphicon-ok-sign"></i> Simpan</button>
              <button class="btn btn-primary btn-xs" id="edd_baru" onclick="baru_saja();"><i class="glyphicon glyphicon glyphicon-ok-sign"></i> Baru</button>
              <button class="btn btn-primary btn-xs" id="edd" onclick="edit_kat();"><i class="glyphicon glyphicon glyphicon-ok-sign"></i> Edit</button>
              <button class="btn btn-danger btn-xs" id="hdd" onclick="hapus_kat();"><i class="glyphicon glyphicon-trash"></i> Hapus</button>
            </div>
            <div class="col-md-12" id="tam_jenis_pengeluaran">

            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>


  <script src="kas/js/j_pengeluaran_kas.js?v=<?php echo uniqid(); ?>"></script>

  </script>