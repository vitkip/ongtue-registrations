<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'คู่มือการใช้งาน - ລະບົບລົງທະບຽນຮັບໃບຢັ້ງຢືນ';
include 'includes/header.php';
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-lao-red to-red-600 text-white">
        <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">📖 ຄູ່ມືການໃຊ້ງານຂອງລະບົບ</h1>
                <h2 class="text-2xl mb-6">ລະບົບລົງທະບຽນຮັບໃບປະກາດນິຍະບັດ</h2>
                <p class="text-xl opacity-90">ຄູ່ມືການນຳໃຊ້ລະບົບສຳລັບນັກສຶກສາ ແລະ ຜູ້ບໍລິຫານ</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Navigation Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <!-- Student Guide -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="bg-gradient-to-r from-green-500 to-green-600 h-24 flex items-center justify-center">
                    <span class="text-4xl">👨‍🎓</span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">ຄູ່ມືສຳລັບນັກສຶກສາ</h3>
                    <p class="text-gray-600 mb-4">ວິທີການລົງທະບຽນຮັບໃບຢັ້ງຢືນ ແລະ ການຕິດຕາມສະຖານະ</p>
                    <a href="guide/student.php" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        ເບິ່ງຄູ່ມື →
                    </a>
                </div>
            </div>

            <!-- Admin Guide -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-24 flex items-center justify-center">
                    <span class="text-4xl">👨‍💼</span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">ຄູ່ມືສຳລັບຜູ້ບໍລິຫານ</h3>
                    <p class="text-gray-600 mb-4">ການຈັດການລົງທະບຽນ, ອະນຸມັດ ແລະ ລາຍງານ</p>
                    <a href="guide/admin.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        ເບິ່ງຄູ່ມື →
                    </a>
                </div>
            </div>

            <!-- FAQ -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-24 flex items-center justify-center">
                    <span class="text-4xl">❓</span>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-3">ຄຳຖາມທີ່ພົບເລື່ອຍ</h3>
                    <p class="text-gray-600 mb-4">ຄຳຖາມແລະຄຳຕອບທີ່ພົບເລື່ອຍໃນການໃຊ້ງານ</p>
                    <a href="guide/faq.php" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                        ເບິ່ງຄຳຖາມ →
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Start Guide -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">🚀 ການເລີ່ມຕົ້ນແບບໄວ</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- For Students -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <span class="bg-green-100 text-green-800 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">1</span>
                        ສຳລັບນັກສຶກສາ
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            <span class="text-gray-700">ກວດສອບຂໍ້ມູນສ່ວນໂຕໃຫ້ຖືກຕ້ອງ</span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            <span class="text-gray-700">ອັບໂຫຼດຮູບຖ່າຍແລະໃບຢັ້ງຢືນການຈ່າຍເງິນ</span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            <span class="text-gray-700">ກົດປຸ່ມລົງທະບຽນແລະລໍຖ້າການອະນຸມັດ</span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-green-500 mr-2">✓</span>
                            <span class="text-gray-700">ຕິດຕາມສະຖານະໃນໜ້າສະແດງຜົນ</span>
                        </div>
                    </div>
                </div>

                <!-- For Admins -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <span class="bg-blue-100 text-blue-800 rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">2</span>
                        ສຳລັບຜູ້ບໍລິຫານ
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <span class="text-blue-500 mr-2">✓</span>
                            <span class="text-gray-700">ເຂົ້າສູ່ລະບົບຜ່ານໜ້າ Admin</span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-blue-500 mr-2">✓</span>
                            <span class="text-gray-700">ກວດສອບລາຍການລົງທະບຽນ</span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-blue-500 mr-2">✓</span>
                            <span class="text-gray-700">ອະນຸມັດຫຼືປະຕິເສດການລົງທະບຽນ</span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-blue-500 mr-2">✓</span>
                            <span class="text-gray-700">ສົ່ງອອກລາຍງານເປັນ Excel</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Overview -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">📋 ພາບລວມຂອງລະບົບ</h2>
            
            <div class="prose max-w-none">
                <p class="text-gray-700 text-lg leading-relaxed mb-6">
                    ລະບົບລົງທະບຽນຮັບໃບຢັ້ງຢືນນີ້ຖືກພັດທະນາຂຶ້ນເພື່ອຊ່ວຍໃຫ້ນິສິດສາມາດລົງທະບຽນຮັບໃບຢັ້ງຢືນໄດ້ງ່າຍແລະສະດວກ 
                    ໂດຍຜ່ານລະບົບອອນລາຍທີ່ທັນສະໄໝ
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <div class="text-center">
                        <div class="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">📝</span>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">ລົງທະບຽນອອນລາຍ</h4>
                        <p class="text-gray-600 text-sm">ນິສິດສາມາດລົງທະບຽນຜ່ານເວັບໄຊທ໌ຂອງງ່າຍແລະໄວ</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">⚡</span>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">ຂະບວນການໄວ</h4>
                        <p class="text-gray-600 text-sm">ການອະນຸມັດແລະການປະມວນຜົນທີ່ໄວແລະມີປະສິດທິພາບ</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">📊</span>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">ລາຍງານຄົບຖ້ວນ</h4>
                        <p class="text-gray-600 text-sm">ສາມາດສົ່ງອອກລາຍງານແລະຕິດຕາມສະຖິຕິໄດ້</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">📞 ຂໍ້ມູນການຕິດຕໍ່</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">ຫາກມີຄຳຖາມຫຼືຕ້ອງການຄວາມຊ່ວຍເຫຼືອ</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <span class="text-blue-500 mr-3">📧</span>
                            <span class="text-gray-700">info@ongtue-ttc.edu.la</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-blue-500 mr-3">📱</span>
                            <span class="text-gray-700">+856 2077772338</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-blue-500 mr-3">⏰</span>
                            <span class="text-gray-700">ວັນຈັນ - ວັນສຸກ: 8:00 - 17:00</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">ຂໍ້ມູນເພີ່ມເຕີມ</h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <span class="text-green-500 mr-2">•</span>
                            <span class="text-gray-700">ລະບົບເປີດໃຫ້ບໍລິການ 24/7</span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-green-500 mr-2">•</span>
                            <span class="text-gray-700">ຂໍ້ມູນຈະຖືກເກັບຮັກສາຢ່າງປອດໄພ</span>
                        </div>
                        <div class="flex items-start">
                            <span class="text-green-500 mr-2">•</span>
                            <span class="text-gray-700">ສາມາດຕິດຕາມສະຖານະແບບເວລາຈິງ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-12">
            <a href="index.php" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-lao-red hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lao-red">
                ← ກັບໄປໜ້າຫຼັກ
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>