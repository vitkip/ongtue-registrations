# ລະບົບລົງທະບຽນຮັບໃບປະກາດນິຍະບັດ
## Certificate Registration System

ລະບົບ Web Application ສຳລັບລົງທະບຽນຮັບໃບປະກາດນິຍະບັດ ທີ່ພັດທະນາດ້ວຍ PHP (PDO), MySQL, Tailwind CSS ແລະ Google Fonts Phetsarath

---

## 🚀 ຄຸນສົມບັດຫຼັກ

### Frontend (ສຳລັບນິສິດ)
- 🏠 **ໜ້າຫຼັກ**: ຂໍ້ມູນລະບົບ, ສະຖິຕິ, ຄຳແນະນຳ
- 📝 **ລົງທະບຽນ**: ຟອມລົງທະບຽນທີ່ຄົບຖ້ວນ
- 📊 **ສະຖິຕິ**: ສະແດງຂໍ້ມູນສະຖິຕິແບບ Real-time
- 📱 **Responsive Design**: ຮອງຮັບທຸກອຸປະກອນ

### Backend (ສຳລັບເຈົ້າໜ້າທີ່)
- 🔐 **ລະບົບເຂົ້າສູ່ລະບົບ**: Authentication & Authorization
- 📊 **Dashboard**: ໜ້າຫຼັກພ້ອມສະຖິຕິ Real-time
- 📋 **ຈັດການລົງທະບຽນ CRUD**: ເບິ່ງ, ແກ້ໄຂ, ອະນຸມັດ, ປະຕິເສດ, ລົບ
- ☑️ **Bulk Operations**: ດຳເນິນການກັບຫຼາຍລາຍການພ້ອມກັນ
- 🔍 **ຄົ້ນຫາ & ກັ່ນຕອງ**: ຄົ້ນຫາແລະກັ່ນຕອງຂໍ້ມູນ
- 👁️ **ເບິ່ງລາຍລະອຽດ**: Modal ສະແດງຂໍ້ມູນຄົບຖ້ວນ
- ✏️ **ແກ້ໄຂຂໍ້ມູນ**: ແກ້ໄຂການລົງທະບຽນແບບສົມບູນ
- 📄 **ການຈັດການໄຟລ໌**: ອັບໂຫຼດແລະຈັດການໄຟລ໌

---

## 🛠️ ເທັກໂນໂລຊີທີ່ໃຊ້

- **Backend**: PHP 7.4+ (PDO)
- **Database**: MySQL 5.7+ (utf8mb4_general_ci)
- **Frontend**: HTML5, CSS3, JavaScript
- **CSS Framework**: Tailwind CSS (CDN)
- **Font**: Google Fonts Phetsarath
- **Server**: Apache (XAMPP)

---

## 📋 ຄວາມຕ້ອງການລະບົບ

- XAMPP (Apache + MySQL + PHP 7.4+)
- Web Browser (Chrome, Firefox, Safari, Edge)
- 50MB ພື້ນທີ່ຈັດເກັບຂໍ້ມູນ

---

## 🔧 ການຕິດຕັ້ງ

### 1. ດາວໂຫຼດແລະຕິດຕັ້ງ XAMPP
```bash
# ດາວໂຫຼດ XAMPP ຈາກ: https://www.apachefriends.org/
# ຕິດຕັ້ງແລະເປີດ Apache + MySQL
```

### 2. Copy ໂປຣເຈັກໄປໃສ່ htdocs
```bash
# Copy ໂຟນເດີ registrations ໄປໃສ່:
# Windows: C:\xampp\htdocs\registrations
# macOS: /Applications/XAMPP/xamppfiles/htdocs/registrations
# Linux: /opt/lampp/htdocs/registrations
```

### 3. ສ້າງຖານຂໍ້ມູນ
```sql
-- ເປີດ phpMyAdmin: http://localhost/phpmyadmin
-- ຫຼື ໃຊ້ຄຳສັ່ງ MySQL:

-- ສ້າງຖານຂໍ້ມູນ
CREATE DATABASE cert_system
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

-- Import ໄຟລ໌ SQL
-- ໃຊ້ໄຟລ໌: config/database.sql
```

### 4. ກຳນົດຄ່າ Configuration
```php
// ແກ້ໄຂໄຟລ໌: config/database.php
// ປ່ຽນການຕັ້ງຄ່າຖ້າຈຳເປັນ:

private $host = 'localhost';
private $db_name = 'cert_system';
private $username = 'root';
private $password = '';
```

