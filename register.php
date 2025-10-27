<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Ensure session is properly started
ensureSessionStarted();

// Initialize variables
$errors = [];
$success = false;

// Handle form submission
if ($_POST) {
    // Enhanced CSRF token validation
    $submittedToken = $_POST['csrf_token'] ?? '';
    
    // Use enhanced verification with fallback
    if (!verifyCSRFTokenWithFallback($submittedToken)) {
        $errors[] = 'ການສົ່ງຟອມບໍ່ຖືກຕ້ອງ - ກະລຸນາລອງໃໝ່';
    }
    
    // Sanitize and validate input
    $firstName = sanitizeInput($_POST['first_name'] ?? '');
    $lastName = sanitizeInput($_POST['last_name'] ?? '');
    $firstNameEn = sanitizeInput($_POST['first_name_en'] ?? '');
    $lastNameEn = sanitizeInput($_POST['last_name_en'] ?? '');
    $major = sanitizeInput($_POST['major'] ?? '');
    $graduationYear = sanitizeInput($_POST['graduation_year'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    
    // Handle amount field
    $amountSelection = $_POST['amount'] ?? '200000';
    if ($amountSelection === 'custom') {
        $amount = floatval(str_replace(',', '', $_POST['custom_amount'] ?? 0));
    } else {
        $amount = floatval($amountSelection);
    }
    
    // Validation
    if (empty($firstName)) $errors[] = 'ກະລຸນາປ້ອນຊື່';
    if (empty($lastName)) $errors[] = 'ກະລຸນາປ້ອນນາມສະກຸນ';
    if (empty($firstNameEn)) $errors[] = 'ກະລຸນາປ້ອນຊື່ພາສາອັງກິດ';
    if (empty($lastNameEn)) $errors[] = 'ກະລຸນາປ້ອນນາມສະກຸນພາສາອັງກິດ';
    if (empty($major)) $errors[] = 'ກະລຸນາເລືອກສາຂາວິຊາ';
    if (empty($graduationYear)) $errors[] = 'ກະລຸນາເລືອກປີສຳເລັດການສຶກສາ';
    if (empty($email)) $errors[] = 'ກະລຸນາປ້ອນອີເມວ';
    if (empty($phone)) $errors[] = 'ກະລຸນາປ້ອນເບີໂທລະສັບ';
    if ($amount <= 0) $errors[] = 'ກະລຸນາລະບຸຈຳນວນເງິນທີ່ຖືກຕ້ອງ';
    
    if (!empty($email) && !validateEmail($email)) {
        $errors[] = 'ອີເມວບໍ່ຖືກຕ້ອງ';
    }
    
    // Check required file uploads
    if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] !== UPLOAD_ERR_OK || empty($_FILES['profile_image']['name'])) {
        $errors[] = 'ກະລຸນາອັບໂຫຼດຮູບໂປຣໄຟລ໌';
    }
    
    if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== UPLOAD_ERR_OK || empty($_FILES['payment_proof']['name'])) {
        $errors[] = 'ກະລຸນາອັບໂຫຼດໃບຢັ້ງຢືນການຈ່າຍເງິນ';
    }
    
    // Check database connection before proceeding
    if ($db === null) {
        $errors[] = 'ເກີດຂໍ້ຜິດພາດໃນການເຊື່ອມຕໍ່ຖານຂໍ້ມູນ ກະລຸນາລອງໃໝ່ພາຍຫຼັງ';
        error_log("Database connection failed during registration attempt");
    }
    
    // Check if email already exists (only if database is available)
    if (!empty($email) && empty($errors) && $db !== null) {
        try {
            $stmt = $db->query("SELECT id FROM registrations WHERE email = ?", [$email]);
            if ($stmt->fetch()) {
                $errors[] = 'ອີເມວນີ້ໄດ້ລົງທະບຽນແລ້ວ';
            }
        } catch (Exception $e) {
            $errors[] = 'ເກີດຂໍ້ຜິດພາດໃນການກວດສອບອີເມວ';
            error_log("Email check error: " . $e->getMessage());
        }
    }
    
    // Handle file uploads
    $profileImage = '';
    $paymentProof = '';
    
    if (empty($errors)) {
        // Upload profile image
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['profile_image'], 'profiles', ALLOWED_IMAGE_TYPES);
            if ($uploadResult['success']) {
                $profileImage = $uploadResult['filename'];
            } else {
                $errors[] = 'ຮູບໂປຣໄຟລ໌: ' . $uploadResult['message'];
            }
        }
        
        // Upload payment proof
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['payment_proof'], 'payments', ALLOWED_DOC_TYPES);
            if ($uploadResult['success']) {
                $paymentProof = $uploadResult['filename'];
            } else {
                $errors[] = 'ໃບຢັ້ງຢືນການຈ່າຍເງິນ: ' . $uploadResult['message'];
            }
        }
    }
    
    // Insert registration if no errors and database is available
    if (empty($errors) && $db !== null) {
        try {
            $db->beginTransaction();
            
            // Generate unique student code
            do {
                $studentCode = generateStudentCode();
                $stmt = $db->query("SELECT id FROM registrations WHERE student_code = ?", [$studentCode]);
            } while ($stmt->fetch());
            
            // Insert registration
            $sql = "INSERT INTO registrations (student_code, first_name, last_name, first_name_en, last_name_en, major, graduation_year, email, phone, profile_image, payment_proof, amount, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $db->query($sql, [
                $studentCode,
                $firstName,
                $lastName,
                $firstNameEn,
                $lastNameEn,
                $major,
                $graduationYear,
                $email,
                $phone,
                $profileImage,
                $paymentProof,
                $amount,
                STATUS_PENDING
            ]);
            
            $db->commit();
            $success = true;
            
            // Log activity
            logActivity('New registration', "Student code: $studentCode, Email: $email");
            
            // Set success message
            setFlashMessage('success', "ລົງທະບຽນສຳເລັດ! ລະຫັດນິສິດຂອງທ່ານ: $studentCode");
            
        } catch (Exception $e) {
            $db->rollback();
            $errors[] = 'ເກີດຂໍ້ຜິດພາດໃນການບັນທຶກຂໍ້ມູນ';
            error_log("Registration error: " . $e->getMessage());
        }
    }
}

