<?php

class Artikel extends Controller
{
    /**
     * Ambil daftar artikel dengan filter soft delete
     */
    public function get_list()
    {
        $post = Security::sanitize($_POST);
        $kategori = $post['kategori'] ?? $_GET['kategori'] ?? '';
        $status = $post['status'] ?? $_GET['status'] ?? '';
        $search = $post['search'] ?? $_GET['search'] ?? '';

        $conditions = ["(is_deleted = 0 OR is_deleted IS NULL)"];
        $params = [];
        $types = "";

        if (!empty($kategori)) {
            $conditions[] = "kategori = ?";
            $params[] = $kategori;
            $types .= "s";
        }

        if (!empty($status)) {
            $conditions[] = "status = ?";
            $params[] = $status;
            $types .= "s";
        }

        if (!empty($search)) {
            $conditions[] = "(judul LIKE ? OR penulis LIKE ? OR ringkasan LIKE ?)";
            $term = "%" . $search . "%";
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $types .= "sss";
        }

        $whereSql = implode(" AND ", $conditions);
        $sql = "SELECT id_artikel, judul, slug, kategori, penulis, role_penulis, thumbnail, ringkasan, status, views, created_at, updated_at 
                FROM tbl_artikel 
                WHERE $whereSql 
                ORDER BY id_artikel DESC";

        $data = db_prepared_all($sql, $params, $types);

        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'total' => count($data),
            'data' => $data
        ]);
    }

    /**
     * Detail satu artikel
     */
    public function get_detail($id = null)
    {
        $id = $id ?? $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID artikel tidak valid']);
            return;
        }

        $sql = "SELECT * FROM tbl_artikel WHERE id_artikel = ? AND (is_deleted = 0 OR is_deleted IS NULL) LIMIT 1";
        $artikel = db_prepared_assoc($sql, [$id], "i");

        header('Content-Type: application/json');
        if ($artikel) {
            echo json_encode(['status' => 'success', 'data' => $artikel]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Artikel tidak ditemukan atau telah dihapus']);
        }
    }

    /**
     * Tambah Artikel Baru
     */
    public function simpan()
    {
        $post = Security::sanitize($_POST);
        $judul = $post['judul'] ?? '';
        $kategori = $post['kategori'] ?? 'Edukasi';
        $penulis = $post['penulis'] ?? $_SESSION['nama_us'] ?? 'Pengurus';
        $rolePenulis = $post['role_penulis'] ?? 'Ustadz/Pengajar';
        $ringkasan = $post['ringkasan'] ?? '';
        $konten = $_POST['konten'] ?? ''; // rich content
        $status = $post['status'] ?? 'published';
        $thumbnail = $post['thumbnail'] ?? '';

        if (empty($judul) || empty($konten)) {
            echo json_encode(['status' => 'error', 'message' => 'Judul dan konten bacaan wajib diisi']);
            return;
        }

        $slug = Security::slugify($judul);
        $userId = $_SESSION['id'] ?? null;

        $sql = "INSERT INTO tbl_artikel (
                    judul, slug, kategori, penulis, role_penulis, user_id, 
                    thumbnail, ringkasan, konten, status, views, is_deleted, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, NOW())";

        $res = db_prepared_query(
            $sql,
            [$judul, $slug, $kategori, $penulis, $rolePenulis, $userId, $thumbnail, $ringkasan, $konten, $status],
            "ssssssssss"
        );

        if ($res) {
            $newId = db_id();
            audit_log('CREATE', 'artikel', $newId, "Membuat artikel baru: $judul oleh $penulis");

            echo json_encode(['status' => 'success', 'message' => 'Artikel berhasil diterbitkan', 'id' => $newId]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan artikel ke database']);
        }
    }

    /**
     * Update Artikel
     */
    public function update()
    {
        $post = Security::sanitize($_POST);
        $id = $post['id_artikel'] ?? null;

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID artikel tidak valid']);
            return;
        }

        $old = db_prepared_assoc("SELECT * FROM tbl_artikel WHERE id_artikel = ?", [$id], "i");
        if (!$old) {
            echo json_encode(['status' => 'error', 'message' => 'Artikel tidak ditemukan']);
            return;
        }

        $judul = $post['judul'] ?? $old['judul'];
        $kategori = $post['kategori'] ?? $old['kategori'];
        $penulis = $post['penulis'] ?? $old['penulis'];
        $rolePenulis = $post['role_penulis'] ?? $old['role_penulis'];
        $ringkasan = $post['ringkasan'] ?? $old['ringkasan'];
        $konten = $_POST['konten'] ?? $old['konten'];
        $status = $post['status'] ?? $old['status'];
        $thumbnail = $post['thumbnail'] ?? $old['thumbnail'];
        $slug = Security::slugify($judul);

        $sql = "UPDATE tbl_artikel SET 
                    judul = ?, slug = ?, kategori = ?, penulis = ?, role_penulis = ?, 
                    thumbnail = ?, ringkasan = ?, konten = ?, status = ?, updated_at = NOW()
                WHERE id_artikel = ?";

        $res = db_prepared_query(
            $sql,
            [$judul, $slug, $kategori, $penulis, $rolePenulis, $thumbnail, $ringkasan, $konten, $status, $id],
            "sssssssssi"
        );

        if ($res !== false) {
            audit_log('UPDATE', 'artikel', $id, "Memperbarui artikel: $judul", $old, $post);
            echo json_encode(['status' => 'success', 'message' => 'Artikel berhasil diperbarui']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui artikel']);
        }
    }

    /**
     * Soft Delete Artikel
     */
    public function hapus()
    {
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID artikel tidak valid']);
            return;
        }

        $artikel = db_prepared_assoc("SELECT * FROM tbl_artikel WHERE id_artikel = ?", [$id], "i");
        if (!$artikel) {
            echo json_encode(['status' => 'error', 'message' => 'Artikel tidak ditemukan']);
            return;
        }

        $sql = "UPDATE tbl_artikel SET is_deleted = 1, deleted_at = NOW() WHERE id_artikel = ?";
        $res = db_prepared_query($sql, [$id], "i");

        if ($res) {
            audit_log('SOFT_DELETE', 'artikel', $id, "Soft delete artikel: {$artikel['judul']}");
            echo json_encode(['status' => 'success', 'message' => 'Artikel berhasil dihapus (Soft Delete)']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus artikel']);
        }
    }
}
