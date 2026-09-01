<?php

/**
 * Audit Logger Engine untuk Sistem Komune
 * Mencatat semua aktivitas perubahan data dan interaksi pengguna secara transparan.
 */
class AuditLogger
{
    /**
     * Catat aktivitas pengguna ke dalam audit_logs
     *
     * @param string $aksi CREATE, UPDATE, SOFT_DELETE, HARD_DELETE, LOGIN, LOGOUT, EXPORT, PAYMENT
     * @param string $modul Nama modul yang sedang diakses (warga, iuran, kas, artikel, dll)
     * @param string|int|null $id_entitas ID dari data yang dimanipulasi
     * @param string|null $keterangan Deskripsi detail tindakan
     * @param array|string|null $data_sebelum Nilai data sebelum perubahan (opsional)
     * @param array|string|null $data_sesudah Nilai data sesudah perubahan (opsional)
     * @return bool
     */
    public static function log($aksi, $modul, $id_entitas = null, $keterangan = null, $data_sebelum = null, $data_sesudah = null)
    {
        global $con;

        if (!$con) {
            return false;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['id'] ?? $_SESSION['id_user'] ?? 'system';
        $userName = $_SESSION['nama_us'] ?? $_SESSION['username'] ?? 'System';
        $ip = Security::get_client_ip();
        $userAgent = Security::get_user_agent();

        $strSebelum = is_array($data_sebelum) ? json_encode($data_sebelum, JSON_UNESCAPED_UNICODE) : (string)$data_sebelum;
        $strSesudah = is_array($data_sesudah) ? json_encode($data_sesudah, JSON_UNESCAPED_UNICODE) : (string)$data_sesudah;

        // Escape string sebelum query
        $userIdEsc = mysqli_real_escape_string($con, (string)$userId);
        $userNameEsc = mysqli_real_escape_string($con, (string)$userName);
        $aksiEsc = mysqli_real_escape_string($con, (string)$aksi);
        $modulEsc = mysqli_real_escape_string($con, (string)$modul);
        $idEntitasEsc = mysqli_real_escape_string($con, (string)$id_entitas);
        $ketEsc = mysqli_real_escape_string($con, (string)$keterangan);
        $sebEsc = mysqli_real_escape_string($con, (string)$strSebelum);
        $sesEsc = mysqli_real_escape_string($con, (string)$strSesudah);
        $ipEsc = mysqli_real_escape_string($con, (string)$ip);
        $uaEsc = mysqli_real_escape_string($con, (string)$userAgent);

        $sql = "INSERT INTO audit_logs (
                    user_id, nama_user, aksi, modul, id_entitas, 
                    keterangan, data_sebelum, data_sesudah, ip_address, user_agent, created_at
                ) VALUES (
                    '$userIdEsc', '$userNameEsc', '$aksiEsc', '$modulEsc', '$idEntitasEsc',
                    '$ketEsc', '$sebEsc', '$sesEsc', '$ipEsc', '$uaEsc', NOW()
                )";

        return @mysqli_query($con, $sql) !== false;
    }
}

/**
 * Global helper function untuk mempermudah pemanggilan audit log
 */
function audit_log($aksi, $modul, $id_entitas = null, $keterangan = null, $data_sebelum = null, $data_sesudah = null)
{
    return AuditLogger::log($aksi, $modul, $id_entitas, $keterangan, $data_sebelum, $data_sesudah);
}
