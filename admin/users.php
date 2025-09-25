<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn()) {
    redirect('login.php');
}

if (!hasRole(ROLE_ADMIN)) {
    setFlashMessage('error', 'ທ່ານບໍ່ມີສິດເຂົ້າເຖິງໜ້ານີ້ - ຕ້ອງເປັນ Admin ເທົ່ານັ້ນ');
    redirect('dashboard.php');
}

$error = '';
$success = '';
$users = [];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $username = trim($_POST['username'] ?? '');
                $password = trim($_POST['password'] ?? '');
                $role = trim($_POST['role'] ?? '');
                
                if (empty($username) || empty($password) || empty($role)) {
                    $error = 'ກະລຸນາໃສ່ຂໍ້ມູນໃຫ້ຄົບຖ້ວນ';
                } elseif (strlen($password) < 6) {
                    $error = 'ລະຫັດຜ່ານຕ້ອງມີຢ່າງໜ້ອຍ 6 ຕົວອັກສອນ';
                } else {
                    try {
                        // Check if username already exists
                        $stmt = $db->query("SELECT id FROM users WHERE username = ?", [$username]);
                        if ($stmt->fetch()) {
                            $error = 'ຊື່ຜູ້ໃຊ້ນີ້ມີໃນລະບົບແລ້ວ';
                        } else {
                            // Create new user
                            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                            $stmt = $db->query("INSERT INTO users (username, password, role) VALUES (?, ?, ?)", 
                                             [$username, $hashedPassword, $role]);
                            $success = 'ສ້າງຜູ້ໃຊ້ໃໝ່ສຳເລັດແລ້ວ';
                        }
                    } catch (Exception $e) {
                        $error = 'ເກີດຂໍ້ຜິດພາດ: ' . $e->getMessage();
                    }
                }
                break;
                
            case 'update':
                $id = intval($_POST['id'] ?? 0);
                $username = trim($_POST['username'] ?? '');
                $role = trim($_POST['role'] ?? '');
                $password = trim($_POST['password'] ?? '');
                
                if (empty($username) || empty($role) || $id <= 0) {
                    $error = 'ຂໍ້ມູນບໍ່ຖືກຕ້ອງ';
                } else {
                    try {
                        // Check if username already exists for other users
                        $stmt = $db->query("SELECT id FROM users WHERE username = ? AND id != ?", [$username, $id]);
                        if ($stmt->fetch()) {
                            $error = 'ຊື່ຜູ້ໃຊ້ນີ້ມີໃນລະບົບແລ້ວ';
                        } else {
                            if (!empty($password)) {
                                // Update with new password
                                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                                $stmt = $db->query("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?", 
                                                 [$username, $hashedPassword, $role, $id]);
                            } else {
                                // Update without password
                                $stmt = $db->query("UPDATE users SET username = ?, role = ? WHERE id = ?", 
                                                 [$username, $role, $id]);
                            }
                            $success = 'ອັບເດດຂໍ້ມູນຜູ້ໃຊ້ສຳເລັດແລ້ວ';
                        }
                    } catch (Exception $e) {
                        $error = 'ເກີດຂໍ້ຜິດພາດ: ' . $e->getMessage();
                    }
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id'] ?? 0);
                if ($id <= 0) {
                    $error = 'ຂໍ້ມູນບໍ່ຖືກຕ້ອງ';
                } elseif ($id == $_SESSION['user_id']) {
                    $error = 'ບໍ່ສາມາດລົບບັນຊີຕົນເອງໄດ້';
                } else {
                    try {
                        $stmt = $db->query("DELETE FROM users WHERE id = ?", [$id]);
                        $success = 'ລົບຜູ້ໃຊ້ສຳເລັດແລ້ວ';
                    } catch (Exception $e) {
                        $error = 'ເກີດຂໍ້ຜິດພາດ: ' . $e->getMessage();
                    }
                }
                break;
        }
    }
}