$pageTitle = 'ລົງທະບຽນ - ' . APP_NAME;
include 'includes/header.php';
?>

<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                📝 ລົງທະບຽນຮັບໃບປະກາດນິຍະບັດ
            </h1>
            <p class="text-lg text-gray-600">
                ກະລຸນາປ້ອນຂໍ້ມູນຂອງທ່ານໃຫ້ຄົບຖ້ວນ ແລະ ຖືກຕ້ອງ
            </p>
        </div>

        <?php if ($success): ?>
        <!-- Success Message -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8 animate-fade-in">
            <div class="flex items-center mb-4">
                <span class="text-3xl mr-3">🎉</span>
                <div>
                    <h3 class="text-xl font-bold text-green-800">ລົງທະບຽນສຳເລັດ!</h3>
                    <p class="text-green-700 mt-1">
                        ທ່ານໄດ້ລົງທະບຽນສຳເລັດແລ້ວ! ລະຫັດການລົງທະບຽນ ສາມາດນຳລະຫັດນີ້ມາກວດສອບສະຖານະ ການອະນຸມັດເຂົ້າຮັບໃບປະກາດນິຍະບັດໄດ້: <strong class="text-green-800 font-mono text-lg"><?php echo htmlspecialchars($studentCode); ?></strong>
                    </p>
                </div>
            </div>
            
            <!-- Process Steps -->
            <div class="bg-white rounded-lg p-6 mb-6">
                <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    📋 ຂັ້ນຕອນຕໍ່ໄປ
                </h4>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="bg-yellow-100 text-yellow-800 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3 mt-1 flex-shrink-0">1</div>
                        <div>
                            <h5 class="font-semibold text-gray-900">ລໍຖ້າການກວດສອບ</h5>
                            <p class="text-gray-600 text-sm">ເຈົ້າໜ້າທີ່ຈະກວດສອບຂໍ້ມູນ ແລະ ເອກະສານຂອງທ່ານ (3-5 ວັນເຮັດການ)</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-blue-100 text-blue-800 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3 mt-1 flex-shrink-0">2</div>
                        <div>
                            <h5 class="font-semibold text-gray-900">ຮັບການແຈ້ງເຕືອນ</h5>
                            <p class="text-gray-600 text-sm">ທ່ານຈະໄດ້ຮັບອີເມວແຈ້ງເຕືອນເມື່ອການລົງທະບຽນໄດ້ຮັບການອະນຸມັດ</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="bg-green-100 text-green-800 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3 mt-1 flex-shrink-0">3</div>
                        <div>
                            <h5 class="font-semibold text-gray-900">ເຂົ້າຮັບໃບປະກາດ</h5>
                            <p class="text-gray-600 text-sm">ມາຮັບໃບປະກາດນິຍະບັດໃນວັນທີ <strong>11 ພະຈິກ 2025</strong></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Certificate Collection Details -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                <h4 class="text-lg font-semibold text-blue-900 mb-4 flex items-center">
                    🎓 ລາຍລະອຽດການເຂົ້າຮັບໃບປະກາດ
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <span class="text-blue-600 mr-2 mt-1">📅</span>
                            <div>
                                <h5 class="font-semibold text-blue-900">ວັນທີ່</h5>
                                <p class="text-blue-800">ວັນທີ 11 ພະຈິກ 2025 (ວັນອັງຄານ)</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <span class="text-blue-600 mr-2 mt-1">⏰</span>
                            <div>
                                <h5 class="font-semibold text-blue-900">ເວລາ</h5>
                                <p class="text-blue-800">12:00 - 16:00 ນ.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <span class="text-blue-600 mr-2 mt-1">📍</span>
                            <div>
                                <h5 class="font-semibold text-blue-900">ສະຖານທີ່</h5>
                                <p class="text-blue-800">ວິທະຍາໄລຄູສົງ ອົງຕື້<br>
                                ທີ່ສາລາໃຫຍ່ ວັດອົງຕື້ວໍຣະມະຫາວິຫານ</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <span class="text-blue-600 mr-2 mt-1">🎭</span>
                            <div>
                                <h5 class="font-semibold text-blue-900">ການແຕ່ງກາຍ</h5>
                                <p class="text-blue-800">ຄຸມຜ້າມັດເອີກ ສີເຫຼືອງທອງ</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <span class="text-blue-600 mr-2 mt-1">👥</span>
                            <div>
                                <h5 class="font-semibold text-blue-900">ຜູ້ສົມທົບ</h5>
                                <p class="text-blue-800">ສາມາດພາຄອບຄົວ ຍາດຕິພີ່ນ້ອງ ມາຮ່ວມງານໄດ້ </p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <span class="text-blue-600 mr-2 mt-1">📱</span>
                            <div>
                                <h5 class="font-semibold text-blue-900">ຕິດຕໍ່</h5>
                                <p class="text-blue-800">020-77772338<br>info@teachercollege.la</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Required Documents -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 mb-6">
                <h4 class="text-lg font-semibold text-amber-900 mb-4 flex items-center">
                    📄 ເອກະສານທີ່ຕ້ອງນຳມາ
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <span class="text-amber-600 mr-2">✅</span>
                            <span class="text-amber-800">ບັດປະຈຳຕົວປະຊາຊົນ (ຕົ້ນສະບັບ)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-amber-600 mr-2">✅</span>
                            <span class="text-amber-800">ໃບຢັ້ງຢືນການລົງທະບຽນ (ພິມອອກ)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-amber-600 mr-2">✅</span>
                            <span class="text-amber-800">ຮູບຖ່າຍ 3x4 ຈຳນວນ 2 ຮູບ</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <span class="text-amber-600 mr-2">✅</span>
                            <span class="text-amber-800">ໃບຢັ້ງຢືນການຈ່າຍເງິນ (ຕົ້ນສະບັບ)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-amber-600 mr-2">✅</span>
                            <span class="text-amber-800">ລະຫັດນິສິດ (ຈົດໄວ້ຫຼືພິມອອກ)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-amber-600 mr-2">💡</span>
                            <span class="text-amber-800 italic">ມາກ່ອນເວລາ 30 ນາທີ</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Important Notes -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <h4 class="text-lg font-semibold text-red-900 mb-3 flex items-center">
                    ⚠️ ຂໍ້ຄວນລະວັງ
                </h4>
                
                <div class="space-y-2 text-red-800">
                    <p class="flex items-start">
                        <span class="text-red-600 mr-2 mt-1">•</span>
                        <span>ກະລຸນາມາຕາມເວລາທີ່ກຳນົດ ເພື່ອຫຼີກລ່ຽງການແອອັດ</span>
                    </p>
                    <p class="flex items-start">
                        <span class="text-red-600 mr-2 mt-1">•</span>
                        <span>ສາມາດກວດສອບສະຖານະການລົງທະບຽນໄດ້ທີ່ໜ້າຫຼັກ</span>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mt-8">
            <a href="index.php#search-section" class="w-full sm:w-auto bg-blue-600 text-white hover:bg-blue-700 px-6 py-3 rounded-lg font-medium transition-colors text-center">
                🔍 ກວດສອບສະຖານະ
            </a>
            <a href="index.php" class="w-full sm:w-auto bg-lao-red text-white hover:bg-red-700 px-6 py-3 rounded-lg font-medium transition-colors text-center">
                🏠 ກັບໄປໜ້າຫຼັກ
            </a>
            <button onclick="window.print()" class="w-full sm:w-auto bg-green-600 text-white hover:bg-green-700 px-6 py-3 rounded-lg font-medium transition-colors text-center">
                🖨️ ພິມໃບຢັ້ງຢືນ
            </button>
        </div>
        
        <?php else: ?>
        
        <!-- Error Messages -->
        <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
            <div class="flex items-center mb-3">
                <span class="text-2xl mr-3">❌</span>
                <h3 class="text-lg font-semibold text-red-800">ກະລຸນາແກ້ໄຂຂໍ້ຜິດພາດ:</h3>
            </div>
            <ul class="list-disc list-inside text-red-700 space-y-1">
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Registration Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" enctype="multipart/form-data" id="registrationForm" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <!-- Personal Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        👤 ຂໍ້ມູນສ່ວນຕົວ
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                ຊື່ (ພາສາລາວ) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="first_name" name="first_name" required
                                   value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                                   placeholder="ຊື່ຂອງທ່ານເປັນພາສາລາວ"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lao-red focus:border-lao-red">
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                ນາມສະກຸນ (ພາສາລາວ) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="last_name" name="last_name" required
                                   value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                                   placeholder="ນາມສະກຸນຂອງທ່ານເປັນພາສາລາວ"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lao-red focus:border-lao-red">
                        </div>
                        
                        <div>
                            <label for="first_name_en" class="block text-sm font-medium text-gray-700 mb-2">
                                ຊື່ (ພາສາອັງກິດ) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="first_name_en" name="first_name_en" required
                                   value="<?php echo htmlspecialchars($_POST['first_name_en'] ?? ''); ?>"
                                   placeholder="Your first name in English"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lao-red focus:border-lao-red">
                        </div>
                        
                        <div>
                            <label for="last_name_en" class="block text-sm font-medium text-gray-700 mb-2">
                                ນາມສະກຸນ (ພາສາອັງກິດ) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="last_name_en" name="last_name_en" required
                                   value="<?php echo htmlspecialchars($_POST['last_name_en'] ?? ''); ?>"
                                   placeholder="Your last name in English"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lao-red focus:border-lao-red">
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        🎓 ຂໍ້ມູນການສຶກສາ
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="major" class="block text-sm font-medium text-gray-700 mb-2">
                                ສາຂາວິຊາ <span class="text-red-500">*</span>
                            </label>
                            <select id="major" name="major" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lao-red focus:border-lao-red">
                                <option value="">-- ເລືອກສາຂາວິຊາ --</option>
                                <option value="ສາຍຄູ ພຸດທະສາສະໜາ - ພາສາລາວ" <?php echo ($_POST['major'] ?? '') === 'ສາຍຄູ ພຸດທະສາສະໜາ - ພາສາລາວ' ? 'selected' : ''; ?>>ສາຍຄູ ພຸດທະສາສະໜາ - ພາສາລາວ</option>
                                <option value="ສາຍຄູ ພາສາອັງກິດ" <?php echo ($_POST['major'] ?? '') === 'ສາຍຄູ ພາສາອັງກິດ' ? 'selected' : ''; ?>>ສາຍຄູ ພາສາອັງກິດ</option>
                                 <option value="ສາຍຄູ ພາສາລາວ - ວັນນະຄະດີ" <?php echo ($_POST['major'] ?? '') === 'ສາຍຄູ ພາສາລາວ - ວັນນະຄະດີ' ? 'selected' : ''; ?>>ສາຍຄູ ພາສາລາວ - ວັນນະຄະດີ</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="graduation_year" class="block text-sm font-medium text-gray-700 mb-2">
                                ປີສຳເລັດການສຶກສາ <span class="text-red-500">*</span>
                            </label>
                            <select id="graduation_year" name="graduation_year" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lao-red focus:border-lao-red">
                                <option value="">-- ເລືອກປີ --</option>
                                <?php
                                $currentYear = date('Y');
                                for ($year = $currentYear; $year >= $currentYear - 5; $year--):
                                ?>
                                <option value="<?php echo $year; ?>" <?php echo ($_POST['graduation_year'] ?? '') == $year ? 'selected' : ''; ?>>
                                    <?php echo $year; ?>
                                </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        📞 ຂໍ້ມູນຕິດຕໍ່
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                ອີເມວ <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" required
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lao-red focus:border-lao-red">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                ເບີໂທລະສັບ <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="phone" name="phone" required
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                   placeholder="020-xxxx-xxxx"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lao-red focus:border-lao-red">
                        </div>
                    </div>
                </div>

                <!-- File Uploads -->
                <div class="pb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        📄 ເອກະສານ ແລະ ການຊຳລະເງິນ                      
                    </h2>
                    <!-- Payment Information -->
                                            <div class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 border border-blue-200 rounded-xl p-4 sm:p-6 mb-6 shadow-sm">
                                                <div class="flex items-center justify-between mb-6">
                                                    <h3 class="text-lg sm:text-xl font-bold text-blue-900 flex items-center">
                                                        🏦 <span class="ml-2">ຂໍ້ມູນການຊຳລະເງິນ</span>
                                                    </h3>
                                                    <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs sm:text-sm font-medium">
                                                        📱 Mobile Ready
                                                    </div>
                                                </div>
                                                
                                                <!-- Main Payment Card -->
                                                <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-blue-100">
                                                    <!-- BCEL Header -->
                                                    <div class="bg-gradient-to-r from-red-600 via-red-700 to-red-800 text-white p-4 sm:p-6 relative overflow-hidden">
                                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent opacity-50"></div>
                                                        <div class="relative flex items-center justify-between">
                                                            <div class="flex items-center space-x-3">
                                                                <div class="bg-white/20 p-2 rounded-lg">
                                                                    <span class="text-2xl">💳</span>
                                                                </div>
                                                                <div>
                                                                    <h4 class="text-lg sm:text-xl font-bold">BCEL ONE</h4>
                                                                    <p class="text-red-100 text-xs sm:text-sm">ບັນຊີຮັບຈ່າຍເງິນ</p>
                                                                </div>
                                                            </div>
                                                            <img src="image/logobcel.png" alt="BCEL ONE" class="h-8 sm:h-10 w-auto opacity-90 filter brightness-0 invert" />
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Account Details -->
                                                    <div class="p-4 sm:p-6 space-y-6">
                                                        <!-- Account Holder -->
                                                        <div class="bg-gray-50 rounded-lg p-4">
                                                            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                                                                👤 ຊື່ເຈົ້າຂອງບັນຊີ
                                                            </label>
                                                            <p class="text-gray-900 font-bold text-base sm:text-lg">ANANTHASAK PHATHASIRA MONK</p>
                                                        </div>
                                                        
                                                        <!-- Account Number with Enhanced Copy -->
                                                        <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4">
                                                            <label class="block text-xs font-semibold text-red-600 uppercase tracking-wide mb-3">
                                                                🏦 ເລກບັນຊີ BCEL ONE
                                                            </label>
                                                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                                                <div class="flex-1 bg-white rounded-lg border border-red-300 p-3 shadow-sm">
                                                                    <p class="text-red-800 font-mono font-black text-lg sm:text-xl tracking-widest select-all" 
                                                                       id="accountNumber">010120001062171001</p>
                                                                </div>
                                                                <button type="button" onclick="copyAccountNumber()" id="copyBtn" 
                                                                        class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-4 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg transform hover:scale-105 active:scale-95 min-w-[120px]">
                                                                    <span id="copyIcon" class="text-lg">📋</span>
                                                                    <span id="copyText" class="text-sm">ຄັດລອກ</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Amount -->
                                                        <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4">
                                                            <label class="block text-xs font-semibold text-green-600 uppercase tracking-wide mb-2">
                                                                💰 ຄ່າລົງທະບຽນ
                                                            </label>
                                                            <div class="space-y-3">
                                                                <div class="flex items-center space-x-2">
                                                                    <input type="radio" id="amount_200k" name="amount" value="200000" 
                                                                           class="w-4 h-4 text-green-600 focus:ring-green-500 border-green-300" 
                                                                           <?php echo (!isset($_POST['amount']) || $_POST['amount'] === '200000') ? 'checked' : ''; ?>>
                                                                    <label for="amount_200k" class="flex items-baseline space-x-2 cursor-pointer">
                                                                        <span class="text-green-800 font-black text-xl">200,000</span>
                                                                        <span class="text-green-600 font-medium">ກີບ (ປົກກະຕິ)</span>
                                                                    </label>
                                                                </div>
                                                                <div class="flex items-center space-x-2">
                                                                    <input type="radio" id="amount_custom" name="amount" value="custom" 
                                                                           class="w-4 h-4 text-green-600 focus:ring-green-500 border-green-300"
                                                                           <?php echo (isset($_POST['amount']) && $_POST['amount'] === 'custom') ? 'checked' : ''; ?>>
                                                                    <label for="amount_custom" class="text-green-600 font-medium cursor-pointer">ລະບຸຈຳນວນເອງ:</label>
                                                                </div>
                                                                <div id="custom_amount_input" class="ml-6 <?php echo (isset($_POST['amount']) && $_POST['amount'] === 'custom') ? '' : 'hidden'; ?>">
                                                                    <input type="number" id="custom_amount" name="custom_amount" 
                                                                           class="w-full px-3 py-2 border border-green-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                                                           placeholder="ກະລຸນາລະບຸຈຳນວນເງິນ (ກີບ)" min="1"
                                                                           value="<?php echo htmlspecialchars($_POST['custom_amount'] ?? ''); ?>">
                                                                </div>
                                                            </div>
                                                        </div>                                                      
                                                    </div>
                                                </div>

                                            </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-2">
                                ຮູບນັກສຶກສາ <span class="text-red-500">*</span>
                            </label>
                            <input type="file" id="profile_image" name="profile_image" accept="image/*" required
                                   onchange="previewImage(this, 'profilePreview')"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lao-red focus:border-lao-red">
                            <p class="text-sm text-gray-500 mt-1">JPG, PNG, GIF (ສູງສຸດ 5MB) - <span class="text-red-600 font-medium">ບັງຄັບ</span></p>
                            <img id="profilePreview" class="mt-3 h-32 w-32 object-cover rounded-lg border hidden" />
                        </div>
                        
                        <div>
                            <label for="payment_proof" class="block text-sm font-medium text-gray-700 mb-2">
                                ແຄບໜ້າຈໍການຈ່າຍສົ່ງ <span class="text-red-500">*</span>
                            </label>
                            <input type="file" id="payment_proof" name="payment_proof" required
                                   accept=".pdf,.doc,.docx,image/*"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lao-red focus:border-lao-red">
                            <p class="text-sm text-gray-500 mt-1">PDF, DOC, DOCX, JPG, PNG (ສູງສຸດ 5MB) - <span class="text-red-600 font-medium">ບັງຄັບ</span></p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6">
                    <button type="submit" id="submitBtn"
                            class="w-full bg-gradient-lao text-white font-semibold py-4 px-6 rounded-lg hover:opacity-90 transition-all duration-300 shadow-lao">
                        🚀 ສົ່ງການລົງທະບຽນ
                    </button>
                </div>
            </form>
        </div>
        
        <?php endif; ?>
    </div>
