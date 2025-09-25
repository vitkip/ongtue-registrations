<?php
/**
 * Configuration Settings
 */

// Application Settings
define('APP_NAME', 'ລະບົບລົງທະບຽນຮັບໃບປະກາດນິຍະບັດ');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/registrations');

// File Upload Settings
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', APP_URL . '/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif']);
define('ALLOWED_DOC_TYPES', ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png']);

// Session Settings
define('SESSION_NAME', 'cert_registration_session');
define('SESSION_LIFETIME', 3600); // 1 hour

// Security Settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('HASH_ALGO', 'sha256');

// Pagination Settings
define('RECORDS_PER_PAGE', 10);

// Status Options
define('STATUS_PENDING', 'pending');
define('STATUS_APPROVED', 'approved');
define('STATUS_REJECTED', 'rejected');

// Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_STAFF', 'staff');
define('ROLE_VIEWER', 'viewer');

// Error Messages (Lao)
define('ERROR_REQUIRED_FIELD', 'ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບຖ້ວນ');
define('ERROR_INVALID_EMAIL', 'ອີເມວບໍ່ຖືກຕ້ອງ');
define('ERROR_FILE_TOO_LARGE', 'ໄຟລ້ໃຫຍ່ເກີນກຳນົດ');
define('ERROR_INVALID_FILE_TYPE', 'ປະເພດໄຟລ້ບໍ່ຮອງຮັບ');
define('ERROR_UPLOAD_FAILED', 'ອັບໂຫຼດໄຟລ້ບໍ່ສຳເລັດ');

// Success Messages (Lao)
define('SUCCESS_REGISTRATION', 'ລົງທະບຽນສຳເລັດແລ້ວ');
define('SUCCESS_UPDATE', 'ອັບເດດຂໍ້ມູນສຳເລັດແລ້ວ');
define('SUCCESS_DELETE', 'ລົບຂໍ້ມູນສຳເລັດແລ້ວ');

// Create upload directories if they don't exist (with error handling)
if (!file_exists(UPLOAD_PATH)) {
    if (!@mkdir(UPLOAD_PATH, 0755, true)) {
        error_log("Failed to create upload directory: " . UPLOAD_PATH);
    }
}
if (!file_exists(UPLOAD_PATH . 'profiles/')) {
    if (!@mkdir(UPLOAD_PATH . 'profiles/', 0755, true)) {
        error_log("Failed to create profiles directory: " . UPLOAD_PATH . 'profiles/');
    }
}
if (!file_exists(UPLOAD_PATH . 'payments/')) {
    if (!@mkdir(UPLOAD_PATH . 'payments/', 0755, true)) {
        error_log("Failed to create payments directory: " . UPLOAD_PATH . 'payments/');
    }
}
?>