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

try {
    // Get statistics
    $stmt = $db->query("SELECT 
        COUNT(*) as total_registrations,
        COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
        COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
        COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected
        FROM registrations");
    $stats = $stmt->fetch();

    // Get recent registrations
    $stmt = $db->query("SELECT id, student_code, first_name, last_name, major, status, created_at 
                        FROM registrations 
                        ORDER BY created_at DESC 
                        LIMIT 10");
    $recentRegistrations = $stmt->fetchAll();

    // Get monthly statistics for chart
    $stmt = $db->query("SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as count
        FROM registrations 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month ASC");
    $monthlyStats = $stmt->fetchAll();

} catch (Exception $e) {
    $stats = ['total_registrations' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0];
    $recentRegistrations = [];
    $monthlyStats = [];
    error_log("Dashboard error: " . $e->getMessage());
}

$pageTitle = 'ໜ້າຫຼັກ - ລະບົບຈັດການ';
include '../includes/header.php';
?>

<div class="min-h-screen bg-gray-50">
    <!-- Page Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        📊 ໜ້າຫຼັກລະບົບຈັດການ
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        ຍິນດີຕ້ອນຮັບ, <?php echo htmlspecialchars($_SESSION['username']); ?> 
                        (<?php echo ucfirst($_SESSION['user_role']); ?>)
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="registrations.php" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-lao-red hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lao-red">
                        📋 ຈັດການລົງທະບຽນ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">📋</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    ລົງທະບຽນທັງໝົດ
                                </dt>
                                <dd class="text-3xl font-bold text-gray-900">
                                    <?php echo number_format($stats['total_registrations']); ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">✅</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    ອະນຸມັດແລ້ວ
                                </dt>
                                <dd class="text-3xl font-bold text-green-600">
                                    <?php echo number_format($stats['approved']); ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">⏳</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    ລໍຖ້າການອະນຸມັດ
                                </dt>
                                <dd class="text-3xl font-bold text-yellow-600">
                                    <?php echo number_format($stats['pending']); ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-3xl">❌</span>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    ປະຕິເສດ
                                </dt>
                                <dd class="text-3xl font-bold text-red-600">
                                    <?php echo number_format($stats['rejected']); ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Registrations -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        📝 ການລົງທະບຽນຫຼ້າສຸດ
                    </h3>
                </div>
                <div class="overflow-hidden">
                    <?php if (!empty($recentRegistrations)): ?>
                    <ul class="divide-y divide-gray-200">
                        <?php foreach ($recentRegistrations as $registration): ?>
                        <li class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
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
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo htmlspecialchars($registration['student_code']); ?> - 
                                            <?php echo htmlspecialchars($registration['major']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo formatLaoDate($registration['created_at']); ?>
                                </div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="px-6 py-4 bg-gray-50">
                        <a href="registrations.php" class="text-sm font-medium text-lao-red hover:text-red-700">
                            ເບິ່ງທັງໝົດ →
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="px-6 py-8 text-center text-gray-500">
                        ຍັງບໍ່ມີການລົງທະບຽນ
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        ⚡ ການດຳເນີນການດ່ວນ
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <a href="registrations.php?status=pending" 
                           class="block p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg border border-yellow-200 transition-colors">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">⏳</span>
                                <div>
                                    <div class="text-sm font-medium text-yellow-800">
                                        ລໍຖ້າການອະນຸມັດ (<?php echo $stats['pending']; ?>)
                                    </div>
                                    <div class="text-xs text-yellow-600">
                                        ກວດສອບ ແລະ ອະນຸມັດການລົງທະບຽນ
                                    </div>
                                </div>
                            </div>
                        </a>

                        <a href="registrations.php" 
                           class="block p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition-colors">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">📋</span>
                                <div>
                                    <div class="text-sm font-medium text-blue-800">
                                        ຈັດການລົງທະບຽນທັງໝົດ
                                    </div>
                                    <div class="text-xs text-blue-600">
                                        ເບິ່ງ, ແກ້ໄຂ, ລົບການລົງທະບຽນ
                                    </div>
                                </div>
                            </div>
                        </a>

                        <?php if (hasRole(ROLE_ADMIN)): ?>
                        <a href="users.php" 
                           class="block p-4 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition-colors">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">👥</span>
                                <div>
                                    <div class="text-sm font-medium text-green-800">
                                        ຈັດການຜູ້ໃຊ້ງານ
                                    </div>
                                    <div class="text-xs text-green-600">
                                        ເພີ່ມ, ແກ້ໄຂ, ລົບຜູ້ໃຊ້ງານ
                                    </div>
                                </div>
                            </div>
                        </a>
                        <?php endif; ?>

                        <a href="../index.php" 
                           class="block p-4 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-colors">
                            <div class="flex items-center">
                                <span class="text-2xl mr-3">🏠</span>
                                <div>
                                    <div class="text-sm font-medium text-gray-800">
                                        ໄປໜ້າຫຼັກ
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        ເບິ່ງໜ້າເວັບຫຼັກ
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Statistics Chart -->
        <?php if (!empty($monthlyStats)): ?>
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    📈 ສະຖິຕິລາຍເດືອນ
                </h3>
            </div>
            <div class="p-6">
                <div class="h-64 flex items-end justify-between space-x-2">
                    <?php 
                    $maxCount = max(array_column($monthlyStats, 'count'));
                    foreach ($monthlyStats as $stat): 
                        $height = $maxCount > 0 ? ($stat['count'] / $maxCount) * 100 : 0;
                    ?>
                    <div class="flex flex-col items-center flex-1">
                        <div class="bg-lao-red rounded-t" style="height: <?php echo $height; ?>%; min-height: 4px; width: 100%; max-width: 40px;"></div>
                        <div class="text-xs text-gray-600 mt-2 transform -rotate-45 origin-left">
                            <?php echo date('M Y', strtotime($stat['month'] . '-01')); ?>
                        </div>
                        <div class="text-xs font-semibold text-gray-800 mt-1">
                            <?php echo $stat['count']; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>