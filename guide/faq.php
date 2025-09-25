<?php
session_start();
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$pageTitle = 'ຄຳຖາມທີ່ພົບເລື່ອຍ - ລະບົບລົງທະບຽນຮັບໃບຢັ້ງຢືນ';
include '../includes/header.php';
?>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <span class="text-6xl mb-4 block">❓</span>
                <h1 class="text-3xl font-bold mb-2">ຄຳຖາມທີ່ພົບເລື່ອຍ</h1>
                <p class="text-xl opacity-90">ຄຳຖາມແລະຄຳຕອບທີ່ພົບເລື່ອຍໃນການໃຊ້ງານລະບົບ</p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Quick Navigation -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">🚀 ຫວບໜາງໄວ</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="#student-faq" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <span class="text-2xl mr-3">👨‍🎓</span>
                    <span class="font-medium text-green-800">ຄຳຖາມນິສິດ</span>
                </a>
                <a href="#admin-faq" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <span class="text-2xl mr-3">👨‍💼</span>
                    <span class="font-medium text-blue-800">ຄຳຖາມຜູ້ບໍລິຫານ</span>
                </a>
                <a href="#technical-faq" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <span class="text-2xl mr-3">🔧</span>
                    <span class="font-medium text-purple-800">ບັນຫາເຕັກນິດ</span>
                </a>
            </div>
        </div>

        <!-- Student FAQ -->
        <div id="student-faq" class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <div class="flex items-center mb-6">
                <span class="text-3xl mr-3">👨‍🎓</span>
                <h2 class="text-2xl font-bold text-gray-900">ຄຳຖາມສຳລັບນິສິດ</h2>
            </div>

            <div class="space-y-6">
                <!-- FAQ Item 1 -->
                <div class="border-l-4 border-green-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">❓ ຂ້ອຍຕ້ອງເຮັດແນວໃດເພື່ອລົງທະບຽນຮັບໃບຢັ້ງຢືນ?</h3>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-green-800 mb-2"><strong>ຄຳຕອບ:</strong></p>
                        <ol class="list-decimal list-inside space-y-1 text-green-700 text-sm">
                            <li>ໄປທີ່ໜ້າລົງທະບຽນ (register.php)</li>
                            <li>ເຕີມຂໍ້ມູນສ່ວນໂຕໃຫ້ຄົບຖ້ວນ</li>
                            <li>ອັບໂຫຼດຮູບຖ່າຍແລະໃບຢັ້ງຢືນການຈ່າຍເງິນ</li>
                            <li>ກົດປຸ່ມ "ລົງທະບຽນ"</li>
                            <li>ລໍຖ້າການອະນຸມັດຈາກເຈົ້າໜ້າທີ່</li>
                        </ol>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="border-l-4 border-green-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">❓ ຄ່າທຳນຽມໃນການຮັບໃບຢັ້ງຢືນເທົ່າໃດ?</h3>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-green-800 mb-2"><strong>ຄຳຕອບ:</strong></p>
                        <div class="text-green-700 text-sm">
                            <p>• <strong>ໃບຢັ້ງຢືນຕົ້ນສະບັບ:</strong> 50,000 ກີບ</p>
                            <p>• <strong>ໃບສຳເນົາ:</strong> 10,000 ກີບ</p>
                            <p class="mt-2 text-green-600">💡 ໂອນເງິນເຂົ້າບັນຊີ BCEL: 0001-23-456789-0</p>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="border-l-4 border-green-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">❓ ຂ້ອຍຈະຮູ້ໄດ້ແນວໃດວ່າການລົງທະບຽນຂອງຂ້ອຍຖືກອະນຸມັດແລ້ວ?</h3>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-green-800 mb-2"><strong>ຄຳຕອບ:</strong></p>
                        <p class="text-green-700 text-sm">ຫຼັງຈາກລົງທະບຽນແລ້ວ ທ່ານຈະເຫັນສະຖານະໃນໜ້າສະແດງຜົນ:</p>
                        <div class="mt-2 space-y-1">
                            <div class="flex items-center">
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs mr-2">⏳</span>
                                <span class="text-sm">ລໍຖ້າການອະນຸມັດ</span>
                            </div>
                            <div class="flex items-center">
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs mr-2">✅</span>
                                <span class="text-sm">ອະນຸມັດແລ້ວ - ສາມາດມາຮັບໄດ້</span>
                            </div>
                            <div class="flex items-center">
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs mr-2">❌</span>
                                <span class="text-sm">ປະຕິເສດ - ຕິດຕໍ່ເຈົ້າໜ້າທີ່</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="border-l-4 border-green-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">❓ ຫາກຂ້ອຍລືມລະຫັດນິສິດ ຈະເຮັດແນວໃດ?</h3>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-green-800 mb-2"><strong>ຄຳຕອບ:</strong></p>
                        <p class="text-green-700 text-sm">ທ່ານສາມາດຊອກຫາລະຫັດນິສິດໄດ້ຈາກ:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1 text-green-700 text-sm">
                            <li>ໃບລົງທະບຽນເກົ່າ</li>
                            <li>ໃບລາຍງານຄະແນນ</li>
                            <li>ບັດນິສິດ</li>
                            <li>ຕິດຕໍ່ຫ້ອງການບໍລິຫານ: 021-123456</li>
                        </ul>
                    </div>
                </div>

                <!-- FAQ Item 5 -->
                <div class="border-l-4 border-green-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">❓ ຂ້ອຍອັບໂຫຼດໄຟລ໌ບໍ່ໄດ້ ເປັນຫຍັງ?</h3>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-green-800 mb-2"><strong>ຄຳຕອບ:</strong></p>
                        <p class="text-green-700 text-sm">ກວດສອບສິ່ງເຫຼົ່ານີ້:</p>
                        <ul class="list-disc list-inside mt-2 space-y-1 text-green-700 text-sm">
                            <li>ໄຟລ໌ຕ້ອງເປັນ JPG, JPEG ຫຼື PNG</li>
                            <li>ຂະໜາດບໍ່ເກີນ 5MB</li>
                            <li>ເຊື່ອມຕໍ່ອິນເຕີເນັດດີ</li>
                            <li>ລອງຮີເຟຼດໜ້າເວັບແລ້ວລອງໃໝ່</li>
                        </ul>
                    </div>
                </div>

                <!-- FAQ Item 6 -->
                <div class="border-l-4 border-green-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">❓ ເມື່ອໃດຂ້ອຍຈະສາມາດມາຮັບໃບຢັ້ງຢືນໄດ້?</h3>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-green-800 mb-2"><strong>ຄຳຕອບ:</strong></p>
                        <div class="text-green-700 text-sm">
                            <p>ຫຼັງຈາກການລົງທະບຽນຖືກອະນຸມັດແລ້ວ:</p>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li><strong>ເວລາຮັບ:</strong> ວັນຈັນ - ວັນສຸກ, 8:00-16:00</li>
                                <li><strong>ສະຖານທີ່:</strong> ຫ້ອງການບໍລິຫານ, ຊັ້ນ 2</li>
                                <li><strong>ເອກະສານທີ່ຕ້ອງນຳມາ:</strong> ບັດປະຈຳຕົວ ແລະ ໃບຢັ້ງຢືນການຈ່າຍເງິນຕົ້ນສະບັບ</li>
                                <li><strong>ໄລຍະເວລາ:</strong> ປົກກະຕິ 3-5 ວັນເຮັດການ</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin FAQ -->
        <div id="admin-faq" class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <div class="flex items-center mb-6">
                <span class="text-3xl mr-3">👨‍💼</span>
                <h2 class="text-2xl font-bold text-gray-900">ຄຳຖາມສຳລັບຜູ້ບໍລິຫານ</h2>
            </div>

            <div class="space-y-6">
                <!-- Admin FAQ Item 1 -->
                <div class="border-l-4 border-blue-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">❓ ແນວໃດຂ້ອຍຈຶ່ງສາມາດເຂົ້າລະບົບ Admin ໄດ້?</h3>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-blue-800 mb-2"><strong>ຄຳຕອບ:</strong></p>
                        <div class="text-blue-700 text-sm">
                            <p>ເຂົ້າໄປທີ່: <code class="bg-blue-100 px-2 py-1 rounded">admin/login.php</code></p>
                            <p class="mt-2">ໃຊ້ຂໍ້ມູນເຂົ້າລະບົບທີ່ໄດ້ຮັບຈາກ Admin ຫຼັກ:</p>
                            <ul class="list-disc list-inside mt-1 space-y-1">
                                <li>Username</li>
                                <li>Password</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Admin FAQ Item 2 -->
                <div class="border-l-4 border-blue-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">❓ ຂ້ອຍຄວນກວດສອບສິ່ງໃດກ່ອນອະນຸມັດການລົງທະບຽນ?</h3>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-blue-800 mb-2"><strong>ຄຳຕອບ:</strong></p>
                        <div class="text-blue-700 text-sm">
                            <p>ກວດສອບສິ່ງເຫຼົ່ານີ້:</p>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li>ຂໍ້ມູນສ່ວນໂຕຄົບຖ້ວນແລະຖືກຕ້ອງ</li>
                                <li>ຮູບຖ່າຍຊັດເຈນແລະເປັນມື້ປັດຈຸບັນ</li>
                                <li>ໃບຢັ້ງຢືນການຈ່າຍເງິນຖືກຕ້ອງ</li>
                                <li>ລະຫັດນິສິດຖືກຕ້ອງ</li>
                                <li>ສາຂາວິຊາແລະປີສຳເລັດການສຶກສາຖືກຕ້ອງ</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Admin FAQ Item 3 -->
                <div class="border-l-4 border-blue-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">❓ ແນວໃດສົ່ງອອກລາຍງານເປັນ Excel?</h3>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-blue-800 mb-2"><strong>ຄຳຕອບ:</strong></p>
                        <div class="text-blue-700 text-sm">
                            <ol class="list-decimal list-inside space-y-1">
                                <li>ໄປທີ່ໜ້າ "ຈັດການລົງທະບຽນ"</li>
                                <li>ຕັ້ງການກັ່ນຕອງຕາມຕ້ອງການ (ຖ້າມີ)</li>
                                <li>ກົດປຸ່ມ "📊 ສົ່ງອອກ Excel"</li>
                                <li>ໄຟລ໌ຈະຖືກດາວໂຫຼດອັດຕະໂນມັດ</li>
                            </ol>
                            <p class="mt-2 text-blue-600">💡 ການສົ່ງອອກຈະລວມເຉພາະຂໍ້ມູນທີ່ຖືກກັ່ນຕອງ</p>
                        </div>
                    </div>
                </div>

                <!-- Admin FAQ Item 4 -->
                <div class="border-l-4 border-blue-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">❓ ຂ້ອຍສາມາດເພີ່ມຜູ້ໃຊ້ໃໝ່ໄດ້ບໍ?</h3>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-blue-800 mb-2"><strong>ຄຳຕອບ:</strong></p>
                        <div class="text-blue-700 text-sm">
                            <p>ສາມາດໄດ້ຖ້າທ່ານມີສິດ Admin:</p>
                            <ol class="list-decimal list-inside mt-2 space-y-1">
                                <li>ໄປທີ່ "ຈັດການຜູ້ໃຊ້"</li>
                                <li>ກົດ "ເພີ່ມຜູ້ໃຊ້ໃໝ່"</li>
                                <li>ເຕີມຂໍ້ມູນແລະເລືອກສິດການໃຊ້ງານ</li>
                                <li>ກົດ "ບັນທຶກ"</li>
                            </ol>
                            <p class="mt-2 text-blue-600">⚠️ ມີແຕ່ Admin ເທົ່ານັ້ນຈຶ່ງສາມາດຈັດການຜູ້ໃຊ້ໄດ້</p>
                        </div>
                    </div>
                </div>

                <!-- Admin FAQ Item 5 -->
                <div class="border-l-4 border-blue-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">❓ ຄວາມແຕກຕ່າງລະຫວ່າງ Admin, Staff, ແລະ Viewer ແມ່ນຫຍັງ?</h3>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-blue-800 mb-2"><strong>ຄຳຕອບ:</strong></p>
                        <div class="text-blue-700 text-sm space-y-2">
                            <div>
                                <strong>Admin:</strong> ສິດເຂົ້າເຖິງທັງໝົດ - ຈັດການຜູ້ໃຊ້, ລົບຂໍ້ມູນ, ອະນຸມັດ/ປະຕິເສດ
                            </div>
                            <div>
                                <strong>Staff:</strong> ອະນຸມັດ/ປະຕິເສດການລົງທະບຽນ, ເບິ່ງລາຍງານ
                            </div>
                            <div>
                                <strong>Viewer:</strong> ເບິ່ງຂໍ້ມູນແລະສົ່ງອອກລາຍງານເທົ່ານັ້ນ
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical FAQ -->
        <div id="technical-faq" class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <div class="flex items-center mb-6">
                <span class="text-3xl mr-3">🔧</span>
                <h2 class="text-2xl font-bold text-gray-900">ບັນຫາເຕັກນິດ</h2>
            </div>

            <div class="space-y-6">
                <!-- Technical FAQ Item 1 -->
                <div class="border-l-4 border-purple-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">❓ ໜ້າເວັບໂຫຼດຊ້າ ມີວິທີແກ້ໄຂບໍ?</h3>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-purple-800 mb-2"><strong>ວິທີແກ້ໄຂ:</strong></p>
                        <ul class="list-disc list-inside space-y-1 text-purple-700 text-sm">
                            <li>ກວດສອບການເຊື່ອມຕໍ່ອິນເຕີເນັດ</li>
                            <li>ລົບ Cache ແລະ Cookies ຂອງບຣາວເຊີ</li>
                            <li>ປິດໂປຣແກຣມອື່ນທີ່ໃຊ້ອິນເຕີເນັດ</li>
                            <li>ລອງໃຊ້ບຣາວເຊີອື່ນ</li>
                            <li>ຮີສະຕາດ Modem/Router</li>
                        </ul>
                    </div>
                </div>

                <!-- Technical FAQ Item 2 -->
                <div class="border-l-4 border-purple-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">❓ ຂ້ອຍໄດ້ຮັບ Error 500 ແມ່ນຫຍັງ?</h3>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-purple-800 mb-2"><strong>ສາເຫດແລະວິທີແກ້ໄຂ:</strong></p>
                        <div class="text-purple-700 text-sm">
                            <p class="mb-2">Error 500 ໝາຍເຖິງບັນຫາໃນເຊີເວີ:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>ລອງຮີເຟຼດໜ້າເວັບ</li>
                                <li>ລໍຖ້າ 5-10 ນາທີແລ້ວລອງໃໝ່</li>
                                <li>ຕິດຕໍ່ຜູ້ບໍລິຫານລະບົບ</li>
                                <li>ລາຍງານບັນຫາໃຫ້ IT Support</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Technical FAQ Item 3 -->
                <div class="border-l-4 border-purple-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">❓ ພອນ iPhone/iPad ສາມາດໃຊ້ລະບົບໄດ້ບໍ?</h3>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-purple-800 mb-2"><strong>ຄຳຕອບ:</strong></p>
                        <div class="text-purple-700 text-sm">
                            <p>ໄດ້! ລະບົບເຮັດງານໄດ້ກັບທຸກອຸປະກອນ:</p>
                            <ul class="list-disc list-inside mt-2 space-y-1">
                                <li>iPhone/iPad (Safari ຫຼື Chrome)</li>
                                <li>Android (Chrome ຫຼື Firefox)</li>
                                <li>Computer (Chrome, Firefox, Safari, Edge)</li>
                                <li>Tablet ທຸກຍີ່ຫໍ້</li>
                            </ul>
                            <p class="mt-2 text-purple-600">💡 ແນະນຳໃຫ້ໃຊ້ບຣາວເຊີລຸ້ນໃໝ່ສຸດ</p>
                        </div>
                    </div>
                </div>

                <!-- Technical FAQ Item 4 -->
                <div class="border-l-4 border-purple-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">❓ ຂໍ້ມູນຂອງຂ້ອຍປອດໄພບໍ?</h3>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-purple-800 mb-2"><strong>ການຮັບປະກັນຄວາມປອດໄພ:</strong></p>
                        <div class="text-purple-700 text-sm">
                            <ul class="list-disc list-inside space-y-1">
                                <li>ຂໍ້ມູນຖືກເຂົ້າລະຫັດ (Encryption)</li>
                                <li>ມີລະບົບສຳຮອງຂໍ້ມູນ</li>
                                <li>ການເຂົ້າເຖິງມີການຄວບຄຸມ</li>
                                <li>ບັນທຶກການໃຊ້ງານທັງໝົດ</li>
                                <li>ປະຕິບັດຕາມມາດຕະຖານຄວາມປອດໄພ</li>
                            </ul>
                            <p class="mt-2 text-purple-600">🔒 ຂໍ້ມູນສ່ວນບຸກຄົນຈະບໍ່ຖືກເປີດເຜີຍໃຫ້ບຸກຄົນທີ່ສາມ</p>
                        </div>
                    </div>
                </div>

                <!-- Technical FAQ Item 5 -->
                <div class="border-l-4 border-purple-500 pl-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">❓ ຂ້ອຍສາມາດໃຊ້ງານລະບົບເວລາໃດແດ່?</h3>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-purple-800 mb-2"><strong>ເວລາໃຫ້ບໍລິການ:</strong></p>
                        <div class="text-purple-700 text-sm">
                            <p class="mb-2"><strong>ລະບົບອອນລາຍ:</strong> 24/7 (ຕະຫຼອດເວລາ)</p>
                            <p class="mb-2"><strong>ສະໜັບສະໜູນ:</strong></p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>ວັນຈັນ - ວັນສຸກ: 8:00 - 17:00</li>
                                <li>ວັນເສົາ: 8:00 - 12:00</li>
                                <li>ວັນອາທິດແລະວັນພັກ: ປິດ</li>
                            </ul>
                            <p class="mt-2 text-purple-600">📞 ໂທສຸກເສີນ: 021-123456 (ນອກເວລາ)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">📞 ຍັງມີຄຳຖາມອື່ນບໍ?</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-3">ຕິດຕໍ່ເຈົ້າໜ້າທີ່</h3>
                    <div class="space-y-2 text-blue-800 text-sm">
                        <div class="flex items-center">
                            <span class="mr-2">📧</span>
                            <span>admin@university.la</span>
                        </div>
                        <div class="flex items-center">
                            <span class="mr-2">📱</span>
                            <span>+856 21 123 456</span>
                        </div>
                        <div class="flex items-center">
                            <span class="mr-2">📍</span>
                            <span>ຫ້ອງການບໍລິຫານ, ຊັ້ນ 2</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-green-900 mb-3">ເວລາໃຫ້ບໍລິການ</h3>
                    <div class="space-y-2 text-green-800 text-sm">
                        <div>ວັນຈັນ - ວັນສຸກ: 8:00 - 17:00</div>
                        <div>ວັນເສົາ: 8:00 - 12:00</div>
                        <div>ພັກກາງວັນ: 12:00 - 13:00</div>
                        <div class="text-green-600">💬 ສາມາດສົ່ງອີເມວໄດ້ຕະຫຼອດເວລາ</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between items-center">
            <a href="../guide.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                ← ກັບໄປຄູ່ມືຫຼັກ
            </a>
            
            <a href="admin.php" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700">
                ເບິ່ງຄູ່ມືຜູ້ບໍລິຫານ →
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>