### 5. ສ້າງໂຟນເດີອັບໂຫຼດ
```bash
# ໂຟນເດີຈະຖືກສ້າງອັດຕະໂນມັດ ຫຼື ສ້າງເອງ:
mkdir uploads
mkdir uploads/profiles
mkdir uploads/payments
chmod 755 uploads -R
```

### 6. ເຂົ້າໃຊ້ລະບົບ
```
Frontend: http://localhost/registrations/
Admin: http://localhost/registrations/admin/

ບັນຊີ Admin ເລີ່ມຕົ້ນ:
Username: admin
Password: admin123
```

---

## 📁 ໂຄງສ້າງໄຟລ໌

```
registrations/
├── config/
│   ├── database.sql          # SQL Script
│   ├── database.php          # ການເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
│   └── config.php            # ການຕັ້ງຄ່າລະບົບ
├── includes/
│   ├── functions.php         # ຟັງຊັນທົວໄປ
│   ├── header.php           # Header Template
│   └── footer.php           # Footer Template
├── admin/
│   ├── login.php            # ເຂົ້າສູ່ລະບົບ
│   ├── logout.php           # ອອກຈາກລະບົບ
│   ├── dashboard.php        # ໜ້າຫຼັກ Admin
│   ├── registrations.php    # ຈັດການລົງທະບຽນ (CRUD + Bulk Operations)
│   ├── view_registration.php # ເບິ່ງລາຍລະອຽດ
│   └── edit_registration.php # ແກ້ໄຂການລົງທະບຽນ
├── uploads/
│   ├── profiles/            # ຮູບໂປຣໄຟລ໌
│   └── payments/            # ໃບຢັ້ງຢືນການຈ່າຍເງິນ
├── logs/                    # Log ການໃຊ້ງານ
├── index.php               # ໜ້າຫຼັກ
├── register.php            # ລົງທະບຽນ
└── README.md               # ເອກະສານນີ້
```

---

## 📋 ໂຄງສ້າງຖານຂໍ້ມູນ

### ຕາຕະລາງ `registrations`
```sql
CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_code VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    major VARCHAR(100) NOT NULL,
    graduation_year YEAR NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    profile_image VARCHAR(255),
    payment_proof VARCHAR(255),
    status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### ຕາຕະລາງ `users`
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','staff') NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🎯 ການໃຊ້ງານ

### ສຳລັບນິສິດ:
1. ເຂົ້າເວັບໄຊທ໌: `http://localhost/registrations/`
2. ກົດ "ລົງທະບຽນດຽວນີ້"
3. ປ້ອນຂໍ້ມູນໃຫ້ຄົບຖ້ວນ
4. ອັບໂຫຼດຮູບໂປຣໄຟລ໌ແລະໃບຢັ້ງຢືນການຈ່າຍເງິນ
5. ກົດ "ສົ່ງການລົງທະບຽນ"
6. ລໍຖ້າການອະນຸມັດຈາກເຈົ້າໜ້າທີ່

### ສຳລັບເຈົ້າໜ້າທີ່:
1. ເຂົ້າສູ່ລະບົບ: `http://localhost/registrations/admin/`
2. ໃຊ້ບັນຊີ: admin / admin123
3. ເບິ່ງ Dashboard ແລະສະຖິຕິ Real-time
4. ໄປ "ຈັດການລົງທະບຽນ"
5. **ການດຳເນີນງານ CRUD**:
   - 👁️ ເບິ່ງລາຍລະອຽດ (View)
   - ✏️ ແກ້ໄຂຂໍ້ມູນ (Edit)
   - ✅ ອະນຸມັດ (Approve)
   - ❌ ປະຕິເສດ (Reject)
   - 🗑️ ລົບ (Delete)
6. **Bulk Operations**: ເລືອກຫຼາຍລາຍການແລະດຳເນີນການພ້ອມກັນ
7. **ຄົ້ນຫາ & ກັ່ນຕອງ**: ຄົ້ນຫາຕາມຊື່, ສະຖານະ, ສາຂາວິຊາ

---

## � ຄຸນສົມບັດພິເສດ