// Get all users
try {
    $stmt = $db->query("SELECT id, username, role, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = 'ເກີດຂໍ້ຜິດພາດໃນການໂຫຼດຂໍ້ມູນ: ' . $e->getMessage();
}

$pageTitle = 'ຈັດການຜູ້ໃຊ້ - ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts - Phetsarath -->
    <link href="https://fonts.googleapis.com/css2?family=Phetsarath:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Phetsarath', sans-serif; }
        .hover-scale { transition: transform 0.2s ease-in-out; }
        .hover-scale:hover { transform: scale(1.02); }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 'phetsarath': ['Phetsarath', 'sans-serif'] }
                }
            }
        }
        
        function editUser(id, username, role) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_role').value = role;
            document.getElementById('edit_password').value = '';
            document.getElementById('editModal').classList.remove('hidden');
        }
        
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
        
        function confirmDelete(id, username) {
            if (confirm('ຕ້ອງການລົບຜູ້ໃຊ້ "' + username + '" ແທ້ບໍ?')) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteForm').submit();
            }
        }
        
        function showCreateForm() {
            document.getElementById('createModal').classList.remove('hidden');
        }
        
        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">🏛️ <?php echo APP_NAME; ?></h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">
                        📊 ໜ້າຫຼັກ
                    </a>
                    <a href="registrations.php" class="hover:bg-blue-700 px-3 py-2 rounded-md text-sm font-medium">
                        📋 ການລົງທະບຽນ
                    </a>
                    <a href="users.php" class="bg-blue-800 px-3 py-2 rounded-md text-sm font-medium">
                        👥 ຈັດການຜູ້ໃຊ້
                    </a>
                    <a href="logout.php" class="hover:bg-red-700 bg-red-600 px-3 py-2 rounded-md text-sm font-medium">
                        🚪 ອອກຈາກລະບົບ
                    </a>
                    <span class="text-blue-200">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">👥 ຈັດການຜູ້ໃຊ້</h2>
                            <p class="mt-1 text-sm text-gray-600">ສ້າງ, ແກ້ໄຂ, ແລະລົບຜູ້ໃຊ້ໃນລະບົບ</p>
                        </div>
                        <button onclick="showCreateForm()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold hover-scale">
                            ➕ ເພີ່ມຜູ້ໃຊ້ໃໝ່
                        </button>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    ❌ <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    ✅ <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Users Table -->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        📋 ລາຍຊື່ຜູ້ໃຊ້ທັງໝົດ (<?php echo count($users); ?> ຄົນ)
                    </h3>
                </div>
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($users as $user): ?>
                        <li class="px-4 py-4 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <?php if ($user['role'] === 'admin'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                👑 Admin
                                            </span>
                                        <?php elseif ($user['role'] === 'staff'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                👨‍💼 Staff
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                👁️ Viewer
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            👤 <?php echo htmlspecialchars($user['username']); ?>
                                            <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                                <span class="text-green-600 text-xs">(ທ່ານ)</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            📅 ສ້າງເມື່ອ: <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button onclick="editUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>', '<?php echo $user['role']; ?>')" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm hover-scale">
                                        ✏️ แก้ไข
                                    </button>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <button onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')" 
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm hover-scale">
                                            🗑️ ลบ
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    
                    <?php if (empty($users)): ?>
                        <li class="px-4 py-8 text-center text-gray-500">
                            📭 ບໍ່ມີຜູ້ໃຊ້ໃນລະບົບ
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

        </div>
    </div>

    <!-- Create User Modal -->
    <div id="createModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">➕ ເພີ່ມຜູ້ໃຊ້ໃໝ່</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">ຊື່ຜູ້ໃຊ້</label>
                        <input type="text" name="username" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">ລະຫັດຜ່ານ</label>
                        <input type="password" name="password" required minlength="6"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">ຢ່າງໜ້ອຍ 6 ຕົວອັກສອນ</p>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">ບົດບາດ</label>
                        <select name="role" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- ເລືອກບົດບາດ --</option>
                            <option value="admin">👑 Admin (ເຂົ້າເຖິງທຸກຢ່າງ)</option>
                            <option value="staff">👨‍💼 Staff (ຈັດການການລົງທະບຽນ)</option>
                            <option value="viewer">👁️ Viewer (ເບິ່ງຂໍ້ມູນເທົ່ານັ້ນ)</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCreateModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            ຍົກເລີກ
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            ➕ ສ້າງຜູ້ໃຊ້
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">✏️ แก้ไขผู้ใช้</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">ຊື່ຜູ້ໃຊ້</label>
                        <input type="text" name="username" id="edit_username" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">ລະຫັດຜ່ານໃໝ່</label>
                        <input type="password" name="password" id="edit_password" minlength="6"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">ປ່ອຍວ່າງໄວ້ຫາກບໍ່ຕ້ອງການປ່ຽນລະຫັດຜ່ານ</p>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">ບົດບາດ</label>
                        <select name="role" id="edit_role" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="admin">👑 Admin (ເຂົ້າເຖິງທຸກຢ່າງ)</option>
                            <option value="staff">👨‍💼 Staff (ຈັດການການລົງທະບຽນ)</option>
                            <option value="viewer">👁️ Viewer (ເບິ່ງຂໍ້ມູນເທົ່ານັ້ນ)</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            ຍົກເລີກ
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            ✏️ ອັບเດດ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden Delete Form -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" id="delete_id">
    </form>

</body>
</html>