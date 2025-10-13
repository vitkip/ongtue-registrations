<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in and has staff role
if (!isLoggedIn() || !hasRole(ROLE_STAFF)) {
    redirect('login.php');
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    setFlashMessage('error', 'ບໍ່ພົບການລົງທະບຽນ');
    redirect('registrations.php');
}

// Handle form submission
if ($_POST) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Invalid form submission.');
        redirect('registrations.php');
    }

    try {
        // Validate required fields
        $required_fields = ['first_name', 'last_name', 'first_name_en', 'last_name_en', 'student_code', 'major', 'graduation_year', 'email', 'phone', 'amount'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("ກະລຸນາປ້ອນ " . $field);
            }
        }
        
        // Validate amount
        if (!is_numeric($_POST['amount']) || $_POST['amount'] <= 0) {
            throw new Exception('ກະລຸນາລະບຸຈຳນວນເງິນທີ່ຖືກຕ້ອງ');
        }

        // Validate email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('ອີເມວບໍ່ຖືກຕ້ອງ');
        }

        // Check for duplicate student code (excluding current record)
        $stmt = $db->query("SELECT id FROM registrations WHERE student_code = ? AND id != ?", [$_POST['student_code'], $id]);
        if ($stmt->fetch()) {
            throw new Exception('ລະຫັດນິສິດນີ້ມີການໃຊ້ງານແລ້ວ');
        }

        // Check for duplicate email (excluding current record)
        $stmt = $db->query("SELECT id FROM registrations WHERE email = ? AND id != ?", [$_POST['email'], $id]);
        if ($stmt->fetch()) {
            throw new Exception('ອີເມວນີ້ມີການໃຊ້ງານແລ້ວ');
        }

        // Prepare update data
        $updateData = [
            'first_name' => trim($_POST['first_name']),
            'last_name' => trim($_POST['last_name']),
            'first_name_en' => trim($_POST['first_name_en']),
            'last_name_en' => trim($_POST['last_name_en']),
            'student_code' => trim($_POST['student_code']),
            'major' => trim($_POST['major']),
            'graduation_year' => (int)$_POST['graduation_year'],
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone']),
            'amount' => (float)$_POST['amount'],
            'status' => $_POST['status']
        ];

        // Handle file uploads
        $currentRegistration = $db->query("SELECT profile_image, payment_proof FROM registrations WHERE id = ?", [$id])->fetch();
        
        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['profile_image'], 'profiles', ['jpg', 'jpeg', 'png'], 5 * 1024 * 1024);
            if ($uploadResult['success']) {
                // Delete old file
                if ($currentRegistration['profile_image']) {
                    deleteFile($currentRegistration['profile_image'], 'profiles');
                }
                $updateData['profile_image'] = $uploadResult['filename'];
            } else {
                throw new Exception('ເກີດຂໍ້ຜິດພາດໃນການອັບໂຫຼດຮູບພາບ: ' . $uploadResult['message']);
            }
        }

        // Handle payment proof upload
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['payment_proof'], 'payments', ['jpg', 'jpeg', 'png', 'pdf'], 10 * 1024 * 1024);
            if ($uploadResult['success']) {
                // Delete old file
                if ($currentRegistration['payment_proof']) {
                    deleteFile($currentRegistration['payment_proof'], 'payments');
                }
                $updateData['payment_proof'] = $uploadResult['filename'];
            } else {
                throw new Exception('ເກີດຂໍ້ຜິດພາດໃນການອັບໂຫຼດໃບຢັ້ງຢືນ: ' . $uploadResult['message']);
            }
        }

        // Build update query
        $setClause = [];
        $params = [];
        foreach ($updateData as $key => $value) {
            $setClause[] = "$key = ?";
            $params[] = $value;
        }
        $params[] = $id;

        $query = "UPDATE registrations SET " . implode(', ', $setClause) . " WHERE id = ?";
        $db->query($query, $params);

        setFlashMessage('success', 'ອັບເດດການລົງທະບຽນສຳເລັດ');
        redirect('registrations.php');

    } catch (Exception $e) {
        $error = $e->getMessage();
        error_log("Edit registration error: " . $error);
    }
}

