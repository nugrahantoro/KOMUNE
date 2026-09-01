<?php

class request_template extends Controller
{
    public function index($param1 = "", $param2 = "")
    {
        $data["title"] = "rekapsemua";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("request_template/view/v_request_template");
        $this->view("main/view/footer");
    }

    public function requesttemplate($param1 = "", $param2 = "")
    {
        $data["title"] = "requesttemplate";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("request_template/view/v_request_template");
        $this->view("main/view/footer");
    }

    public function ex($param1,$params2)
	{
		$ex_controller = '';
		if (file_exists("app/module/request_template/controller/$param1.php"))
		{
			$ex_controller=$param1;
		}
		if($ex_controller != '')
		{
			require_once "$param1.php";
			$ex_controller = new $ex_controller;
			//Method
			if(isset($params2))
			{
				if (method_exists($ex_controller,$params2))
				{
				//Run Controller & Method. Send Paramaters if exists
				call_user_func_array([$ex_controller,$params2],[]);
				}
			}
		}
	}
    function data_request_template() {
        global $con;

        $limit = 10;
        $pages = $_GET["page"];
        $start = ($pages - 1) * 10;

        $sql="
            SELECT
                ref_template_wa.*,
                users_log.nama_us
            FROM
                ref_template_wa
                JOIN users_log ON ref_template_wa.id_user = users_log.id
            ORDER BY
                ref_template_wa.created_at DESC
            LIMIT $start, $limit
        ";
        $result = mysqli_query($con,$sql);

        $sql_page="
            SELECT
                * 
            FROM
                ref_template_wa
                JOIN users_log ON ref_template_wa.id_user = users_log.id
        ";
        $result_page = mysqli_query($con,$sql_page);
        $count = mysqli_num_rows($result_page);
        $page = ceil($count / $limit);

        // echo '<div class="col-md-12">';
        // echo '<select id="myselects_thal" style="border: 1px solid #E1E1E1;" onChange="pindah_pagination(this);">';
        // echo '<option>Page</option>';
        // for($i=1; $i<=$page; $i++){
        //     echo '<option value="'.$i.'" id="'.$i.'">'.$i.'</option>';
        // }
        // echo '</select>
        //     <span style="font-family:tahoma;font-size:11px;margin-left:10px;">halaman '.$pages.' dari '.$count.' data</span>
        //     <button class="btn btn-primary btn-xs" onclick="tampil_modal_tambah_request_template();" style="border-radius:3px;border:0px;padding:5px;margin-left:10px;">Tambah</button>
        //     <br>
        //     <br>
        
        echo '
      
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table mb-0" id="tabsesur" style="font-size: 12px;">
                        
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Pesan</th>
                                    <th>Status</th>
                                    <th>User</th>
                                    <th>Nama Template</th>
                                    <th>Client ID</th>             
                                    <th>Project ID</th>             
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Aktif</th>
                                    <th>Action</th>
                                </tr>
                        
        ';
        $no = 1;
        while($hs = mysqli_fetch_array($result)){
            // Status
            if ($hs['status'] == '0') {
                $status = "Submit";
            } elseif ($hs['status'] == '1') {
                $status = "On Process";
            } elseif ($hs['status'] == '2') {
                $status = "Approved";
            } elseif ($hs['status'] == '3') {
                $status = "Rejected";
            }

            // Aktif
            if ($hs['aktif'] == '1') {
                $aktif = "Aktif";
            } else {
                $aktif = "Non-Aktif";
            }
            $ambilin = $hs['id'];
            // Aksi
            if ($_SESSION['id'] != 'exex77') {
                if ($hs['status'] == '0' || $hs['status'] == '3') {
                    $aksi = '
                        <button class="btn btn-primary btn-xs" onclick="tampil_modal_edit_request_template('.$ambilin.');" style="border-radius:3px;border:0px;padding:5px;">
                            Edit
                        </button>
                    ';
                } else {
                    $aksi = '';
                }
            } else {
                if ($hs['status'] == '2') {
                    if ($hs['aktif'] == '1') {
                        $aksi = '
                            <button class="btn btn-primary btn-xs" onclick="tampil_modal_edit_request_template('.$ambilin.');" style="border-radius:3px;border:0px;padding:5px;">
                                Edit
                            </button>
                            <button class="btn btn-warning btn-xs" onclick="non_aktif_request_template('.$ambilin.');" style="border-radius:3px;border:0px;padding:5px;">
                                Non-Aktif
                            </button>
                        ';
                    } else {
                        $aksi = '
                            <button class="btn btn-primary btn-xs" onclick="tampil_modal_edit_request_template('.$ambilin.');" style="border-radius:3px;border:0px;padding:5px;">
                                Edit
                            </button>
                            <button class="btn btn-success btn-xs" onclick="aktif_request_template();" style="border-radius:3px;border:0px;padding:5px;">
                                Aktif
                            </button>
                        ';
                    }
                } else {
                    $aksi = '
                        <button class="btn btn-primary btn-xs" onclick="tampil_modal_edit_request_template('.$ambilin.');" style="border-radius:3px;border:0px;padding:5px;">
                            Edit
                        </button>
                    ';
                }
            }

            echo '
                                <tr>
                                    <td>'.$no.'</td>
                                    <td>'.$hs['created_at'].'</td>
                                    <td>'.$hs['pesan'].'</td>
                                    <td>'.$status.'</td>
                                    <td>'.$hs['nama_us'].'</td>
                                    <td>'.$hs['template_name'].'</td>
                                    <td>'.$hs['client_id'].'</td>
                                    <td>'.$hs['project_id'].'</td>
                                    <td>'.$hs['username'].'</td>
                                    <td>'.$hs['password'].'</td>
                                    <td>'.$aktif.'</td>
                                    <td>
                                        <center>
                                            ' . $aksi . '
                                        </center>
                                    </td>
                                </tr>    
            ';
            $no++;
            }
                echo ' </table>
            </div>
             </div>
             </div>
         </div>
        </div>  
                </table>
            ';
            echo '</div>';
    }

