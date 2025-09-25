<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Check user role
if (!hasRole(ROLE_VIEWER)) {
    setFlashMessage('error', 'ທ່ານບໍ່ມີສິດເຂົ້າເຖິງໜ້ານີ້');
    redirect('login.php');
}

// Handle actions
if ($_POST && hasRole(ROLE_STAFF)) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid form submission.');
        redirect('registrations.php');
    }

    // Handle bulk actions
    if (isset($_POST['bulk_action']) && isset($_POST['selected_ids']) && is_array($_POST['selected_ids'])) {
        $bulkAction = $_POST['bulk_action'];
        $selectedIds = array_map('intval', $_POST['selected_ids']);
        $selectedIds = array_filter($selectedIds, function($id) { return $id > 0; });
        
        if (!empty($selectedIds)) {
            try {
                $placeholders = str_repeat('?,', count($selectedIds) - 1) . '?';
                $successCount = 0;
                
                switch ($bulkAction) {
                    case 'approve':
                        $db->query("UPDATE registrations SET status = ? WHERE id IN ($placeholders)", 
                                  array_merge([STATUS_APPROVED], $selectedIds));
                        $successCount = count($selectedIds);
                        logActivity('Bulk approve registrations', "IDs: " . implode(',', $selectedIds));
                        setFlashMessage('success', "ອະນຸມັດການລົງທະບຽນ $successCount ລາຍການສຳເລັດ");
                        break;
                        
                    case 'reject':
                        $db->query("UPDATE registrations SET status = ? WHERE id IN ($placeholders)", 
                                  array_merge([STATUS_REJECTED], $selectedIds));
                        $successCount = count($selectedIds);
                        logActivity('Bulk reject registrations', "IDs: " . implode(',', $selectedIds));
                        setFlashMessage('success', "ປະຕິເສດການລົງທະບຽນ $successCount ລາຍການສຳເລັດ");
                        break;
                        
                    case 'pending':
                        $db->query("UPDATE registrations SET status = ? WHERE id IN ($placeholders)", 
                                  array_merge([STATUS_PENDING], $selectedIds));
                        $successCount = count($selectedIds);
                        logActivity('Bulk set to pending registrations', "IDs: " . implode(',', $selectedIds));
                        setFlashMessage('success', "ປ່ຽນສະຖານະເປັນລໍຖ້າ $successCount ລາຍການສຳເລັດ");
                        break;
                        
                    case 'delete':
                        if (hasRole(ROLE_ADMIN)) {
                            // Get files info before deletion
                            $stmt = $db->query("SELECT profile_image, payment_proof FROM registrations WHERE id IN ($placeholders)", $selectedIds);
                            $filesInfo = $stmt->fetchAll();
                            
                            // Delete files
                            foreach ($filesInfo as $fileInfo) {
                                if ($fileInfo['profile_image']) {
                                    deleteFile($fileInfo['profile_image'], 'profiles');
                                }
                                if ($fileInfo['payment_proof']) {
                                    deleteFile($fileInfo['payment_proof'], 'payments');
                                }
                            }
                            
                            // Delete registrations
                            $db->query("DELETE FROM registrations WHERE id IN ($placeholders)", $selectedIds);
                            $successCount = count($selectedIds);
                            logActivity('Bulk delete registrations', "IDs: " . implode(',', $selectedIds));
                            setFlashMessage('success', "ລົບການລົງທະບຽນ $successCount ລາຍການສຳເລັດ");
                        }
                        break;
                }
            } catch (Exception $e) {
                setFlashMessage('error', 'ເກີດຂໍ້ຜິດພາດໃນການປະມວນຜົນ');
                error_log("Bulk action error: " . $e->getMessage());
            }
        }
        
        redirect('registrations.php');
    }

    // Handle single actions
    $action = $_POST['action'] ?? '';
    $registrationId = (int)($_POST['registration_id'] ?? 0);

    if ($registrationId > 0) {
        try {
            if ($action === 'approve') {
                $db->query("UPDATE registrations SET status = ? WHERE id = ?", [STATUS_APPROVED, $registrationId]);
                logActivity('Registration approved', "Registration ID: $registrationId");
                setFlashMessage('success', 'ອະນຸມັດການລົງທະບຽນສຳເລັດ');
            } elseif ($action === 'reject') {
                $db->query("UPDATE registrations SET status = ? WHERE id = ?", [STATUS_REJECTED, $registrationId]);
                logActivity('Registration rejected', "Registration ID: $registrationId");
                setFlashMessage('success', 'ປະຕິເສດການລົງທະບຽນສຳເລັດ');
            } elseif ($action === 'pending') {
                $db->query("UPDATE registrations SET status = ? WHERE id = ?", [STATUS_PENDING, $registrationId]);
                logActivity('Registration set to pending', "Registration ID: $registrationId");
                setFlashMessage('success', 'ປ່ຽນສະຖານະເປັນລໍຖ້າການອະນຸມັດສຳເລັດ');
            } elseif ($action === 'delete' && hasRole(ROLE_ADMIN)) {
                // Get file info before deletion
                $stmt = $db->query("SELECT profile_image, payment_proof FROM registrations WHERE id = ?", [$registrationId]);
                $fileInfo = $stmt->fetch();
                
                // Delete files
                if ($fileInfo['profile_image']) {
                    deleteFile($fileInfo['profile_image'], 'profiles');
                }
                if ($fileInfo['payment_proof']) {
                    deleteFile($fileInfo['payment_proof'], 'payments');
                }
                
                // Delete registration
                $db->query("DELETE FROM registrations WHERE id = ?", [$registrationId]);
                logActivity('Registration deleted', "Registration ID: $registrationId");
                setFlashMessage('success', 'ລົບການລົງທະບຽນສຳເລັດ');
            }
        } catch (Exception $e) {
            setFlashMessage('error', 'ເກີດຂໍ້ຜິດພາດໃນການປະມວນຜົນ');
            error_log("Registration action error: " . $e->getMessage());
        }
    }
    
    redirect('registrations.php');
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? '';
$searchQuery = $_GET['search'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = RECORDS_PER_PAGE;
$offset = ($page - 1) * $limit;

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
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM registrations $whereClause";
    $stmt = $db->query($countQuery, $params);
    $totalRecords = $stmt->fetch()['total'];
    $totalPages = ceil($totalRecords / $limit);

    // Get registrations
    $query = "SELECT * FROM registrations $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    $stmt = $db->query($query, $params);
    $registrations = $stmt->fetchAll();

} catch (Exception $e) {
    $registrations = [];
    $totalRecords = 0;
    $totalPages = 0;
    error_log("Registrations query error: " . $e->getMessage());
}