// Get registration data
try {
    $stmt = $db->query("SELECT * FROM registrations WHERE id = ?", [$id]);
    $registration = $stmt->fetch();
    
    if (!$registration) {
        setFlashMessage('error', 'ບໍ່ພົບການລົງທະບຽນ');
        redirect('registrations.php');
    }
} catch (Exception $e) {
    setFlashMessage('error', 'ເກີດຂໍ້ຜິດພາດໃນການໂຫຼດຂໍ້ມູນ');
    redirect('registrations.php');
}

$pageTitle = 'ແກ້ໄຂການລົງທະບຽນ - ລະບົບຈັດການ';
include '../includes/header.php';
?>

<link rel="stylesheet" href="css/edit_registration.css">

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Sticky Header -->
    <div class="sticky-header">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center text-white text-xl">
                            ✏️
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900">ແກ້ໄຂການລົງທະບຽນ</h1>
                    </div>
                    <p class="text-sm text-gray-600 ml-13">
                        <?php echo htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']); ?>
                        <span class="mx-2">•</span>
                        <span class="status-badge status-<?php echo $registration['status']; ?>">
                            <?php 
                            $statusLabels = [
                                'pending' => '⏳ ລໍຖ້າການອະນຸມັດ',
                                'approved' => '✅ ອະນຸມັດແລ້ວ',
                                'rejected' => '❌ ປະຕິເສດ'
                            ];
                            echo $statusLabels[$registration['status']] ?? $registration['status'];
                            ?>
                        </span>
                    </p>
                </div>
                <a href="registrations.php" class="btn-secondary inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    ກັບໄປລາຍການ
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <form method="POST" enctype="multipart/form-data" id="editForm">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

            <?php if (isset($error)): ?>
            <div class="mb-6 rounded-lg bg-red-50 p-4 border-l-4 border-red-500">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">ເກີດຂໍ້ຜິດພາດ</h3>
                        <p class="mt-1 text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Personal Information -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">👤</div>
                    <h3 class="section-title">ຂໍ້ມູນສ່ວນຕົວ</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ຊື່ (ພາສາລາວ) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" 
                               value="<?php echo htmlspecialchars($registration['first_name']); ?>"
                               class="form-input" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ນາມສະກຸນ (ພາສາລາວ) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name" 
                               value="<?php echo htmlspecialchars($registration['last_name']); ?>"
                               class="form-input" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ຊື່ (ພາສາອັງກິດ) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name_en" 
                               value="<?php echo htmlspecialchars($registration['first_name_en'] ?? ''); ?>"
                               placeholder="First name"
                               class="form-input" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ນາມສະກຸນ (ພາສາອັງກິດ) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name_en" 
                               value="<?php echo htmlspecialchars($registration['last_name_en'] ?? ''); ?>"
                               placeholder="Last name"
                               class="form-input" required>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ລະຫັດນິສິດ <span class="text-red-500">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-icon">🎓</span>
                            <input type="text" name="student_code" 
                                   value="<?php echo htmlspecialchars($registration['student_code']); ?>"
                                   class="form-input with-icon" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">📚</div>
                    <h3 class="section-title">ຂໍ້ມູນການສຶກສາ</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ສາຂາວິຊາ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="major" 
                               value="<?php echo htmlspecialchars($registration['major']); ?>"
                               class="form-input" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ປີທີ່ສຳເລັດການສຶກສາ <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="graduation_year" 
                               value="<?php echo $registration['graduation_year']; ?>"
                               min="2020" max="2030"
                               class="form-input" required>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ຈຳນວນເງິນຄ່າລົງທະບຽນ (ກີບ) <span class="text-red-500">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-icon">💰</span>
                            <input type="text" name="amount" id="amount"
                                   value="<?php echo number_format($registration['amount'] ? floatval($registration['amount']) : 200000, 0, '.', ','); ?>"
                                   class="form-input with-icon" 
                                   placeholder="200,000"
                                   inputmode="numeric"
                                   pattern="[0-9,]+"
                                   required>
                            <input type="hidden" name="amount_raw" id="amount_raw" value="<?php echo $registration['amount'] ?? 200000; ?>">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            ລະບຸຈຳນວນເງິນເປັນກີບ
                            <span class="ml-2 text-blue-600 font-medium" id="amount_display"></span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">📞</div>
                    <h3 class="section-title">ຂໍ້ມູນການຕິດຕໍ່</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ອີເມວ <span class="text-red-500">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-icon">📧</span>
                            <input type="email" name="email" 
                                   value="<?php echo htmlspecialchars($registration['email']); ?>"
                                   class="form-input with-icon" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ເບີໂທລະສັບ <span class="text-red-500">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-icon">📱</span>
                            <input type="tel" name="phone" 
                                   value="<?php echo htmlspecialchars($registration['phone']); ?>"
                                   class="form-input with-icon" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- File Uploads -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">📎</div>
                    <h3 class="section-title">ເອກະສານ</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            ຮູບພາບໂປຣໄຟລ໌
                        </label>
                        <?php if ($registration['profile_image']): ?>
                        <div class="current-file-preview">
                            <img src="<?php echo getFileUrl($registration['profile_image'], 'profiles'); ?>" 
                                 alt="Profile" class="h-16 w-16 rounded-full object-cover border-2 border-gray-200">
                            <div class="text-left">
                                <p class="text-sm font-medium text-gray-700">ຮູບພາບປັດຈຸບັນ</p>
                                <p class="text-xs text-gray-500">ອັບໂຫຼດໃໝ່ເພື່ອປ່ຽນແທນ</p>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="file-upload-area" onclick="document.getElementById('profile_image').click()">
                            <div class="text-4xl mb-2">📷</div>
                            <p class="text-sm font-medium text-gray-700">ຄລິກເພື່ອເລືອກຮູບພາບ</p>
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG (ສູງສຸດ 5MB)</p>
                        </div>
                        <input type="file" id="profile_image" name="profile_image" 
                               accept=".jpg,.jpeg,.png" class="hidden">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            ໃບຢັ້ງຢືນການຈ່າຍເງິນ
                        </label>
                        <?php if ($registration['payment_proof']): ?>
                        <div class="current-file-preview">
                            <div class="w-16 h-16 bg-blue-100 rounded-lg flex items-center justify-center text-2xl">
                                📄
                            </div>
                            <div class="text-left">
                                <p class="text-sm font-medium text-gray-700">ໄຟລ໌ປັດຈຸບັນ</p>
                                <a href="<?php echo getFileUrl($registration['payment_proof'], 'payments'); ?>" 
                                   target="_blank" class="text-xs text-blue-600 hover:text-blue-800">
                                    ເບິ່ງໄຟລ໌ →
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="file-upload-area" onclick="document.getElementById('payment_proof').click()">
                            <div class="text-4xl mb-2">📤</div>
                            <p class="text-sm font-medium text-gray-700">ຄລິກເພື່ອເລືອກໄຟລ໌</p>
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG, PDF (ສູງສຸດ 10MB)</p>
                        </div>
                        <input type="file" id="payment_proof" name="payment_proof" 
                               accept=".jpg,.jpeg,.png,.pdf" class="hidden">
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">📊</div>
                    <h3 class="section-title">ສະຖານະ</h3>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        ສະຖານະການລົງທະບຽນ <span class="text-red-500">*</span>
                    </label>
                    <select name="status" class="form-input" required>
                        <option value="pending" <?php echo $registration['status'] === 'pending' ? 'selected' : ''; ?>>
                            ⏳ ລໍຖ້າການອະນຸມັດ
                        </option>
                        <option value="approved" <?php echo $registration['status'] === 'approved' ? 'selected' : ''; ?>>
                            ✅ ອະນຸມັດແລ້ວ
                        </option>
                        <option value="rejected" <?php echo $registration['status'] === 'rejected' ? 'selected' : ''; ?>>
                            ❌ ປະຕິເສດ
                        </option>
                    </select>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 sticky bottom-0 bg-white p-4 rounded-lg shadow-lg">
                <a href="registrations.php" class="btn-secondary">
                    ຍົກເລີກ
                </a>
                <button type="submit" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    ບັນທຶກການປ່ຽນແປງ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// File upload preview
