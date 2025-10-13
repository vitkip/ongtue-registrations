<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$registrationId = (int)($_GET['id'] ?? 0);

if ($registrationId <= 0) {
    header('Location: registrations.php');
    exit;
}

// Check if database connection exists
if (!$db) {
    $_SESSION['error'] = 'ເກີດຂໍ້ຜິດພາດໃນການເຊື່ອມຕໍ່ຖານຂໍ້ມູນ';
    header('Location: registrations.php');
    exit;
}

try {
    $stmt = $db->query("SELECT * FROM registrations WHERE id = ?", [$registrationId]);
    $registration = $stmt->fetch();
    
    if (!$registration) {
        header('Location: registrations.php');
        exit;
    }
} catch (Exception $e) {
    error_log("Database error in view_registration.php: " . $e->getMessage());
    $_SESSION['error'] = 'ເກີດຂໍ້ຜິດພາດໃນການດຶງຂໍ້ມູນ';
    header('Location: registrations.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && hasRole(ROLE_STAFF)) {
    $action = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($csrf_token)) {
        $_SESSION['error'] = 'Invalid CSRF token';
        header('Location: view_registration.php?id=' . $registrationId);
        exit;
    }
    
    if ($action === 'approve' && $registration['status'] !== STATUS_APPROVED) {
        try {
            $stmt = $db->query("UPDATE registrations SET status = ? WHERE id = ?", [STATUS_APPROVED, $registrationId]);
            $_SESSION['success'] = 'ອະນຸມັດການລົງທະບຽນສຳເລັດ';
            logActivity('APPROVE_REGISTRATION', "Approved registration ID: $registrationId");
        } catch (Exception $e) {
            $_SESSION['error'] = 'ເກີດຂໍ້ຜິດພາດໃນການອະນຸມັດ';
        }
    } elseif ($action === 'reject' && $registration['status'] !== STATUS_REJECTED) {
        try {
            $stmt = $db->query("UPDATE registrations SET status = ? WHERE id = ?", [STATUS_REJECTED, $registrationId]);
            $_SESSION['success'] = 'ປະຕິເສດການລົງທະບຽນສຳເລັດ';
            logActivity('REJECT_REGISTRATION', "Rejected registration ID: $registrationId");
        } catch (Exception $e) {
            $_SESSION['error'] = 'ເກີດຂໍ້ຜິດພາດໃນການປະຕິເສດ';
        }
    }
    
    // Refresh registration data
    $stmt = $db->query("SELECT * FROM registrations WHERE id = ?", [$registrationId]);
    $registration = $stmt->fetch();
}

$pageTitle = 'ລາຍລະອຽດການລົງທະບຽນ';
include '../includes/header.php';
?>

<link rel="stylesheet" href="css/view_registration.css">

