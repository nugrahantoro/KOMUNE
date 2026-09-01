<style>
  #div_cari_siswa {
		position: absolute;
		z-index: 999;
		background: white;
		border: lightgray solid 1px;
		padding: 15pt 20pt;
		left: 50%;
		border-radius: 3pt;
		transform: translateX(-50%);
		font-size: initial;
		font-weight: initial;
		text-align: left;
		white-space: nowrap;
	}
	
	#div_cari_siswa thead tr th:last-child {
	  min-width:40pt;
	}
</style>
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
         



            <div class="card-header">
              <h4 class="card-title">Tabungan Siswa</h4>
            </div>
   
            <div class="box-content row">


            <div style="margin-left:5px;padding:7px;border: 1px solid #E1E1E1;width:100%;background-color:#efeeee;">
            <!-- <button class="btn btn-primary btn-xs" id="popcr" style="margin-left:10px;">Cari</button> -->
           
            <button class="btn btn-primary btn-xs" id="popcr" onclick="cet();" style="margin-left:10px;">Cetak Transaksi</button>
            <button class="btn btn-primary btn-xs" id="popcr" onclick="cetakbuk_tab();" style="margin-left:10px;">Cetak Cover Buku</button>
            <!-- <button class="btn btn-primary btn-xs" id="popcr" onclick="cetakbuk_tab_lm();" style="margin-left:10px;">Buku baru</button>-->
            <button class="btn btn-primary btn-xs" id="popcr" onclick="cet_ilang();" style="margin-left:10px;">Cetak ulang</button>
            <!--<button class="btn btn-primary btn-xs" id="popcr" onclick="perb_cet();" style="margin-left:10px;">Perbaikan cetak</button>-->
            <!-- <input type="text" class="form-control" id="txt_cari_siswa" onkeyup='cari_siswa();' style='width:unset;display:inline;' placeholder="Nama Siswa">
            <div id='list_siswa_cari' class = 'frozeCol' style="max-height:300px;"></div> -->
            <div style=" display:inline-block;text-align:center;position:relative;">
								<input type="text" class="form-control" id="txt_cari_siswa" onkeyup='cari_siswa();' style='width:unset;display:inline;' placeholder="Nama Siswa / NIS">
								<div id='div_cari_siswa' class='' style="display:none;">
									<button type="button" class="btn btn-xs btn-outline-dark waves-effect waves-light" style="position: absolute;z-index: 99;right: 30pt;" onclick='hide_list_siswa_cari();'>X</button>
									<div id='list_siswa_cari' class = 'frozeCol' style="max-height:300px;"></div>
								</div>
							</div>
            </div>
            <div id="ar_sis" style="height:430px;">

            <div style="margin-right:10px;margin-top:10px;height:360px;width:300px;margin-left:10px;border:solid 1px #E1E1E1;overflow:scroll;overflow-x:hidden;overflow-y:hidden;float:left;">
            <div id="lbl" style="font-family:tahoma;font-size:12px;">ID</div>
            <input style="font-family:tahoma;font-size:12px;width:250px;" type="text" id="i" disabled/><p>
            <!--<div id="lbl" style="font-family:tahoma;font-size:12px;">NIS</div>
            <input style="font-family:tahoma;font-size:12px;width:250px;" type="text" id="a" disabled/><p> -->
            <div id="lbl" style="font-family:tahoma;font-size:12px;">Nama</div>
            <input style="font-family:tahoma;font-size:12px;width:250px;" type="text" id="b" disabled/><p>
            <!-- <div id="lbl" style="font-family:tahoma;font-size:12px;">Kelas</div>
            <input style="font-family:tahoma;font-size:12px;width:250px;" type="text" id="bkel" disabled/><p>-->
            <div id="lbl" style="font-family:tahoma;font-size:12px;">Transaksi</div>
            <select id='myselects_ths' style='border:1px #000 solid;font-family:tahoma;font-size:12px;width:200px;'>
            <option value="0" onclick="nole();">Pilihan</option>
            <option value="T" onclick="tabu();">Tabungan</option>
            <option value="P" onclick="penar();">Penarikan</option>
            </select><p>
            <div id="tam_tabu">
            </div>
            <div id="tot_sal">
            </div>
          
            </div>
            <div id="cek">
            </div>

            <div class="col-lg-12">
       
                <div class="card">
                  <div class="card-header">
										<div class='row'>
											<div class='col'>
												<h4 class="card-title">Data Tagihan</h4>
												<div id='blink_tagihan'></div>
											</div>
									
										</div>
                    </div><!-- end card header -->

                  <div class="card-body frozeCol pt-0">
                      <div id='tabel_tagihan'>
                      </div>
										<div id='tabel_data'>Pilih Data Siswa</div>
                  </div><!-- end card-body -->
                </div><!-- end card -->
              </div><!-- end col -->

            <p>
            <!-- <div id="keranjang">
            </div> -->

            <!-- <div id="kewajiban">
            </div> -->
            </div>
            </div>
          </div>
          
        </div>
        
      </div> 
    </div>
  </div>
</div>



<!-- <div id="bg"></div>
<div id="modal-kotak">
  <div id="bawah">
  <button id="tombol-tutup">CLOSE</button>
  </div>
  <div id="atas">
    <div id="ar_sis">
    <div id="popdalamcr">
    </div>	
    <div id="tam">
    </div>
    </div>
  </div>
</div> -->

<!-- <script>
    function show_list_siswa_cari()
    {
        $('#div_cari_siswa').slideDown('fast');
        $('#txt_cari_siswa').focus();
    }

    function cari_siswa(){
	txt = $('#txt_cari_siswa').val();
	par="#list_siswa_cari";
	cari=cariable(par,txt);
	show_list_siswa_cari();
	if(cari.stat=='no_result' || cari.count==0)
	{
		$(par+' .no_result').show(); 
	}
	else
	{
		$(par+' .no_result').hide();
	}
    }
</script> -->




<script src="transaksi_tabungan/js/j_laporan.js?v=<?php echo uniqid(); ?>"></script>