$pageTitle = 'ຈັດການລົງທະບຽນ - ລະບົບຈັດການ';
include '../includes/header.php';
?>

<div class="min-h-screen bg-gray-50">
    <!-- Page Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        📋 ຈັດການລົງທະບຽນ
                        <?php if ($statusFilter): ?>
                        <span class="text-lg font-normal text-gray-500">
                            - <?php 
                            switch($statusFilter) {
                                case 'pending': echo '⏳ ລໍຖ້າການອະນຸມັດ'; break;
                                case 'approved': echo '✅ ອະນຸມັດແລ້ວ'; break;
                                case 'rejected': echo '❌ ປະຕິເສດ'; break;
                            }
                            ?>
                        </span>
                        <?php endif; ?>
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        <?php if ($searchQuery): ?>
                        ພົບ <?php echo number_format($totalRecords); ?> ລາຍການຈາກການຄົ້ນຫາ "<?php echo htmlspecialchars($searchQuery); ?>"
                        <?php else: ?>
                        ທັງໝົດ <?php echo number_format($totalRecords); ?> ການລົງທະບຽນ
                        <?php endif; ?>
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
                    <a href="export_excel_final.php?<?php echo http_build_query(['status' => $statusFilter, 'search' => $searchQuery]); ?>" 
                       class="inline-flex items-center px-4 py-2 border border-green-600 rounded-md shadow-sm text-sm font-medium text-green-600 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        📊 ສົ່ງອອກ Excel
                    </a>
                    <a href="dashboard.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lao-red">
                        ← ກັບໄປໜ້າຫຼັກ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Statistics Cards -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <?php
            try {
                // Get real-time statistics
                $statsQuery = "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                    COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
                    COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected
                    FROM registrations";
                
                if ($statusFilter || $searchQuery) {
                    $statsQuery .= " " . $whereClause;
                }
                
                $statsStmt = $db->query($statsQuery, $statusFilter || $searchQuery ? $params : []);
                $stats = $statsStmt->fetch();
            ?>
            
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">📊</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">ທັງໝົດ</dt>
                                <dd class="text-lg font-medium text-gray-900"><?php echo number_format($stats['total']); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">⏳</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">ລໍຖ້າ</dt>
                                <dd class="text-lg font-medium text-yellow-600"><?php echo number_format($stats['pending']); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">✅</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">ອະນຸມັດ</dt>
                                <dd class="text-lg font-medium text-green-600"><?php echo number_format($stats['approved']); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">❌</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">ປະຕິເສດ</dt>
                                <dd class="text-lg font-medium text-red-600"><?php echo number_format($stats['rejected']); ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php
            } catch (Exception $e) {
                echo '<div class="col-span-4 text-center text-red-600">ບໍ່ສາມາດໂຫຼດສະຖິຕິໄດ້</div>';
            }
            ?>
        </div>

        <!-- Filters and Search -->
        <div class="mb-6 bg-white shadow rounded-lg p-6">
            <form method="GET" class="md:flex md:items-center md:space-x-4">
                <div class="flex-1 mb-4 md:mb-0">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">ຄົ້ນຫາ</label>
                    <input type="text" id="search" name="search" 
                           value="<?php echo htmlspecialchars($searchQuery); ?>"
                           placeholder="ຊື່, ລະຫັດນິສິດ, ອີເມວ, ສາຂາວິຊາ..."
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lao-red focus:border-lao-red sm:text-sm">
                </div>
                
                <div class="flex-none mb-4 md:mb-0">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">ສະຖານະ</label>
                    <select id="status" name="status" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lao-red focus:border-lao-red sm:text-sm">
                        <option value="">ທັງໝົດ</option>
                        <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>⏳ ລໍຖ້າການອະນຸມັດ</option>
                        <option value="approved" <?php echo $statusFilter === 'approved' ? 'selected' : ''; ?>>✅ ອະນຸມັດແລ້ວ</option>
                        <option value="rejected" <?php echo $statusFilter === 'rejected' ? 'selected' : ''; ?>>❌ ປະຕິເສດ</option>
                    </select>
                </div>
                
                <div class="flex-none">
                    <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-lao-red hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lao-red">
                        🔍 ຄົ້ນຫາ
                    </button>
                </div>
            </form>
        </div>

        <!-- Bulk Actions -->
        <?php if (hasRole(ROLE_STAFF) && !empty($registrations)): ?>
        <div class="mb-4 bg-white shadow rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <label for="selectAll" class="flex items-center">
                        <input type="checkbox" id="selectAll" class="h-4 w-4 text-lao-red focus:ring-lao-red border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">ເລືອກທັງໝົດ</span>
                    </label>
                    <span id="selectedCount" class="text-sm text-gray-500">0 ລາຍການທີ່ເລືອກ</span>
                </div>
                
                <div id="bulkActions" class="hidden flex items-center space-x-2">
                    <select id="bulkActionSelect" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-lao-red focus:border-lao-red">
                        <option value="">-- ເລືອກການດຳເນີນການ --</option>
                        <option value="approve">✅ ອະນຸມັດທັງໝົດ</option>
                        <option value="reject">❌ ປະຕິເສດທັງໝົດ</option>
                        <option value="pending">⏳ ປ່ຽນເປັນລໍຖ້າ</option>
                        <?php if (hasRole(ROLE_ADMIN)): ?>
                        <option value="delete">🗑️ ລົບທັງໝົດ</option>
                        <?php endif; ?>
                    </select>
                    <button type="button" onclick="executeBulkAction()" 
                            class="px-4 py-2 bg-lao-red text-white rounded-md text-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-lao-red">
                        ປະຕິບັດ
                    </button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Registrations Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <?php if (!empty($registrations)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <?php if (hasRole(ROLE_STAFF)): ?>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="headerCheckbox" class="h-4 w-4 text-lao-red focus:ring-lao-red border-gray-300 rounded">
                            </th>
                            <?php endif; ?>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ນິສິດ
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ຂໍ້ມູນການສຶກສາ
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ຕິດຕໍ່
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ສະຖານະ
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ວັນທີ່ລົງທະບຽນ
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ການດຳເນີນການ
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($registrations as $registration): ?>
                        <tr class="hover:bg-gray-50">
                            <?php if (hasRole(ROLE_STAFF)): ?>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="rowCheckbox h-4 w-4 text-lao-red focus:ring-lao-red border-gray-300 rounded" 
                                       value="<?php echo $registration['id']; ?>">
                            </td>
                            <?php endif; ?>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <?php if ($registration['profile_image']): ?>
                                        <img class="h-10 w-10 rounded-full object-cover" 
                                             src="<?php echo getFileUrl($registration['profile_image'], 'profiles'); ?>" 
                                             alt="Profile">
                                        <?php else: ?>
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-gray-600 font-medium">
                                                <?php echo strtoupper(substr($registration['first_name'], 0, 1)); ?>
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo htmlspecialchars($registration['student_code']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($registration['major']); ?></div>
                                <div class="text-sm text-gray-500">ສຳເລັດປີ <?php echo $registration['graduation_year']; ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($registration['email']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($registration['phone']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    <?php 
                                    switch ($registration['status']) {
                                        case 'approved': echo 'bg-green-100 text-green-800'; break;
                                        case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                        case 'rejected': echo 'bg-red-100 text-red-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?php 
                                    switch ($registration['status']) {
                                        case 'approved': echo '✅ ອະນຸມັດ'; break;
                                        case 'pending': echo '⏳ ລໍຖ້າ'; break;
                                        case 'rejected': echo '❌ ປະຕິເສດ'; break;
                                        default: echo $registration['status'];
                                    }
                                    ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo formatLaoDate($registration['created_at']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- View Button -->
                                    <button type="button" onclick="viewRegistration(<?php echo $registration['id']; ?>)"
                                            class="text-blue-600 hover:text-blue-900 p-1 rounded" title="ເບິ່ງລາຍລະອຽດ">
                                        👁️
                                    </button>
                                    
                                    <?php if (hasRole(ROLE_STAFF)): ?>
                                    <!-- Edit Button -->
                                    <a href="edit_registration.php?id=<?php echo $registration['id']; ?>"
                                       class="text-yellow-600 hover:text-yellow-900 p-1 rounded" title="ແກ້ໄຂ">
                                        ✏️
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if (hasRole(ROLE_STAFF)): ?>
                                    <!-- Action Buttons -->
                                    <?php if ($registration['status'] !== STATUS_APPROVED): ?>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <input type="hidden" name="registration_id" value="<?php echo $registration['id']; ?>">
                                        <button type="submit" onclick="return confirm('ຢືນຢັນການອະນຸມັດ?')"
                                                class="text-green-600 hover:text-green-900 p-1 rounded" title="ອະນຸມັດ">
                                            ✅
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($registration['status'] !== STATUS_REJECTED): ?>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <input type="hidden" name="registration_id" value="<?php echo $registration['id']; ?>">
                                        <button type="submit" onclick="return confirm('ຢືນຢັນການປະຕິເສດ?')"
                                                class="text-red-600 hover:text-red-900 p-1 rounded" title="ປະຕິເສດ">
                                            ❌
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($registration['status'] !== STATUS_PENDING): ?>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="action" value="pending">
                                        <input type="hidden" name="registration_id" value="<?php echo $registration['id']; ?>">
                                        <button type="submit" onclick="return confirm('ປ່ຽນເປັນລໍຖ້າການອະນຸມັດ?')"
                                                class="text-yellow-600 hover:text-yellow-900 p-1 rounded" title="ປ່ຽນເປັນລໍຖ້າ">
                                            ⏳
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    
                                    <?php if (hasRole(ROLE_ADMIN)): ?>
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="registration_id" value="<?php echo $registration['id']; ?>">
                                        <button type="submit" onclick="return confirm('ຢືນຢັນການລົບ? ການດຳເນີນການນີ້ບໍ່ສາມາດຍົກເລີກໄດ້!')"
                                                class="text-red-600 hover:text-red-900 p-1 rounded" title="ລົບ">
                                            🗑️
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&status=<?php echo urlencode($statusFilter); ?>&search=<?php echo urlencode($searchQuery); ?>" 
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        ກ່ອນໜ້າ
                    </a>
                    <?php endif; ?>
                    <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&status=<?php echo urlencode($statusFilter); ?>&search=<?php echo urlencode($searchQuery); ?>" 
                       class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        ຕໍ່ໄປ
                    </a>
                    <?php endif; ?>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            ສະແດງ <span class="font-medium"><?php echo $offset + 1; ?></span> ເຖິງ 
                            <span class="font-medium"><?php echo min($offset + $limit, $totalRecords); ?></span> 
                            ຈາກ <span class="font-medium"><?php echo number_format($totalRecords); ?></span> ລາຍການ
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&status=<?php echo urlencode($statusFilter); ?>&search=<?php echo urlencode($searchQuery); ?>" 
                               class="<?php echo $i === $page ? 'bg-lao-red text-white' : 'bg-white text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium">
                                <?php echo $i; ?>
                            </a>
                            <?php endfor; ?>
                        </nav>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="text-center py-12">
                <span class="text-4xl mb-4 block">📋</span>
                <h3 class="text-lg font-medium text-gray-900 mb-2">ບໍ່ມີການລົງທະບຽນ</h3>
                <p class="text-gray-500">ຍັງບໍ່ມີການລົງທະບຽນໃດໆ ຫຼື ບໍ່ພົບຂໍ້ມູນທີ່ຄົ້ນຫາ</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Registration Details Modal -->
<div id="registrationModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div id="modalContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewRegistration(id) {
    // Show loading
    document.getElementById('modalContent').innerHTML = '<div class="p-6 text-center">⏳ ກຳລັງໂຫຼດ...</div>';
    document.getElementById('registrationModal').classList.remove('hidden');
    
    // Load registration details via AJAX
    fetch('view_registration.php?id=' + id)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContent').innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('modalContent').innerHTML = '<div class="p-6 text-center text-red-600">❌ ເກີດຂໍ້ຜິດພາດໃນການໂຫຼດຂໍ້ມູນ</div>';
        });
}

function closeModal() {
    document.getElementById('registrationModal').classList.add('hidden');
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Bulk Actions Management
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const headerCheckbox = document.getElementById('headerCheckbox');
    const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkActionsDiv = document.getElementById('bulkActions');

    function updateSelectedCount() {
        const selectedCheckboxes = document.querySelectorAll('.rowCheckbox:checked');
        const count = selectedCheckboxes.length;
        
        if (selectedCountSpan) {
            selectedCountSpan.textContent = count + ' ລາຍການທີ່ເລືອກ';
        }
        
        if (bulkActionsDiv) {
            if (count > 0) {
                bulkActionsDiv.classList.remove('hidden');
            } else {
                bulkActionsDiv.classList.add('hidden');
            }
        }

        // Update header checkbox state
        if (headerCheckbox) {
            if (count === 0) {
                headerCheckbox.indeterminate = false;
                headerCheckbox.checked = false;
            } else if (count === rowCheckboxes.length) {
                headerCheckbox.indeterminate = false;
                headerCheckbox.checked = true;
            } else {
                headerCheckbox.indeterminate = true;
                headerCheckbox.checked = false;
            }
        }
    }

    // Select All functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }

    // Header checkbox functionality
    if (headerCheckbox) {
        headerCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }

    // Row checkbox functionality
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Initialize
    updateSelectedCount();
});

function executeBulkAction() {
    const selectedCheckboxes = document.querySelectorAll('.rowCheckbox:checked');
    const action = document.getElementById('bulkActionSelect').value;
    
    if (!action) {
        alert('ກະລຸນາເລືອກການດຳເນີນການ');
        return;
    }
    
    if (selectedCheckboxes.length === 0) {
        alert('ກະລຸນາເລືອກລາຍການທີ່ຕ້ອງການປະມວນຜົນ');
        return;
    }

    const selectedIds = Array.from(selectedCheckboxes).map(cb => cb.value);
    let confirmMessage = '';
    
    switch (action) {
        case 'approve':
            confirmMessage = `ຢືນຢັນການອະນຸມັດ ${selectedIds.length} ລາຍການ?`;
            break;
        case 'reject':
            confirmMessage = `ຢືນຢັນການປະຕິເສດ ${selectedIds.length} ລາຍການ?`;
            break;
        case 'pending':
            confirmMessage = `ຢືນຢັນການປ່ຽນເປັນລໍຖ້າ ${selectedIds.length} ລາຍການ?`;
            break;
        case 'delete':
            confirmMessage = `ຢືນຢັນການລົບ ${selectedIds.length} ລາຍການ? ການດຳເນີນການນີ້ບໍ່ສາມາດຍົກເລີກໄດ້!`;
            break;
    }
    
    if (!confirm(confirmMessage)) {
        return;
    }

    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    
    // Add CSRF token
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = 'csrf_token';
    csrfInput.value = '<?php echo generateCSRFToken(); ?>';
    form.appendChild(csrfInput);
    
    // Add bulk action
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'bulk_action';
    actionInput.value = action;
    form.appendChild(actionInput);
    
    // Add selected IDs
    selectedIds.forEach(id => {
        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'selected_ids[]';
        idInput.value = id;
        form.appendChild(idInput);
    });
    
    document.body.appendChild(form);
    form.submit();
}
</script>

<?php include '../includes/footer.php'; ?>