### 📊 **Real-time Statistics**
- ຈຳນວນຜູ້ສະມັກທັງໝົດ
- ຈຳນວນລໍຖ້າອະນຸມັດ  
- ຈຳນວນອະນຸມັດແລ້ວ
- ຈຳນວນປະຕິເສດ

### ☑️ **Bulk Operations** 
- ເລືອກຫຼາຍລາຍການພ້ອມກັນ
- ອະນຸມັດຫຼາຍລາຍການພ້ອມກັນ
- ປະຕິເສດຫຼາຍລາຍການພ້ອມກັນ
- ລົບຫຼາຍລາຍການພ້ອມກັນ

### 🔍 **ລະບົບຄົ້ນຫາ**
- ຄົ້ນຫາຕາມຊື່-ນາມສະກຸນ
- ກັ່ນຕອງຕາມສະຖານະ
- ກັ່ນຕອງຕາມສາຂາວິຊາ
- ຈັດຮຽງຂໍ້ມູນ

### 📱 **Responsive Design**
- ຮອງຮັບມືຖື (Mobile-first)
- ຮອງຮັບແທັບເລັດ
- ຮອງຮັບ Desktop
- Navigation ແບບ Hamburger Menu

---

## �🔒 ຄຸນສົມບັດຄວາມປອດໄພ

- **CSRF Protection**: ປ້ອງກັນການໂຈມຕີ CSRF
- **SQL Injection Protection**: ໃຊ້ PDO Prepared Statements
- **File Upload Validation**: ກວດສອບປະເພດແລະຂະໜາດໄຟລ໌
- **Input Sanitization**: ກວດສອບແລະທຳຄວາມສະອາດຂໍ້ມູນ
- **Session Management**: ຈັດການ Session ທີ່ປອດໄພ
- **Role-based Access**: ຄວບຄຸມການເຂົ້າເຖິງຕາມບົດບາດ
- **Duplicate Prevention**: ປ້ອງກັນຂໍ້ມູນຊ້ຳ (ອີເມວ, ລະຫັດນິສິດ)

---

## 🎨 ການປັບແຕ່ງ

### ປ່ຽນສີລະບົບ:
```css
/* ໃນໄຟລ໌ includes/header.php */
.bg-gradient-lao {
    background: linear-gradient(135deg, #dc2626 0%, #f59e0b 50%, #059669 100%);
}
```

### ເພີ່ມສາຂາວິຊາ:
```php
// ໃນໄຟລ໌ register.php ຫາ <select name="major">
<option value="ວິສະວະກຳຄອມພິວເຕີ">ວິສະວະກຳຄອມພິວເຕີ</option>
<option value="ເສດຖະກິດ">ເສດຖະກິດ</option>
<option value="ກົດໝາຍ">ກົດໝາຍ</option>
<option value="ສາຂາໃໝ່">ສາຂາໃໝ່</option> // ເພີ່ມແຖວນີ້
```

### ປ່ຽນການຕັ້ງຄ່າໄຟລ໌:
```php
// ໃນໄຟລ໌ config/config.php
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('RECORDS_PER_PAGE', 20); // 20 ລາຍການຕໍ່ໜ້າ
```

---

## 🐛 ການແກ້ໄຂບັນຫາ

### ບັນຫາທົວໄປ:

**1. ບໍ່ສາມາດເຊື່ອມຕໍ່ຖານຂໍ້ມູນ:**
```
- ກວດສອບວ່າ MySQL ເປີດແລ້ວ
- ກວດສອບການຕັ້ງຄ່າໃນ config/database.php
- ກວດສອບວ່າສ້າງຖານຂໍ້ມູນແລ້ວ
```

**2. ອັບໂຫຼດໄຟລ໌ບໍ່ໄດ້:**
```
- ກວດສອບວ່າໂຟນເດີ uploads/ ມີແລະ chmod 755
- ກວດສອບ php.ini: upload_max_filesize ແລະ post_max_size
- ກວດສອບວ່າໄຟລ໌ບໍ່ເກີນຂະໜາດທີ່ກຳນົດ
```

**3. ຟອນ Phetsarath ບໍ່ສະແດງ:**
```
- ກວດສອບການເຊື່ອມຕໍ່ອິນເຕີເນັດ
- ລອງ refresh ໜ້າເວັບ
- ກວດສອບ Browser Support
```

