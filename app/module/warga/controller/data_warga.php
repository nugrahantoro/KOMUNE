<?php

class Data_Warga extends Controller
{
    /**
     * Mengambil daftar warga dengan filter dan pencarian yang tangguh
     */
    public function get_list_warga()
    {
        global $con;
        header('Content-Type: application/json; charset=UTF-8');

        $id_jenjang = trim($_POST['id_jenjang'] ?? $_GET['id_jenjang'] ?? '');
        $id_kelas = trim($_POST['id_kelas'] ?? $_GET['id_kelas'] ?? '');
        $search = trim($_POST['search'] ?? $_GET['search'] ?? '');

        $conditions = ["1=1"];

        if (!empty($id_jenjang)) {
            $id_jenjangEsc = mysqli_real_escape_string($con, $id_jenjang);
            $conditions[] = "(dk.id_jenjang = '$id_jenjangEsc' OR ds.id_jenjang = '$id_jenjangEsc')";
        }

        if (!empty($id_kelas)) {
            $id_kelasEsc = mysqli_real_escape_string($con, $id_kelas);
            $conditions[] = "(ps.id_kelas = '$id_kelasEsc' OR ds.id_kelas = '$id_kelasEsc')";
        }

        if (!empty($search)) {
            $searchEsc = mysqli_real_escape_string($con, $search);
            $conditions[] = "(ds.nama_siswa LIKE '%$searchEsc%' OR ds.nis LIKE '%$searchEsc%' OR ds.alamat LIKE '%$searchEsc%' OR ds.no_hp_ortu LIKE '%$searchEsc%' OR ds.hp_wali LIKE '%$searchEsc%')";
        }

        $whereSql = implode(" AND ", $conditions);

        $sql = "SELECT 
                    ds.id_siswa,
                    ds.nis AS nomor_anggota,
                    ds.nama_siswa AS nama_warga,
                    IFNULL(ds.gender, '-') AS jenis_kelamin,
                    IFNULL(ds.alamat, '-') AS alamat,
                    IFNULL(IFNULL(ds.no_hp_ortu, ds.hp_wali), '-') AS no_telepon,
                    IFNULL(IFNULL(ds.nama_ortu, ds.nama_ayah), IFNULL(ds.nama_wali, '-')) AS nama_kepala_keluarga,
                    IFNULL(ds.status, '1') AS status,
                    IFNULL(dk.nama_kelas, '-') AS kelompok_warga,
                    IFNULL(dj.nama_jenjang, '-') AS wilayah
                FROM data_siswa ds
                LEFT JOIN penempatan_siswa ps ON ps.nis_from_idsiswa = ds.id_siswa
                LEFT JOIN data_kelas dk ON dk.id_kelas = ps.id_kelas
                LEFT JOIN data_jenjang dj ON dj.id_jenjang = dk.id_jenjang
                WHERE $whereSql
                GROUP BY ds.id_siswa
                ORDER BY ds.nama_siswa ASC";

        $data = [];
        $res = @mysqli_query($con, $sql);
        if ($res && is_object($res)) {
            while ($row = mysqli_fetch_assoc($res)) {
                $data[] = $row;
            }
        }

        echo json_encode([
            'status' => 'success',
            'total' => count($data),
            'data' => $data
        ]);
    }

    /**
     * Mengambil detail warga berdasarkan ID
     */
    public function get_detail($id = null)
    {
        global $con;
        header('Content-Type: application/json; charset=UTF-8');

        $id = $id ?? $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID warga tidak valid']);
            return;
        }

        $idEsc = mysqli_real_escape_string($con, $id);
        $sql = "SELECT ds.*, ps.id_kelas, dk.id_jenjang 
                FROM data_siswa ds
                LEFT JOIN penempatan_siswa ps ON ps.nis_from_idsiswa = ds.id_siswa
                LEFT JOIN data_kelas dk ON dk.id_kelas = ps.id_kelas
                WHERE ds.id_siswa = '$idEsc' LIMIT 1";

        $res = @mysqli_query($con, $sql);
        $warga = ($res && is_object($res)) ? mysqli_fetch_assoc($res) : null;

        if ($warga) {
            echo json_encode(['status' => 'success', 'data' => $warga]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data warga tidak ditemukan']);
        }
    }

