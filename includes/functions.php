<?php
/**
 * Utility Functions
 */

/**
 * Ensure session is properly initialized
 */
function ensureSessionStarted() {
    if (session_status() === PHP_SESSION_NONE) {
        // Configure session settings if not already configured
        if (!ini_get('session.name') || ini_get('session.name') === 'PHPSESSID') {
            session_name(SESSION_NAME);
        }
        
        session_start();
        
        // Log session initialization
        if (defined('DEBUG_CSRF') && DEBUG_CSRF) {
            error_log("Session initialized: " . session_id());
        }
    }
    
    // Regenerate session ID periodically for security (but preserve CSRF token)
    if (!isset($_SESSION['session_regenerated'])) {
        $oldToken = $_SESSION[CSRF_TOKEN_NAME] ?? null;
        session_regenerate_id(true);
        $_SESSION['session_regenerated'] = time();
        
        // Restore CSRF token after regeneration
        if ($oldToken) {
            $_SESSION[CSRF_TOKEN_NAME] = $oldToken;
        }
        
        if (defined('DEBUG_CSRF') && DEBUG_CSRF) {
            error_log("Session ID regenerated: " . session_id());
        }
    }
    
    return session_id();
}

/**
 * Generate CSRF Token
 */
function generateCSRFToken() {
    // Ensure session is started
    ensureSessionStarted();
    
    // Generate token if not exists
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        
        // Log token generation for debugging (only in development)
        if (defined('DEBUG_CSRF') && DEBUG_CSRF) {
            error_log("CSRF Debug: Generated new token for session " . session_id());
        }
    }
    
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF Token
 */
function verifyCSRFToken($token) {
    // Ensure session is started
    ensureSessionStarted();
    
    // Check if session token exists
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        if (defined('DEBUG_CSRF') && DEBUG_CSRF) {
            error_log("CSRF Debug: No session token found");
        }
        return false;
    }
    
    // Check if provided token is empty
    if (empty($token)) {
        if (defined('DEBUG_CSRF') && DEBUG_CSRF) {
            error_log("CSRF Debug: Provided token is empty");
        }
        return false;
    }
    
    // Perform secure comparison
    $result = hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    
    // Log verification attempt for debugging (only in development)
    if (defined('DEBUG_CSRF') && DEBUG_CSRF) {
        error_log("CSRF Debug: Verifying token for session " . session_id());
        error_log("CSRF Debug: Session token exists: YES");
        error_log("CSRF Debug: Provided token length: " . strlen($token));
        error_log("CSRF Debug: Verification result: " . ($result ? 'PASS' : 'FAIL'));
    }
    
    return $result;
}

/**
 * Enhanced CSRF Token Verification with Fallback
 */
function verifyCSRFTokenWithFallback($token) {
    // First attempt normal verification
    if (verifyCSRFToken($token)) {
        return true;
    }
    
    // Fallback: Check if token was just generated (for same-request scenarios)
    if (isset($_SESSION[CSRF_TOKEN_NAME]) && !empty($token)) {
        // Trim whitespace that might have been added
        $cleanToken = trim($token);
        if (hash_equals($_SESSION[CSRF_TOKEN_NAME], $cleanToken)) {
            if (defined('DEBUG_CSRF') && DEBUG_CSRF) {
                error_log("CSRF Debug: Fallback verification successful (whitespace issue)");
            }
            return true;
        }
    }
    
    // Log final failure (always log CSRF failures for security)
    error_log("CSRF Error: Token verification failed for session " . session_id());
    return false;
}

/**
 * Generate unique student code
 */
function generateStudentCode() {
    return 'STU' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

/**
 * Upload file
 */
function uploadFile($file, $directory, $allowedTypes = ALLOWED_IMAGE_TYPES) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => ERROR_UPLOAD_FAILED];
    }

    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => ERROR_FILE_TOO_LARGE];
    }

    // Check file type
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedTypes)) {
        return ['success' => false, 'message' => ERROR_INVALID_FILE_TYPE];
    }

    // Generate unique filename
    $fileName = uniqid() . '_' . time() . '.' . $fileExtension;
    $uploadPath = UPLOAD_PATH . $directory . '/' . $fileName;

    // Create directory if it doesn't exist
    if (!is_dir(UPLOAD_PATH . $directory)) {
        if (!mkdir(UPLOAD_PATH . $directory, 0755, true)) {
            return ['success' => false, 'message' => ERROR_UPLOAD_FAILED];
        }
    }

    // Check if directory is writable
    if (!is_writable(UPLOAD_PATH . $directory)) {
        return ['success' => false, 'message' => ERROR_UPLOAD_FAILED];
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => true, 'filename' => $fileName];
    } else {
        return ['success' => false, 'message' => ERROR_UPLOAD_FAILED];
    }
}

/**
 * Delete file
 */
function deleteFile($filename, $directory) {
    $filePath = UPLOAD_PATH . $directory . '/' . $filename;
    if (file_exists($filePath)) {
        return unlink($filePath);
    }
    return true;
}

/**
 * Get file URL
 */
function getFileUrl($filename, $directory) {
    if (empty($filename)) return '';
    return UPLOAD_URL . $directory . '/' . $filename;
}

/**
 * Format Lao date
 */
function formatLaoDate($date) {
    $months = [
        1 => 'ມັງກອນ', 2 => 'ກຸມພາ', 3 => 'ມີນາ', 4 => 'ເມສາ',
        5 => 'ພຶດສະພາ', 6 => 'ມິຖຸນາ', 7 => 'ກໍລະກົດ', 8 => 'ສິງຫາ',
        9 => 'ກັນຍາ', 10 => 'ຕຸລາ', 11 => 'ພະຈິກ', 12 => 'ທັນວາ'
    ];
    
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = $months[date('n', $timestamp)];
    $year = date('Y', $timestamp) + 543; // Convert to Buddhist year
    
    return "{$day} {$month} {$year}";
}

/**
 * Redirect function
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check user role
 */
function hasRole($requiredRole) {
    if (!isLoggedIn()) return false;
    
    $userRole = $_SESSION['user_role'] ?? '';
    
    $roleHierarchy = [
        ROLE_VIEWER => 1,
        ROLE_STAFF => 2,
        ROLE_ADMIN => 3
    ];
    
    return ($roleHierarchy[$userRole] ?? 0) >= ($roleHierarchy[$requiredRole] ?? 0);
}

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Log activity
 */
function logActivity($action, $details = '') {
    try {
        $logFile = dirname(__DIR__) . '/logs/activity.log';
        $logDir = dirname($logFile);
        
        if (!is_dir($logDir)) {
            if (!@mkdir($logDir, 0755, true)) {
                error_log("Failed to create logs directory: $logDir");
                return false;
            }
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $userId = $_SESSION['user_id'] ?? 'anonymous';
        $username = $_SESSION['username'] ?? 'anonymous';
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        $logEntry = "[{$timestamp}] User: {$username} (ID: {$userId}) | IP: {$ip} | Action: {$action} | Details: {$details}" . PHP_EOL;
        
        if (!@file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX)) {
            error_log("Failed to write to activity log: $logFile");
            return false;
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Log activity error: " . $e->getMessage());
        return false;
    }
}
?>