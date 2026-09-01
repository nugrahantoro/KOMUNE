<script type="text/javascript">
  $(document).ready(function() {

    var modald = document.getElementById("myModale");


    var spanempat = document.getElementsByClassName("close")[0];



  });
</script>
<script type="text/javascript">
  $(document).ready(function() {
    // Get the modal
    var modal = document.getElementById("myModale");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

  });
</script>

<script type="text/javascript">
  $(document).ready(function() {
    // Get the modal
    var modalinputtrans = document.getElementById("myModalinputtrans");
    var modalkelompok = document.getElementById("myModalkelompok");
    var modalimport = document.getElementById("myModaldata");
    var modal = document.getElementById("myModal");
    var modals = document.getElementById("myModals");
    var modalimport = document.getElementById("myModaldata");


    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    span.onclick = function() {
      modalinputtrans.style.display = "none";
    }




  });
</script>


<div id="myModaldata" class="modal">
  <!-- Modal content -->
  <div style="height:800px;">

    <div id="konpig_data"></div>
  </div>

</div>

<div id="myModalinputtrans" class="modal" style="margin-left:400px;">
  <div class="modal-content" style="width:40%;">
    <div class="modal-header">
      <span class="close">&times;</span>

      <i class="glyphicon glyphicon-edit"></i>
      Edit kategori laporan

    </div>

    <div class="modal-body">
      <div class="control-group">
        <input type="hidden" id="id_jenis_transaksi" />
        <div id="kategori_lap"></div>
      </div>
    </div>
  </div>
</div>


<div class="main-content">

  <div class="page-content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-xl-12">
          <div class="card">
            <!-- start page title -->



            <div class="card-header">
              <h4 class="card-title">Laporan Tabungan Harian</h4>
            </div>
            <div class="card-body">
              <form class="needs-validation" novalidate>
                <div class="row">
                  <div class="col-lg-2">
                    <!-- <div id="tam_tahun"></div> <br>
                    <div id="tampil_kelas"></div><br>
                    <div id="tampil_trans"></div> -->
                    
                    <div>
                      <input type="text" class="form-control" id="e" style='border:1px solid #E1E1E1;font-family:tahoma;font-size:14px;width:185px;height:40px;margin-left:5px;'placeholder="Pilih tanggal awal">
                    </div>

                  </div>
                  <div class="col-lg-2">
                    <!-- <div id="jenjang"></div><br>
                    <div id="tampil_user"></div><br>
                    <div id="tampil_metode"></div> -->
                    
                    <div style="margin-left:10px;">
                      <input type="text" class="form-control" id="ee" style='border:1px solid #E1E1E1;font-family:tahoma;font-size:14px;width:180px;height:40px;' placeholder="Pilih tanggal akhir">
                    </div>
                  </div>
                </div>


              </form>

              <div style="margin-top:10px;margin-left:5px">
                <button class="btn btn-primary waves-effect waves-light"onclick="laporan_harian_tab();" style="margin-top:15px;margin-bottom:5px">Tampil Tabungan</button>
                <!-- <button class="btn btn-primary waves-effect waves-light" id="popcrtaksx" onclick="flag_kategori();" style="margin-top:15px;margin-bottom:5px;">Tampilkan penerimaan kategori</button>
                <button class="btn btn-primary waves-effect waves-light" id="popcrtaksx" onclick="flag_estimasi();" style="margin-top:15px;margin-bottom:5px;">Tampilkan estimasi pendapatan</button>
                <button class="btn btn-primary waves-effect waves-light" id="popcrtaksx" onclick="tamtag();" style="margin-top:15px;margin-bottom:5px;">Buka laporan tagihan</button>
                <button class="btn btn-primary waves-effect waves-light" id="popcrtaksx" onclick="cethar_pdf();" style="display:none;">Cetak pdf</button> -->
                <br>
                
                <!-- <div id="tampil_data"></div> -->
                <div id="data_tabungan"></div>
                <!-- <div id="tampil_default"></div> -->
                <p><br><p><br>
              </div>

           



            </div>
            <!-- <div id="data_absen"> -->
            <!-- data -->

          </div>
        </div>
      </div> <!-- container-fluid -->
    </div>
  </div>
</div>




