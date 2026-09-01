
<script type="text/javascript">
$(document).ready(function(){


});

</script>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                  <div class="card">
              <!-- start page title -->
          
                      
              
                          <div class="card-header">   
                                  <h4 class="card-title">Riwayat Dompet Digital</h4>     
                          </div>
                          <div class="card-body">
                            <form class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-lg-2">
                                      <div style="margin-left:10px;margin-top:5px;"> 
                                        <label class='form-label'>Tanggal Awal :</label>               
                                        <input type="text" class="form-control" id="tgl_awal" placeholder="" value="<?= date('Y-m-d') ?>">
                                      </div>
                                    </div>
                                    <div class="col-lg-2">
                                      <div style="margin-left:10px;margin-top:5px;"> 
                                        <label class='form-label'>Tanggal Akhir :</label>                
                                        <input type="text" class="form-control" id="tgl_akhir" placeholder ="" value="<?= date('Y-m-d') ?>">
                                      </div>
                                    </div>
                                </div>

                            </form>

                                <div style="margin-top:20px;margin-left:10px;">
                                    <button class="btn btn-primary waves-effect waves-light" id="popcrtaksx" onclick="flag();" style="margin-top:15px;margin-bottom:5px">
                                    <i class="bx bx-menu "></i>
                                    Tampilkan           
                                    </button>                
                                    <button class="btn btn-primary waves-effect waves-light" id="popcrtaksx" onclick="flag_masuk();" style="margin-top:15px;margin-bottom:5px;">
                                    <i class="bx bxs-download"></i>
                                    Export Uang Masuk
                                  </button>
                                    <button class="btn btn-primary waves-effect waves-light" id="popcrtaksx" onclick="flag_keluar();" style="margin-top:15px;margin-bottom:5px;">
                                    <i class="bx bxs-download"></i>
                                    Export Uang Keluar
                                    </button>
                                    
                                    <br>
                                    <div id="tampil_data"></div>
                                </div>
                                                              
                          </div>
                                

                  </div>
            </div>
        </div> <!-- container-fluid -->
 


<!-- <div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
              <div class="box col-md-12">
                <div class="box-inner">
                  <div class="box-header well">
                  <h2><i class="glyphicon glyphicon-info-sign"></i> Laporan Uang Masuk</h2>
                  <div class="box-icon">
                  <a href="#" class="btn btn-minimize btn-round btn-default"><i
                  class="glyphicon glyphicon-chevron-up"></i></a>
                  <a href="#" class="btn btn-close btn-round btn-default"><i
                  class="glyphicon glyphicon-remove"></i></a>
                  </div>
                  </div>
                  <div class="box-content row">
                  <div id="ar_sis" style="height:300px;">
                  <div id="tampil_masuk"></div>
                  </div>
                  </div>
                </div>
              </div>
            </div>


            <div class="row">
              <div class="box col-md-12">
                <div class="box-inner">
                  <div class="box-header well">
                  <h2><i class="glyphicon glyphicon-info-sign"></i> Laporan Uang Keluar</h2>
                  <div class="box-icon">
                  <a href="#" class="btn btn-minimize btn-round btn-default"><i
                  class="glyphicon glyphicon-chevron-up"></i></a>
                  <a href="#" class="btn btn-close btn-round btn-default"><i
                  class="glyphicon glyphicon-remove"></i></a>
                  </div>
                  </div>
                  <div class="box-content row">
                  <div id="ar_sis" style="height:300px;">
                  <div id="tampil_keluar"></div>
                  </div>
                  </div>
                </div>
              </div>
            </div>

        </div>
    </div>
</div> -->



            <div class="row">
              <div class="col-md-6" id='col-userkeu'>
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex flex-wrap align-items-center mb-4">
                                <h3 class="card-title me-2">        
                                  Laporan Uang Masuk</h3>         
                      </div>
                      <div id="tampil_masuk"></div>
                      <div id="tampil_default_masuk"></div>
									</div>
                  </div>
              </div><!-- end col -->

              <div class="col-md-6" id='col-userlain'>
                  <div class="card">
                      <div class="card-body">
                         <div class="d-flex flex-wrap align-items-center mb-4">
                             <h3 class="card-title me-2">             
                                  Laporan Uang Keluar</h3>     
                          </div> 
                          <div id="tampil_keluar"></div>
                          <div id="tampil_default_keluar"></div>
                      </div>
                  </div> 
                </div><!-- end col -->

            </div>
        </div>
    </div>
</div>


<script src="laporan_uangsaku/js/j_laporan_uangsaku.js?v=<?php echo uniqid(); ?>"></script>

