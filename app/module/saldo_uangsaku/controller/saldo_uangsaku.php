<?php

class saldo_uangsaku extends Controller
{
    var $content;

    public function index($param1 = "", $param2 = "")
    {
        $data["title"] = "Saldo Dompet Digital Warga";
        $data["href"] = "";
        $this->view("main/view/header", $data);
        $this->view("saldo_uangsaku/view/v_laporan_saldo");
        $this->view("main/view/footer");
    }

    public function ex($param1, $params2)
    {
        $ex_controller = '';
        if (file_exists("app/module/saldo_uangsaku/controller/$param1.php")) {
            $ex_controller = $param1;
        }
        if ($ex_controller != '') {
            require_once "$param1.php";
            $ex_controller = new $ex_controller;
            if (isset($params2)) {
                if (method_exists($ex_controller, $params2)) {
                    call_user_func_array([$ex_controller, $params2], []);
                }
            }
        }
    }

    function send_th()
    {
        global $con;
        $querykls = "SELECT * FROM data_tahunajar ORDER BY tgl_mulai DESC";
        $result = mysqli_query($con, $querykls);
        echo "<label class='form-label font-size-13 text-muted mb-1'>Periode Operasional</label>";
        echo "<select required class='form-select form-select-sm mb-3' id='myselects_th' onChange='tampil_kls();'>";
        echo '<option value="">-- Semua Periode --</option>';
        if ($result) {
            while ($dt = mysqli_fetch_array($result)) {
                echo '<option value="' . $dt['id_tahunajar'] . '">' . htmlspecialchars($dt['nama_tahunajar']) . '</option>';
            }
        }
        echo '</select>';
    }

    function send_kls_tag()
    {
        global $con;
        $sesi = isset($_GET['th']) ? mysqli_real_escape_string($con, $_GET['th']) : "";
        $where = !empty($sesi) ? "WHERE id_sesi='$sesi'" : "";
        $querykls = "SELECT * FROM data_kelas $where ORDER BY nama_kelas ASC";
        $result = mysqli_query($con, $querykls);
        echo "<label class='form-label font-size-13 text-muted mb-1'>Kelompok / RT</label>";
        echo "<select class='form-select form-select-sm mb-3' id='myselects_kls'>";
        echo '<option value="">-- Semua Kelompok / RT --</option>';
        if ($result) {
            while ($dt = mysqli_fetch_array($result)) {
                echo '<option value="' . $dt['id_kelas'] . '">' . htmlspecialchars($dt['nama_kelas']) . '</option>';
            }
        }
        echo '</select>';
    }