    public function js($param1)
    {
        $this->get_js("request_template/js/" .$param1);
    }

    function simpan_request_template() {
        session_start();
        global $con;
        $pesan = isset($_POST['pesan']) ? $_POST['pesan'] : "";
        $id_user = $_SESSION['id'];
        $created_at = date('Y-m-d H:i:s');
        $username = $_SESSION['username'];
        $password = $_SESSION['password'];
        $query = mysqli_query($con,"INSERT INTO ref_template_wa(id_user, pesan, created_at, username, password) VALUES ('$id_user', '$pesan', '$created_at', '$username', '$password')");
        var_dump("INSERT INTO ref_template_wa(id_user, pesan, created_at, username, password) VALUES ('$id_user', '$pesan', '$created_at', '$username', '$password')");
        if ($query) {
            echo 'Berhasil disimpan';
        } else {
            echo 'Gagal disimpan';
        }
    }

    function edit_request_template() {
        global $con;
        $id = isset($_POST['id']) ? $_POST['id'] : "";
        $pesan = isset($_POST['pesan']) ? $_POST['pesan'] : "";
        $status = isset($_POST['status']) ? $_POST['status'] : "";
        $nama_template = isset($_POST['nama_template']) ? $_POST['nama_template'] : "";
        $client_id = isset($_POST['client_id']) ? $_POST['client_id'] : "";
        $project_id = isset($_POST['project_id']) ? $_POST['project_id'] : "";
        $username = isset($_POST['username']) ? $_POST['username'] : "";
        $password = isset($_POST['password']) ? $_POST['password'] : "";

        $query = mysqli_query($con,"UPDATE ref_template_wa SET pesan = '$pesan', status = '$status', template_name = '$nama_template', client_id = '$client_id', project_id = '$project_id', username = '$username', password = '$password' WHERE id = '$id'");
        if($query) {
            echo 'Berhasil diedit';
        } else {
            echo 'Gagal diedit';
        }
    }

}

?>