<!-- <div class="main-content">

    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12" id='col-userkeu'>
                  <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" onclick='flag();'>
                      <a class="nav-link active" data-bs-toggle="tab" href="#data_kelas_siswa" role="tab" aria-selected="false">
                        <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                        <span class="d-none d-sm-block">Tampilkan Pennerima</span>
                      </a>
                    </li>
                    <li class="nav-item" onclick='flag_kategori();'>
                      <a class="nav-link " data-bs-toggle="tab" href="#mst_jns_transaksi" role="tab" aria-selected="true">
                        <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                        <span class="d-none d-sm-block">Tampilkan Penerima Kategori </span>
                      </a>
                    </li>
                    <li class="nav-item" onclick="flag_estimasi();">
                      <a class="nav-link" data-bs-toggle="tab" href="#konfig_bayar" role="tab" aria-selected="false">
                        <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                        <span class="d-none d-sm-block">Tampilkan Estimasi Pendapatan</span>
                      </a>
                    </li>
                    <li class="nav-item" onclick="tamtag();">
                      <a class="nav-link" data-bs-toggle="tab" href="#konfig_bayar" role="tab" aria-selected="false">
                        <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                        <span class="d-none d-sm-block">Buka Laporan Tagihan</span>
                      </a>
                    </li>
                  </ul>
                </div>
            </div> 
          </div>
    </div>
</div> -->




<div class="modal fade" id="aksitransi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-check-double "></i> Edit Kategori Laporan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="aksitrans">
        </div>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="kelompok_lapi" tabindex="1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-check-double "></i> Data Kelompok Laporan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div id="keldua">
              <input type="hidden" id="id_kelompok_laporan" />
              <input id="nama_kelompok_lap" type="text" style="margin-bottom:2px;width: 90%;" /><br>
              <button id="btnaddkelompoklap" onclick="simpan_kel_lap();" class="btn btn-primary btn-xs">Simpan</button>
              <button id="btneditkelompoklap" onclick="simpan_edit_kel_lap();" class="btn btn-primary btn-xs">Edit</button>
            </div>
            <hr>
            <div id="kelompok_lap"></div>
          </div>
          <p>
        </div>
      </div>
    </div>
  </div>
</div>



<!-- <div class="modal fade" id="konpig" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-check-double "></i> Kategori Laporan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="section_kategori_laporan">
        </div>
      </div>
    </div>
  </div>
</div> -->



<div class="modal fade" id="konpig" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class=" bx bx-check-double "></i> Kategori Laporan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class='form continer'>
          <div class='row'>
            <div id="section_kategori_laporan">
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
        <h5 class="modal-title" id="exampleModalLabel">Tagihan Pembayaran Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form>
          <div class="row">
            <div class="col-md-12" id="tam_tahun_tag">

            </div>
            <div class="col-md-12" id="jenjang_tag">

            </div>
            <div class="col-md-12" id="tam_kelas_tag">

            </div>
            <div class="col-md-12" id="tam_trans">

            </div>
            <div class="col-md-12" id="tamp_laporan">

            </div>

          </div>
        </form>
      </div>
    </div>
  </div>
</div>






<div class="modal fade" id="tag_lap" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Pembayaran Siswa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12" id="per_tahun">

          </div>

        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="kat_jatuh_tempo" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Bulan Transaksi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div id="bul"></div>
          <p>
          <div id="kecuali"></div>

        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="laporan_bul" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Laporan Tagihan Semua Tahun Ajaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12" id="tampil_data_tag">


          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- percobaan -->

<!-- 
<div class="main-content">

	<div class="page-content">
		<div class="container-fluid">
      <div class="row">
          <div class="col-md-12" id='col-userkeu'>
            <ul class="nav nav-tabs" role="tablist">
              <li class="nav-item" onclick='flag();'>
                <a class="nav-link active" data-bs-toggle="tab" href="#data_kelas_siswa" role="tab" aria-selected="false">
                  <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                  <span class="d-none d-sm-block">Tampilkan Pennerima</span>
                </a>
              </li>
              <li class="nav-item" onclick='flag_kategori();'>
                <a class="nav-link " data-bs-toggle="tab" href="#mst_jns_transaksi" role="tab" aria-selected="true">
                  <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                  <span class="d-none d-sm-block">Tampilkan Penerima Kategori </span>
                </a>
              </li>
              <li class="nav-item" onclick="flag_estimasi();">
                <a class="nav-link" data-bs-toggle="tab" href="#konfig_bayar" role="tab" aria-selected="false">
                  <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                  <span class="d-none d-sm-block">Tampilkan Estimasi Pendapatan</span>
                </a>
              </li>
              <li class="nav-item" onclick="tamtag();">
                <a class="nav-link" data-bs-toggle="tab" href="#konfig_bayar" role="tab" aria-selected="false">
                  <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                  <span class="d-none d-sm-block">Buka Laporan Tagihan</span>
                </a>
              </li>
            </ul>
          </div>
      </div>
    </div>
  </div>
</div> -->


<script src="laporan_tabungan_harian/js/j_laporan.js?v=<?php echo uniqid(); ?>"></script>

</script>