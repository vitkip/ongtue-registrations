<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Initialize variables
$errors = [];
$success = false;

// Handle form submission
if ($_POST) {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'ການສົ່ງຟອມບໍ່ຖືກຕ້ອງ';
    }
    
    // Sanitize and validate input
    $firstName = sanitizeInput($_POST['first_name'] ?? '');
    $lastName = sanitizeInput($_POST['last_name'] ?? '');
    $major = sanitizeInput($_POST['major'] ?? '');
    $graduationYear = sanitizeInput($_POST['graduation_year'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    
    // Validation
    if (empty($firstName)) $errors[] = 'ກະລຸນາປ້ອນຊື່';
    if (empty($lastName)) $errors[] = 'ກະລຸນາປ້ອນນາມສະກຸນ';
    if (empty($major)) $errors[] = 'ກະລຸນາເລືອກສາຂາວິຊາ';
    if (empty($graduationYear)) $errors[] = 'ກະລຸນາເລືອກປີສຳເລັດການສຶກສາ';
    if (empty($email)) $errors[] = 'ກະລຸນາປ້ອນອີເມວ';
    if (empty($phone)) $errors[] = 'ກະລຸນາປ້ອນເບີໂທລະສັບ';
    
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
    
    // Check if email already exists
    if (!empty($email) && empty($errors)) {
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
    
    // Insert registration if no errors
    if (empty($errors)) {
        try {
            $db->beginTransaction();
            
            // Generate unique student code
            do {
                $studentCode = generateStudentCode();
                $stmt = $db->query("SELECT id FROM registrations WHERE student_code = ?", [$studentCode]);
            } while ($stmt->fetch());
            
            // Insert registration
            $sql = "INSERT INTO registrations (student_code, first_name, last_name, major, graduation_year, email, phone, profile_image, payment_proof, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $db->query($sql, [
                $studentCode,
                $firstName,
                $lastName, 
                $major,
                $graduationYear,
                $email,
                $phone,
                $profileImage,
                $paymentProof,
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
                            <span class="text-amber-800">ຮູບຖ່າຍ 4x6 ຈຳນວນ 2 ຮູບ</span>
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
                                ຊື່ <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="first_name" name="first_name" required
                                   value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lao-red focus:border-lao-red">
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                ນາມສະກຸນ <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="last_name" name="last_name" required
                                   value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
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
                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                                                <h3 class="text-lg font-semibold text-blue-900 mb-4 flex items-center">
                                                    🏦 ຂໍ້ມູນການຊຳລະເງິນ
                                                </h3>
                                                <div class="bg-white rounded-lg p-4">
                                                    <div class="flex items-center justify-between bg-red-600 text-white p-4 rounded-lg">
                                                        <div class="flex items-center">
                                                            <span class="text-2xl mr-3">💳</span>
                                                            <div>
                                                                <p class="font-semibold">ຊື່ບັນຊີ: ANANTHASAK PHATHASIRA MONNK</p>
                                                                <div class="flex items-center justify-between flex-wrap gap-2">
                                                                    <div class="flex-1">
                                                                        <p class="text-red-100">ເລກບັນຊີ BCEL ONE:</p>
                                                                        <p class="text-white font-mono text-lg font-bold" id="accountNumber">010120001062171001</p>
                                                                    </div>
                                                                    <button type="button" onclick="copyAccountNumber()" id="copyBtn" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-1 hover:scale-105">
                                                                        <span id="copyIcon">📋</span>
                                                                        <span id="copyText">Copy</span>
                                                                    </button>
                                                                </div>

                                                                <script>
                                                                function copyAccountNumber() {
                                                                    const accountText = '010120001062171001';
                                                                    const copyBtn = document.getElementById('copyBtn');
                                                                    const copyIcon = document.getElementById('copyIcon');
                                                                    const copyText = document.getElementById('copyText');
                                                                    
                                                                    // Show loading state
                                                                    copyBtn.disabled = true;
                                                                    copyIcon.textContent = '⏳';
                                                                    copyText.textContent = 'ກຳລັງ...';
                                                                    
                                                                    if (navigator.clipboard && window.isSecureContext) {
                                                                        // Use modern clipboard API
                                                                        navigator.clipboard.writeText(accountText).then(function() {
                                                                            showCopySuccess();
                                                                        }).catch(function() {
                                                                            fallbackCopy(accountText);
                                                                        });
                                                                    } else {
                                                                        // Fallback for older browsers
                                                                        fallbackCopy(accountText);
                                                                    }
                                                                    
                                                                    function showCopySuccess() {
                                                                        // Show success state
                                                                        copyIcon.textContent = '✅';
                                                                        copyText.textContent = 'ຄັດລອກແລ້ວ!';
                                                                        copyBtn.className = 'bg-green-500 bg-opacity-90 text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-1';
                                                                        
                                                                        // Show toast notification
                                                                        showToast('✅ ຄັດລອກເລກບັນຊີສຳເລັດແລ້ວ!', 'success');
                                                                        
                                                                        // Reset button after 2 seconds
                                                                        setTimeout(function() {
                                                                            copyBtn.disabled = false;
                                                                            copyIcon.textContent = '📋';
                                                                            copyText.textContent = 'Copy';
                                                                            copyBtn.className = 'bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-1 hover:scale-105';
                                                                        }, 2000);
                                                                    }
                                                                    
                                                                    function showCopyError() {
                                                                        // Show error state
                                                                        copyIcon.textContent = '❌';
                                                                        copyText.textContent = 'ຜິດພາດ';
                                                                        copyBtn.className = 'bg-red-500 bg-opacity-90 text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-1';
                                                                        
                                                                        // Show error notification
                                                                        showToast('❌ ບໍ່ສາມາດຄັດລອກໄດ້: ' + accountText, 'error');
                                                                        
                                                                        // Reset button after 3 seconds
                                                                        setTimeout(function() {
                                                                            copyBtn.disabled = false;
                                                                            copyIcon.textContent = '📋';
                                                                            copyText.textContent = 'Copy';
                                                                            copyBtn.className = 'bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 flex items-center gap-1 hover:scale-105';
                                                                        }, 3000);
                                                                    }
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

                                                                // Toast notification system
                                                                function showToast(message, type = 'success') {
                                                                    // Remove existing toast if any
                                                                    const existingToast = document.getElementById('copyToast');
                                                                    if (existingToast) {
                                                                        existingToast.remove();
                                                                    }
                                                                    
                                                                    // Create toast element
                                                                    const toast = document.createElement('div');
                                                                    toast.id = 'copyToast';
                                                                    toast.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white font-medium animate-fade-in ${
                                                                        type === 'success' ? 'bg-green-500' : 'bg-red-500'
                                                                    }`;
                                                                    toast.style.maxWidth = '300px';
                                                                    toast.textContent = message;
                                                                    
                                                                    // Add to page
                                                                    document.body.appendChild(toast);
                                                                    
                                                                    // Auto remove after 3 seconds
                                                                    setTimeout(function() {
                                                                        if (toast && toast.parentNode) {
                                                                            toast.style.opacity = '0';
                                                                            setTimeout(function() {
                                                                                if (toast && toast.parentNode) {
                                                                                    toast.remove();
                                                                                }
                                                                            }, 300);
                                                                        }
                                                                    }, 3000);
                                                                }
                                                                </script>
                                                                <p class="font-semibold">ຄ່າລົງທະບຽນ: 200.000 ກີບ</p>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center"> 
                                                            <img src="image/logobcel.png" alt="BCEL ONE" class="h-8 w-auto" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                    </h2>
                    
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
// Form validation and submission handling
document.getElementById('registrationForm').addEventListener('submit', function(e) {
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

// Reset button if page is refreshed/reloaded
window.addEventListener('load', function() {
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '🚀 ສົ່ງການລົງທະບຽນ';
    }
});
</script>

<?php include 'includes/footer.php'; ?>