**4. ຂໍ້ຜິດພາດ Logo ບໍ່ສະແດງ:**
```
- ກວດສອບວ່າມີໄຟລ໌ college-logo.png ໃນໂຟນເດີ assets/images/
- ກວດສອບ path ໃນໄຟລ໌ header.php, index.php
- ຂະໜາດຮູບບໍ່ຄວນເກີນ 2MB
```

**5. ການລົງທະບຽນບໍ່ສຳເລັດ:**
```
- ກວດສອບການເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
- ກວດສອບວ່າໄຟລ໌ທີ່ອັບໂຫຼດຕອງກັບຂໍ້ກຳນົດ
- ກວດສອບ PHP error log ໃນ logs/
- ກວດສອບວ່າອີເມວຫຼືລະຫັດນິສິດບໍ່ຊ້ຳ
```

---

## 🎯 Features Overview

| ຄຸນສົມບັດ | Frontend | Backend | ສະຖານະ |
|---------|----------|---------|--------|
| 📝 ລົງທະບຽນ | ✅ | ✅ | ສຳເລັດ |
| 👁️ ເບິ່ງລາຍລະອຽດ | - | ✅ | ສຳເລັດ |
| ✏️ ແກ້ໄຂຂໍ້ມູນ | - | ✅ | ສຳເລັດ |
| ✅ ອະນຸມັດ/ປະຕິເສດ | - | ✅ | ສຳເລັດ |
| 🗑️ ລົບຂໍ້ມູນ | - | ✅ | ສຳເລັດ |
| ☑️ Bulk Operations | - | ✅ | ສຳເລັດ |
| 🔍 ຄົ້ນຫາ/ກັ່ນຕອງ | - | ✅ | ສຳເລັດ |
| 📊 ສະຖິຕິ Real-time | ✅ | ✅ | ສຳເລັດ |
| 📱 Responsive | ✅ | ✅ | ສຳເລັດ |
| � ຄວາມປອດໄພ | ✅ | ✅ | ສຳເລັດ |

---

## �📞 ການສະໜັບສະໜູນ

- **Issues**: ລາຍງານບັນຫາຜ່ານ Issues
- **Email**: support@college.la  
- **Documentation**: README.md
- **Version**: 1.2.0 (Latest)

---

## 📄 License

MIT License - ເບິ່ງລາຍລະອຽດໃນ LICENSE file

---

## 🎉 ຂອບໃຈ

- **Tailwind CSS**: CSS Framework
- **Google Fonts**: Phetsarath Font
- **PHP Community**: PHP Resources
- **MySQL**: Database System

---

## 📝 Changelog

### Version 1.2.0 (2025-09-25)
- ✅ **CRUD System**: ເພີ່ມລະບົບ CRUD ສຳລັບ Admin
- ✏️ **Edit Registration**: ຟອມແກ້ໄຂການລົງທະບຽນແບບສົມບູນ
- ☑️ **Bulk Operations**: ດຳເນີນການກັບຫຼາຍລາຍການພ້ອມກັນ
- 📊 **Real-time Stats**: ສະຖິຕິແບບ Real-time ໃນ Dashboard
- 🔍 **Enhanced Search**: ລະບົບຄົ້ນຫາແລະກັ່ນຕອງທີ່ດີຂຶ້ນ
- 🖼️ **Logo Fix**: ແກ້ໄຂບັນຫາ Logo ບໍ່ສະແດງ
- 📱 **Mobile UI**: ປັບປຸງ UI ສຳລັບມືຖື

### Version 1.1.0 (2024-12-30)
- 🎨 **UI Improvements**: ປັບປຸງ Interface ໃຫ້ສວຍງາມ
- 🔒 **Security**: ເພີ່ມລະບົບຄວາມປອດໄພ
- 📂 **File Management**: ປັບປຸງລະບົບຈັດການໄຟລ໌

### Version 1.0.0 (2024-12-19)
- 🚀 **ເປີດຕົວລະບົບຄັ້ງທຳອິດ**
- 📝 **ລະບົບລົງທະບຽນຄົບຖ້ວນ**
- 👨‍💼 **Admin Dashboard**
- 📄 **ລະບົບຈັດການໄຟລ໌**
- 🔐 **ຄຸນສົມບັດຄວາມປອດໄພ**

---

**🎓 ລະບົບລົງທະບຽນຮັບໃບປະກາດນິຍະບັດ**
*Made with ❤️ for Education*