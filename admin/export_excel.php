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

// Function to create Excel file
function createExcelFile($data, $headers) {
    $timestamp = date('Y-m-d_H-i-s');
    $filename = "registrations_export_{$timestamp}.xlsx";
    
    // Create styles XML for formatting
    $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' .
        '<fonts count="2">' .
        '<font><sz val="11"/><name val="Calibri"/></font>' .
        '<font><sz val="11"/><name val="Calibri"/><b/></font>' .
        '</fonts>' .
        '<fills count="3">' .
        '<fill><patternFill patternType="none"/></fill>' .
        '<fill><patternFill patternType="gray125"/></fill>' .
        '<fill><patternFill patternType="solid"><fgColor rgb="FF4472C4"/></patternFill></fill>' .
        '</fills>' .
        '<borders count="2">' .
        '<border><left/><right/><top/><bottom/><diagonal/></border>' .
        '<border><left style="thin"><color rgb="FF000000"/></left><right style="thin"><color rgb="FF000000"/></right><top style="thin"><color rgb="FF000000"/></top><bottom style="thin"><color rgb="FF000000"/></bottom><diagonal/></border>' .
        '</borders>' .
        '<cellXfs count="2">' .
        '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>' .
        '<xf numFmtId="0" fontId="1" fillId="2" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/>' .
        '</cellXfs>' .
        '</styleSheet>';
    
    // Create XML content for Excel workbook
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' . "\n";
    $xml .= '<sheets><sheet name="ການລົງທະບຽນ" sheetId="1" r:id="rId1"/></sheets>' . "\n";
    $xml .= '</workbook>';
    
    // Create worksheet XML with formatting
    $worksheet = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $worksheet .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . "\n";
    
    // Add column widths for better formatting
    $worksheet .= '<cols>' . "\n";
    $colWidths = [15, 12, 15, 20, 8, 25, 15, 12, 18, 10, 15]; // Column widths
    for ($i = 0; $i < count($colWidths); $i++) {
        $worksheet .= '<col min="' . ($i + 1) . '" max="' . ($i + 1) . '" width="' . $colWidths[$i] . '" customWidth="1"/>' . "\n";
    }
    $worksheet .= '</cols>' . "\n";
    
    $worksheet .= '<sheetData>' . "\n";
    
    // Add title row
    $worksheet .= '<row r="1">' . "\n";
    $worksheet .= '<c r="A1" t="inlineStr" s="1"><is><t>ລາຍງານການລົງທະບຽນຮັບໃບຢັ້ງຢືນ - ສົ່ງອອກວັນທີ່: ' . date('d/m/Y H:i') . '</t></is></c>' . "\n";
    $worksheet .= '</row>' . "\n";
    
    // Add empty row
    $worksheet .= '<row r="2"></row>' . "\n";
    
    // Add headers with styling
    $worksheet .= '<row r="3">' . "\n";
    $colIndex = 0;
    foreach ($headers as $header) {
        $colLetter = chr(65 + $colIndex); // A, B, C, etc.
        $worksheet .= '<c r="' . $colLetter . '3" t="inlineStr" s="1"><is><t>' . htmlspecialchars($header) . '</t></is></c>' . "\n";
        $colIndex++;
    }
    $worksheet .= '</row>' . "\n";
    
    // Add data rows
    $rowIndex = 4;
    foreach ($data as $row) {
        $worksheet .= '<row r="' . $rowIndex . '">' . "\n";
        $colIndex = 0;
        foreach ($row as $cell) {
            $colLetter = chr(65 + $colIndex);
            if (is_numeric($cell) && $colIndex != 0) { // Don't treat student code as number
                $worksheet .= '<c r="' . $colLetter . $rowIndex . '"><v>' . $cell . '</v></c>' . "\n";
            } else {
                $worksheet .= '<c r="' . $colLetter . $rowIndex . '" t="inlineStr"><is><t>' . htmlspecialchars($cell) . '</t></is></c>' . "\n";
            }
            $colIndex++;
        }
        $worksheet .= '</row>' . "\n";
        $rowIndex++;
    }
    
    $worksheet .= '</sheetData>' . "\n";
    
    // Add auto filter
    $lastCol = chr(64 + count($headers));
    $lastRow = count($data) + 3;
    $worksheet .= '<autoFilter ref="A3:' . $lastCol . $lastRow . '"/>' . "\n";
    
    $worksheet .= '</worksheet>';
    
    // Create ZIP file structure for .xlsx
    $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'excel_export_' . uniqid() . '.xlsx';
    
    // Ensure we can write to the temp file
    if (!is_writable(dirname($tempFile))) {
        return false;
    }
    
    $zip = new ZipArchive();
    $result = $zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    
    if ($result === TRUE) {
        // Add required files for Excel format
        $zip->addFromString('[Content_Types].xml', 
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>' .
            '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>' .
            '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>' .
            '</Types>');
        
        $zip->addFromString('_rels/.rels',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>' .
            '</Relationships>');
        
        $zip->addFromString('xl/_rels/workbook.xml.rels',
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>' .
            '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>' .
            '</Relationships>');
        
        $zip->addFromString('xl/workbook.xml', $xml);
        $zip->addFromString('xl/worksheets/sheet1.xml', $worksheet);
        $zip->addFromString('xl/styles.xml', $styles);
        
        $zip->close();
        
        return [$tempFile, $filename];
    }
    
    return false;
}

// Prepare data for Excel
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

$data = [];
foreach ($registrations as $registration) {
    $data[] = [
        $registration['student_code'],
        $registration['first_name'],
        $registration['last_name'],
        $registration['major'],
        $registration['graduation_year'],
        $registration['email'],
        $registration['phone'],
        getStatusText($registration['status']),
        date('d/m/Y H:i', strtotime($registration['created_at'])),
        $registration['profile_image'] ? 'ມີ' : 'ບໍ່ມີ',
        $registration['payment_proof'] ? 'ມີ' : 'ບໍ່ມີ'
    ];
}

// Try to create Excel file, fallback to CSV if it fails
$result = createExcelFile($data, $headers);

if ($result) {
    [$tempFile, $filename] = $result;
    
    // Set headers for Excel download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    header('Content-Length: ' . filesize($tempFile));
    
    // Output file
    readfile($tempFile);
    
    // Clean up temp file
    unlink($tempFile);
    
    // Log the export activity
    logActivity('Excel export', "Exported " . count($registrations) . " registrations as XLSX by " . ($_SESSION['username'] ?? 'unknown'));
} else {
    // Fallback to CSV export
    $timestamp = date('Y-m-d_H-i-s');
    $filename = "registrations_export_{$timestamp}.csv";
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    
    // Add BOM for proper UTF-8 encoding in Excel
    echo "\xEF\xBB\xBF";
    
    // Create file pointer
    $output = fopen('php://output', 'w');
    
    // Write title
    fputcsv($output, ['ລາຍງານການລົງທະບຽນຮັບໃບຢັ້ງຢືນ - ສົ່ງອອກວັນທີ່: ' . date('d/m/Y H:i')]);
    fputcsv($output, []); // Empty row
    
    // Write headers
    fputcsv($output, $headers);
    
    // Write data
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    // Close file pointer
    fclose($output);
    
    // Log the export activity
    logActivity('Excel export', "Exported " . count($registrations) . " registrations as CSV by " . ($_SESSION['username'] ?? 'unknown'));
}

// Exit to prevent any additional output
exit;
?>