<style>
.tooltip-inner {
    text-align: left;
}

.icon-prev .feather {
	scale:2;
}

.icon-item {
	border-radius: 5px;
	padding: 5px;
}

.icon-item:hover {
	background-color: whitesmoke;
	cursor:pointer;
}

.item_menu .unread-message, .item_submenu .unread-message  {
	top: 13px !important;
}

.drag-background-class {
	background-color:lavender;
}
</style>
<div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">

                        <!-- start page title -->
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                    <h4 class="mb-sm-0 font-size-18">Manajemen Menu</h4>

                                    <div class="page-title-right">
                                        <ol class="breadcrumb m-0">
                                            <li class="breadcrumb-item"><a href="javascript: void(0);">Sistem</a></li>
                                            <li class="breadcrumb-item active">Manajemen Menu</li>
                                        </ol>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end page title -->

                       <div class="d-lg-flex">
                            <div class="col-md-5 card">
                                <div class="p-3 px-4 border-bottom">
                                    <div class="d-flex align-items-start ">
                                        <div class="flex-grow-1">
                                            <h5 class="font-size-16 mb-1">
											<i class="bx bx-user"></i>
											<a href="#" class="text-dark">Data Menu </a></h5>
                                        </div>
														
                                    </div>
                                </div>

                                <div class="p-2">
                                    <div class="search-box position-relative">
                                        <input id='q_menu' type="text" class="form-control rounded border" placeholder="Cari..." onkeyup='cari_menu()'>
                                        <i class="bx bx-search search-icon"></i>
                                    </div>
                                </div>

                                <div class="chat-leftsidebar-nav">
                                    <div class="tab-content">
                                        <div class="tab-pane show active" id="chat">
                                            <div class="chat-message-list" data-simplebar="init" style='height:47vh;'>
												<div class="simplebar-wrapperr" style="margin: 0px;">
													<div class="simplebar-mask">
														<div class="simplebar-offset" style="right: -20px; bottom: 0px;" id='col_par_menu' >
															<div class="simplebar-content-wrapper" style="height: 100%; padding-right: 20px; padding-bottom: 0px; overflow: hidden scroll;">
															<div class="simplebar-content" style="padding: 0px;">
																<div class="pt-3">
																	<ul id='col_data_menu' class="list-unstyled chat-list" style='height:45vh;overflow:auto;'>
																	</ul>
																</div>
															</div>
														</div>
														</div>
													</div>
												</div>
											</div>
											
												<div class='mt-3 mb-3'>
													<i style="font-size: smaller;">*) Drag Menu untuk merubah urutan</i><br>
													<div class='tc'>
														<button class='btn btn-sm btn-info' onclick='modal_menu()'>+ Tambah Menu</button>
														<button id='btn_sort_menu' class='btn btn-sm btn-secondary' onclick='sort_menu()'>
															<i class=' bx bx-sort '></i> Simpan Urutan</button>
															<button class="btn btn-sm btn-outline-dark fr" title="Reload List Menu" onclick="list_menu()" style="float: right;margin-right:15px;">
															<icon class=" bx bx-revision "></icon>
														</button>
													</div>
												</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <!-- end chat-leftsidebar -->

                            <div id='root_submenu' class=" col-md-7 mt-4 mt-sm-0 ms-lg-1">
                                <div class="card">
                                    <div class="p-3 px-lg-4 border-bottom">
                                        <div class="row">
                                            <div class="col-xl-10 col-10">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1">
                                                        <h5 class="font-size-16 mb-1">
														<i class="bx bx-check-double"></i>
														<a href="#" class="text-dark">Submenu </a> <span class='cur_menu'></span></h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
									<div class='p-2'>
										<div class="search-box position-relative  submenu-elem" style="display:none;">
											<input id='q_submenu' type="text" class="form-control rounded border" placeholder="Cari..." onkeyup='cari_submenu()'>
											<i class="bx bx-search search-icon"></i>
										</div>
									</div>
                                    <div class=" p-3 px-2" data-simplebar="init">
										<ul id='col_data_submenu' class="list-unstyled chat-list" style='height:45vh;overflow:auto;'>
											<div class='tc p-5' style='font-size: initial;'>
												Klik Menu Accordion untuk menampilkan Submenu
											</div>
										</ul>
									<div class=' submenu-elem mt-2'  style="display:none;">
										<i style="font-size: smaller;">*) Drag Submenu untuk merubah urutan</i><br>
													<div class='tc'>
														<button class='btn btn-sm btn-info' onclick='modal_submenu()'>+ Tambah Submenu</button>
														<button id='btn_sort_submenu' class='btn btn-sm btn-secondary' onclick='sort_submenu()'>
															<i class=' bx bx-sort '></i> Simpan Urutan</button>
															<button class="btn btn-sm btn-outline-dark fr" title="Reload List Submenu" onclick="list_submenu()" style="float: right;margin-right:15px;">
															<icon class=" bx bx-revision "></icon>
														</button>
													</div>
									</div>
									</div>
                                </div>
                            </div>
                            <!-- end user chat -->
                        </div>
					   
                    </div>
                    <!-- container-fluid -->
                </div>
                <!-- End Page-content -->
