<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Initialize variables
$error = '';

// Handle form submission
if ($_POST) {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'ກະລຸນາປ້ອນຊື່ຜູ້ໃຊ້ ແລະ ລະຫັດຜ່ານ';
    } else {
        try {
            $stmt = $db->query("SELECT id, username, password, role FROM users WHERE username = ?", [$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                
                // Log activity
                logActivity('User login', "Username: $username");
                
                // Redirect to dashboard
                redirect('dashboard.php');
            } else {
                $error = 'ຊື່ຜູ້ໃຊ້ ຫຼື ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ';
            }
        } catch (Exception $e) {
            $error = 'ເກີດຂໍ້ຜິດພາດໃນການເຂົ້າສູ່ລະບົບ';
            error_log("Login error: " . $e->getMessage());
        }
    }
}

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$pageTitle = 'ເຂົ້າສູ່ລະບົບ - ' . APP_NAME;
include '../includes/header.php';
?>

<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <img src="../image/logo.png" alt="Logo" class="h-16 w-16 mx-auto mb-4">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">🔐</h1>
            <h2 class="text-3xl font-bold text-gray-900">ເຂົ້າສູ່ລະບົບ</h2>
            <p class="mt-2 text-sm text-gray-600">
                ລະບົບຈັດການລົງທະບຽນໃບປະກາດນິຍະບັດ
            </p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-lg sm:rounded-lg sm:px-10">
            <!-- Error Message -->
            <?php if (!empty($error)): ?>
            <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <span class="text-red-400 mr-3">❌</span>
                    <p class="text-sm text-red-600"><?php echo htmlspecialchars($error); ?></p>
                </div>
            </div>
            <?php endif; ?>

            <form class="space-y-6" method="POST">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">
                        ຊື່ຜູ້ໃຊ້
                    </label>
                    <div class="mt-1">
                        <input id="username" name="username" type="text" required
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-lao-red focus:border-lao-red sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        ລະຫັດຜ່ານ
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required
                               class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:ring-lao-red focus:border-lao-red sm:text-sm">
                    </div>
                </div>

                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-lao hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lao-red transition-all duration-300">
                        🚀 ເຂົ້າສູ່ລະບົບ
                    </button>
                </div>
            </form>

            <!-- Demo Account Info -->
            <div class="mt-8 border-t border-gray-200 pt-6">
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <h3 class="text-sm font-medium text-blue-800 mb-2">🔑 ບັນຊີທົດລອງ:</h3>
                    <div class="text-sm text-blue-700">
                        <p><strong>ຊື່ຜູ້ໃຊ້:</strong> admin</p>
                        <p><strong>ລະຫັດຜ່ານ:</strong> admin123</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Home -->
    <div class="mt-6 text-center">
        <a href="../index.php" class="text-lao-red hover:text-red-700 text-sm font-medium">
            ← ກັບໄປໜ້າຫຼັກ
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>