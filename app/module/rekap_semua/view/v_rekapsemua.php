
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
                
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18">Laporan Absensi</h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Lapor Absensi</a></li>
                                    <li class="breadcrumb-item active">Absensi</li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>

            <div class="row">
                <div class="col-md-12" id='col-userkeu'>
                    <div class="card" >
                        <div class="card-body" style="margin-bottom:100px;">

                            <div>
                                <div class="row">
                                        <div class="col-lg-2">
                                            <div style="margin-left:10px;margin-top:5px;"> 
                                                <label class='form-label'>Tanggal Awal :</label>               
                                                <input type="text" class="form-control" id="e" placeholder="" value="2023-06-01">
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div style="margin-left:10px;margin-top:5px;"> 
                                                <label class='form-label'>Tanggal Akhir :</label>                
                                                <input type="text" class="form-control" id="ee" placeholder ="" value="<?= date('Y-m-d') ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div style="margin-left:10px;margin-top:32px;"> 
                                            <!-- <button class="btn btn-primary" class="btn btn-primary waves-effect waves-light"onclick="flag();">Tampilkan Semua Data</button> -->
                                            <button class="btn btn-primary waves-effect waves-light" id="popcrtaksx" onclick="flag();">Tampilkan Semua Data</button>
                                            </div>
                                        </div>
                                </div>

                            </div>
                                

                            <hr>
                            

                            <!-- Nav tabs -->
                            
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" onclick='tab_dinas_luar();'>
                                    <a class="nav-link active" data-bs-toggle="tab" href="#data_default" role="tab" aria-selected="false">
                                        <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                        <span class="d-none d-sm-block">Tampil Data</span>
                                        
                                    </a>
                                    
                                </li>
                            </ul>
                            <div class="tab-content p-3 text-muted" style="border: 1px var(--bs-gray-400) solid;border-top: unset;border-radius: 0px 0px 5px 5px;">
                                <div class="row">
                                    <div class="col-xl-12" id='data_default'>
                                    <div id="tampil_data"></div> 
                                        
                                    </div><br>
                                </div><!-- end col -->
                            </div>
                            <!-- Tab panes -->
 
                          
                        </div>
                    </div>
                </div><!-- end col -->

            </div>


        </div>
    </div>

</div> <!-- container-fluid -->



<script src="rekap_semua/js/semua_rekap.js?v=<?php echo uniqid() ?>"></script>         