</div>

<div class="modal fade" id="modal_menu" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="bx bx-user"></i> <span id='ket_modal'></span> Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class='form'>
			<div class=" mb-2 tc">
			  <label>Icon Menu</label> 
			  <div>
					<div class='icon-prev mb-3' style='font-size:larger;'>
						<i data-feather="table"></i> 
					</div>
					<div class="btn-group">
                                                <button type="button" class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <span id='nm_icon'>Forms</span> <i class="mdi mdi-chevron-down"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-md p-4" style="">
													<div class="search-box position-relative">
														<input id='q_icon' type="text" class="form-control rounded border mb-3" placeholder="Cari..." onkeyup='cari_icon()'>
														<i class="bx bx-search search-icon"></i>
													</div>
													<div id='daftar_icon' class="row tc" style="font-size: smaller; max-height: 200px; overflow-y:auto;">
														<?php
															$arr_icon = ["activity", "airplay", "alert-circle", "alert-octagon", "alert-triangle", "align-center", "align-justify", "align-left", "align-right", "anchor", "aperture", "archive", "arrow-down-circle", "arrow-down-left", "arrow-down-right", "arrow-down", "arrow-left-circle", "arrow-left", "arrow-right-circle", "arrow-right", "arrow-up-circle", "arrow-up-left", "arrow-up-right", "arrow-up", "at-sign", "award", "bar-chart-2", "bar-chart", "battery-charging", "battery", "bell-off", "bell", "bluetooth", "bold", "book-open", "book", "bookmark", "box", "briefcase", "calendar", "camera-off", "camera", "cast", "check-circle", "check-square", "check", "chevron-down", "chevron-left", "chevron-right", "chevron-up", "chevrons-down", "chevrons-left", "chevrons-right", "chevrons-up", "chrome", "circle", "clipboard", "clock", "cloud-drizzle", "cloud-lightning", "cloud-off", "cloud-rain", "cloud-snow", "cloud", "code", "codepen", "codesandbox", "coffee", "columns", "command", "compass", "copy", "corner-down-left", "corner-down-right", "corner-left-down", "corner-left-up", "corner-right-down", "corner-right-up", "corner-up-left", "corner-up-right", "cpu", "credit-card", "crop", "crosshair", "database", "delete", "disc", "divide-circle", "divide-square", "divide", "dollar-sign", "download-cloud", "download", "dribbble", "droplet", "edit-2", "edit-3", "edit", "external-link", "eye-off", "eye", "facebook", "fast-forward", "feather", "figma", "file-minus", "file-plus", "file-text", "file", "film", "filter", "flag", "folder-minus", "folder-plus", "folder", "framer", "frown", "gift", "git-branch", "git-commit", "git-merge", "git-pull-request", "github", "gitlab", "globe", "grid", "hard-drive", "hash", "headphones", "heart", "help-circle", "hexagon", "home", "image", "inbox", "info", "instagram", "italic", "key", "layers", "layout", "life-buoy", "link-2", "link", "linkedin", "list", "loader", "lock", "log-in", "log-out", "mail", "map-pin", "map", "maximize-2", "maximize", "meh", "menu", "message-circle", "message-square", "mic-off", "mic", "minimize-2", "minimize", "minus-circle", "minus-square", "minus", "monitor", "moon", "more-horizontal", "more-vertical", "mouse-pointer", "move", "music", "navigation-2", "navigation", "octagon", "package", "paperclip", "pause-circle", "pause", "pen-tool", "percent", "phone-call", "phone-forwarded", "phone-incoming", "phone-missed", "phone-off", "phone-outgoing", "phone", "pie-chart", "play-circle", "play", "plus-circle", "plus-square", "plus", "pocket", "power", "printer", "radio", "refresh-ccw", "refresh-cw", "repeat", "rewind", "rotate-ccw", "rotate-cw", "rss", "save", "scissors", "search", "send", "server", "settings", "share-2", "share", "shield-off", "shield", "shopping-bag", "shopping-cart", "shuffle", "sidebar", "skip-back", "skip-forward", "slack", "slash", "sliders", "smartphone", "smile", "speaker", "square", "star", "stop-circle", "sun", "sunrise", "sunset", "table", "tablet", "tag", "target", "terminal", "thermometer", "thumbs-down", "thumbs-up", "toggle-left", "toggle-right", "tool", "trash-2", "trash", "trello", "trending-down", "trending-up", "triangle", "truck", "tv", "twitch", "twitter", "type", "umbrella", "underline", "unlock", "upload-cloud", "upload", "user-check", "user-minus", "user-plus", "user-x", "user", "users", "video-off", "video", "voicemail", "volume-1", "volume-2", "volume-x", "volume", "watch", "wifi-off", "wifi", "wind", "x-circle", "x-octagon", "x-square", "x", "youtube", "zap-off", "zap", "zoom-in", "zoom-out"];
														foreach($arr_icon as $ic)
														{
															echo '<div class="icon-item col-sm-6">
																	<div><i data-feather="'.$ic.'"></i> </div><span class="cari-q">'.$ic.'</span>
																</div>';
														}
														?>
														<div class='icon_no_result' style='display:none;'>Tidak ada data</div>
													</div>
                                                </div>
                                            </div>
			  </div>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="mn_nama" placeholder="">
			  <label for="mn_nama">Nama Menu</label>
			</div>
			<div class="form-floating mb-2">
			  <select class="form-control" id="mn_jenis" onchange='verify_jenis_menu();'>
				<option value = 0>-pilih-</option>
				<option value = 'single'>Single</option>
				<option value = 'accordion'>Kelompok/Accordion</option>
			  </select>
			  <label for="mn_jenis">Jenis Menu</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="mn_url" placeholder="">
			  <label for="mn_url">URL</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="mn_urutan" placeholder="">
			  <label for="mn_urutan">Urutan</label>
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='btn_save_menu' onclick='save_menu();'><i class='bx bx-save'></i> <span></span></button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-danger" id='btn_del_menu' onclick='del_menu();' title='Hapus Menu'><i class='bx bx-trash'></i></button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal_submenu" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><i class="bx bx-user"></i> <span id='ket_modal_submenu'></span> Submenu <span class='cur_menu'></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class='form'>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="smn_nama" placeholder="">
			  <label for="smn_nama">Nama Submenu</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="smn_url" placeholder="">
			  <label for="smn_url">URL</label>
			</div>
			<div class="form-floating mb-2">
			  <input type="text" class="form-control" id="smn_urutan" placeholder="">
			  <label for="smn_urutan">Urutan</label>
			</div>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='btn_save_submenu' onclick='save_submenu();'><i class='bx bx-save'></i> <span></span></button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-danger" id='btn_del_submenu' onclick='del_submenu();' title='Hapus Submenu'><i class='bx bx-trash'></i></button>
      </div>
    </div>
  </div>
</div>

<script src="sistem/js/j_manajemen_menu.js?v=<?php echo uniqid() ?>"></script>