    function laporan_saldo($cetak = 0)
    {
        global $con;
        $sampai_tgl = isset($_GET['sampai_tgl']) ? trim($_GET['sampai_tgl']) : "";
        $th = isset($_GET['th']) ? trim($_GET['th']) : "";
        $id_kls = isset($_GET['id_kls']) ? trim($_GET['id_kls']) : "";
        $id_siswa = isset($_GET['nis_from_idsiswa']) ? trim($_GET['nis_from_idsiswa']) : "";

        $where = ["1=1"];

        if (!empty($id_siswa)) {
            $id_siswaEsc = mysqli_real_escape_string($con, $id_siswa);
            $where[] = "u.id_siswa = '$id_siswaEsc'";
        }

        if (!empty($sampai_tgl)) {
            $tglEsc = mysqli_real_escape_string($con, $sampai_tgl);
            $where[] = "DATE(u.tanggal) <= '$tglEsc'";
        }

        if (!empty($th)) {
            $thEsc = mysqli_real_escape_string($con, $th);
            $where[] = "ps.thn_ajar = '$thEsc'";
        }

        if (!empty($id_kls)) {
            $idKlsEsc = mysqli_real_escape_string($con, $id_kls);
            $where[] = "ps.id_kelas = '$idKlsEsc'";
        }

        $whereSql = implode(" AND ", $where);

        $que = "SELECT 
                    u.id_tbl_uangku,
                    u.id_siswa,
                    u.id_tbl_status_transaksi,
                    u.jumlah,
                    u.keterangan,
                    u.tanggal,
                    ds.nama_siswa AS nama_warga,
                    IFNULL(dk.nama_kelas, '-') AS nama_kelompok
                FROM tbl_uangku u
                INNER JOIN data_siswa ds ON ds.id_siswa = u.id_siswa
                LEFT JOIN penempatan_siswa ps ON ps.nis_from_idsiswa = ds.id_siswa
                LEFT JOIN data_kelas dk ON dk.id_kelas = ps.id_kelas
                WHERE $whereSql
                ORDER BY u.id_tbl_uangku DESC";

        $sql = @mysqli_query($con, $que);
        $warga_trans = [];

        if ($sql) {
            while ($rs = mysqli_fetch_assoc($sql)) {
                $sid = $rs['id_siswa'];
                if (!isset($warga_trans[$sid])) {
                    $warga_trans[$sid] = [
                        'nama_warga' => $rs['nama_warga'],
                        'nama_kelompok' => $rs['nama_kelompok'],
                        'last_jumlah' => $rs['jumlah'],
                        'last_ket' => $rs['keterangan'],
                        'last_tgl' => $rs['tanggal'],
                        'saldo' => 0
                    ];
                }
                if (in_array($rs['id_tbl_status_transaksi'], [1, 3])) {
                    $warga_trans[$sid]['saldo'] += (double)$rs['jumlah'];
                } else {
                    $warga_trans[$sid]['saldo'] -= (double)$rs['jumlah'];
                }
            }
        }

        $tbody = "";
        $no = 1;
        $totalAll = 0;

        foreach ($warga_trans as $sid => $w) {
            $totalAll += $w['saldo'];
            $tglFmt = !empty($w['last_tgl']) ? date('d-m-Y', strtotime($w['last_tgl'])) : '-';

            $tbody .= "
                <tr>
                    <td class='text-center text-muted'>$no</td>
                    <td><span class='badge bg-light text-dark border font-size-11'>".htmlspecialchars($w['nama_kelompok'])."</span></td>
                    <td class='fw-semibold text-dark'>".htmlspecialchars($w['nama_warga'])."</td>
                    <td class='text-end text-muted font-size-12'>Rp ".number_format($w['last_jumlah'], 0, ',', '.')."</td>
                    <td class='text-muted font-size-12'>".htmlspecialchars($w['last_ket'] ?: '-')."</td>
                    <td class='text-end fw-bold text-success'>Rp ".number_format($w['saldo'], 0, ',', '.')."</td>
                    <td class='text-center'>
                        <button class='btn btn-xs btn-outline-primary' onclick='event.preventDefault(); rinci_warga($sid);' title='Rincian Mutasi'>
                            <i class='bx bx-list-ul me-1'></i> Rincian
                        </button>
                    </td>
                </tr>
            ";
            $no++;
        }

        if (empty($warga_trans)) {
            $tbody = "<tr><td colspan='7' class='text-center py-4 text-muted'>Belum ada transaksi saldo dompet digital</td></tr>";
        }

        echo "
            <div class='table-responsive'>
                <table class='table table-hover align-middle mb-0' id='tabsesur' style='font-size:13px;'>
                    <thead class='table-light text-muted font-size-12 text-uppercase'>
                        <tr>
                            <th width='5%' class='text-center'>No</th>
                            <th width='15%'>Kelompok / RT</th>
                            <th>Nama Warga</th>
                            <th width='15%' class='text-end'>Transaksi Terakhir</th>
                            <th width='20%'>Keterangan</th>
                            <th width='15%' class='text-end'>Saldo Akhir</th>
                            <th width='10%' class='text-center'>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        $tbody
                    </tbody>
                    <tfoot class='table-light fw-bold font-size-13'>
                        <tr>
                            <td colspan='5' class='text-end'>Total Saldo Dompet Digital Warga:</td>
                            <td class='text-end text-danger font-size-14'>Rp " . number_format($totalAll, 0, ',', '.') . "</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        ";
    }

