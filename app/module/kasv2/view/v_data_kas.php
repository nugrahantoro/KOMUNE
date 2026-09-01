<style>
	.fr {
		float: right;
	}
	
	.tr {
		text-align: right;
	}

	.frozeCol thead th {
		background: white;
		top: 0px;
		text-align: center;
		z-index: 9;
	}

	#div_list_kelas thead th {
		top: -20px;
	}

	.nav-tabs-custom .nav-item .nav-link::after {
		height: 2px;
	}

	

	#prs_photo_preview {
		width: 150px;
		height: 150px;
		display: inline-block;
		background-size: contain;
		background-position: center;
		background-repeat: no-repeat;
	}

	#tsiswa_ortu .card-body {
		padding: 0.5rem 1rem;
	}

	.col-form-label.required:after {
		content: "*";
		color: red;
	}
	
	.table .act_item {
		opacity: 0.0;
		transition: opacity 0.5s ease;
		white-space: nowrap;
	}
	
	.table tr:hover .act_item {
		 opacity: 1;
	}
	
	#modal_add_trans table input[type="checkbox"] {
		scale: 1.3;
	}
	
	.filter_tanggal input 
	{
		background-color:white !important;
	}
	
	#div_jurnal {
		border: 1px solid lightgray;
		padding: 1rem;
		padding-top: 0;
		max-height: 400px;
	}
	
	#div_jurnal_pemasukan th,	#div_jurnal_pengeluaran th {
		text-align:center;
	}
	
</style>

<?php
	function make_list_periode()
	{
		// Array of Indonesian month names
		$month_names_id = [
			1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
			7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
		];

		// Set the starting month and year
		$starting_month = 7; // July
		$starting_year = 2023;

		// Get the current month and year
		$current_month = date('n');
		$current_year = date('Y');

		$opts='';
		// Loop through the months and years
		for ($year = $current_year; $year >= $starting_year; $year--) {
			$start = ($year === $starting_year) ? $starting_month : 1;
			$end = ($year === $current_year) ? $current_month : 12;

			for ($month = $end; $month >= $start; $month--) {
				$month_name = $month_names_id[$month];
				$formatted_month = sprintf("%02d", $month);
				$formatted_year = $year;
				
				$formatted_date = "$formatted_year-$formatted_month";
				$display_date = "<option value='$formatted_date'>$month_name $formatted_year</option>";
				$opts.= $display_date . "";
			}
		}
		
		return $opts;
	}
?>

<div class="main-content">

	<div class="page-content">
		<div class="container-fluid">

			<!-- start page title -->
			<div class="row">
				<div class="col-12">
					<div class="page-title-box d-sm-flex align-items-center justify-content-between">
						<h4 class="mb-sm-0 font-size-18">Data Kas 
						</h4>
						<div class="page-title-right">
							<ol class="breadcrumb m-0">
								<li class="breadcrumb-item"><a href="javascript: void(0);">TU</a></li>
								<li class="breadcrumb-item active">Konfigurasi Pembayaran</li>
							</ol>
						</div>

					</div>
				</div>
			</div>
			<!-- end page title -->

			<div class="row">
			
				<div class="col-md-5" id='col-userkeu'>

					<div class="card">
					  <div class="card-body">
						<div class="d-flex flex-wrap align-items-center mb-4">
						  <h3 class="card-title me-2">
							Data Jenis Kas
						  </h3>
						  <div class="ms-auto">
							<div class="btn-group btn-group-sm ms-auto" role="group" aria-label="Basic example">
							  <button type="button" class="btn btn-primary" onclick="modal_jns_kas(1);">
								<i class="bx bx-plus"></i> Tambah </button>
							</div>
						  </div>
						</div>
						<div id="div_list_kas">
						
						</div>
					  </div>
					</div>

				</div><!-- end col -->
				
				<div class="col-md-7" id='col-transaksi'>

					<div class="card">
					  <div class="card-body">
						<div class="d-flex flex-wrap align-items-center mb-4">
						  <h3 class="card-title me-2">
							Transaksi Transfer Antar Kas
						  </h3>
						  <div class="ms-auto">
							<div class="btn-group btn-group-sm ms-auto" role="group" aria-label="Basic example">
							  <button type="button" class="btn btn-success" onclick="">
								<i class="bx bx-download"></i> excel </button>
							</div>
						  </div>
						</div>
						<div id="div_list_trans_transfer">
						
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


<div id='modal_jns_kas' class="modal fade" tabindex="-1" data-bs-focus="false">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span class='modal_ket'></span> Jenis Kas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-6 pr-5 pl-5">
			<div class="row mb-2">
				<label for="" class="col-sm-4 control-label">Nama Jenis Kas</label>
				<div class="col-sm-8">
					<input type="text" id="in_nama_jns_kas" class="form-control">
				</div>
			</div>
			<div class="row mb-2" style='display:none;'>
				<label for="" class="col-sm-4 control-label">Saldo Awal</label>
				<div class="col-sm-8">
					<div class="input-group">
						<div class="input-group-text">Rp</div>
						<input type="text" id="in_saldo_awal" class="form-control number_sep text-end">
					</div>
				</div>
			</div>
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-info" onclick='save_jns_kas();'><span class='modal_ket'></span></button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<div id='modal_transfer' class="modal fade" tabindex="-1" data-bs-focus="false">
  <div class="modal-dialog ">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span class='modal_ket'></span> Transfer Antar Kas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-6 pr-5 pl-5">
			<div class="row mb-2">
				<label for="" class="col-sm-4 control-label">Nama Jenis Kas</label>
				<div class="col-sm-8">
					<input type="text" id="in_nama_jns_kas" class="form-control">
				</div>
			</div>
			<div class="row mb-2" style='display:none;'>
				<label for="" class="col-sm-4 control-label">Saldo Awal</label>
				<div class="col-sm-8">
					<div class="input-group">
						<div class="input-group-text">Rp</div>
						<input type="text" id="in_saldo_awal" class="form-control number_sep text-end">
					</div>
				</div>
			</div>
      </div>
	  <div class="modal-footer">
        <button type="button" class="btn btn-info" onclick='save_jns_kas();'><span class='modal_ket'></span></button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script src="kasv2/js/j_data_kas.js?v=<?php echo uniqid() ?>"></script>