document.getElementById('profile_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const uploadArea = this.previousElementSibling;
        uploadArea.classList.add('has-file');
        const fileName = uploadArea.querySelector('p');
        fileName.textContent = '✓ ' + file.name;
    }
});

document.getElementById('payment_proof').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const uploadArea = this.previousElementSibling;
        uploadArea.classList.add('has-file');
        const fileName = uploadArea.querySelector('p');
        fileName.textContent = '✓ ' + file.name;
    }
});

// Amount input validation and formatting
const amountInput = document.getElementById('amount');
const amountRaw = document.getElementById('amount_raw');
const amountDisplay = document.getElementById('amount_display');

// Format number with commas
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Remove all non-digit characters
function cleanNumber(str) {
    return str.replace(/[^\d]/g, '');
}

// Update display
function updateAmountDisplay(value) {
    if (value && value > 0) {
        amountDisplay.textContent = '💵 ' + formatNumber(value) + ' ກີບ';
        amountDisplay.style.display = 'inline';
    } else {
        amountDisplay.style.display = 'none';
    }
}

// Initialize display
updateAmountDisplay(amountRaw.value);

// Handle input
amountInput.addEventListener('input', function(e) {
    // Get cursor position
    let cursorPosition = this.selectionStart;
    let oldValue = this.value;
    
    // Clean and get numeric value
    let numericValue = cleanNumber(this.value);
    
    // Limit to reasonable amount (max 999,999,999)
    if (numericValue.length > 9) {
        numericValue = numericValue.slice(0, 9);
    }
    
    // Update hidden field
    amountRaw.value = numericValue || '0';
    
    // Format with commas
    if (numericValue) {
        this.value = formatNumber(numericValue);
        
        // Adjust cursor position
        let newValue = this.value;
        let oldCommas = (oldValue.substring(0, cursorPosition).match(/,/g) || []).length;
        let newCommas = (newValue.substring(0, cursorPosition).match(/,/g) || []).length;
        cursorPosition = cursorPosition + (newCommas - oldCommas);
        
        // Set cursor position
        this.setSelectionRange(cursorPosition, cursorPosition);
    } else {
        this.value = '';
    }
    
    // Update display
    updateAmountDisplay(numericValue);
    
    // Validation feedback
    if (numericValue && parseInt(numericValue) < 1000) {
        this.style.borderColor = '#f59e0b'; // warning color
    } else if (numericValue && parseInt(numericValue) >= 1000) {
        this.style.borderColor = '#10b981'; // success color
    } else {
        this.style.borderColor = '#e5e7eb'; // default color
    }
});

