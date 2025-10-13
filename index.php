<?php
// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Check if system is properly configured
$systemReady = true;
$errors = [];

// Try to load configuration files
try {
    require_once 'config/config.php';
} catch (Exception $e) {
    $systemReady = false;
    $errors[] = "Config error: " . $e->getMessage();
}

try {
    require_once 'config/database.php';
    if ($db === null) {
        $systemReady = false;
        $errors[] = "Database connection failed. MySQL เปิดอยู่หรือไม่? ฐานข้อมูล 'cert_system' สร้างแล้วหรือยัง?";
    }
} catch (Exception $e) {
    $systemReady = false;
    $errors[] = "Database error: " . $e->getMessage();
}

try {
    require_once 'includes/functions.php';
} catch (Exception $e) {
    $systemReady = false;
    $errors[] = "Functions error: " . $e->getMessage();
}

$pageTitle = defined('APP_NAME') ? 'ໜ້າຫຼັກ - ' . APP_NAME : 'ໜ້າຫຼັກ - ລະບົບລົງທະບຽນໃບປະກາດນິຍະບັດ';
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Phetsarath:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Phetsarath', sans-serif; }
        .bg-gradient-lao { background: linear-gradient(135deg, #dc2626 0%, #371cceff 50%, #09068bff 100%); }
        .shadow-lao { box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.2), 0 4px 6px -2px rgba(220, 38, 38, 0.1); }
        .text-shadow { text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        .animate-fade-in { animation: fadeIn 0.5s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slide-in { animation: slideIn 0.6s ease-out; }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
        .hover-scale { transition: transform 0.2s ease-in-out; }
        .hover-scale:hover { transform: scale(1.05); }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 'phetsarath': ['Phetsarath', 'sans-serif'] },
                    colors: { 'lao-red': '#dc2626', 'lao-blue': '#371cceff', 'lao-white': '#09068bff' }
                }
            }
        }
        
        // Keep scroll position after form submission
        document.addEventListener('DOMContentLoaded', function() {
            // If form was submitted, scroll to search section
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_status'])): ?>
            setTimeout(function() {
                document.getElementById('search-section').scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }, 100);
            <?php endif; ?>
            
            // Start countdown timer
            startCountdown();
        });
        
        // Countdown Timer Function
        function startCountdown() {
            const targetDate = new Date('2025-11-11T00:00:00').getTime();
            
            function updateCountdown() {
                const now = new Date().getTime();
                const timeLeft = targetDate - now;
                
                if (timeLeft > 0) {
                    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                    
                    const daysEl = document.getElementById('days');
                    const hoursEl = document.getElementById('hours');
                    const minutesEl = document.getElementById('minutes');
                    const secondsEl = document.getElementById('seconds');
                    const messageEl = document.getElementById('countdown-message');
                    
                    if (daysEl) daysEl.textContent = String(days).padStart(2, '0');
                    if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
                    if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
                    if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
                    
                    if (messageEl) {
                        messageEl.innerHTML = '🎓 ອີກ <strong>' + days + '</strong> ວັນເຖິງວັນຮັບໃບປະກາດ!';
                    }
                } else {
                    const timerEl = document.getElementById('countdown-timer');
                    if (timerEl) {
                        timerEl.innerHTML = 
                            '<div class="text-center p-4 sm:p-6 bg-white/20 backdrop-blur-sm rounded-lg border border-white/30">' +
                            '<h3 class="text-xl sm:text-2xl font-bold text-white mb-2">🎉 ຮອດວັນຮັບໃບປະກາດແລ້ວ!</h3>' +
                            '<p class="text-white/90 text-sm sm:text-base">ຍິນດີຕ້ອນຮັບສູ່ວັນຮັບໃບປະກາດນິຍະບັດ ວັນທີ 11 ພະຈິກ 2025</p>' +
                            '</div>';
                    }
                }
            }
            
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Navigation -->
    <nav class="bg-gradient-lao shadow-lao">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo Section -->
                <div class="flex items-center flex-shrink-0">
                    <a href="index.php" class="flex items-center">
                        <span class="text-white font-bold text-xl text-shadow">🎓</span>
                        
                        <span class="ml-2 text-white font-bold text-sm sm:text-base lg:text-lg text-shadow">
                            <span class="hidden sm:inline">ວິທະຍາໄລຄູສົງ ອົງຕື້</span>
                            <span class="sm:hidden">ວິທະຍາໄລຄູສົງ</span>
                        </span>
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="index.php" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        🏠 ໜ້າຫຼັກ
                    </a>
                    <a href="register.php" class="bg-white text-lao-red hover:bg-gray-100 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        📝 ລົງທະບຽນ
                    </a>
                    <a href="admin/login.php" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        🔐 ເຂົ້າສູ່ລະບົບ
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" type="button" class="text-white hover:text-gray-200 focus:outline-none focus:text-gray-200 transition-colors" aria-label="Toggle mobile menu">
                        <svg id="menu-open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg id="menu-close" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 bg-gradient-to-r from-red-600 to-blue-600 rounded-lg mt-2">
                    <a href="index.php" class="text-white hover:bg-white/20 block px-4 py-3 rounded-md text-base font-medium transition-colors">
                        🏠 ໜ້າຫຼັກ
                    </a>
                    <a href="register.php" class="bg-white text-lao-red hover:bg-gray-100 block px-4 py-3 rounded-md text-base font-medium transition-colors text-center font-semibold">
                        📝 ລົງທະບຽນ
                    </a>
                    <a href="admin/login.php" class="bg-blue-600 text-white hover:bg-blue-700 block px-4 py-3 rounded-md text-base font-medium transition-colors text-center">
                        🔐 ເຂົ້າສູ່ລະບົບ
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const menuOpen = document.getElementById('menu-open');
            const menuClose = document.getElementById('menu-close');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    const isHidden = mobileMenu.classList.contains('hidden');
                    
                    if (isHidden) {
                        // Show menu
                        mobileMenu.classList.remove('hidden');
                        mobileMenu.classList.add('animate-fade-in');
                        menuOpen.classList.add('hidden');
                        menuClose.classList.remove('hidden');
                    } else {
                        // Hide menu
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('animate-fade-in');
                        menuOpen.classList.remove('hidden');
                        menuClose.classList.add('hidden');
                    }
                });

                // Close mobile menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('animate-fade-in');
                        menuOpen.classList.remove('hidden');
                        menuClose.classList.add('hidden');
                    }
                });

                // Close mobile menu on window resize to desktop size
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 768) {
                        mobileMenu.classList.add('hidden');
                        menuOpen.classList.remove('hidden');
                        menuClose.classList.add('hidden');
                    }
                });

                // Close mobile menu when scrolling (optional UX improvement)
                let lastScrollY = window.scrollY;
                window.addEventListener('scroll', function() {
                    if (Math.abs(window.scrollY - lastScrollY) > 50) {
                        mobileMenu.classList.add('hidden');
                        mobileMenu.classList.remove('animate-fade-in');
                        menuOpen.classList.remove('hidden');
                        menuClose.classList.add('hidden');
                        lastScrollY = window.scrollY;
                    }
                });
            }
        });
    </script>

