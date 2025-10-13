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
$yearFilter = $_GET['year'] ?? '';

// Build query
$whereConditions = [];
$params = [];

if ($statusFilter && in_array($statusFilter, [STATUS_PENDING, STATUS_APPROVED, STATUS_REJECTED])) {
    $whereConditions[] = "status = ?";
    $params[] = $statusFilter;
}

if ($searchQuery) {
    $whereConditions[] = "(first_name LIKE ? OR last_name LIKE ? OR first_name_en LIKE ? OR last_name_en LIKE ? OR student_code LIKE ? OR email LIKE ? OR major LIKE ?)";
    $searchParam = "%$searchQuery%";
    $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
}

if ($yearFilter) {
    $whereConditions[] = "graduation_year = ?";
    $params[] = $yearFilter;
}

$whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";

try {
    // Get all registrations (no limit for export)
    $query = "SELECT * FROM registrations $whereClause ORDER BY major ASC, graduation_year ASC, created_at DESC";
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
echo '<tr><td colspan="13" style="font-weight:bold; background-color:#4472C4; color:white;">';
echo 'ລາຍງານການລົງທະບຽນຮັບໃບຢັ້ງຢືນ - ສົ່ງອອກວັນທີ່: ' . date('d/m/Y H:i');
echo '</td></tr>';

// Filter info row
echo '<tr><td colspan="13" style="background-color:#F2F2F2;">';
echo 'ຕົວກອງ: ';
if ($statusFilter) {
    echo 'ສະຖານະ: ' . getStatusText($statusFilter) . ' | ';
}
if ($yearFilter) {
    echo 'ປີສຳເລັດການສຶກສາ: ' . htmlspecialchars($yearFilter) . ' | ';
}
if ($searchQuery) {
    echo 'ຄົ້ນຫາ: ' . htmlspecialchars($searchQuery) . ' | ';
}
echo 'ຈຳນວນລວມ: ' . count($registrations) . ' ລາຍການ';
echo '</td></tr>';

// Empty row
echo '<tr><td colspan="13"></td></tr>';

// Headers
$headers = [
    'ລະຫັດນິສິດ',
    'ຊື່ (ລາວ)',
    'ນາມສະກຸນ (ລາວ)', 
    'ຊື່ (English)',
    'ນາມສະກຸນ (English)',
    'ສາຂາວິຊາ',
    'ປີສຳເລັດການສຶກສາ',
    'ອີເມວ',
    'ເບີໂທລະສັບ',
    'ສະຖານະ',
    'ວັນທີ່ລົງທະບຽນ',
    'ຮູບໂປຣໄຟລ໌',
    'ຈຳນວນເງິນ (ກີບ)'
];

echo '<tr style="font-weight:bold; background-color:#E7E6E6;">';
foreach ($headers as $header) {
    echo '<td>' . htmlspecialchars($header) . '</td>';
}
echo '</tr>';

// Calculate totals
$totalAmount = 0;
$totalApproved = 0;
$approvedAmount = 0;
$totalPending = 0;
$pendingAmount = 0;
$totalRejected = 0;

foreach ($registrations as $registration) {
    // Ensure we get the actual amount from database
    $amount = !empty($registration['amount']) ? floatval($registration['amount']) : 200000;
    $totalAmount += $amount;
    
    switch ($registration['status']) {
        case 'approved':
            $totalApproved++;
            $approvedAmount += $amount;
            break;
        case 'pending':
            $totalPending++;
            $pendingAmount += $amount;
            break;
        case 'rejected':
            $totalRejected++;
            break;
    }
}

// Data rows
foreach ($registrations as $registration) {
    // Ensure we get the actual amount from database, display full number
    $amount = !empty($registration['amount']) ? floatval($registration['amount']) : 200000;
    
    echo '<tr>';
    echo '<td>' . htmlspecialchars($registration['student_code']) . '</td>';
    echo '<td>' . htmlspecialchars($registration['first_name']) . '</td>';
    echo '<td>' . htmlspecialchars($registration['last_name']) . '</td>';
    echo '<td>' . htmlspecialchars($registration['first_name_en'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($registration['last_name_en'] ?? '') . '</td>';
    echo '<td>' . htmlspecialchars($registration['major']) . '</td>';
    echo '<td>' . htmlspecialchars($registration['graduation_year']) . '</td>';
    echo '<td>' . htmlspecialchars($registration['email']) . '</td>';
    echo '<td>' . htmlspecialchars($registration['phone']) . '</td>';
    echo '<td>' . htmlspecialchars(getStatusText($registration['status'])) . '</td>';
    echo '<td>' . date('d/m/Y H:i', strtotime($registration['created_at'])) . '</td>';
    echo '<td>' . ($registration['profile_image'] ? 'ມີ' : 'ບໍ່ມີ') . '</td>';
    // Display as number format that Excel can recognize as number
    echo '<td style="mso-number-format:\@;">' . number_format($amount, 0, '', '') . '</td>';
    echo '</tr>';
}

// Add summary rows
echo '<tr><td colspan="13"></td></tr>'; // Empty row

// Summary section header
echo '<tr><td colspan="13" style="font-weight:bold; background-color:#4472C4; color:white; text-align:center;">';
echo 'ສະຫຸຼບຈຳນວນເງິນ';
echo '</td></tr>';

// Summary by status
echo '<tr style="background-color:#E7F3FF;">';
echo '<td colspan="10" style="font-weight:bold;">ຈຳນວນທັງໝົດ (' . count($registrations) . ' ລາຍການ):</td>';
echo '<td colspan="2" style="font-weight:bold; text-align:right;">ລວມເງິນທັງໝົດ:</td>';
echo '<td style="font-weight:bold; background-color:#FFE6CC; mso-number-format:\@;">' . number_format($totalAmount, 0, '', '') . '</td>';
echo '</tr>';

if ($totalApproved > 0) {
    echo '<tr style="background-color:#E8F5E8;">';
    echo '<td colspan="10">✅ ອະນຸມັດແລ້ວ (' . $totalApproved . ' ລາຍການ):</td>';
    echo '<td colspan="2" style="text-align:right;">ເງິນທີ່ອະນຸມັດແລ້ວ:</td>';
    echo '<td style="background-color:#D4EDDA; mso-number-format:\@;">' . number_format($approvedAmount, 0, '', '') . '</td>';
    echo '</tr>';
}

if ($totalPending > 0) {
    echo '<tr style="background-color:#FFF3CD;">';
    echo '<td colspan="10">⏳ ລໍຖ້າການອະນຸມັດ (' . $totalPending . ' ລາຍການ):</td>';
    echo '<td colspan="2" style="text-align:right;">ເງິນທີ່ລໍຖ້າອະນຸມັດ:</td>';
    echo '<td style="background-color:#FCF8E3; mso-number-format:\@;">' . number_format($pendingAmount, 0, '', '') . '</td>';
    echo '</tr>';
}

if ($totalRejected > 0) {
    echo '<tr style="background-color:#F8D7DA;">';
    echo '<td colspan="10">❌ ປະຕິເສດ (' . $totalRejected . ' ລາຍການ):</td>';
    echo '<td colspan="2" style="text-align:right;">ບໍ່ມີການຊຳລະເງິນ:</td>';
    echo '<td style="background-color:#F5C6CB;">0</td>';
    echo '</tr>';
}

// Percentage breakdown (if there are multiple statuses)
if (count($registrations) > 1 && ($totalApproved > 0 || $totalPending > 0)) {
    echo '<tr><td colspan="13"></td></tr>'; // Empty row
    echo '<tr><td colspan="13" style="font-weight:bold; background-color:#6C757D; color:white; text-align:center;">';
    echo 'ສັດສ່ວນເປີເຊັນ';
    echo '</td></tr>';
    
    if ($totalApproved > 0) {
        $approvedPercent = ($totalApproved / count($registrations)) * 100;
        $approvedAmountPercent = ($approvedAmount / $totalAmount) * 100;
        echo '<tr>';
        echo '<td colspan="8">✅ ອັດຕາການອະນຸມັດ:</td>';
        echo '<td>' . number_format($approvedPercent, 1) . '%</td>';
        echo '<td colspan="3">ສັດສ່ວນເງິນທີ່ອະນຸມັດ:</td>';
        echo '<td>' . number_format($approvedAmountPercent, 1) . '%</td>';
        echo '</tr>';
    }
    
    if ($totalPending > 0) {
        $pendingPercent = ($totalPending / count($registrations)) * 100;
        $pendingAmountPercent = ($pendingAmount / $totalAmount) * 100;
        echo '<tr>';
        echo '<td colspan="8">⏳ ອັດຕາລໍຖ້າອະນຸມັດ:</td>';
        echo '<td>' . number_format($pendingPercent, 1) . '%</td>';
        echo '<td colspan="3">ສັດສ່ວນເງິນທີ່ລໍຖ້າ:</td>';
        echo '<td>' . number_format($pendingAmountPercent, 1) . '%</td>';
        echo '</tr>';
    }
    
    if ($totalRejected > 0) {
        $rejectedPercent = ($totalRejected / count($registrations)) * 100;
        echo '<tr>';
        echo '<td colspan="8">❌ ອັດຕາການປະຕິເສດ:</td>';
        echo '<td>' . number_format($rejectedPercent, 1) . '%</td>';
        echo '<td colspan="3"></td>';
        echo '<td>-</td>';
        echo '</tr>';
    }
}

echo '</table></body></html>';

// Log the export activity
$filterInfo = [];
if ($statusFilter) $filterInfo[] = "status:{$statusFilter}";
if ($yearFilter) $filterInfo[] = "year:{$yearFilter}";
if ($searchQuery) $filterInfo[] = "search:{$searchQuery}";
$filterString = !empty($filterInfo) ? " with filters: " . implode(", ", $filterInfo) : "";

logActivity('Excel export', "Exported " . count($registrations) . " registrations as Excel{$filterString} by " . ($_SESSION['username'] ?? 'unknown'));

// Exit to prevent any additional output
exit;
?>