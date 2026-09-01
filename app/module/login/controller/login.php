<?php

class Login extends Controller
{
    public function index($param1 = '', $param2 = '')
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
            global $basepath;
            header("Location: " . $basepath . "main");
            exit;
        }
        $this->view('login/view/index');
    }

    public function proses()
    {
        global $con;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            echo "0";
            return;
        }

        if (!$con) {
            error_log("Database connection not ready during login.");
            echo "0";
            return;
        }

        $usernameEsc = mysqli_real_escape_string($con, $username);
        $query = mysqli_query($con, "SELECT * FROM users_log WHERE username = '$usernameEsc' LIMIT 1");

        if ($query && ($user = mysqli_fetch_assoc($query))) {
            // Cek soft delete jika kolom exists di database
            if (isset($user['is_deleted']) && (int)$user['is_deleted'] === 1) {
                echo "0";
                return;
            }

            // Verifikasi password fleksibel (Bcrypt, MD5, SHA1, atau Plain-Text)
            $isValid = Security::verify_password($password, $user['password']);

            if (!$isValid) {
                // Fallback direct check
                if ($password === $user['password'] || md5($password) === $user['password']) {
                    $isValid = true;
                }
            }

            if ($isValid) {
                // Register Session
                $_SESSION['username'] = $user['username'];
                $_SESSION['password'] = $user['password'];
                $_SESSION['id'] = $user['id'];
                $_SESSION['id_user'] = $user['id'];
                $_SESSION['jns'] = $user['jenis_user'];
                $_SESSION['nama_us'] = $user['nama_us'];
                $_SESSION['jabat'] = $user['jabat'] ?? 'Pengurus';
                $_SESSION['file_foto'] = $user['file_foto'] ?? '';

                // Log audit (suppressed if table not created yet)
                if (function_exists('audit_log')) {
                    @audit_log('LOGIN', 'auth', $user['id'], "Login berhasil: {$user['username']} ({$user['nama_us']})");
                }

                echo "1";
                return;
            }
        }

        echo "0";
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['id'] ?? null;
        $userName = $_SESSION['username'] ?? 'User';

        if ($userId && function_exists('audit_log')) {
            @audit_log('LOGOUT', 'auth', $userId, "User $userName logout dari sistem");
        }

        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();

        global $basepath;
        $target = !empty($basepath) ? $basepath . "login" : "login";
        header("Location: " . $target);
        exit;
    }

    public function js($param1)
    {
        $this->get_js('login/js/' . $param1);
    }
}