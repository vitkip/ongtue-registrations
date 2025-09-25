    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-auto">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">ກ່ຽວກັບລະບົບ</h3>
                    <p class="text-gray-300 text-sm">
                        ລົງທະບຽນເຂົ້າຮັບໃບປະກາສະນີຍະບັດ ວິທະຍາໄລຄະສົງ ອົງຕື້ ພັດທະນາໂດຍ: ພະແນກວິຊາການ
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">ຕິດຕໍ່ພວກເຮົາ</h3>
                    <div class="text-gray-300 text-sm space-y-2">
                        <p>📧 info@ongtue-ttc.edu.la</p>
                        <p>📞 020 77772338</p>
                        <p>📍 ວັດອົງຕື້ວໍຣະມະຫາວິຫານ ຈັນທະບູລີ ນະຄອນຫຼວງວຽງຈັນ</p>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">ລິ້ງທີ່ເປັນປະໂຫຍດ</h3>
                    <div class="text-gray-300 text-sm space-y-2">
                        <a href="#" class="block hover:text-white transition-colors">📋 ຄູ່ມືການໃຊ້ງານ</a>
                        <a href="#" class="block hover:text-white transition-colors">❓ ຄຳຖາມທີ່ພົບເລື້ອຍ</a>
                        <a href="#" class="block hover:text-white transition-colors">📞 ຊ່ວຍເຫຼືອ</a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-400 text-sm">
                    © <?php echo date('Y'); ?> ລົງທະບຽນເຂົ້າຮັບໃບປະກາສະນີຍະບັດ ວິທະຍາໄລຄະສົງ ອົງຕື້. ສະຫງວນລິຂະສິດທັງໝົດ.
                </p>
                <p class="text-gray-500 text-xs mt-2">
                    Version <?php echo APP_VERSION; ?> | ພັດທະນາໂດຍ: ປອ.ອານັນທະສັກ ພັດທະສີລາ
                </p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Form validation
        function validateForm(formId) {
            const form = document.getElementById(formId);
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            return isValid;
        }

        // File upload preview
        function previewImage(input, previewId) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).src = e.target.result;
                    document.getElementById(previewId).classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        // Confirm delete
        function confirmDelete(message = 'ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລົບ?') {
            return confirm(message);
        }

        // Loading spinner
        function showLoading(buttonId) {
            const button = document.getElementById(buttonId);
            if (button) {
                button.disabled = true;
                button.innerHTML = '<span class="animate-spin">⏳</span> ກຳລັງປະມວນຜົນ...';
            }
        }

        // Search functionality
        function searchTable(inputId, tableId) {
            const input = document.getElementById(inputId);
            const table = document.getElementById(tableId);
            const rows = table.querySelectorAll('tbody tr');

            input.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>