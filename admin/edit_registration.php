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
        $required_fields = ['first_name', 'last_name', 'student_code', 'major', 'graduation_year', 'email', 'phone'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("ກະລຸນາປ້ອນ " . $field);
            }
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
            'student_code' => trim($_POST['student_code']),
            'major' => trim($_POST['major']),
            'graduation_year' => (int)$_POST['graduation_year'],
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone']),
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

<div class="min-h-screen bg-gray-50">
    <!-- Page Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        ✏️ ແກ້ໄຂການລົງທະບຽນ
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        ແກ້ໄຂຂໍ້ມູນການລົງທະບຽນຂອງ <?php echo htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']); ?>
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="registrations.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lao-red">
                        ← ກັບໄປລາຍການ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <form method="POST" enctype="multipart/form-data" class="space-y-6 p-6">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">

                <?php if (isset($error)): ?>
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">❌ ເກີດຂໍ້ຜິດພາດ</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Personal Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">👤 ຂໍ້ມູນສ່ວນຕົວ</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">ຊື່ <span class="text-red-500">*</span></label>
                            <input type="text" id="first_name" name="first_name" 
                                   value="<?php echo htmlspecialchars($registration['first_name']); ?>"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lao-red focus:border-lao-red sm:text-sm" required>
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">ນາມສະກຸນ <span class="text-red-500">*</span></label>
                            <input type="text" id="last_name" name="last_name" 
                                   value="<?php echo htmlspecialchars($registration['last_name']); ?>"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lao-red focus:border-lao-red sm:text-sm" required>
                        </div>

                        <div>
                            <label for="student_code" class="block text-sm font-medium text-gray-700">ລະຫັດນິສິດ <span class="text-red-500">*</span></label>
                            <input type="text" id="student_code" name="student_code" 
                                   value="<?php echo htmlspecialchars($registration['student_code']); ?>"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lao-red focus:border-lao-red sm:text-sm" required>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">🎓 ຂໍ້ມູນການສຶກສາ</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="major" class="block text-sm font-medium text-gray-700">ສາຂາວິຊາ <span class="text-red-500">*</span></label>
                            <input type="text" id="major" name="major" 
                                   value="<?php echo htmlspecialchars($registration['major']); ?>"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lao-red focus:border-lao-red sm:text-sm" required>
                        </div>

                        <div>
                            <label for="graduation_year" class="block text-sm font-medium text-gray-700">ປີທີ່ສຳເລັດການສຶກສາ <span class="text-red-500">*</span></label>
                            <input type="number" id="graduation_year" name="graduation_year" 
                                   value="<?php echo $registration['graduation_year']; ?>"
                                   min="2020" max="2030"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lao-red focus:border-lao-red sm:text-sm" required>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">📞 ຂໍ້ມູນການຕິດຕໍ່</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">ອີເມວ <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($registration['email']); ?>"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lao-red focus:border-lao-red sm:text-sm" required>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">ເບີໂທລະສັບ <span class="text-red-500">*</span></label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($registration['phone']); ?>"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lao-red focus:border-lao-red sm:text-sm" required>
                        </div>
                    </div>
                </div>

                <!-- File Uploads -->
                <div class="border-b border-gray-200 pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">📎 ເອກະສານ</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="profile_image" class="block text-sm font-medium text-gray-700">ຮູບພາບໂປຣໄຟລ໌</label>
                            <?php if ($registration['profile_image']): ?>
                            <div class="mt-2 mb-2">
                                <img src="<?php echo getFileUrl($registration['profile_image'], 'profiles'); ?>" 
                                     alt="Current Profile" class="h-20 w-20 rounded-full object-cover">
                                <p class="text-sm text-gray-500 mt-1">ຮູບພາບປັດຈຸບັນ</p>
                            </div>
                            <?php endif; ?>
                            <input type="file" id="profile_image" name="profile_image" accept=".jpg,.jpeg,.png"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lao-red focus:border-lao-red sm:text-sm">
                            <p class="mt-1 text-sm text-gray-500">JPG, JPEG, PNG ຂະໜາດບໍ່ເກີນ 5MB</p>
                        </div>

                        <div>
                            <label for="payment_proof" class="block text-sm font-medium text-gray-700">ໃບຢັ້ງຢືນການຈ່າຍເງິນ</label>
                            <?php if ($registration['payment_proof']): ?>
                            <div class="mt-2 mb-2">
                                <a href="<?php echo getFileUrl($registration['payment_proof'], 'payments'); ?>" 
                                   target="_blank" class="text-blue-600 hover:text-blue-800">
                                    📎 ເບິ່ງໄຟລ໌ປັດຈຸບັນ
                                </a>
                            </div>
                            <?php endif; ?>
                            <input type="file" id="payment_proof" name="payment_proof" accept=".jpg,.jpeg,.png,.pdf"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lao-red focus:border-lao-red sm:text-sm">
                            <p class="mt-1 text-sm text-gray-500">JPG, JPEG, PNG, PDF ຂະໜາດບໍ່ເກີນ 10MB</p>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="pb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">📊 ສະຖານະ</h3>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">ສະຖານະການລົງທະບຽນ <span class="text-red-500">*</span></label>
                        <select id="status" name="status" 
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-lao-red focus:border-lao-red sm:text-sm" required>
                            <option value="pending" <?php echo $registration['status'] === 'pending' ? 'selected' : ''; ?>>⏳ ລໍຖ້າການອະນຸມັດ</option>
                            <option value="approved" <?php echo $registration['status'] === 'approved' ? 'selected' : ''; ?>>✅ ອະນຸມັດແລ້ວ</option>
                            <option value="rejected" <?php echo $registration['status'] === 'rejected' ? 'selected' : ''; ?>>❌ ປະຕິເສດ</option>
                        </select>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="registrations.php" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lao-red">
                        ຍົກເລີກ
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-lao-red hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lao-red">
                        💾 ບັນທຶກການປ່ຽນແປງ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>