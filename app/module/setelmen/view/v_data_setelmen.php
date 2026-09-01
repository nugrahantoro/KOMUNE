<style>

</style>
<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Setelmen Dana</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dahsboard Setelmen</a></li>
                                <li class="breadcrumb-item active">Setelmen Dana</li>
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
                                <table>
                                	<tr>
                                		<td> 
                                			<input type="date" id="tgl_awal" placeholder="format: yyyy-mm-dd" value="<?php echo date("Y-m"); ?>-01" class="form-control rounded border"> 
                                		</td>
                                		<td> s.d. </td>
                                		<td> 
                                			<input type="date" id="tgl_akhir" placeholder="format: yyyy-mm-dd" value="<?php echo date("Y-m-d"); ?>" class="form-control rounded border"> 
                                		</td>
                                		<td></td>
                                		<td> <button type="button" class="btn btn-primary waves-effect waves-light w-lg" onclick="proses_task();"> Proses </button> </td>
                                	</tr>
                                </table>
                            </div>

                            <!-- Nav tabs -->
							<ul class="nav nav-tabs" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" data-bs-toggle="tab" href="#data_setelmen_pembayaran" role="tab" aria-selected="false">
										<span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
										<span class="d-none d-sm-block">Setelmen Pembayaran</span>
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link " data-bs-toggle="tab" href="#data_setelmen_saku" role="tab" aria-selected="true">
										<span class="d-block d-sm-none"><i class="far fa-user"></i></span>
										<span class="d-none d-sm-block">Setelmen Uangku</span>
									</a>
								</li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content p-3 text-muted" style="border: 1px var(--bs-gray-400) solid;border-top: unset;border-radius: 0px 0px 5px 5px;">
								<div class="tab-pane active" id="data_setelmen_pembayaran" role="tabpanel">
									<div class="row">
										<div id='div_data_setelmen'></div>
									</div>
								</div>
								<div class="tab-pane p-2" id="data_setelmen_saku" role="tabpanel">
									<div class='row'>
										<div id='div_data_setelmen_saku'></div>
									</div>
								</div>
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

<script src="setelmen/js/j_data_setelmen.js?v=<?php echo uniqid() ?>"></script>