</div>

<script>
// =======================
// CONSOLIDATED JAVASCRIPT
// =======================

// Payment Functions
// -----------------
function copyAccountNumber() {
    const accountText = '010120001062171001';
    const copyBtn = document.getElementById('copyBtn');
    const copyIcon = document.getElementById('copyIcon');
    const copyText = document.getElementById('copyText');
    
    // Loading state with haptic feedback
    copyBtn.disabled = true;
    copyBtn.classList.add('scale-95');
    copyIcon.textContent = '⏳';
    copyText.textContent = 'ກຳລັງ...';
    
    // Haptic feedback for mobile
    if (navigator.vibrate) {
        navigator.vibrate(50);
    }
    
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(accountText).then(function() {
            showCopySuccess();
        }).catch(function() {
            fallbackCopy(accountText);
        });
    } else {
        fallbackCopy(accountText);
    }
    
    function showCopySuccess() {
        copyIcon.textContent = '✅';
        copyText.textContent = 'ຄັດລອກແລ້ວ!';
        copyBtn.className = 'w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white px-4 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center justify-center gap-2 shadow-md min-w-[120px] scale-100';
        
        showToast('✅ ຄັດລອກເລກບັນຊີສຳເລັດ!<br><small>ເປີດແອັບ BCEL ONE ເພື່ອໂອນເງິນ</small>', 'success');
        
        // Haptic success feedback
        if (navigator.vibrate) {
            navigator.vibrate([100, 30, 100]);
        }
        
        setTimeout(function() {
            copyBtn.disabled = false;
            copyIcon.textContent = '📋';
            copyText.textContent = 'ຄັດລອກ';
            copyBtn.className = 'w-full sm:w-auto bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-4 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg transform hover:scale-105 active:scale-95 min-w-[120px]';
        }, 3000);
    }
    
    function showCopyError() {
        copyIcon.textContent = '❌';
        copyText.textContent = 'ຜິດພາດ';
        copyBtn.className = 'w-full sm:w-auto bg-red-500 hover:bg-red-600 text-white px-4 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center justify-center gap-2 shadow-md min-w-[120px] scale-100';
        
        showToast('❌ ບໍ່ສາມາດຄັດລອກໄດ້<br><small>ກະລຸນາຄັດລອກເອງ: ' + accountText + '</small>', 'error');
        
        setTimeout(function() {
            copyBtn.disabled = false;
            copyIcon.textContent = '📋';
            copyText.textContent = 'ຄັດລອກ';
            copyBtn.className = 'w-full sm:w-auto bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-4 py-3 rounded-lg font-semibold transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg transform hover:scale-105 active:scale-95 min-w-[120px]';
        }, 4000);
    }
    
    function fallbackCopy(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        textArea.style.top = '-999999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showCopySuccess();
            } else {
                showCopyError();
            }
        } catch (err) {
            showCopyError();
        }
        
        document.body.removeChild(textArea);
    }
}