<?php if (!$systemReady): ?>
<!-- Database Setup Required -->
<div class="bg-gradient-lao py-20">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h1 class="text-3xl md:text-4xl font-bold text-red-600 mb-6">
                ⚠️ ຕ້ອງການການຕິດຕັ້ງຖານຂໍ້ມູນ
            </h1>
            <p class="text-lg text-gray-700 mb-8">
                ກະລຸນາຕິດຕັ້ງຖານຂໍ້ມູນກ່ອນໃຊ້ງານລະບົບ
            </p>
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-red-800 mb-2">❌ ຂໍ້ຜິດພາດ:</h3>
                <?php foreach ($errors as $error): ?>
                <p class="text-red-700 mb-1"><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-left">
                <h3 class="text-lg font-semibold text-blue-800 mb-4">📋 ຂັ້ນຕອນການຕິດຕັ້ງ:</h3>
                <ol class="space-y-3 text-blue-700">
                    <li class="flex items-start">
                        <span class="font-bold mr-2">1.</span>
                        <div>
                            <strong>ເປີດ phpMyAdmin:</strong>
                            <a href="http://localhost/phpmyadmin" target="_blank" class="text-blue-600 underline ml-2">
                                http://localhost/phpmyadmin
                            </a>
                        </div>
                    </li>
                    <li class="flex items-start">
                        <span class="font-bold mr-2">2.</span>
                        <span>ສ້າງຖານຂໍ້ມູນໃໝ່ຊື່ <code class="bg-gray-200 px-2 py-1 rounded">cert_system</code></span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-bold mr-2">3.</span>
                        <span>Import ໄຟລ໌ SQL ຈາກ <code class="bg-gray-200 px-2 py-1 rounded">config/database.sql</code></span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-bold mr-2">4.</span>
                        <span>ກວດສອບວ່າ XAMPP Apache ແລະ MySQL ເປີດແລ້ວ</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-bold mr-2">5.</span>
                        <span>Refresh ໜ້ານີ້</span>
                    </li>
                </ol>
            </div>

            <div class="mt-8 space-x-4">
                <a href="http://localhost/phpmyadmin" target="_blank" 
                   class="bg-blue-600 text-white hover:bg-blue-700 px-6 py-3 rounded-lg font-semibold transition-colors">
                    🗄️ ເປີດ phpMyAdmin
                </a>
                <a href="test.php" 
                   class="bg-green-600 text-white hover:bg-green-700 px-6 py-3 rounded-lg font-semibold transition-colors">
                    🧪 ທົດສອບລະບົບ
                </a>
                <button onclick="location.reload()" 
                        class="bg-gray-600 text-white hover:bg-gray-700 px-6 py-3 rounded-lg font-semibold transition-colors">
                    🔄 Refresh
                </button>
            </div>
        </div>
    </div>
