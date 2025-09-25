<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in and has appropriate role
if (!isLoggedIn() || !hasRole(ROLE_VIEWER)) {
    http_response_code(403);
    die('Access denied');
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? '';
$searchQuery = $_GET['search'] ?? '';

// Build query
$whereConditions = [];
$params = [];

if ($statusFilter && in_array($statusFilter, [STATUS_PENDING, STATUS_APPROVED, STATUS_REJECTED])) {
    $whereConditions[] = "status = ?";
    $params[] = $statusFilter;
}

if ($searchQuery) {
    $whereConditions[] = "(first_name LIKE ? OR last_name LIKE ? OR student_code LIKE ? OR email LIKE ? OR major LIKE ?)";
    $searchParam = "%$searchQuery%";
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
}

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

try {
    // Get all registrations (no limit for export)
    $query = "SELECT * FROM registrations $whereClause ORDER BY created_at DESC";
    $stmt = $db->query($query, $params);
    $registrations = $stmt->fetchAll();

} catch (Exception $e) {
    http_response_code(500);
    die('Database error: ' . $e->getMessage());
}

// Function to convert status to Lao text
function getStatusText($status) {
    switch ($status) {
        case 'approved': return 'ອະນຸມັດແລ້ວ';
        case 'pending': return 'ລໍຖ້າການອະນຸມັດ';
        case 'rejected': return 'ປະຕິເສດ';
        default: return $status;
    }
}

// Force CSV export (Excel compatible)
$timestamp = date('Y-m-d_H-i-s');
$filename = "registrations_export_{$timestamp}.xls"; // .xls extension for better Excel compatibility

// Set headers for Excel-compatible CSV
header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// Add BOM for proper UTF-8 encoding in Excel
echo "\xEF\xBB\xBF";

// Create HTML table format for better Excel compatibility
echo '<html><head><meta charset="UTF-8"></head><body>';
echo '<table border="1">';

// Title row
echo '<tr><td colspan="11" style="font-weight:bold; background-color:#4472C4; color:white;">';
echo 'ລາຍງານການລົງທະບຽນຮັບໃບຢັ້ງຢືນ - ສົ່ງອອກວັນທີ່: ' . date('d/m/Y H:i');
echo '</td></tr>';

// Empty row
echo '<tr><td colspan="11"></td></tr>';

// Headers
$headers = [
    'ລະຫັດນິສິດ',
    'ຊື່',
    'ນາມສະກຸນ', 
    'ສາຂາວິຊາ',
    'ປີສຳເລັດການສຶກສາ',
    'ອີເມວ',
    'ເບີໂທລະສັບ',
    'ສະຖານະ',
    'ວັນທີ່ລົງທະບຽນ',
    'ຮູບໂປຣໄຟລ໌',
    'ໃບຢັ້ງຢືນການຈ່າຍເງິນ'
];

echo '<tr style="font-weight:bold; background-color:#E7E6E6;">';
foreach ($headers as $header) {
    echo '<td>' . htmlspecialchars($header) . '</td>';
}
echo '</tr>';

// Data rows
foreach ($registrations as $registration) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($registration['student_code']) . '</td>';
    echo '<td>' . htmlspecialchars($registration['first_name']) . '</td>';
    echo '<td>' . htmlspecialchars($registration['last_name']) . '</td>';
    echo '<td>' . htmlspecialchars($registration['major']) . '</td>';
    echo '<td>' . htmlspecialchars($registration['graduation_year']) . '</td>';
    echo '<td>' . htmlspecialchars($registration['email']) . '</td>';
    echo '<td>' . htmlspecialchars($registration['phone']) . '</td>';
    echo '<td>' . htmlspecialchars(getStatusText($registration['status'])) . '</td>';
    echo '<td>' . date('d/m/Y H:i', strtotime($registration['created_at'])) . '</td>';
    echo '<td>' . ($registration['profile_image'] ? 'ມີ' : 'ບໍ່ມີ') . '</td>';
    echo '<td>' . ($registration['payment_proof'] ? 'ມີ' : 'ບໍ່ມີ') . '</td>';
    echo '</tr>';
}

echo '</table></body></html>';

// Log the export activity
logActivity('Excel export', "Exported " . count($registrations) . " registrations as Excel by " . ($_SESSION['username'] ?? 'unknown'));

// Exit to prevent any additional output
exit;
?>