// Toast Notification System
// -------------------------
function showToast(message, type = 'success') {
    const existingToast = document.getElementById('paymentToast');
    if (existingToast) {
        existingToast.remove();
    }
    
    const toast = document.createElement('div');
    toast.id = 'paymentToast';
    toast.className = `fixed top-4 left-4 right-4 sm:left-auto sm:right-4 sm:w-80 z-50 px-6 py-4 rounded-xl shadow-2xl text-white font-medium transform transition-all duration-300 ${
        type === 'success' ? 'bg-gradient-to-r from-green-500 to-green-600' : 'bg-gradient-to-r from-red-500 to-red-600'
    }`;
    toast.style.transform = 'translateY(-20px) scale(0.95)';
    toast.style.opacity = '0';
    toast.innerHTML = message;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.style.transform = 'translateY(0) scale(1)';
        toast.style.opacity = '1';
    }, 10);
    
    // Auto remove with animation
    setTimeout(function() {
        if (toast && toast.parentNode) {
            toast.style.transform = 'translateY(-20px) scale(0.95)';
            toast.style.opacity = '0';
            setTimeout(function() {
                if (toast && toast.parentNode) {
                    toast.remove();
                }
            }, 300);
        }
    }, 4000);
}

// Image Preview Function
// ----------------------
function previewImage(input, previewId) {
    const file = input.files[0];
    const preview = document.getElementById(previewId);
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        preview.classList.add('hidden');
    }
}

