<?php

class Audit_Log extends Controller
{
    public function index($param1 = "", $param2 = "")
    {
        $data["title"] = "Audit Log & Jejak Aktivitas";
        $data["href"] = "audit_log";
        $this->view("main/view/header", $data);
        $this->view("audit_log/view/v_audit_log", $data);
        $this->view("main/view/footer");
    }

    public function get_logs()
    {
        global $con;
        header('Content-Type: application/json; charset=UTF-8');

        // Auto-create table if not exists for resilience
        if ($con) {
            @mysqli_query($con, "CREATE TABLE IF NOT EXISTS audit_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(50) NULL,
                nama_user VARCHAR(100) NULL,
                aksi VARCHAR(50) NOT NULL,
                modul VARCHAR(50) NOT NULL,
                id_entitas VARCHAR(50) NULL,
                keterangan TEXT NULL,
                data_sebelum LONGTEXT NULL,
                data_sesudah LONGTEXT NULL,
                ip_address VARCHAR(45) NULL,
                user_agent TEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_audit_modul (modul),
                INDEX idx_audit_aksi (aksi),
                INDEX idx_audit_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        }

        $aksi = trim($_POST['aksi'] ?? $_GET['aksi'] ?? '');
        $modul = trim($_POST['modul'] ?? $_GET['modul'] ?? '');
        $search = trim($_POST['search'] ?? $_GET['search'] ?? '');
        $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 100;
        if ($limit < 1 || $limit > 500) {
            $limit = 100;
        }

        $conditions = ["1=1"];

        if (!empty($aksi)) {
            $aksiEsc = mysqli_real_escape_string($con, $aksi);
            $conditions[] = "aksi = '$aksiEsc'";
        }

        if (!empty($modul)) {
            $modulEsc = mysqli_real_escape_string($con, $modul);
            $conditions[] = "modul = '$modulEsc'";
        }

        if (!empty($search)) {
            $searchEsc = mysqli_real_escape_string($con, $search);
            $conditions[] = "(nama_user LIKE '%$searchEsc%' OR keterangan LIKE '%$searchEsc%' OR ip_address LIKE '%$searchEsc%')";
        }

        $whereSql = implode(" AND ", $conditions);
        $sql = "SELECT * FROM audit_logs WHERE $whereSql ORDER BY id DESC LIMIT $limit";

        $logs = [];
        $res = @mysqli_query($con, $sql);
        if ($res && is_object($res)) {
            while ($row = mysqli_fetch_assoc($res)) {
                $logs[] = $row;
            }
        }

        echo json_encode([
            'status' => 'success',
            'total' => count($logs),
            'data' => $logs
        ]);
    }

    public function ex($subcontroller = '', $method = '', $param = '')
    {
        if ($subcontroller === 'get_logs' || $method === 'get_logs') {
            $this->get_logs();
            return;
        }
        $this->get_logs();
    }

    public function js($param1)
    {
        $safeJs = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $param1);
        $this->get_js("audit_log/js/" . $safeJs);
    }
}