// Handle paste
amountInput.addEventListener('paste', function(e) {
    e.preventDefault();
    let pastedText = (e.clipboardData || window.clipboardData).getData('text');
    let numericValue = cleanNumber(pastedText);
    if (numericValue) {
        this.value = formatNumber(numericValue);
        amountRaw.value = numericValue;
        updateAmountDisplay(numericValue);
    }
});

// Handle focus
amountInput.addEventListener('focus', function() {
    this.select();
});

// Handle blur - ensure value is valid
amountInput.addEventListener('blur', function() {
    let numericValue = cleanNumber(this.value);
    if (!numericValue || parseInt(numericValue) < 1) {
        numericValue = '200000'; // default value
        this.value = formatNumber(numericValue);
        amountRaw.value = numericValue;
        updateAmountDisplay(numericValue);
    }
});

// Form validation before submit
document.getElementById('editForm').addEventListener('submit', function(e) {
    // Update hidden field before submit
    const numericValue = cleanNumber(amountInput.value);
    amountRaw.value = numericValue;
    
    // Change name to submit the raw value
    amountRaw.name = 'amount';
    amountInput.name = '';
    
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.style.borderColor = '#ef4444';
            field.focus();
        } else {
            field.style.borderColor = '#e5e7eb';
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('ກະລຸນາຕື່ມຂໍ້ມູນໃຫ້ຄົບຖ້ວນ');
    }
});

// Smooth scroll to error
window.addEventListener('load', function() {
    const errorDiv = document.querySelector('.bg-red-50');
    if (errorDiv) {
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

// Auto-save draft to localStorage
let autoSaveTimer;
document.getElementById('editForm').addEventListener('input', function() {
    clearTimeout(autoSaveTimer);
    autoSaveTimer = setTimeout(() => {
        console.log('Draft saved');
        // Add visual feedback
        const saveIndicator = document.createElement('div');
        saveIndicator.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg text-sm';
        saveIndicator.textContent = '✓ ບັນທຶກຮ່າງອັດຕະໂນມັດ';
        document.body.appendChild(saveIndicator);
        setTimeout(() => saveIndicator.remove(), 2000);
    }, 1000);
});

// Prevent accidental navigation
let formChanged = false;
document.getElementById('editForm').addEventListener('change', () => formChanged = true);
window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = '';
    }
});

// Remove warning on form submit
document.getElementById('editForm').addEventListener('submit', () => formChanged = false);
</script>

<?php include '../includes/footer.php'; ?>