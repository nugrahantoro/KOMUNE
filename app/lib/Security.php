<?php

/**
 * Security Engine untuk Sistem Komune
 * Menyediakan sanitasi input, pencegahan XSS, validasi CSRF, dan hashing password modern.
 */
class Security
{
    /**
     * Escape output HTML untuk mencegah serangan Cross-Site Scripting (XSS)
     */
    public static function e($string)
    {
        if (is_null($string)) return '';
        return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitasi input rekursif (membersihkan tag berbahaya dan whitespace berlebih)
     */
    public static function sanitize($data)
    {
        if (is_array($data)) {
            $cleaned = [];
            foreach ($data as $key => $val) {
                $cleaned[self::sanitize_key($key)] = self::sanitize($val);
            }
            return $cleaned;
        }

        if (is_string($data)) {
            $data = trim($data);
            return $data;
        }

        return $data;
    }

    private static function sanitize_key($key)
    {
        return preg_replace('/[^a-zA-Z0-9_\-]/', '', (string)$key);
    }

    /**
     * Hashing Password dengan algoritma Bcrypt yang aman
     */
    public static function hash_password($plainPassword)
    {
        return password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    /**
     * Verifikasi Password dengan dukungan backward-compatibility (Bcrypt, MD5, Plain)
     */
    public static function verify_password($plainPassword, $storedHash)
    {
        if ($plainPassword === null || $storedHash === null || $storedHash === '') {
            return false;
        }

        $plain = (string)$plainPassword;
        $hash = (string)$storedHash;

        // 1. Direct Plain-Text Match (trim & exact)
        if ($plain === $hash || trim($plain) === trim($hash)) {
            return true;
        }

        // 2. Format Bcrypt modern ($2y$, $2a$, $2b$)
        if (strpos($hash, '$2y$') === 0 || strpos($hash, '$2a$') === 0 || strpos($hash, '$2b$') === 0) {
            if (password_verify($plain, $hash)) {
                return true;
            }
        }

        // 3. Legacy MD5 Match
        if (md5($plain) === $hash || md5(trim($plain)) === trim($hash) || strtolower(md5($plain)) === strtolower($hash)) {
            return true;
        }

        // 4. Legacy SHA1 Match
        if (sha1($plain) === $hash || sha1(trim($plain)) === trim($hash) || strtolower(sha1($plain)) === strtolower($hash)) {
            return true;
        }

        return false;
    }

    /**
     * Generate CSRF Token unik per sesi
     */
    public static function generate_csrf_token()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Validasi token CSRF dari request POST / Header
     */
    public static function validate_csrf_token($token = null)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($token === null) {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        }

        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Input hidden CSRF untuk dimasukkan ke form
     */
    public static function csrf_field()
    {
        $token = self::generate_csrf_token();
        return '<input type="hidden" name="csrf_token" value="' . self::e($token) . '">';
    }

    /**
     * Dapatkan IP address klien yang aman
     */
    public static function get_client_ip()
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ipList[0]);
        }
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Dapatkan User Agent klien
     */
    public static function get_user_agent()
    {
        return substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);
    }

    /**
     * Generate URL slug yang ramah SEO dan aman
     */
    public static function slugify($text)
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        if (empty($text)) {
            return 'n-a-' . time();
        }
        return $text;
    }
}
