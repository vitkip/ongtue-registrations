<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$pageTitle = 'ຄູ່ມືສຳລັບຜູ້ບໍລິຫານ - ລະບົບລົງທະບຽນຮັບໃບຢັ້ງຢືນ';
include '../includes/header.php';
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <span class="text-6xl mb-4 block">👨‍💼</span>
                <h1 class="text-3xl font-bold mb-2">ຄູ່ມືສຳລັບຜູ້ບໍລິຫານ</h1>
                <p class="text-xl opacity-90">ການຈັດການລະບົບລົງທະບຽນແລະການອະນຸມັດ</p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Getting Started -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">🚀 ການເລີ່ມຕົ້ນ</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-3">ເຂົ້າສູ່ລະບົບ</h3>
                    <div class="space-y-2 text-sm text-blue-800">
                        <p>• ໄປທີ່: <code>admin/login.php</code></p>
                        <p>• ໃຊ້ Username ແລະ Password ທີ່ໄດ້ຮັບ</p>
                        <p>• ກົດ "ເຂົ້າສູ່ລະບົບ"</p>
                    </div>
                </div>
                
                <div class="bg-green-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-green-900 mb-3">ສິດການເຂົ້າເຖິງ</h3>
                    <div class="space-y-2 text-sm text-green-800">
                        <p>• <strong>Admin:</strong> ສິດເຂົ້າເຖິງທັງໝົດ</p>
                        <p>• <strong>Staff:</strong> ອະນຸມັດ/ປະຕິເສດ</p>
                        <p>• <strong>Viewer:</strong> ເບິ່ງຂໍ້ມູນເທົ່ານັ້ນ</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Overview -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">📊 Dashboard ແລະການນຳທາງ</h2>
            
            <div class="space-y-6">
                <div class="border-l-4 border-blue-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">ໜ້າຫຼັກ Dashboard</h3>
                    <p class="text-gray-700 mb-3">ສະແດງສະຖິຕິສຳຄັນແລະຂໍ້ມູນພາບລວມ:</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="bg-green-50 p-3 rounded">
                            <strong class="text-green-800">ການລົງທະບຽນທັງໝົດ</strong><br>
                            <span class="text-gray-600">ຈຳນວນການລົງທະບຽນລວມ</span>
                        </div>
                        <div class="bg-yellow-50 p-3 rounded">
                            <strong class="text-yellow-800">ລໍຖ້າການອະນຸມັດ</strong><br>
                            <span class="text-gray-600">ຕ້ອງການການກວດສອບ</span>
                        </div>
                        <div class="bg-blue-50 p-3 rounded">
                            <strong class="text-blue-800">ອະນຸມັດແລ້ວ</strong><br>
                            <span class="text-gray-600">ຈຳນວນທີ່ອະນຸມັດແລ້ວ</span>
                        </div>
                    </div>
                </div>
                
                <div class="border-l-4 border-green-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">ເມນູການນຳທາງ</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <h4 class="font-medium text-gray-900">ຈັດການລົງທະບຽນ</h4>
                            <p class="text-gray-600">ເບິ່ງ, ອະນຸມັດ, ປະຕິເສດການລົງທະບຽນ</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">ຈັດການຜູ້ໃຊ້</h4>
                            <p class="text-gray-600">ເພີ່ມ, ແກ້ໄຂ, ລົບບັນຊີຜູ້ໃຊ້</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">ສົ່ງອອກຂໍ້ມູນ</h4>
                            <p class="text-gray-600">ດາວໂຫຼດລາຍງານເປັນ Excel</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">ປະຫວັດການໃຊ້ງານ</h4>
                            <p class="text-gray-600">ເບິ່ງບັນທຶກການເຄື່ອນໄຫວ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registration Management -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">📋 ການຈັດການລົງທະບຽນ</h2>
            
            <!-- Search and Filter -->
            <div class="mb-6 border-l-4 border-purple-500 pl-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">🔍 ການຄົ້ນຫາແລະກັ່ນຕອງ</h3>
                <div class="space-y-3">
                    <div>
                        <h4 class="font-medium text-gray-900">ການຄົ້ນຫາ:</h4>
                        <p class="text-gray-700 text-sm">ສາມາດຄົ້ນຫາໂດຍ: ຊື່, ລະຫັດນິສິດ, ອີເມວ, ສາຂາວິຊາ</p>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900">ກັ່ນຕອງຕາມສະຖານະ:</h4>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">⏳ ລໍຖ້າການອະນຸມັດ</span>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">✅ ອະນຸມັດແລ້ວ</span>
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">❌ ປະຕິເສດ</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="border-l-4 border-green-500 pl-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">⚡ ການດຳເນີນການ</h3>
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">👁️ ເບິ່ງລາຍລະອຽດ</h4>
                        <p class="text-gray-700 text-sm">ກົດທີ່ໄອຄອນຕາເພື່ອເບິ່ງຂໍ້ມູນຄົບຖ້ວນ, ຮູບຖ່າຍ, ແລະໃບຢັ້ງຢືນການຈ່າຍເງິນ</p>
                    </div>
                    
                    <div class="bg-green-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">✅ ອະນຸມັດ</h4>
                        <p class="text-gray-700 text-sm">ກົດປຸ່ມ ✅ ເພື່ອອະນຸມັດການລົງທະບຽນ (ສຳລັບ Staff ຂຶ້ນໄປ)</p>
                    </div>
                    
                    <div class="bg-red-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">❌ ປະຕິເສດ</h4>
                        <p class="text-gray-700 text-sm">ກົດປຸ່ມ ❌ ເພື່ອປະຕິເສດການລົງທະບຽນ (ສຳລັບ Staff ຂຶ້ນໄປ)</p>
                    </div>
                    
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">⏳ ປ່ຽນເປັນລໍຖ້າ</h4>
                        <p class="text-gray-700 text-sm">ປ່ຽນສະຖານະກັບມາເປັນລໍຖ້າການອະນຸມັດ</p>
                    </div>
                    
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h4 class="font-medium text-red-900 mb-2">🗑️ ລົບ (Admin ເທົ່ານັ້ນ)</h4>
                        <p class="text-red-700 text-sm">ລົບການລົງທະບຽນອອກຈາກລະບົບ (ບໍ່ສາມາດຍົກເລີກໄດ້)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Management -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">👥 ການຈັດການຜູ້ໃຊ້ (Admin ເທົ່ານັ້ນ)</h2>
            
            <div class="space-y-6">
                <div class="border-l-4 border-blue-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">➕ ເພີ່ມຜູ້ໃຊ້ໃໝ່</h3>
                    <p class="text-gray-700 mb-3">ຂັ້ນຕອນການເພີ່ມຜູ້ໃຊ້:</p>
                    <ol class="list-decimal list-inside space-y-1 text-sm text-gray-600">
                        <li>ກົດປຸ່ມ "ເພີ່ມຜູ້ໃຊ້ໃໝ່"</li>
                        <li>ເຕີມຂໍ້ມູນ: Username, Password, ສິດການໃຊ້ງານ</li>
                        <li>ເລືອກບົດບາດ: Admin, Staff, ຫຼື Viewer</li>
                        <li>ກົດ "ບັນທຶກ"</li>
                    </ol>
                </div>
                
                <div class="border-l-4 border-yellow-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">✏️ ແກ້ໄຂຜູ້ໃຊ້</h3>
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <p class="text-yellow-800 text-sm">ສາມາດແກ້ໄຂ: ຊື່ຜູ້ໃຊ້, ລະຫັດຜ່ານ, ສິດການໃຊ້ງານ</p>
                        <p class="text-yellow-700 text-xs mt-1">⚠️ ການປ່ຽນແປງຈະມີຜົນທັນທີ</p>
                    </div>
                </div>
                
                <div class="border-l-4 border-red-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">🗑️ ລົບຜູ້ໃຊ້</h3>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-red-800 text-sm font-medium">ຂໍ້ຄວນລະວັງ:</p>
                        <ul class="text-red-700 text-xs mt-1 space-y-1">
                            <li>• ການລົບບໍ່ສາມາດຍົກເລີກໄດ້</li>
                            <li>• ຜູ້ໃຊ້ທີ່ຖືກລົບຈະບໍ່ສາມາດເຂົ້າລະບົບໄດ້</li>
                            <li>• ຄວນປ່ຽນສິດການໃຊ້ງານແທນການລົບ</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Excel Export -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">📊 ການສົ່ງອອກຂໍ້ມູນ</h2>
            
            <div class="space-y-6">
                <div class="border-l-4 border-green-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">📥 ສົ່ງອອກເປັນ Excel</h3>
                    <div class="space-y-3">
                        <p class="text-gray-700">ກົດປຸ່ມ "📊 ສົ່ງອອກ Excel" ເພື່ອດາວໂຫຼດລາຍງານ</p>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="font-medium text-blue-900 mb-2">ຂໍ້ມູນທີ່ຈະຖືກສົ່ງອອກ:</h4>
                            <div class="grid grid-cols-2 gap-2 text-sm text-blue-800">
                                <div>• ລະຫັດນິສິດ</div>
                                <div>• ຊື່ - ນາມສະກຸນ</div>
                                <div>• ສາຂາວິຊາ</div>
                                <div>• ປີສຳເລັດການສຶກສາ</div>
                                <div>• ອີເມວ</div>
                                <div>• ເບີໂທ</div>
                                <div>• ສະຖານະ</div>
                                <div>• ວັນທີ່ລົງທະບຽນ</div>
                                <div>• ສະຖານະໄຟລ໌</div>
                            </div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <h4 class="font-medium text-green-900 mb-2">ຄຸນສົມບັດ:</h4>
                            <ul class="text-sm text-green-800 space-y-1">
                                <li>• ສົ່ງອອກຕາມການກັ່ນຕອງປັດຈຸບັນ</li>
                                <li>• ໄຟລ໌ມີການຈັດຮູບແບບທີ່ສວຍງາມ</li>
                                <li>• ສະໜັບສະໜູນພາສາລາວ</li>
                                <li>• ເປີດໄດ້ໃນ Excel ແລະ LibreOffice</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Logs -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">📜 ປະຫວັດການໃຊ້ງານ</h2>
            
            <div class="space-y-4">
                <p class="text-gray-700">ລະບົບຈະບັນທຶກກິດຈະກຳສຳຄັນທັງໝົດ:</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900 mb-2">ການເຂົ້າ-ອອກລະບົບ</h4>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• ເວລາເຂົ້າລະບົບ</li>
                            <li>• ເວລາອອກລະບົບ</li>
                            <li>• IP Address</li>
                        </ul>
                    </div>
                    
                    <div class="bg-green-50 rounded-lg p-4">
                        <h4 class="font-medium text-green-900 mb-2">ການດຳເນີນການ</h4>
                        <ul class="text-sm text-green-800 space-y-1">
                            <li>• ການອະນຸມັດ/ປະຕິເສດ</li>
                            <li>• ການສົ່ງອອກຂໍ້ມູນ</li>
                            <li>• ການຈັດການຜູ້ໃຊ້</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Best Practices -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">💡 ຄຳແນະນຳການໃຊ້ງານ</h2>
            
            <div class="space-y-6">
                <div class="border-l-4 border-green-500 pl-6">
                    <h3 class="text-lg font-semibold text-green-900 mb-3">✅ ການປະຕິບັດທີ່ດີ</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">•</span>
                            <span>ກວດສອບຂໍ້ມູນແລະໄຟລ໌ຢ່າງລະມັດລະວັງກ່ອນອະນຸມັດ</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">•</span>
                            <span>ສົ່ງອອກລາຍງານເປັນປະຈຳເພື່ອການສຳຮອງຂໍ້ມູນ</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">•</span>
                            <span>ໃຊ້ການກັ່ນຕອງເພື່ອຈັດລຳດັບຄວາມສຳຄັນ</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">•</span>
                            <span>ອອກຈາກລະບົບເມື່ອໃຊ້ງານສຳເລັດ</span>
                        </li>
                    </ul>
                </div>
                
                <div class="border-l-4 border-red-500 pl-6">
                    <h3 class="text-lg font-semibold text-red-900 mb-3">⚠️ ຂໍ້ຄວນຫຼີກລ່ຽງ</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2 mt-1">•</span>
                            <span>ບໍ່ແບ່ງປັນຂໍ້ມູນເຂົ້າລະບົບໃຫ້ຄົນອື່ນ</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2 mt-1">•</span>
                            <span>ບໍ່ລົບຂໍ້ມູນໂດຍບໍ່ມີການຢືນຢັນ</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2 mt-1">•</span>
                            <span>ບໍ່ອະນຸມັດການລົງທະບຽນໂດຍບໍ່ກວດສອບ</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2 mt-1">•</span>
                            <span>ບໍ່ປະໄວ້ໜ້າລະບົບເປີດຢູ່ໂດຍບໍ່ມີການເຝົ້າລະວັງ</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between items-center">
            <a href="../guide.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                ← ກັບໄປຄູ່ມືຫຼັກ
            </a>
            
            <a href="student.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                ເບິ່ງຄູ່ມືນິສິດ →
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>