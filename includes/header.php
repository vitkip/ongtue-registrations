<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? APP_NAME; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts - Phetsarath -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Phetsarath:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Phetsarath', sans-serif;
        }
        
        .bg-gradient-lao {
            background: linear-gradient(135deg, #dc2626 0%, #371cceff 50%, #09068bff 100%);
        }
        
        .shadow-lao {
            box-shadow: 0 10px 25px -5px rgba(220, 38, 38, 0.2), 0 4px 6px -2px rgba(220, 38, 38, 0.1);
        }
        
        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-slide-in {
            animation: slideIn 0.6s ease-out;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        .hover-scale {
            transition: transform 0.2s ease-in-out;
        }
        
        .hover-scale:hover {
            transform: scale(1.05);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #dc2626;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #b91c1c;
        }
    </style>
    
    <!-- Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'phetsarath': ['Phetsarath', 'sans-serif'],
                    },
                    colors: {
                        'lao-red': '#dc2626',
                        'lao-blue': '#1e40af',
                        'lao-white': '#ffffff',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php
    // Display flash messages
    $flashMessage = getFlashMessage();
    if ($flashMessage):
    ?>
    <div id="flashMessage" class="fixed top-4 right-4 z-50 animate-fade-in">
        <div class="<?php echo $flashMessage['type'] === 'success' ? 'bg-green-500' : 'bg-red-500'; ?> text-white px-6 py-4 rounded-lg shadow-lg">
            <div class="flex items-center">
                <span class="mr-2">
                    <?php if ($flashMessage['type'] === 'success'): ?>
                        ✅
                    <?php else: ?>
                        ❌
                    <?php endif; ?>
                </span>
                <?php echo htmlspecialchars($flashMessage['message']); ?>
                <button onclick="closeFlashMessage()" class="ml-4 text-white hover:text-gray-200">
                    ✕
                </button>
            </div>
        </div>
    </div>
    <script>
        function closeFlashMessage() {
            document.getElementById('flashMessage').style.display = 'none';
        }
        // Auto close after 5 seconds
        setTimeout(closeFlashMessage, 5000);
    </script>
    <?php endif; ?>

    <!-- Navigation -->
    <nav class="bg-gradient-lao shadow-lao">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo Section -->
                <div class="flex items-center flex-shrink-0">
                    <a href="<?php echo APP_URL; ?>" class="flex items-center">
                        <span class="text-white font-bold text-xl text-shadow">🎓</span>
                        <span class="ml-2 text-white font-bold text-sm sm:text-base lg:text-lg text-shadow">
                            <span class="hidden sm:inline">ລະບົບລົງທະບຽນໃບປະກາດນິຍະບັດ</span>
                            <span class="sm:hidden">ວິທະຍາໄລຄູສົງ</span>
                        </span>
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-4">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo APP_URL; ?>/admin/dashboard.php" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            📊 ໜ້າຫຼັກ
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin/registrations.php" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            📋 ຈັດການລົງທະບຽນ
                        </a>
                        <a href="<?php echo APP_URL; ?>/guide.php" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            📖 ຄູ່ມືການໃຊ້
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin/logout.php" class="bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            🚪 ອອກຈາກລະບົບ
                        </a>
                    <?php else: ?>
                        <a href="<?php echo APP_URL; ?>/index.php" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            🏠 ໜ້າຫຼັກ
                        </a>
                        <a href="<?php echo APP_URL; ?>/guide.php" class="text-white hover:text-gray-200 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                            📖 ຄູ່ມືການໃຊ້
                        </a>
                        <a href="<?php echo APP_URL; ?>/register.php" class="bg-white text-lao-red hover:bg-gray-100 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            📝 ລົງທະບຽນ
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin/login.php" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            🔐 ເຂົ້າສູ່ລະບົບ
                        </a>
                    <?php endif; ?>
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
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo APP_URL; ?>/admin/dashboard.php" class="text-white hover:bg-white/20 block px-4 py-3 rounded-md text-base font-medium transition-colors">
                            📊 ໜ້າຫຼັກ
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin/registrations.php" class="text-white hover:bg-white/20 block px-4 py-3 rounded-md text-base font-medium transition-colors">
                            📋 ຈັດການລົງທະບຽນ
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin/logout.php" class="bg-red-600 text-white hover:bg-red-700 block px-4 py-3 rounded-md text-base font-medium transition-colors text-center">
                            🚪 ອອກຈາກລະບົບ
                        </a>
                    <?php else: ?>
                        <a href="<?php echo APP_URL; ?>/index.php" class="text-white hover:bg-white/20 block px-4 py-3 rounded-md text-base font-medium transition-colors">
                            🏠 ໜ້າຫຼັກ
                        </a>
                        <a href="<?php echo APP_URL; ?>/guide.php" class="text-white hover:bg-white/20 block px-4 py-3 rounded-md text-base font-medium transition-colors">
                            📖 ຄູ່ມືການໃຊ້
                        </a>
                        <a href="<?php echo APP_URL; ?>/register.php" class="bg-white text-lao-red hover:bg-gray-100 block px-4 py-3 rounded-md text-base font-medium transition-colors text-center font-semibold">
                            📝 ລົງທະບຽນ
                        </a>
                        <a href="<?php echo APP_URL; ?>/admin/login.php" class="bg-blue-600 text-white hover:bg-blue-700 block px-4 py-3 rounded-md text-base font-medium transition-colors text-center">
                            🔐 ເຂົ້າສູ່ລະບົບ
                        </a>
                    <?php endif; ?>
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
            }
        });
    </script>