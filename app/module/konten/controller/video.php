<?php

class Video extends Controller
{
    /**
     * Parse YouTube ID dari berbagai format link (youtube.com/watch?v=, youtu.be/, shorts, embed)
     */
    private function parse_youtube_id($url)
    {
        $pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i';
        if (preg_match($pattern, $url, $match)) {
            return $match[1];
        }
        return null;
    }

    /**
     * Ambil daftar video aktif
     */
    public function get_list()
    {
        $post = Security::sanitize($_POST);
        $kategori = $post['kategori'] ?? $_GET['kategori'] ?? '';
        $search = $post['search'] ?? $_GET['search'] ?? '';

        $conditions = ["(is_deleted = 0 OR is_deleted IS NULL)"];
        $params = [];
        $types = "";

        if (!empty($kategori)) {
            $conditions[] = "kategori = ?";
            $params[] = $kategori;
            $types .= "s";
        }

        if (!empty($search)) {
            $conditions[] = "(judul LIKE ? OR pemateri LIKE ? OR deskripsi LIKE ?)";
            $term = "%" . $search . "%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $types .= "sss";
        }

        $whereSql = implode(" AND ", $conditions);
        $sql = "SELECT * FROM tbl_video WHERE $whereSql ORDER BY id_video DESC";

        $data = db_prepared_all($sql, $params, $types);

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'total' => count($data),
            'data' => $data
        ]);
    }

    /**
     * Detail satu video
     */
    public function get_detail($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID video tidak valid']);
            return;
        }

        $sql = "SELECT * FROM tbl_video WHERE id_video = ? AND (is_deleted = 0 OR is_deleted IS NULL) LIMIT 1";
        $video = db_prepared_assoc($sql, [$id], "i");

        header('Content-Type: application/json');
        if ($video) {
            echo json_encode(['status' => 'success', 'data' => $video]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Video tidak ditemukan']);
        }
    }

    /**
     * Simpan Video Baru
     */
    public function simpan()
    {
        $post = Security::sanitize($_POST);
        $judul = $post['judul'] ?? '';
        $kategori = $post['kategori'] ?? 'Kajian';
        $pemateri = $post['pemateri'] ?? $_SESSION['nama_us'] ?? 'Ustadz / Pengajar';
        $rolePemateri = $post['role_pemateri'] ?? 'Ustadz';
        $urlVideo = $post['url_video'] ?? '';
        $deskripsi = $post['deskripsi'] ?? '';
        $status = $post['status'] ?? 'published';

        if (empty($judul) || empty($urlVideo)) {
            echo json_encode(['status' => 'error', 'message' => 'Judul dan URL link video wajib diisi']);
            return;
        }

        $embedId = $this->parse_youtube_id($urlVideo);
        $platform = $embedId ? 'youtube' : 'other';
        $thumbnail = $embedId ? "https://img.youtube.com/vi/$embedId/hqdefault.jpg" : ($post['thumbnail'] ?? '');

        $userId = $_SESSION['id'] ?? null;

        $sql = "INSERT INTO tbl_video (
                    judul, kategori, pemateri, role_pemateri, url_video, platform, 
                    embed_id, thumbnail, deskripsi, status, views, user_id, is_deleted, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, 0, NOW())";

        $res = db_prepared_query(
            $sql,
            [$judul, $kategori, $pemateri, $rolePemateri, $urlVideo, $platform, $embedId, $thumbnail, $deskripsi, $status, $userId],
            "sssssssssss"
        );

        if ($res) {
            $newId = db_id();
            audit_log('CREATE', 'video', $newId, "Menambahkan video kajian: $judul oleh $pemateri");

            echo json_encode(['status' => 'success', 'message' => 'Video berhasil ditambahkan', 'id' => $newId]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data video ke database']);
        }
    }

    /**
     * Update Video
     */
    public function update()
    {
        $post = Security::sanitize($_POST);
        $id = $post['id_video'] ?? null;

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID video tidak valid']);
            return;
        }

        $old = db_prepared_assoc("SELECT * FROM tbl_video WHERE id_video = ?", [$id], "i");
        if (!$old) {
            echo json_encode(['status' => 'error', 'message' => 'Video tidak ditemukan']);
            return;
        }

        $judul = $post['judul'] ?? $old['judul'];
        $kategori = $post['kategori'] ?? $old['kategori'];
        $pemateri = $post['pemateri'] ?? $old['pemateri'];
        $rolePemateri = $post['role_pemateri'] ?? $old['role_pemateri'];
        $urlVideo = $post['url_video'] ?? $old['url_video'];
        $deskripsi = $post['deskripsi'] ?? $old['deskripsi'];
        $status = $post['status'] ?? $old['status'];

        $embedId = $this->parse_youtube_id($urlVideo);
        $platform = $embedId ? 'youtube' : $old['platform'];
        $thumbnail = $embedId ? "https://img.youtube.com/vi/$embedId/hqdefault.jpg" : ($post['thumbnail'] ?? $old['thumbnail']);

        $sql = "UPDATE tbl_video SET 
                    judul = ?, kategori = ?, pemateri = ?, role_pemateri = ?, url_video = ?, 
                    platform = ?, embed_id = ?, thumbnail = ?, deskripsi = ?, status = ?, updated_at = NOW()
                WHERE id_video = ?";

        $res = db_prepared_query(
            $sql,
            [$judul, $kategori, $pemateri, $rolePemateri, $urlVideo, $platform, $embedId, $thumbnail, $deskripsi, $status, $id],
            "ssssssssssi"
        );

        if ($res !== false) {
            audit_log('UPDATE', 'video', $id, "Memperbarui video kajian: $judul", $old, $post);
            echo json_encode(['status' => 'success', 'message' => 'Video berhasil diperbarui']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui data video']);
        }
    }

    /**
     * Soft Delete Video
     */
    public function hapus()
    {
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID video tidak valid']);
            return;
        }

        $video = db_prepared_assoc("SELECT * FROM tbl_video WHERE id_video = ?", [$id], "i");
        if (!$video) {
            echo json_encode(['status' => 'error', 'message' => 'Video tidak ditemukan']);
            return;
        }

        $sql = "UPDATE tbl_video SET is_deleted = 1, deleted_at = NOW() WHERE id_video = ?";
        $res = db_prepared_query($sql, [$id], "i");

        if ($res) {
            audit_log('SOFT_DELETE', 'video', $id, "Soft delete video: {$video['judul']}");
            echo json_encode(['status' => 'success', 'message' => 'Video berhasil dihapus (Soft Delete)']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus video']);
        }
    }
}
