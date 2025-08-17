<p align="center">
  <img src="https://via.placeholder.com/400x100/4F46E5/FFFFFF?text=TKR+CRM" width="400" alt="TKR CRM Logo">
</p>

<p align="center">
<a href="#"><img src="https://img.shields.io/badge/version-2.0.0-blue.svg" alt="Version"></a>
<a href="#"><img src="https://img.shields.io/badge/php-8.2+-777BB4.svg" alt="PHP Version"></a>
<a href="#"><img src="https://img.shields.io/badge/license-MIT-green.svg" alt="License"></a>
<a href="#"><img src="https://img.shields.io/badge/status-stable-brightgreen.svg" alt="Status"></a>
</p>

# TKR CRM System v2

ระบบ CRM สำหรับการจัดการลูกค้าและตั๋วประกันภัยของบริษัททิพยประกันภัย พัฒนาด้วย PHP และ Filament Admin Panel

## 📄 Table of Contents

- [Features](#-features)
- [Technical Stack](#-technical-stack)
- [Installation](#-installation)
- [Usage](#-usage)
- [Project Structure](#-project-structure)
- [API Documentation](#-api-documentation)
- [Screenshots](#-screenshots)
- [Contributing](#-contributing)
- [Support](#-support)

## 🚀 Features

### 📋 การจัดการ Policy Tickets
- ✅ ระบบ CRUD ครบครัน พร้อมการจัดระเบียบแบบ 5 sections
- ✅ แบ่งข้อมูลอย่างชัดเจนระหว่างข้อมูลลูกค้าและข้อมูลจากทิพย
- ✅ สร้าง URL สาธารณะสำหรับลูกค้า และ URL สำหรับพนักงาน
- ✅ ระบบแนบไฟล์ที่รองรับ PDF, DOC, DOCX, ZIP ขนาดสูงสุด 300MB
- ✅ ดาวน์โหลดและดูไฟล์แนบได้โดยตรง

### 👥 การจัดการลูกค้า
- ✅ ระบบข้อมูลลูกค้าแบบละเอียด พร้อมหมายเหตุภาษาไทย
- ✅ ติดตาม Follow-up และกิจกรรมต่างๆ
- ✅ Dashboard สำหรับวิเคราะห์ข้อมูลลูกค้า

### 💰 การจัดการการเงิน
- ✅ ระบบชำระเงินและเครดิตธุรกรรม
- ✅ รายงานการเงินและสถิติแบบ Real-time
- ✅ เป้าหมายการขายและการติดตาม
- ✅ แดชบอร์ดเครดิตและการวิเคราะห์

### 🔐 ระบบผู้ใช้งานและความปลอดภัย
- ✅ การจัดการสิทธิ์และบทบาทต่างๆ
- ✅ ระบบ Authentication และ Authorization
- ✅ Activity Logging ครบครัน พร้อมติดตาม IP และเวลา
- ✅ ระบบปิดการใช้งาน Global Search เพื่อความปลอดภัย

### 📊 Dashboard และรายงาน  
- ✅ Dashboard หลักพร้อมสถิติครบครัน
- ✅ วิดเจ็ตแสดงข้อมูลแบบ Real-time
- ✅ Charts และกราฟวิเคราะห์ข้อมูล
- ✅ รายงานการเงินแบบละเอียด

## 🛠 Technical Stack

- **Backend**: PHP 8.2+ with modern architecture
- **Admin Panel**: Filament v4.0 - Modern PHP Admin Panel
- **Database**: SQLite (พร้อมรองรับ MySQL/PostgreSQL)  
- **File Storage**: Secure File Management System
- **Frontend**: Livewire + Alpine.js
- **UI/UX**: Modern Responsive Design with Thai language support
- **Authentication**: Session-based with role management
- **Logging**: Comprehensive activity tracking system

## 📦 Installation

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js และ npm

### Setup Steps

1. **Clone repository**
```bash
git clone https://github.com/thd-will/crmtkr-v2.git
cd tkrcrm-system
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
# แก้ไข .env ตามความต้องการ
php artisan key:generate
```

4. **Database setup**
```bash
php artisan migrate
php artisan db:seed
```

5. **Build assets**
```bash
npm run build
# หรือสำหรับ development
npm run dev
```

6. **Start server**
```bash
php artisan serve
```

7. **เข้าใช้งาน**
- เปิดเบราว์เซอร์ไปที่: `http://localhost:8000/admin`
- Login: `admin@example.com` / `password`

## 🎯 Usage

### Admin Panel
**URL**: `http://localhost:8000/admin`

**เมนูหลัก** (จัดเรียงตามลำดับความสำคัญ):

🔥 **งานประจำวัน**
- ลูกค้า - จัดการข้อมูลลูกค้า
- ตั๋วประกัน - จัดการคำขอประกันภัย  
- การชำระเงิน - บันทึกและติดตามการชำระ
- รายการค้างชำระ - ติดตามหนี้ค้างชำระ

📈 **รายงานและวิเคราะห์**
- รายงานการเงิน - สรุปรายได้และค่าใช้จ่าย
- แดชบอร์ดเครดิต - วิเคราะห์เครดิตลูกค้า
- ประวัติการใช้เครดิต - ติดตามธุรกรรมเครดิต

👥 **การจัดการระบบ**  
- บันทึกการใช้งาน - Activity logs
- บริหารจัดการผู้ใช้ - User management

### Public URLs
- **Customer verification**: `/ticket/{ticket_number}`
- **Staff verification**: `/ticket/staff/{ticket_number}`

### API Endpoints
ดูรายละเอียดใน [API_ROUTES.md](API_ROUTES.md)

## 📁 Project Structure

```
tkrcrm-system/
├── app/
│   ├── Filament/
│   │   ├── Resources/           # Admin panel resources
│   │   │   ├── Customers/       # 👥 ระบบจัดการลูกค้า
│   │   │   ├── PolicyTickets/   # 🎫 ระบบจัดการตั๋วประกัน
│   │   │   ├── Payments/        # 💰 ระบบการเงิน
│   │   │   ├── CreditTransactions/ # 💳 ระบบเครดิต
│   │   │   ├── ActivityLogs/    # 📝 บันทึกการใช้งาน
│   │   │   └── UserResource.php # 👤 จัดการผู้ใช้
│   │   ├── Pages/               # Custom pages
│   │   │   ├── FinancialReport.php     # 📊 รายงานการเงิน
│   │   │   ├── PendingPayments.php     # ⏰ รายการค้างชำระ
│   │   │   └── CreditDashboard.php     # 💳 แดชบอร์ดเครดิต
│   │   └── Widgets/             # Dashboard widgets
│   │       ├── StatsOverview.php       # สถิติภาพรวม
│   │       ├── CustomersChart.php      # กราฟลูกค้า
│   │       ├── ActivityStatsWidget.php # สถิติการใช้งาน
│   │       └── ...
│   ├── Http/Controllers/
│   │   └── PublicPolicyTicketController.php  # Public ticket access
│   └── Models/                  # Data models
│       ├── Customer.php         # โมเดลลูกค้า
│       ├── PolicyTicket.php     # โมเดลตั๋วประกัน
│       ├── Payment.php          # โมเดลการชำระเงิน
│       └── ...
├── database/
│   ├── migrations/              # Database schema
│   └── seeders/                 # Sample data
├── routes/
│   └── web.php                  # Application routes
├── storage/app/                 # File storage
│   ├── policy-requests/         # ไฟล์จากลูกค้า
│   └── staff-files/            # ไฟล์จากเจ้าหน้าที่
└── API_ROUTES.md               # 📋 API documentation
```

## 🎨 Key Features Details

### 📄 Policy Ticket Management (5 Sections)
1. **ข้อมูลคำขอประกันภัย** - ข้อมูลพื้นฐานและรายละเอียดกรมธรรม์
2. **ข้อมูลจากทิพยประกันภัย** - ข้อมูลที่เจ้าหน้าที่ทิพยกรอก  
3. **ข้อมูลระบบ** - การตั้งค่าระบบและ URL generation
4. **ข้อมูลการชำระเงิน** - สถานะและรายละเอียดการชำระเงิน
5. **การจัดการงาน** - ความสำคัญ วันครบกำหนด และการติดตาม

### 📁 Advanced File Management
- **รองรับไฟล์**: PDF, DOC, DOCX, ZIP, Images
- **โครงสร้างโฟลเดอร์**: 
  - `policy-requests/` - ไฟล์จากลูกค้า (max 300MB)
  - `staff-files/` - ไฟล์จากเจ้าหน้าที่ (max 10MB)  
  - `payment-slips/` - ไฟล์สลิปการชำระเงิน
- **ฟีเจอร์**: Download, Preview, Delete, Secure access

### 📊 Advanced Analytics
- **Real-time Dashboard** - ข้อมูลสดทันทีที่มีการเปลี่ยนแปลง
- **Financial Analytics** - วิเคราะห์รายได้ กำไร ค่าใช้จ่าย
- **Customer Insights** - พฤติกรรมลูกค้า การชำระเงิน
- **Activity Tracking** - ติดตามการใช้งานระบบแบบละเอียด

## 📋 API Documentation

ดูรายละเอียด API ทั้งหมดได้ที่: **[API_ROUTES.md](API_ROUTES.md)**

- 🌐 Web Routes (4 endpoints)
- 🔧 Admin Panel Routes (28 endpoints)  
- 🎫 Public Ticket Routes (9 endpoints)
- 🔌 API Endpoints (1 endpoint)
- 🔧 System Routes (6 endpoints)

**รวม 48 routes**

## 📷 Screenshots

### Admin Dashboard
![Dashboard](https://via.placeholder.com/800x400/4F46E5/FFFFFF?text=TKR+CRM+Dashboard)

### Policy Ticket Management
![Policy Tickets](https://via.placeholder.com/800x400/059669/FFFFFF?text=Policy+Ticket+Management)

### Financial Reports  
![Financial Reports](https://via.placeholder.com/800x400/DC2626/FFFFFF?text=Financial+Reports)

## � Future Enhancements

- [ ] **Mobile App API** - RESTful API สำหรับแอปมือถือ
- [ ] **Real-time Notifications** - แจ้งเตือนแบบ Real-time  
- [ ] **Advanced Reporting** - รายงานที่ซับซ้อนและการส่งออกข้อมูล
- [ ] **Third-party Integration** - เชื่อมต่อกับระบบภายนอก
- [ ] **Multi-tenant Support** - รองรับหลายบริษัท
- [ ] **AI-powered Analytics** - วิเคราะห์ข้อมูลด้วย AI
- [ ] **Automated Workflows** - ระบบอัตโนมัติ

## 🤝 Contributing

การมีส่วนร่วมในการพัฒนา:

1. **Fork** โปรเจกต์นี้
2. **สร้าง branch** สำหรับฟีเจอร์ใหม่ (`git checkout -b feature/AmazingFeature`)
3. **Commit** การเปลี่ยนแปลง (`git commit -m 'Add: AmazingFeature'`)
4. **Push** ไป branch (`git push origin feature/AmazingFeature`)
5. **เปิด Pull Request**

### Development Guidelines
- ใช้ภาษาไทยในคอมเมนต์และเอกสาร
- ตั้งชื่อ variable และ function เป็นภาษาอังกฤษ
- เขียน test สำหรับฟีเจอร์ใหม่
- ปฏิบัติตาม PSR-12 coding standards

## 📝 License

โปรเจกต์นี้ใช้สัญญาอนุญาต **MIT License** - ดูรายละเอียดในไฟล์ [LICENSE](LICENSE)

## 📞 Support

**สำหรับการสนับสนุนหรือคำถาม:**

- 📧 Email: support@tkrcrm.com
- 🐛 Bug Reports: [GitHub Issues](https://github.com/thd-will/crmtkr-v2/issues)
- 📖 Documentation: [GitHub Wiki](https://github.com/thd-will/crmtkr-v2/wiki)
- 💬 Community: [GitHub Discussions](https://github.com/thd-will/crmtkr-v2/discussions)

---

## 🏆 Credits

**Built with ❤️ for Insurance Industry**

### Development Team
- **Lead Developer**: TKR Development Team
- **UI/UX Design**: Modern Admin Interface
- **Database Design**: Optimized for Insurance Business
- **Security Consultant**: Data Protection & Privacy

### Technologies Used
- **[Filament](https://filamentphp.com/)** - Modern PHP Admin Panel
- **[Livewire](https://laravel-livewire.com/)** - Dynamic Frontend Components  
- **[Alpine.js](https://alpinejs.dev/)** - Lightweight JavaScript Framework
- **[Tailwind CSS](https://tailwindcss.com/)** - Utility-First CSS Framework

---

<p align="center">
  <strong>© 2025 TKR CRM System. All rights reserved.</strong>
</p>
