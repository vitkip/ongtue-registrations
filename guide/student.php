<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$pageTitle = 'ຄູ່ມືສຳລັບນິສິດ - ລະບົບລົງທະບຽນຮັບໃບຢັ້ງຢືນ';
include '../includes/header.php';
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <span class="text-6xl mb-4 block">👨‍🎓</span>
                <h1 class="text-3xl font-bold mb-2">ຄູ່ມືສຳລັບນິສິດ</h1>
                <p class="text-xl opacity-90">ວິທີການລົງທະບຽນຮັບໃບຢັ້ງຢືນແບບລະອຽດ</p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Step by Step Guide -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">📋 ຂັ້ນຕອນການລົງທະບຽນ</h2>

            <!-- Step 1 -->
            <div class="mb-8 border-l-4 border-green-500 pl-6">
                <div class="flex items-center mb-4">
                    <span class="bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">1</span>
                    <h3 class="text-xl font-semibold text-gray-900">ກະກຽມເອກະສານ</h3>
                </div>
                <p class="text-gray-700 mb-4">ກ່ອນເລີ່ມລົງທະບຽນ ທ່ານຕ້ອງກະກຽມເອກະສານດັ່ງນີ້:</p>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <span class="text-green-500 mr-2">✓</span>
                            <span class="text-gray-700">ຮູບຖ່າຍໜ້າຕັ້ງ 4x6 ຊັມ (ໄຟລ໌ JPG ຫຼື PNG, ຂະໜາດບໍ່ເກີນ 5MB)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-green-500 mr-2">✓</span>
                            <span class="text-gray-700">ໃບຢັ້ງຢືນການຈ່າຍເງິນ (ໄຟລ໌ JPG ຫຼື PNG, ຂະໜາດບໍ່ເກີນ 5MB)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-green-500 mr-2">✓</span>
                            <span class="text-gray-700">ຂໍ້ມູນສ່ວນໂຕທີ່ຖືກຕ້ອງ (ຊື່, ນາມສະກຸນ, ລະຫັດນິສິດ, ສາຂາວິຊາ)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-green-500 mr-2">✓</span>
                            <span class="text-gray-700">ອີເມວແລະເບີໂທລະສັບທີ່ໃຊ້ງານໄດ້</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="mb-8 border-l-4 border-green-500 pl-6">
                <div class="flex items-center mb-4">
                    <span class="bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">2</span>
                    <h3 class="text-xl font-semibold text-gray-900">ເຂົ້າສູ່ຫນ້າລົງທະບຽນ</h3>
                </div>
                <p class="text-gray-700 mb-4">ກົດປຸ່ມ "ລົງທະບຽນ" ໃນໜ້າຫຼັກ ຫຼື ເຂົ້າໄປທີ່ URL:</p>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <code class="text-blue-800">http://localhost/registrations/register.php</code>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="mb-8 border-l-4 border-green-500 pl-6">
                <div class="flex items-center mb-4">
                    <span class="bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">3</span>
                    <h3 class="text-xl font-semibold text-gray-900">ເຕີມຂໍ້ມູນສ່ວນໂຕ</h3>
                </div>
                <p class="text-gray-700 mb-4">ກະລຸນາເຕີມຂໍ້ມູນໃຫ້ຄົບຖ້ວນແລະຖືກຕ້ອງ:</p>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">ຂໍ້ມູນພື້ນຖານ:</h4>
                            <ul class="space-y-1 text-sm text-gray-700">
                                <li>• ຊື່ (ພາສາອັງກິດ)</li>
                                <li>• ນາມສະກຸນ (ພາສາອັງກິດ)</li>
                                <li>• ລະຫັດນິສິດ</li>
                                <li>• ສາຂາວິຊາ</li>
                                <li>• ປີສຳເລັດການສຶກສາ</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">ຂໍ້ມູນຕິດຕໍ່:</h4>
                            <ul class="space-y-1 text-sm text-gray-700">
                                <li>• ອີເມວ</li>
                                <li>• ເບີໂທລະສັບ</li>
                                <li>• ຮູບຖ່າຍໜ້າຕັ້ງ</li>
                                <li>• ໃບຢັ້ງຢືນການຈ່າຍເງິນ</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4 -->
            <div class="mb-8 border-l-4 border-green-500 pl-6">
                <div class="flex items-center mb-4">
                    <span class="bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">4</span>
                    <h3 class="text-xl font-semibold text-gray-900">ອັບໂຫຼດໄຟລ໌</h3>
                </div>
                <p class="text-gray-700 mb-4">ອັບໂຫຼດຮູບຖ່າຍແລະໃບຢັ້ງຢືນການຈ່າຍເງິນ:</p>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="font-semibold text-yellow-800 mb-2">⚠️ ຂໍ້ກຳນົດໄຟລ໌:</h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• ປະເພດໄຟລ໌: JPG, JPEG, PNG ເທົ່ານັ້ນ</li>
                        <li>• ຂະໜາດສູງສຸດ: 5MB ຕໍ່ໄຟລ໌</li>
                        <li>• ຮູບຖ່າຍຄວນຊັດເຈນແລະເບິ່ງໄດ້ງ່າຍ</li>
                        <li>• ໃບຢັ້ງຢືນການຈ່າຍເງິນຕ້ອງອ່ານໄດ້ຊັດເຈນ</li>
                    </ul>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="mb-8 border-l-4 border-green-500 pl-6">
                <div class="flex items-center mb-4">
                    <span class="bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">5</span>
                    <h3 class="text-xl font-semibold text-gray-900">ຍືນຍັນການລົງທະບຽນ</h3>
                </div>
                <p class="text-gray-700 mb-4">ກວດສອບຂໍ້ມູນທັງໝົດອີກຄັ້ງແລ້ວກົດ "ລົງທະບຽນ"</p>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-green-800">✅ ຫຼັງຈາກລົງທະບຽນສຳເລັດ ທ່ານຈະໄດ້ຮັບການແຈ້ງເຕືອນແລະສາມາດຕິດຕາມສະຖານະໄດ້</p>
                </div>
            </div>
        </div>

        <!-- Status Tracking -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">📊 ການຕິດຕາມສະຖານະ</h2>
            
            <div class="space-y-6">
                <div class="flex items-start">
                    <span class="bg-yellow-100 text-yellow-800 rounded-full px-3 py-1 text-sm font-semibold mr-4">⏳ ລໍຖ້າການອະນຸມັດ</span>
                    <div>
                        <h4 class="font-semibold text-gray-900">ສະຖານະເບື້ອງຕົ້ນ</h4>
                        <p class="text-gray-600">ການລົງທະບຽນຂອງທ່ານຖືກສົ່ງແລ້ວ ແລະ ກຳລັງລໍຖ້າການກວດສອບຈາກເຈົ້າໜ້າທີ່</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <span class="bg-green-100 text-green-800 rounded-full px-3 py-1 text-sm font-semibold mr-4">✅ ອະນຸມັດແລ້ວ</span>
                    <div>
                        <h4 class="font-semibold text-gray-900">ອະນຸມັດສຳເລັດ</h4>
                        <p class="text-gray-600">ການລົງທະບຽນຂອງທ່ານຖືກອະນຸມັດແລ້ວ ສາມາດມາຮັບໃບຢັ້ງຢືນໄດ້</p>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <span class="bg-red-100 text-red-800 rounded-full px-3 py-1 text-sm font-semibold mr-4">❌ ປະຕິເສດ</span>
                    <div>
                        <h4 class="font-semibold text-gray-900">ປະຕິເສດ</h4>
                        <p class="text-gray-600">ການລົງທະບຽນຂອງທ່ານຖືກປະຕິເສດ ກະລຸນາຕິດຕໍ່ເຈົ້າໜ້າທີ່ເພື່ອສອບຖາມເຫດຜົນ</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">💳 ຂໍ້ມູນການຊຳລະເງິນ</h2>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">ບັນຊີສຳລັບໂອນເງິນ</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">BCEL</h4>
                        <div class="space-y-1 text-gray-700">
                            <p><span class="font-medium">ຊື່ບັນຊີ:</span> University Account</p>
                            <p><span class="font-medium">ເລກບັນຊີ:</span> 0001-23-456789-0</p>
                            <p><span class="font-medium">ສາຂາ:</span> ວຽງຈັນ</p>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">ຄ່າທຳນຽມ</h4>
                        <div class="space-y-1 text-gray-700">
                            <p><span class="font-medium">ໃບຢັ້ງຢືນ:</span> 50,000 ກີບ</p>
                            <p><span class="font-medium">ໃບສຳເນົາ:</span> 10,000 ກີບ</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 text-sm text-gray-600">
                <p>💡 <strong>ຄຳແນະນຳ:</strong> ກະລຸນາຖ່າຍຮູບໃບຢັ້ງຢືນການໂອນເງິນຢ່າງຊັດເຈນ ແລະ ອັບໂຫຼດໃນຂັ້ນຕອນການລົງທະບຽນ</p>
            </div>
        </div>

        <!-- Common Issues -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">⚠️ ບັນຫາທີ່ພົບເລື່ອຍ</h2>
            
            <div class="space-y-6">
                <div class="border-l-4 border-red-500 pl-4">
                    <h4 class="font-semibold text-gray-900 mb-2">ອັບໂຫຼດໄຟລ໌ບໍ່ໄດ້</h4>
                    <p class="text-gray-700 mb-2">ອາດເປັນເພາະ:</p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• ໄຟລ໌ມີຂະໜາດໃຫຍ່ເກີນ 5MB</li>
                        <li>• ປະເພດໄຟລ໌ບໍ່ຖືກຕ້ອງ (ຕ້ອງເປັນ JPG, PNG)</li>
                        <li>• ອິນເຕີເນັດຊ້າ ລອງອັບໂຫຼດໃໝ່</li>
                    </ul>
                </div>
                
                <div class="border-l-4 border-yellow-500 pl-4">
                    <h4 class="font-semibold text-gray-900 mb-2">ຂໍ້ມູນບໍ່ຖືກບັນທຶກ</h4>
                    <p class="text-gray-700 mb-2">ການແກ້ໄຂ:</p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• ກວດສອບການເຊື່ອມຕໍ່ອິນເຕີເນັດ</li>
                        <li>• ເຕີມຂໍ້ມູນບັງຄັບໃຫ້ຄົບ</li>
                        <li>• ລອງຮີເຟຼດໜ້າເວັບ</li>
                    </ul>
                </div>
                
                <div class="border-l-4 border-blue-500 pl-4">
                    <h4 class="font-semibold text-gray-900 mb-2">ລືມລະຫັດນິສິດ</h4>
                    <p class="text-gray-700">ຕິດຕໍ່ຫ້ອງການບໍລິຫານ ຫຼື ເບິ່ງໃນໃບລາຍງານຄະແນນ/ໃບລົງທະບຽນເກົ່າ</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between items-center">
            <a href="../guide.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                ← ກັບໄປຄູ່ມືຫຼັກ
            </a>
            
            <a href="faq.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                ເບິ່ງຄຳຖາມທີ່ພົບເລື່ອຍ →
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>