<div class="min-h-screen bg-gray-50">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div>
                <h1 class="page-title">
                    📋 ລາຍລະອຽດການລົງທະບຽນ
                </h1>
                <p class="page-subtitle">
                    ລະຫັດການລົງທະບຽນ: #<?php echo $registration['id']; ?> | <?php echo htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']); ?>
                </p>
            </div>
            <a href="registrations.php" class="back-button">
                ← ກັບໄປລາຍການ
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <span class="alert-icon">✅</span>
            <span><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <span class="alert-icon">❌</span>
            <span><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></span>
        </div>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                    <!-- Personal Information -->
                    <div>
                        <h3 class="section-header">
                            <span class="section-icon">👤</span>
                            ຂໍ້ມູນສ່ວນຕົວ
                        </h3>
                        <div class="space-y-4">
                            <div class="info-card">
                                <div class="info-label">🎫 ລະຫັດນິສິດ</div>
                                <div class="info-value large"><?php echo htmlspecialchars($registration['student_code']); ?></div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-label">👨‍🎓 ຊື່ - ນາມສະກຸນ (ລາວ)</div>
                                <div class="info-value large"><?php echo htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']); ?></div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-label">🌏 ຊື່ - ນາມສະກຸນ (English)</div>
                                <div class="info-value english"><?php echo htmlspecialchars(($registration['first_name_en'] ?? 'N/A') . ' ' . ($registration['last_name_en'] ?? '')); ?></div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-label">✉️ ອີເມວ</div>
                                <div class="info-value"><?php echo htmlspecialchars($registration['email']); ?></div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-label">📱 ເບີໂທລະສັບ</div>
                                <div class="info-value"><?php echo htmlspecialchars($registration['phone']); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div>
                        <h3 class="section-header">
                            <span class="section-icon">🎓</span>
                            ຂໍ້ມູນການສຶກສາ
                        </h3>
                        <div class="space-y-4">
                            <div class="info-card">
                                <div class="info-label">📚 ສາຂາວິຊາ</div>
                                <div class="info-value"><?php echo htmlspecialchars($registration['major']); ?></div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-label">🎯 ປີສຳເລັດການສຶກສາ</div>
                                <div class="info-value"><?php echo $registration['graduation_year']; ?></div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-label">💰 ຈຳນວນເງິນຄ່າລົງທະບຽນ</div>
                                <div class="amount-display">
                                    <span class="amount-icon">💵</span>
                                    <span class="amount-value"><?php echo number_format($registration['amount'] ?? 200000, 0, '.', ','); ?> ກີບ</span>
                                </div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-label">📅 ວັນທີ່ລົງທະບຽນ</div>
                                <div class="info-value"><?php echo formatLaoDate($registration['created_at']); ?></div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-label">📊 ສະຖານະ</div>
                                <div>
                                    <span class="status-badge <?php 
                                        switch ($registration['status']) {
                                            case 'approved': echo 'approved'; break;
                                            case 'pending': echo 'pending'; break;
                                            case 'rejected': echo 'rejected'; break;
                                            default: echo 'pending';
                                        }
                                    ?>">
                                        <?php 
                                        switch ($registration['status']) {
                                            case 'approved': echo '✅ ອະນຸມັດແລ້ວ'; break;
                                            case 'pending': echo '⏳ ລໍຖ້າການອະນຸມັດ'; break;
                                            case 'rejected': echo '❌ ປະຕິເສດ'; break;
                                            default: echo $registration['status'];
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Files Section -->
        <div class="px-6 py-6 border-t border-gray-200">
            <h3 class="section-header">
                <span class="section-icon">📎</span>
                ເອກະສານແນບ
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Profile Image -->
                <div class="file-card">
                    <h4 class="file-card-header">
                        <span>📸</span>
                        ຮູບໂປຣໄຟລ໌
                    </h4>
                    <div class="file-preview">
                        <?php if ($registration['profile_image']): ?>
                            <img src="<?php echo getFileUrl($registration['profile_image'], 'profiles'); ?>" 
                                 alt="Profile Image" 
                                 class="file-image">
                            <a href="<?php echo getFileUrl($registration['profile_image'], 'profiles'); ?>" 
                               target="_blank" 
                               class="file-action-button">
                                <span>🔍</span> ເບິ່ງຮູບເຕັມ
                            </a>
                        <?php else: ?>
                            <div class="file-placeholder">
                                <svg class="file-placeholder-icon" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="file-placeholder-text">ບໍ່ມີຮູບໂປຣໄຟລ໌</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Payment Proof -->
                <div class="file-card">
                    <h4 class="file-card-header">
                        <span>💳</span>
                        ໃບຢັ້ງຢືນການຈ່າຍເງິນ
                    </h4>
                    <div class="file-preview">
                        <?php if ($registration['payment_proof']): ?>
                            <?php 
                            $fileExtension = strtolower(pathinfo($registration['payment_proof'], PATHINFO_EXTENSION));
                            $fileUrl = getFileUrl($registration['payment_proof'], 'payments');
                            ?>
                            
                            <?php if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                <img src="<?php echo $fileUrl; ?>" 
                                     alt="Payment Proof" 
                                     class="file-image">
                            <?php else: ?>
                                <div class="file-placeholder">
                                    <span style="font-size: 4rem;">📄</span>
                                    <p class="file-placeholder-text" style="margin-top: 1rem;">PDF Document</p>
                                </div>
                            <?php endif; ?>
                            
                            <a href="<?php echo $fileUrl; ?>" 
                               target="_blank" 
                               class="file-action-button">
                                <span>📄</span> ເບິ່ງເອກະສານ
                            </a>
                        <?php else: ?>
                            <div class="file-placeholder">
                                <svg class="file-placeholder-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="file-placeholder-text">ບໍ່ມີໃບຢັ້ງຢືນການຈ່າຍເງິນ</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <?php if (hasRole(ROLE_STAFF)): ?>
        <div class="action-section">
            <div class="action-buttons">
                <a href="edit_registration.php?id=<?php echo $registration['id']; ?>" 
                   class="action-button edit">
                    ✏️ ແກ້ໄຂຂໍ້ມູນ
                </a>
                
                <div style="display: flex; gap: 0.75rem;">
                    <?php if ($registration['status'] !== STATUS_APPROVED): ?>
                    <form method="POST" class="inline">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" onclick="return confirm('ຢືນຢັນການອະນຸມັດ?')"
                                class="action-button approve">
                            ✅ ອະນຸມັດ
                        </button>
                    </form>
                    <?php endif; ?>
                    
                    <?php if ($registration['status'] !== STATUS_REJECTED): ?>
                    <form method="POST" class="inline">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" onclick="return confirm('ຢືນຢັນການປະຕິເສດ?')"
                                class="action-button reject">
                            ❌ ປະຕິເສດ
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>

<?php include '../includes/footer.php'; ?>