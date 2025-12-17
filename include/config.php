<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Detect environment - Replit uses DATABASE_URL with PostgreSQL
$is_replit = getenv('DATABASE_URL') !== false;

if ($is_replit) {
    // Replit PostgreSQL Configuration
    $database_url = getenv('DATABASE_URL');
    $db_parts = parse_url($database_url);
    
    $db_host = $db_parts['host'];
    $db_port = $db_parts['port'] ?? 5432;
    $db_user = $db_parts['user'];
    $db_pass = $db_parts['pass'];
    $db_name = ltrim($db_parts['path'], '/');
    
    // PostgreSQL connection using PDO
    try {
        $pdo = new PDO(
            "pgsql:host=$db_host;port=$db_port;dbname=$db_name",
            $db_user,
            $db_pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $use_pdo = true;
        $con = null;
    } catch (PDOException $e) {
        $use_pdo = false;
        $con = null;
        $pdo = null;
    }
} else {
    // Local XAMPP MySQL Configuration
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "bhairavnath_construction";
    
    $con = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    $use_pdo = false;
    $pdo = null;
    
    if ($con) {
        mysqli_set_charset($con, "utf8mb4");
    }
}

// Site Configuration
define('SITE_NAME', 'Bhairavnath Construction');
define('SITE_URL', $is_replit ? 'https://' . getenv('REPL_SLUG') . '.' . getenv('REPL_OWNER') . '.repl.co/' : 'http://localhost/bhairavnath-construction/');
define('UPLOADS_PATH', __DIR__ . '/../admin/uploads/');
define('UPLOADS_URL', 'uploads/');
define('IS_REPLIT', $is_replit);

// Helper Functions
function sanitize($data) {
    global $con, $pdo, $use_pdo;
    $data = htmlspecialchars(trim($data));
    if ($use_pdo) {
        return $data;
    } elseif ($con) {
        return mysqli_real_escape_string($con, $data);
    }
    return $data;
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function db_query($query) {
    global $con, $pdo, $use_pdo;
    
    if ($use_pdo) {
        try {
            return $pdo->query($query);
        } catch (PDOException $e) {
            return false;
        }
    } elseif ($con) {
        return mysqli_query($con, $query);
    }
    return false;
}

function db_fetch($result) {
    global $use_pdo;
    
    if (!$result) return null;
    
    if ($use_pdo) {
        return $result->fetch(PDO::FETCH_ASSOC);
    } else {
        return mysqli_fetch_assoc($result);
    }
}

function db_num_rows($result) {
    global $use_pdo;
    
    if (!$result) return 0;
    
    if ($use_pdo) {
        return $result->rowCount();
    } else {
        return mysqli_num_rows($result);
    }
}

function db_insert_id() {
    global $con, $pdo, $use_pdo;
    
    if ($use_pdo) {
        return $pdo->lastInsertId();
    } elseif ($con) {
        return mysqli_insert_id($con);
    }
    return 0;
}

function getAdmin() {
    if (isLoggedIn()) {
        $id = $_SESSION['admin_id'];
        $result = db_query("SELECT * FROM admin WHERE id = $id");
        return db_fetch($result);
    }
    return null;
}

function uploadImage($file, $folder = 'projects') {
    $target_dir = UPLOADS_PATH . $folder . '/';
    
    if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            error_log("Failed to create directory: $target_dir");
            return false;
        }
    }
    
    // Check for upload errors
    if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
        error_log("Upload error code: " . $file['error']);
        return false;
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($file_extension, $allowed)) {
        error_log("Invalid file extension: $file_extension");
        return false;
    }
    
    $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        // Return relative path for database storage
        return UPLOADS_URL . $folder . '/' . $new_filename;
    }
    
    error_log("Failed to move uploaded file to: $target_file");
    return false;
}

function uploadMedia($file, $folder = 'gallery') {
    $target_dir = UPLOADS_PATH . $folder . '/';
    
    if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            return ['success' => false, 'error' => 'Failed to create upload directory'];
        }
    }
    
    // Check for upload errors
    if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write to disk',
        ];
        $error_msg = $errors[$file['error']] ?? 'Unknown upload error';
        return ['success' => false, 'error' => $error_msg];
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $image_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $video_types = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'];
    $allowed = array_merge($image_types, $video_types);
    
    if (!in_array($file_extension, $allowed)) {
        return ['success' => false, 'error' => 'Invalid file type: ' . $file_extension];
    }
    
    $file_type = in_array($file_extension, $image_types) ? 'image' : 'video';
    $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return [
            'success' => true,
            'path' => UPLOADS_URL . $folder . '/' . $new_filename,
            'type' => $file_type
        ];
    }
    
    return ['success' => false, 'error' => 'Failed to move uploaded file'];
}

function deleteImage($path) {
    // Handle both absolute and relative paths
    $full_path = $path;
    if (strpos($path, UPLOADS_PATH) === false) {
        // Convert relative URL path to absolute file path
        $full_path = UPLOADS_PATH . str_replace(UPLOADS_URL, '', $path);
    }
    
    if (file_exists($full_path)) {
        unlink($full_path);
        return true;
    }
    return false;
}

// Database escape function for both MySQL and PDO
function db_escape($value) {
    global $con, $pdo, $use_pdo;
    
    if ($use_pdo) {
        return $pdo->quote($value);
    } elseif ($con) {
        return "'" . mysqli_real_escape_string($con, $value) . "'";
    }
    return "'" . addslashes($value) . "'";
}

// Raw escape without quotes for compatibility
function db_escape_raw($value) {
    global $con, $pdo, $use_pdo;
    
    if ($use_pdo) {
        return substr($pdo->quote($value), 1, -1);
    } elseif ($con) {
        return mysqli_real_escape_string($con, $value);
    }
    return addslashes($value);
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

function getProjectCount($status = null) {
    if ($status) {
        $result = db_query("SELECT COUNT(*) as count FROM projects WHERE status = '$status'");
    } else {
        $result = db_query("SELECT COUNT(*) as count FROM projects");
    }
    $row = db_fetch($result);
    return $row['count'] ?? 0;
}

function getUnreadInquiries() {
    $result = db_query("SELECT COUNT(*) as count FROM inquiries WHERE is_read = 0");
    $row = db_fetch($result);
    return $row['count'] ?? 0;
}
?>
