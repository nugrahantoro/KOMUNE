<?php

class Feed extends Controller
{
    /**
     * Endpoint API JSON Feed Terpadu untuk Aplikasi Mobile (iOS / Android / Web)
     */
    public function get_feed_json()
    {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        $kategori = $_GET['kategori'] ?? '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $search = $_GET['search'] ?? '';

        // 1. Ambil Artikel Published Aktif
        $artConditions = ["(status = 'published')", "(is_deleted = 0 OR is_deleted IS NULL)"];
        $artParams = [];
        $artTypes = "";

        if (!empty($kategori)) {
            $artConditions[] = "kategori = ?";
            $artParams[] = $kategori;
            $artTypes .= "s";
        }
        if (!empty($search)) {
            $artConditions[] = "(judul LIKE ? OR penulis LIKE ?)";
            $term = "%" . $search . "%";
            $artParams[] = $term;
            $artParams[] = $term;
            $artTypes .= "ss";
        }

        $artWhere = implode(" AND ", $artConditions);
        $artSql = "SELECT id_artikel, judul, slug, kategori, penulis, role_penulis, thumbnail, ringkasan, views, created_at, 'artikel' AS tipe 
                   FROM tbl_artikel 
                   WHERE $artWhere 
                   ORDER BY id_artikel DESC LIMIT $limit";
        $articles = db_prepared_all($artSql, $artParams, $artTypes);

        // 2. Ambil Video Published Aktif
        $vidConditions = ["(status = 'published')", "(is_deleted = 0 OR is_deleted IS NULL)"];
        $vidParams = [];
        $vidTypes = "";

        if (!empty($kategori)) {
            $vidConditions[] = "kategori = ?";
            $vidParams[] = $kategori;
            $vidTypes .= "s";
        }
        if (!empty($search)) {
            $vidConditions[] = "(judul LIKE ? OR pemateri LIKE ?)";
            $term = "%" . $search . "%";
            $vidParams[] = $term;
            $vidParams[] = $term;
            $vidTypes .= "ss";
        }

        $vidWhere = implode(" AND ", $vidConditions);
        $vidSql = "SELECT id_video, judul, kategori, pemateri, role_pemateri, url_video, platform, embed_id, thumbnail, deskripsi, views, created_at, 'video' AS tipe 
                   FROM tbl_video 
                   WHERE $vidWhere 
                   ORDER BY id_video DESC LIMIT $limit";
        $videos = db_prepared_all($vidSql, $vidParams, $vidTypes);

        echo json_encode([
            'status' => '00',
            'message' => 'Feed konten berhasil dimuat',
            'data' => [
                'articles' => $articles,
                'videos' => $videos,
                'total_articles' => count($articles),
                'total_videos' => count($videos)
            ]
        ]);
    }

    /**
     * Increment view counter artikel / video
     */
    public function view_counter()
    {
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        $tipe = $_POST['tipe'] ?? $_GET['tipe'] ?? 'artikel';

        if (!$id) return;

        if ($tipe === 'artikel') {
            db_prepared_query("UPDATE tbl_artikel SET views = views + 1 WHERE id_artikel = ?", [$id], "i");
        } else if ($tipe === 'video') {
            db_prepared_query("UPDATE tbl_video SET views = views + 1 WHERE id_video = ?", [$id], "i");
        }

        echo json_encode(['status' => 'success']);
    }
}
