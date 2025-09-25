<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(403);
    echo '<div class="p-6 text-center text-red-600">❌ ທ່ານບໍ່ມີສິດເຂົ້າເຖິງ</div>';
    exit;
}

$registrationId = (int)($_GET['id'] ?? 0);

if ($registrationId <= 0) {
    echo '<div class="p-6 text-center text-red-600">❌ ບໍ່ພົບຂໍ້ມູນການລົງທະບຽນ</div>';
    exit;
}

try {
    $stmt = $db->query("SELECT * FROM registrations WHERE id = ?", [$registrationId]);
    $registration = $stmt->fetch();
    
    if (!$registration) {
        echo '<div class="p-6 text-center text-red-600">❌ ບໍ່ພົບຂໍ້ມູນການລົງທະບຽນ</div>';
        exit;
    }
} catch (Exception $e) {
    echo '<div class="p-6 text-center text-red-600">❌ ເກີດຂໍ້ຜິດພາດໃນການໂຫຼດຂໍ້ມູນ</div>';
    exit;
}
?>

<div class="bg-white">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">
                📋 ລາຍລະອຽດການລົງທະບຽນ
            </h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <span class="sr-only">ປິດ</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Content -->
    <div class="px-6 py-4 max-h-96 overflow-y-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Personal Information -->
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">👤 ຂໍ້ມູນສ່ວນຕົວ</h4>
                <dl class="space-y-2">
                    <div>
                        <dt class="text-xs font-medium text-gray-500">ລະຫັດນິສິດ</dt>
                        <dd class="text-sm text-gray-900 font-medium"><?php echo htmlspecialchars($registration['student_code']); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">ຊື່ - ນາມສະກຸນ</dt>
                        <dd class="text-sm text-gray-900"><?php echo htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">ອີເມວ</dt>
                        <dd class="text-sm text-gray-900"><?php echo htmlspecialchars($registration['email']); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">ເບີໂທລະສັບ</dt>
                        <dd class="text-sm text-gray-900"><?php echo htmlspecialchars($registration['phone']); ?></dd>
                    </div>
                </dl>
            </div>

            <!-- Academic Information -->
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-3">🎓 ຂໍ້ມູນການສຶກສາ</h4>
                <dl class="space-y-2">
                    <div>
                        <dt class="text-xs font-medium text-gray-500">ສາຂາວິຊາ</dt>
                        <dd class="text-sm text-gray-900"><?php echo htmlspecialchars($registration['major']); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">ປີສຳເລັດການສຶກສາ</dt>
                        <dd class="text-sm text-gray-900"><?php echo $registration['graduation_year']; ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">ວັນທີ່ລົງທະບຽນ</dt>
                        <dd class="text-sm text-gray-900"><?php echo formatLaoDate($registration['created_at']); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">ສະຖານະ</dt>
                        <dd>
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
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Files -->
        <div class="mt-6">
            <h4 class="text-sm font-medium text-gray-900 mb-3">📎 ເອກະສານແນບ</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Profile Image -->
                <div>
                    <h5 class="text-xs font-medium text-gray-500 mb-2">ຮູບໂປຣໄຟລ໌</h5>
                    <?php if ($registration['profile_image']): ?>
                    <div class="border border-gray-200 rounded-lg p-3">
                        <img src="<?php echo getFileUrl($registration['profile_image'], 'profiles'); ?>" 
                             alt="Profile Image" 
                             class="w-32 h-32 object-cover rounded-lg mx-auto">
                        <div class="mt-2 text-center">
                            <a href="<?php echo getFileUrl($registration['profile_image'], 'profiles'); ?>" 
                               target="_blank" 
                               class="text-xs text-blue-600 hover:text-blue-800">
                                ເບິ່ງຮູບເຕັມ
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="border border-gray-200 rounded-lg p-3 text-center text-gray-500 text-sm">
                        ບໍ່ມີຮູບໂປຣໄຟລ໌
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Payment Proof -->
                <div>
                    <h5 class="text-xs font-medium text-gray-500 mb-2">ໃບຢັ້ງຢືນການຈ່າຍເງິນ</h5>
                    <?php if ($registration['payment_proof']): ?>
                    <div class="border border-gray-200 rounded-lg p-3">
                        <?php 
                        $fileExtension = strtolower(pathinfo($registration['payment_proof'], PATHINFO_EXTENSION));
                        $fileUrl = getFileUrl($registration['payment_proof'], 'payments');
                        ?>
                        
                        <?php if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                        <img src="<?php echo $fileUrl; ?>" 
                             alt="Payment Proof" 
                             class="w-32 h-32 object-cover rounded-lg mx-auto">
                        <?php else: ?>
                        <div class="w-32 h-32 bg-gray-100 rounded-lg mx-auto flex items-center justify-center">
                            <span class="text-3xl">📄</span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mt-2 text-center">
                            <a href="<?php echo $fileUrl; ?>" 
                               target="_blank" 
                               class="text-xs text-blue-600 hover:text-blue-800">
                                ເບິ່ງເອກະສານ
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="border border-gray-200 rounded-lg p-3 text-center text-gray-500 text-sm">
                        ບໍ່ມີໃບຢັ້ງຢືນການຈ່າຍເງິນ
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php if (hasRole(ROLE_STAFF)): ?>
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        <div class="flex justify-end space-x-3">
            <?php if ($registration['status'] !== STATUS_APPROVED): ?>
            <form method="POST" action="registrations.php" class="inline">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="action" value="approve">
                <input type="hidden" name="registration_id" value="<?php echo $registration['id']; ?>">
                <button type="submit" onclick="return confirm('ຢືນຢັນການອະນຸມັດ?')"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    ✅ ອະນຸມັດ
                </button>
            </form>
            <?php endif; ?>
            
            <?php if ($registration['status'] !== STATUS_REJECTED): ?>
            <form method="POST" action="registrations.php" class="inline">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="registration_id" value="<?php echo $registration['id']; ?>">
                <button type="submit" onclick="return confirm('ຢືນຢັນການປະຕິເສດ?')"
                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    ❌ ປະຕິເສດ
                </button>
            </form>
            <?php endif; ?>
            
            <button type="button" onclick="closeModal()"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lao-red">
                ປິດ
            </button>
        </div>
    </div>
    <?php else: ?>
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        <div class="flex justify-end">
            <button type="button" onclick="closeModal()"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lao-red">
                ປິດ
            </button>
        </div>
    </div>
    <?php endif; ?>
</div>