    /**
     * Simpan data warga baru atau update
     */
    public function simpan()
    {
        global $con;
        header('Content-Type: application/json; charset=UTF-8');

        $id = trim($_POST['id_warga'] ?? '');
        $nis = trim($_POST['nomor_anggota'] ?? '');
        $nama = trim($_POST['nama_warga'] ?? '');
        $gender = trim($_POST['jenis_kelamin'] ?? 'L');
        $alamat = trim($_POST['alamat'] ?? '');
        $no_hp = trim($_POST['no_telepon'] ?? '');
        $nama_kk = trim($_POST['nama_kepala_keluarga'] ?? '');
        $id_kelas = trim($_POST['id_kelompok'] ?? '');

        if (empty($nama)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama lengkap warga wajib diisi']);
            return;
        }

        $namaEsc = mysqli_real_escape_string($con, $nama);
        $nisEsc = mysqli_real_escape_string($con, $nis);
        $genderEsc = mysqli_real_escape_string($con, $gender);
        $alamatEsc = mysqli_real_escape_string($con, $alamat);
        $noHpEsc = mysqli_real_escape_string($con, $no_hp);
        $namaKkEsc = mysqli_real_escape_string($con, $nama_kk);
        $idKelasEsc = (int)$id_kelas;

        if (!empty($id)) {
            // Update
            $idEsc = (int)$id;
            $sqlUpdate = "UPDATE data_siswa SET 
                            nis = '$nisEsc', 
                            nama_siswa = '$namaEsc', 
                            gender = '$genderEsc', 
                            alamat = '$alamatEsc', 
                            no_hp_ortu = '$noHpEsc', 
                            hp_wali = '$noHpEsc',
                            nama_ortu = '$namaKkEsc',
                            nama_ayah = '$namaKkEsc'
                          WHERE id_siswa = '$idEsc'";
            $res = @mysqli_query($con, $sqlUpdate);

            if ($res) {
                // Update penempatan
                if ($idKelasEsc > 0) {
                    @mysqli_query($con, "DELETE FROM penempatan_siswa WHERE nis_from_idsiswa = '$idEsc'");
                    @mysqli_query($con, "INSERT INTO penempatan_siswa (nis_from_idsiswa, id_kelas) VALUES ('$idEsc', '$idKelasEsc')");
                }
                @audit_log('UPDATE', 'warga', $idEsc, "Memperbarui data warga: $nama ($nis)");
                echo json_encode(['status' => 'success', 'message' => 'Data warga berhasil diperbarui']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data warga']);
            }
        } else {
            // Insert Baru
            $sqlInsert = "INSERT INTO data_siswa (nis, nama_siswa, gender, alamat, no_hp_ortu, hp_wali, nama_ortu, nama_ayah, status) 
                          VALUES ('$nisEsc', '$namaEsc', '$genderEsc', '$alamatEsc', '$noHpEsc', '$noHpEsc', '$namaKkEsc', '$namaKkEsc', '1')";
            $res = @mysqli_query($con, $sqlInsert);

            if ($res) {
                $newId = mysqli_insert_id($con);
                if ($idKelasEsc > 0) {
                    @mysqli_query($con, "INSERT INTO penempatan_siswa (nis_from_idsiswa, id_kelas) VALUES ('$newId', '$idKelasEsc')");
                }
                @audit_log('CREATE', 'warga', $newId, "Menambahkan warga baru: $nama ($nis)");
                echo json_encode(['status' => 'success', 'message' => 'Warga baru berhasil didaftarkan']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan warga baru']);
            }
        }
    }

    /**
     * Soft delete warga
     */
    public function hapus()
    {
        global $con;
        header('Content-Type: application/json; charset=UTF-8');

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
            return;
        }

        $wargaRes = @mysqli_query($con, "SELECT nama_siswa FROM data_siswa WHERE id_siswa = '$id' LIMIT 1");
        $warga = ($wargaRes && is_object($wargaRes)) ? mysqli_fetch_assoc($wargaRes) : null;
        $nama = $warga['nama_siswa'] ?? "ID $id";

        $res = @mysqli_query($con, "UPDATE data_siswa SET is_deleted = 1, deleted_at = NOW() WHERE id_siswa = '$id'");
        if (!$res) {
            // Fallback direct delete if is_deleted column does not exist
            $res = @mysqli_query($con, "DELETE FROM data_siswa WHERE id_siswa = '$id'");
        }

        if ($res) {
            @audit_log('SOFT_DELETE', 'warga', $id, "Menghapus warga: $nama");
            echo json_encode(['status' => 'success', 'message' => 'Data warga berhasil dihapus']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data warga']);
        }
    }

    // ==========================================
    // CRUD WILAYAH (RW / ZONA) & KELOMPOK (RT / UNIT)
    // ==========================================

    public function simpan_wilayah()
    {
        global $con;
        header('Content-Type: application/json; charset=UTF-8');

        $id = (int)($_POST['id_wilayah'] ?? 0);
        $nama = trim($_POST['nama_wilayah'] ?? '');

        if (empty($nama)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama wilayah wajib diisi']);
            return;
        }

        $namaEsc = mysqli_real_escape_string($con, $nama);

        if ($id > 0) {
            $res = @mysqli_query($con, "UPDATE data_jenjang SET nama_jenjang = '$namaEsc' WHERE id_jenjang = '$id'");
            $msg = 'Wilayah berhasil diperbarui';
            @audit_log('UPDATE', 'wilayah', $id, "Memperbarui nama wilayah: $nama");
        } else {
            $res = @mysqli_query($con, "INSERT INTO data_jenjang (nama_jenjang) VALUES ('$namaEsc')");
            $msg = 'Wilayah baru berhasil ditambahkan';
            $id = mysqli_insert_id($con);
            @audit_log('CREATE', 'wilayah', $id, "Menambahkan wilayah baru: $nama");
        }

        if ($res) {
            echo json_encode(['status' => 'success', 'message' => $msg]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan wilayah']);
        }
    }

    public function hapus_wilayah()
    {
        global $con;
        header('Content-Type: application/json; charset=UTF-8');

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
            return;
        }

        $res = @mysqli_query($con, "UPDATE data_jenjang SET is_deleted = 1, deleted_at = NOW() WHERE id_jenjang = '$id'");
        if (!$res) {
            $res = @mysqli_query($con, "DELETE FROM data_jenjang WHERE id_jenjang = '$id'");
        }

        if ($res) {
            @audit_log('SOFT_DELETE', 'wilayah', $id, "Menghapus wilayah ID $id");
            echo json_encode(['status' => 'success', 'message' => 'Wilayah berhasil dihapus']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus wilayah']);
        }
    }

    public function simpan_kelompok()
    {
        global $con;
        header('Content-Type: application/json; charset=UTF-8');

        $id = (int)($_POST['id_kelompok'] ?? 0);
        $nama = trim($_POST['nama_kelompok'] ?? '');
        $id_jenjang = (int)($_POST['id_jenjang'] ?? 0);

        if (empty($nama)) {
            echo json_encode(['status' => 'error', 'message' => 'Nama kelompok/RT wajib diisi']);
            return;
        }

        $namaEsc = mysqli_real_escape_string($con, $nama);

        if ($id > 0) {
            $res = @mysqli_query($con, "UPDATE data_kelas SET nama_kelas = '$namaEsc', id_jenjang = '$id_jenjang' WHERE id_kelas = '$id'");
            $msg = 'Kelompok berhasil diperbarui';
            @audit_log('UPDATE', 'kelompok', $id, "Memperbarui kelompok: $nama");
        } else {
            $res = @mysqli_query($con, "INSERT INTO data_kelas (nama_kelas, id_jenjang, id_sesi) VALUES ('$namaEsc', '$id_jenjang', '1')");
            $msg = 'Kelompok baru berhasil ditambahkan';
            $id = mysqli_insert_id($con);
            @audit_log('CREATE', 'kelompok', $id, "Menambahkan kelompok baru: $nama");
        }

        if ($res) {
            echo json_encode(['status' => 'success', 'message' => $msg]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan kelompok']);
        }
    }

    public function hapus_kelompok()
    {
        global $con;
        header('Content-Type: application/json; charset=UTF-8');

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'ID tidak valid']);
            return;
        }

        if ($res) {
            @audit_log('SOFT_DELETE', 'kelompok', $id, "Menghapus kelompok ID $id");
            echo json_encode(['status' => 'success', 'message' => 'Kelompok berhasil dihapus']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus kelompok']);
        }
    }

    public function simpan_warga()
    {
        $this->simpan();
    }

    public function update_warga()
    {
        $this->simpan();
    }

    public function hapus_warga()
    {
        $this->hapus();
    }
}