</div>

<?php else: ?>

<!-- Hero Section -->
<div class="bg-gradient-lao py-12 sm:py-16 lg:py-20 min-h-screen flex items-center">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center w-full">
        <div class="animate-fade-in">
            <!-- Main Title -->
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white text-shadow mb-4 sm:mb-6 leading-tight">
                <span class="block sm:inline">🎓 ຍິນດີຕ້ອນຮັບ</span>
                <span class="block sm:inline mt-2 sm:mt-0">ບັນດິດໃໝ່ທຸກໆ ອົງ</span>
            </h1>
            
            <!-- Subtitle -->
            <p class="text-lg sm:text-xl md:text-2xl text-white text-shadow mb-6 sm:mb-8 leading-relaxed px-2">
                <span class="block sm:inline">ລົງທະບຽນເຂົ້າຮັບໃບປະກາດ</span>
                <span class="block sm:inline mt-1 sm:mt-0">ເລີ່ມແຕ່ມື້ນີ້ ຫາ ວັນທີ 07 ພະຈິກ 2025 ປິດຮັບລົງທະບຽນ</span>
            </p>
            
            <!-- Description -->
            <p class="text-base sm:text-lg text-white/90 mb-8 sm:mb-10 max-w-3xl mx-auto px-4 leading-relaxed">
                ນັກສຶກສາສົກ 2020-2025 ມີສິດລົງທະບຽນເຂົ້າຮັບໃບປະກາດ
            </p>
            
            <!-- Countdown Timer -->
            <div id="countdown-timer" class="mb-8 sm:mb-10">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold text-white text-shadow mb-4 sm:mb-6 leading-tight">
                <span class="block sm:inline">ມື້ຮັບ ທີ 11 ພະຈິກ 2025</span>
                
                </h1>
                <h3 class="text-lg sm:text-xl font-bold text-white/90 mb-4 sm:mb-6 text-center">⏰ ນັບຖອຍຫຼັງໄປຫາວັນຮັບໃບປະກາດ</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 max-w-md sm:max-w-2xl mx-auto px-4">
                    <div class="bg-white/20 backdrop-blur-sm text-white text-center p-3 sm:p-4 rounded-lg border border-white/30">
                        <div id="days" class="text-2xl sm:text-3xl font-bold">00</div>
                        <div class="text-xs sm:text-sm">ວັນ</div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm text-white text-center p-3 sm:p-4 rounded-lg border border-white/30">
                        <div id="hours" class="text-2xl sm:text-3xl font-bold">00</div>
                        <div class="text-xs sm:text-sm">ຊົ່ວໂມງ</div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm text-white text-center p-3 sm:p-4 rounded-lg border border-white/30">
                        <div id="minutes" class="text-2xl sm:text-3xl font-bold">00</div>
                        <div class="text-xs sm:text-sm">ນາທີ</div>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm text-white text-center p-3 sm:p-4 rounded-lg border border-white/30">
                        <div id="seconds" class="text-2xl sm:text-3xl font-bold">00</div>
                        <div class="text-xs sm:text-sm">ວິນາທີ</div>
                    </div>
                </div>
                <div id="countdown-message" class="text-center mt-3 sm:mt-4 text-sm sm:text-base font-medium text-white/90 px-4"></div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6 px-4">
                <a href="register.php" 
                   class="w-full sm:w-auto bg-white text-lao-red hover:bg-gray-100 px-6 sm:px-8 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-semibold transition-all duration-300 hover-scale shadow-lao text-center min-w-[200px]">
                    📝 ລົງທະບຽນດຽວນີ້
                </a>
                <a href="#search-section" 
                   class="w-full sm:w-auto bg-transparent border-2 border-white text-white hover:bg-white hover:text-lao-red px-6 sm:px-8 py-3 sm:py-4 rounded-lg text-base sm:text-lg font-semibold transition-all duration-300 text-center min-w-[200px]">
                    🔍 ກວດສອບສະຖານະ
                </a>
            </div>
            
            <!-- Mobile specific hint -->
            <div class="mt-8 sm:hidden">
                <p class="text-white/80 text-sm">
                    👆 ເລື່ອນຂຶ້ນລຸ່ມເພື່ອເບິ່ງຂໍ້ມູນເພີ່ມເຕີມ
                </p>
                <div class="animate-bounce mt-2">
                    <svg class="w-6 h-6 text-white/60 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div id="info" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                ຂັ້ນຕອນການລົງທະບຽນ
            </h2>
            <p class="text-xl text-gray-600">
                ກຽມຂໍ້ມູນການລົງທະບຽນໃຫ້ພ້ອມ ແລະ ຕິດຕາມຂໍ້ມູນເພື່ອເຂົ້າຮັບໃບປະກາດນິຍະບັດ
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center animate-slide-in">
                <div class="bg-gradient-to-br from-red-500 to-red-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <span class="text-3xl text-white">1️⃣</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">ກຸ້ມຂໍ້ມູນ</h3>
                <p class="text-gray-600">
                    ປ້ອນຂໍ້ມູນສ່ວນຕົວ, ສາຂາວິຊາ, ປີສຳເລັດການສຶກສາ ແລະ ຂໍ້ມູນຕິດຕໍ່
                </p>
            </div>

            <div class="text-center animate-slide-in" style="animation-delay: 0.2s;">
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <span class="text-3xl text-white">2️⃣</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">ອັບໂຫຼດເອກະສານ</h3>
                <p class="text-gray-600">
                    ອັບໂຫຼດຮູບພາບໂປຣໄຟລ໌ ແລະ ໃບຢັ້ງຢືນການຈ່າຍເງິນ
                </p>
            </div>

            <div class="text-center animate-slide-in" style="animation-delay: 0.4s;">
                <div class="bg-gradient-to-br from-green-500 to-green-600 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <span class="text-3xl text-white">3️⃣</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">ລໍຖ້າການອະນຸມັດ</h3>
                <p class="text-gray-600">
                    ລໍຖ້າໃຫ້ເຈົ້າໜ້າທີ່ກວດສອບ ແລະ ອະນຸມັດການລົງທະບຽນ
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="py-16 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
            <?php
            try {
                // Get statistics
                $stmt = $db->query("SELECT 
                    COUNT(*) as total_registrations,
                    COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                    COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected
                    FROM registrations");
                $stats = $stmt->fetch();
            ?>
            
            <div class="bg-white p-6 rounded-lg shadow-md hover-scale">
                <div class="text-3xl font-bold text-lao-red mb-2"><?php echo number_format($stats['total_registrations']); ?> ອົງ/ທ່ານ</div>
                <div class="text-gray-600">ລົງທະບຽນທັງໝົດ</div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md hover-scale">
                <div class="text-3xl font-bold text-green-600 mb-2"><?php echo number_format($stats['approved']); ?> ອົງ/ທ່ານ</div>
                <div class="text-gray-600">ອະນຸມັດແລ້ວ</div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md hover-scale">
                <div class="text-3xl font-bold text-yellow-600 mb-2"><?php echo number_format($stats['pending']); ?> ອົງ/ທ່ານ</div>
                <div class="text-gray-600">ລໍຖ້າການອະນຸມັດ</div>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md hover-scale">
                <div class="text-3xl font-bold text-red-600 mb-2"><?php echo number_format($stats['rejected']); ?> ອົງ/ທ່ານ</div>
                <div class="text-gray-600">ປະຕິເສດ</div>
            </div>
            
            <?php
            } catch (Exception $e) {
                echo '<div class="col-span-4 text-center text-red-600">ບໍ່ສາມາດໂຫຼດຂໍ້ມູນສະຖິຕິໄດ້</div>';
            }
            ?>
        </div>
    </div>
</div>

<!-- Requirements Section -->
<div class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                ເອກະສານທີ່ຕ້ອງການ
            </h2>
            <p class="text-xl text-gray-600">
                ກະລຸນາກຽມເອກະສານເຫຼົ່ານີ້ໃຫ້ພ້ອມ
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 p-8 rounded-xl">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
                    📄 ເອກະສານຈຳເປັນ
                </h3>
                <ul class="space-y-4 text-gray-700">
                    <li class="flex items-start">
                        <span class="text-green-500 mr-3 mt-1">✅</span>
                        <span>ຮູບພາບໂປຣໄຟລ໌ (ຂະໜາດບໍ່ເກີນ 5MB)</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-500 mr-3 mt-1">✅</span>
                        <span>ໃບຢັ້ງຢືນການຈ່າຍເງິນ (PDF, JPG, PNG)</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-500 mr-3 mt-1">✅</span>
                        <span>ຂໍ້ມູນສ່ວນຕົວທີ່ຖືກຕ້ອງ</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-500 mr-3 mt-1">✅</span>
                        <span>ອີເມວແລະເບີໂທລະສັບທີ່ໃຊ້ງານໄດ້</span>
                    </li>
                </ul>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-orange-100 p-8 rounded-xl">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6 flex items-center">
                    ⚠️ ຂໍ້ຄວນລະວັງ
                </h3>
                <ul class="space-y-4 text-gray-700">
                    <li class="flex items-start">
                        <span class="text-amber-500 mr-3 mt-1">⚠️</span>
                        <span>ກວດສອບຂໍ້ມູນໃຫ້ຖືກຕ້ອງກ່ອນສົ່ງ</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-amber-500 mr-3 mt-1">⚠️</span>
                        <span>ຮູບພາບຕ້ອງມີຄຸນນະພາບທີ່ດີ</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-amber-500 mr-3 mt-1">⚠️</span>
                        <span>ໃບຢັ້ງຢືນການຈ່າຍເງິນຕ້ອງຊັດເຈນ</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-amber-500 mr-3 mt-1">⚠️</span>
                        <span>ການອະນຸມັດໃຊ້ເວລາ 3-5 ວັນເຮັດການ</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Status Check Section -->
<div id="search-section" class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                🔍 ກວດສອບສະຖານະການລົງທະບຽນ
            </h2>
            <p class="text-xl text-gray-600">
                ປ້ອນຂໍ້ມູນຂອງທ່ານເພື່ອກວດສອບສະຖານະ
            </p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-8">
            <?php
            $checkResult = null;
            $searchError = null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_status'])) {
                $searchQuery = trim($_POST['search_query'] ?? '');

                if (empty($searchQuery)) {
                    $searchError = "ກະລຸນາປ້ອນຂໍ້ມູນທີ່ຕ້ອງການຄົ້ນຫາ";
                } else {
                    try {
                        // Search by student_code, first_name, last_name, first_name_en, last_name_en, email, or phone
                        $stmt = $db->query("SELECT * FROM registrations 
                                          WHERE student_code = ? 
                                          OR first_name LIKE ? 
                                          OR last_name LIKE ?
                                          OR first_name_en LIKE ?
                                          OR last_name_en LIKE ?
                                          OR email = ? 
                                          OR phone = ?
                                          LIMIT 10", 
                                          [$searchQuery, "%$searchQuery%", "%$searchQuery%", "%$searchQuery%", "%$searchQuery%", $searchQuery, $searchQuery]);
                        
                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (count($results) === 1) {
                            $checkResult = $results[0];
                        } elseif (count($results) > 1) {
                            $searchError = "ພົບຂໍ້ມູນຫຼາຍກວ່າ 1 ລາຍການ ກະລຸນາປ້ອນຂໍ້ມູນທີ່ສະເພາະເຈາະຈົງກວ່ານີ້";
                            $checkResult = $results; // Store multiple results for display
                        } else {
                            $searchError = "ບໍ່ພົບຂໍ້ມູນການລົງທະບຽນທີ່ຕົງກັບ: " . htmlspecialchars($searchQuery);
                        }
                    } catch (Exception $e) {
                        $searchError = "ເກີດຂໍ້ຜິດພາດໃນການຄົ້ນຫາ: " . $e->getMessage();
                    }
                }
            }
            ?>

            <!-- Search Form -->
            <form method="POST" class="space-y-6">
                <div class="max-w-2xl mx-auto">
                    <div>
                        <label for="search_query" class="block text-sm font-medium text-gray-700 mb-2">
                            � ຄົ້ນຫາດ້ວຍ <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="search_query" 
                                   name="search_query" 
                                   value="<?php echo htmlspecialchars($_POST['search_query'] ?? ''); ?>"
                                   placeholder="ລະຫັດນິສິດ, ຊື່, ອີເມວ ຫຼື ເບີໂທລະສັບ"
                                   class="w-full px-4 py-4 pr-12 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-400 text-xl">🔍</span>
                            </div>
                        </div>
                        <div class="mt-2 text-sm text-gray-600">
                            <p class="flex items-center flex-wrap gap-4">
                                <span class="flex items-center">
                                    <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                    📝 ລະຫັດນິສິດ (ເຊັ່ນ: STU001)
                                </span>
                                <span class="flex items-center">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    👤 ຊື່ (ເຊັ່ນ: ສົມຊາຍ)
                                </span>
                                <span class="flex items-center">
                                    <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                                    📧 ອີເມວ
                                </span>
                                <span class="flex items-center">
                                    <span class="w-2 h-2 bg-orange-500 rounded-full mr-2"></span>
                                    📱 ເບີໂທ
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" 
                            name="check_status"
                            class="bg-blue-600 text-white hover:bg-blue-700 px-10 py-4 rounded-lg text-lg font-semibold transition-colors duration-300 hover-scale shadow-lg">
                        🔍 ກວດສອບສະຖານະ
                    </button>
                </div>
            </form>

            <!-- Search Results -->
            <?php if ($searchError): ?>
            <div class="mt-8 bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">❌</span>
                    <div>
                        <h3 class="text-lg font-semibold text-red-800">
                            <?php if (is_array($checkResult) && count($checkResult) > 1): ?>
                                ພົບຫຼາຍລາຍການ
                            <?php else: ?>
                                ບໍ່ພົບຂໍ້ມູນ
                            <?php endif; ?>
                        </h3>
                        <p class="text-red-700"><?php echo htmlspecialchars($searchError); ?></p>
                        
                        <?php if (is_array($checkResult) && count($checkResult) > 1): ?>
                        <div class="mt-4">
                            <p class="text-red-700 mb-3">ພົບ <?php echo count($checkResult); ?> ລາຍການ:</p>
                            <div class="space-y-2">
                                <?php foreach ($checkResult as $result): ?>
                                <div class="bg-white rounded-lg p-3 border border-red-200">
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div><strong>ລະຫັດ:</strong> <?php echo htmlspecialchars($result['student_code']); ?></div>
                                        <div><strong>ຊື່ (ລາວ):</strong> <?php echo htmlspecialchars($result['first_name'] . ' ' . $result['last_name']); ?></div>
                                        <div><strong>ຊື່ (Eng):</strong> <span class="font-mono"><?php echo htmlspecialchars(($result['first_name_en'] ?? '') . ' ' . ($result['last_name_en'] ?? '')); ?></span></div>
                                        <div><strong>ອີເມວ:</strong> <?php echo htmlspecialchars($result['email']); ?></div>
                                        <div><strong>ເບີໂທ:</strong> <?php echo htmlspecialchars($result['phone']); ?></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <p class="text-red-600 mt-3 text-sm">💡 ກະລຸນາໃຊ້ລະຫັດນິສິດຫຼືອີເມວທີ່ສະເພາະເຈາະຈົງກວ່ານີ້</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>


            <?php if (isset($checkResult) && is_array($checkResult) && !isset($checkResult[0])): ?>
            <div class="mt-8 bg-white border rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-4">
                    <h3 class="text-xl font-bold text-white">📋 ຂໍ້ມູນການລົງທະບຽນ</h3>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <span class="font-semibold text-gray-700 w-32">ລະຫັດນິສິດ:</span>
                                <span class="text-gray-900"><?php echo htmlspecialchars($checkResult['student_code']); ?></span>
                            </div>
                            <div class="flex items-center">
                                <span class="font-semibold text-gray-700 w-32">ຊື່-ນາມສະກຸນ:</span>
                                <span class="text-gray-900"><?php echo htmlspecialchars($checkResult['first_name'] . ' ' . $checkResult['last_name']); ?></span>
                            </div>
                            <div class="flex items-center">
                                <span class="font-semibold text-gray-700 w-32">English Name:</span>
                                <span class="text-gray-900 font-mono"><?php echo htmlspecialchars(($checkResult['first_name_en'] ?? '') . ' ' . ($checkResult['last_name_en'] ?? '')); ?></span>
                            </div>
                            <div class="flex items-center">
                                <span class="font-semibold text-gray-700 w-32">ສາຂາວິຊາ:</span>
                                <span class="text-gray-900"><?php echo htmlspecialchars($checkResult['major']); ?></span>
                            </div>
                            <div class="flex items-center">
                                <span class="font-semibold text-gray-700 w-32">ປີສຳເລັດ:</span>
                                <span class="text-gray-900"><?php echo htmlspecialchars($checkResult['graduation_year']); ?></span>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center">
                                <span class="font-semibold text-gray-700 w-32">ອີເມວ:</span>
                                <span class="text-gray-900"><?php echo htmlspecialchars($checkResult['email']); ?></span>
                            </div>
                            <div class="flex items-center">
                                <span class="font-semibold text-gray-700 w-32">ເບີໂທ:</span>
                                <span class="text-gray-900"><?php echo htmlspecialchars($checkResult['phone']); ?></span>
                            </div>
                            <div class="flex items-center">
                                <span class="font-semibold text-gray-700 w-32">ວັນທີ່ລົງທະບຽນ:</span>
                                <span class="text-gray-900"><?php echo date('d/m/Y H:i', strtotime($checkResult['created_at'])); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Status Display -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="text-center">
                            <span class="text-lg font-semibold text-gray-700 mr-4">ສະຖານະປັດຈຸບັນ:</span>
                            <?php
                            $statusConfig = [
                                'pending' => [
                                    'icon' => '⏳',
                                    'text' => 'ລໍຖ້າການອະນຸມັດ',
                                    'class' => 'bg-yellow-100 text-yellow-800 border-yellow-200'
                                ],
                                'approved' => [
                                    'icon' => '✅',
                                    'text' => 'ອະນຸມັດແລ້ວ',
                                    'class' => 'bg-green-100 text-green-800 border-green-200'
                                ],
                                'rejected' => [
                                    'icon' => '❌',
                                    'text' => 'ປະຕິເສດ',
                                    'class' => 'bg-red-100 text-red-800 border-red-200'
                                ]
                            ];
                            
                            $status = $statusConfig[$checkResult['status']] ?? $statusConfig['pending'];
                            ?>
                            <span class="inline-flex items-center px-6 py-3 rounded-full text-lg font-semibold border-2 <?php echo $status['class']; ?>">
                                <span class="mr-2 text-xl"><?php echo $status['icon']; ?></span>
                                <?php echo $status['text']; ?>
                            </span>
                        </div>

                        <?php if ($checkResult['status'] === 'pending'): ?>
                        <div class="mt-4 text-center text-gray-600">
                            <p>📝 ການລົງທະບຽນຂອງທ່ານກຳລັງຢູ່ໃນຂະບວນການກວດສອບ</p>
                            <p>⏰ ກະລຸນາລໍຖ້າ 3-5 ວັນເຮັດການ</p>
                        </div>
                        <?php elseif ($checkResult['status'] === 'approved'): ?>
                        <div class="mt-6 bg-gradient-to-br from-green-50 to-emerald-100 border-2 border-green-200 rounded-xl p-6">
                            <div class="text-center">
                                <div class="mb-4">
                                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-green-800 mb-2">🎉 ຍິນດີດ້ວຍ!</h3>
                                    <p class="text-lg text-green-700 font-semibold">ການລົງທະບຽນຂອງທ່ານໄດ້ຮັບການອະນຸມັດແລ້ວ</p>
                                </div>
                                
                                <div class="bg-white rounded-lg p-4 mb-4 border border-green-200">
                                    <h4 class="font-bold text-green-800 mb-2">📋 ຂັ້ນຕອນຕໍ່ໄປ:</h4>
                                    <div class="space-y-2 text-green-700">
                                        <div class="flex items-start text-left">
                                            <span class="text-green-600 mr-2 mt-1">1️⃣</span>
                                            <span>ມາຮັບໃບປະກາດນິຍະບັດວັນທີ <strong>11 ພະຈິກ 2025</strong></span>
                                        </div>
                                        <div class="flex items-start text-left">
                                            <span class="text-green-600 mr-2 mt-1">2️⃣</span>
                                            <span>ເວລາ: <strong>12:00 - 16:00 ໂມງ</strong></span>
                                        </div>
                                        <div class="flex items-start text-left">
                                            <span class="text-green-600 mr-2 mt-1">3️⃣</span>
                                            <span>ສະຖານທີ່: <strong>ສາລາໃຫຍ່ ວັດອົງຕື້ວໍຣະມະຫາວິຫານ</strong></span>
                                        </div>
                                        <div class="flex items-start text-left">
                                            <span class="text-green-600 mr-2 mt-1">4️⃣</span>
                                            <span>ນຳເອົາບັດປະຈຳຕົວ ຫຼື ເອກະສານຢັ້ງຢືນຕົວຕົນມາສະແດງ</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                    <h4 class="font-bold text-blue-800 mb-2">📞 ຂໍ້ມູນການຕິດຕໍ່:</h4>
                                    <div class="space-y-1 text-blue-700 text-sm">
                                        <p>📧 ອີເມວ: info@ongtue.edu.la</p>
                                        <p>📱 ໂທລະສັບ: 020 77772338</p>
                                        <p>🏢 ທີ່ຢູ່: ວິທະຍາໄລຄູສົງ ອົງຕື້ ເມືອງຈັນທະບູລີ ນະຄອນວຽງຈັນ</p>
                                    </div>
                                </div>

                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <h4 class="font-bold text-yellow-800 mb-2">⚠️ ຂໍ້ສຳຄັນ:</h4>
                                    <p class="text-yellow-700 text-sm">
                                        ກະລຸນາມາຮັບໃບປະກາດນິຍະບັດໃນວັນ ແລະ ເວລາທີ່ກຳນົດ. 
                                        ຫາກບໍ່ສາມາດມາໄດ້ ກະລຸນາຕິດຕໍ່ຫ້ອງການລ່ວງໜ້າ.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php elseif ($checkResult['status'] === 'rejected'): ?>
                        <div class="mt-4 text-center text-red-700">
                            <p>😔 ເສຍໃຈດ້ວຍ ການລົງທະບຽນຂອງທ່ານຖືກປະຕິເສດ</p>
                            <p>📞 ກະລຸນາຟ້ອງຕິດຕໍ່ຫ້ອງການເພື່ອສອບຖາມລາຍລະອຽດ</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-gradient-lao py-12 sm:py-16 lg:py-20">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <!-- Mobile-responsive heading -->
        <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold text-white text-shadow mb-4 sm:mb-6 leading-tight">
            <span class="block sm:inline">ພ້ອມລົງທະບຽນ</span>
            <span class="block sm:inline mt-1 sm:mt-0">แລ້ວບໍ?</span>
        </h2>
        
        <!-- Mobile-responsive subtitle -->
        <p class="text-base sm:text-lg md:text-xl lg:text-2xl text-white/90 mb-6 sm:mb-8 lg:mb-10 leading-relaxed px-2">
            <span class="block sm:inline">ເລີ່ມຕົ້ນການລົງທະບຽນ</span>
            <span class="block sm:inline mt-1 sm:mt-0">ຮັບໃບປະກາດນິຍະບັດຂອງທ່ານດຽວນີ້</span>
        </p>
        
        <!-- Mobile-responsive buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6 px-4">
            <a href="register.php" 
               class="w-full sm:w-auto bg-white text-lao-red hover:bg-gray-100 px-6 sm:px-8 lg:px-10 py-3 sm:py-4 rounded-lg text-base sm:text-lg lg:text-xl font-semibold transition-all duration-300 hover-scale shadow-lao text-center min-w-[200px] sm:min-w-[220px]">
                🚀 ເລີ່ມລົງທະບຽນ
            </a>
            <a href="#info" 
               onclick="document.getElementById('search_query').focus(); return true;" 
               class="w-full sm:w-auto bg-transparent border-2 border-white text-white hover:bg-white hover:text-lao-red px-6 sm:px-8 lg:px-10 py-3 sm:py-4 rounded-lg text-base sm:text-lg lg:text-xl font-semibold transition-all duration-300 text-center min-w-[200px] sm:min-w-[220px]">
                📖 ອ່ານລາຍລະອຽດ
            </a>
        </div>
        
        <!-- Mobile-specific encouragement text -->
        <div class="mt-6 sm:mt-8">
            <p class="text-white/80 text-sm sm:text-base">
                <span class="hidden sm:inline">💡 ການລົງທະບຽນໃຊ້ເວລາພຽງ 5 ນາທີ</span>
                <span class="sm:hidden">💡 ງ່າຍດາຍ ພຽງ 5 ນາທີ</span>
            </p>
        </div>
        
        <!-- Mobile scroll hint -->
        <div class="mt-4 sm:hidden">
            <p class="text-white/70 text-xs">
                👆 ກົດປຸ່ມຂ້າງເທິງເພື່ອເລີ່ມຕົ້ນ
            </p>
        </div>
    </div>
</div>

<?php endif; ?>

<?php 
if ($systemReady && $db) {
    include 'includes/footer.php';
} else {
    echo '</body></html>';
}
?>