    function cari_siswa()
    {
        global $con;
        $nama = isset($_GET['nama']) ? mysqli_real_escape_string($con, trim($_GET['nama'])) : "";
        $th = isset($_GET['th']) ? mysqli_real_escape_string($con, trim($_GET['th'])) : "";

        if (empty($nama)) {
            echo "<div class='text-muted text-center py-3 font-size-12'>Ketik nama warga untuk mencari...</div>";
            return;
        }

        $que = "SELECT 
                    ds.id_siswa,
                    ds.nama_siswa,
                    IFNULL(dk.nama_kelas, '-') AS nama_kelas,
                    ps.id_kelas
                FROM data_siswa ds
                LEFT JOIN penempatan_siswa ps ON ps.nis_from_idsiswa = ds.id_siswa
                LEFT JOIN data_kelas dk ON dk.id_kelas = ps.id_kelas
                WHERE ds.nama_siswa LIKE '%$nama%'
                ORDER BY ds.nama_siswa ASC LIMIT 30";

        $sql = mysqli_query($con, $que);
        $no = 1;
        $tbody = "";

        if ($sql) {
            while ($rs = mysqli_fetch_assoc($sql)) {
                $tbody .= "
                    <tr>
                        <td class='text-center text-muted'>$no</td>
                        <td class='fw-semibold text-dark'>".htmlspecialchars($rs['nama_siswa'])."</td>
                        <td><span class='badge bg-light text-dark border font-size-11'>".htmlspecialchars($rs['nama_kelas'])."</span></td>
                        <td class='text-center'>
                            <button class='btn btn-primary btn-xs' onclick='pilih_warga_cari(".$rs['id_siswa'].", \"".addslashes($rs['nama_siswa'])."\");'>Pilih</button>
                        </td>
                    </tr>
                ";
                $no++;
            }
        }

        echo "
            <table class='table table-sm table-hover align-middle mb-0' style='font-size:12px;'>
                <thead class='table-light'>
                    <tr>
                        <th width='5%' class='text-center'>#</th>
                        <th>Nama Warga</th>
                        <th>Kelompok / RT</th>
                        <th width='15%' class='text-center'>Pilih</th>
                    </tr>
                </thead>
                <tbody>
                    $tbody
                </tbody>
            </table>
        ";
    }

    function laporan_saldo_persis()
    {
        global $con;
        $id_siswa = (int)($_GET['id_siswa'] ?? 0);

        $sql = "SELECT u.*, ds.nama_siswa 
                FROM tbl_uangku u
                INNER JOIN data_siswa ds ON ds.id_siswa = u.id_siswa
                WHERE u.id_siswa = '$id_siswa' 
                ORDER BY u.tanggal ASC, u.id_tbl_uangku ASC";
        $result = mysqli_query($con, $sql);

        $tbody = "";
        $saldo = 0;

        if ($result && mysqli_num_rows($result) > 0) {
            while ($r = mysqli_fetch_assoc($result)) {
                $isMasuk = in_array($r['id_tbl_status_transaksi'], [1, 3]);
                $db = $isMasuk ? "Rp " . number_format($r['jumlah'], 0, ',', '.') : "-";
                $kr = !$isMasuk ? "Rp " . number_format($r['jumlah'], 0, ',', '.') : "-";

                if ($isMasuk) {
                    $saldo += (double)$r['jumlah'];
                } else {
                    $saldo -= (double)$r['jumlah'];
                }

                $tgl = !empty($r['tanggal']) ? date('d-m-Y H:i', strtotime($r['tanggal'])) : '-';

                $tbody .= "
                    <tr>
                        <td class='text-muted font-size-12'>$tgl</td>
                        <td class='fw-semibold text-dark'>".htmlspecialchars($r['keterangan'] ?: '-')."</td>
                        <td class='text-end text-success font-size-12'>$db</td>
                        <td class='text-end text-danger font-size-12'>$kr</td>
                        <td class='text-end fw-bold font-size-12'>Rp " . number_format($saldo, 0, ',', '.') . "</td>
                    </tr>
                ";
            }
        } else {
            $tbody = "<tr><td colspan='5' class='text-center py-4 text-muted'>Belum ada catatan mutasi transaksi</td></tr>";
        }

        echo "
            <div class='table-responsive'>
                <table class='table table-bordered table-hover align-middle mb-0' style='font-size:12px;'>
                    <thead class='table-light text-muted font-size-11 text-uppercase'>
                        <tr>
                            <th width='22%'>Tanggal</th>
                            <th>Keterangan</th>
                            <th width='18%' class='text-end'>Masuk (Debit)</th>
                            <th width='18%' class='text-end'>Keluar (Kredit)</th>
                            <th width='20%' class='text-end'>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        $tbody
                    </tbody>
                </table>
            </div>
        ";
    }

    function tampilkan_default($cetak = 0)
    {
        $this->laporan_saldo($cetak);
    }

    public function js($param1)
    {
        $this->get_js("saldo_uangsaku/js/" . $param1);
    }
}
?>