// Form Validation Function
// ------------------------
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('border-red-500');
            isValid = false;
        } else {
            field.classList.remove('border-red-500');
        }
    });
    
    return isValid;
}

// Form Submission Handler
// -----------------------
document.addEventListener('DOMContentLoaded', function() {
    const registrationForm = document.getElementById('registrationForm');
    
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            
            // Check required files
            const profileImage = document.getElementById('profile_image');
            const paymentProof = document.getElementById('payment_proof');
            
            if (!profileImage.files.length) {
                e.preventDefault();
                alert('ກະລຸນາອັບໂຫຼດຮູບໂປຣໄຟລ໌');
                profileImage.focus();
                return;
            }
            
            if (!paymentProof.files.length) {
                e.preventDefault();
                alert('ກະລຸນາອັບໂຫຼດໃບຢັ້ງຢືນການຈ່າຍເງິນ');
                paymentProof.focus();
                return;
            }
            
            // Validate form first
            if (!validateForm('registrationForm')) {
                e.preventDefault();
                alert('ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບຖ້ວນ');
                return;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="animate-spin inline-block">⏳</span> ກຳລັງປະມວນຜົນ...';
            
            // Re-enable button after 30 seconds as safety measure
            setTimeout(function() {
                if (submitBtn.disabled) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '🚀 ສົ່ງການລົງທະບຽນ';
                }
            }, 30000);
        });
    }
});

// Amount Selection Handler
// ------------------------
document.addEventListener('DOMContentLoaded', function() {
    const amountRadios = document.querySelectorAll('input[name="amount"]');
    const customAmountInput = document.getElementById('custom_amount_input');
    const customAmountField = document.getElementById('custom_amount');
    
    amountRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'custom') {
                customAmountInput.classList.remove('hidden');
                customAmountField.required = true;
                customAmountField.focus();
            } else {
                customAmountInput.classList.add('hidden');
                customAmountField.required = false;
                customAmountField.value = '';
            }
        });
    });
    
    // Format number input with commas
    if (customAmountField) {
        customAmountField.addEventListener('input', function() {
            let value = this.value.replace(/,/g, '');
            if (value && !isNaN(value)) {
                this.value = parseInt(value).toLocaleString();
            }
        });
    }
});

// Page Load Handler
// -----------------
window.addEventListener('load', function() {
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '🚀 ສົ່ງການລົງທະບຽນ';
    }
});
</script>

<?php include 'includes/footer.php'; ?>