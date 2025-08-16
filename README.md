<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# TKR CRM System v2

โระบบ CRM สำหรับการจัดการลูกค้าและตั๋วประกันภัยของบริษัททิพยประกันภัย พัฒนาด้วย Laravel และ Filament

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
- ✅ รายงานการเงินและสถิติ
- ✅ เป้าหมายการขายและการติดตาม

### 🔐 ระบบผู้ใช้งาน
- ✅ การจัดการสิทธิ์และบทบาทต่างๆ
- ✅ ระบบ Authentication และ Authorization
- ✅ Activity Logging ครบครัน

## 🛠 Technical Stack

- **Backend**: Laravel 12.24.0
- **Admin Panel**: Filament v4.0
- **Database**: SQLite (สามารถเปลี่ยนเป็น MySQL/PostgreSQL ได้)
- **File Storage**: Laravel Storage System
- **UI**: Modern Responsive Design with Thai language support

## 📦 Installation

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js และ npm

### Setup Steps

1. Clone repository
```bash
git clone https://github.com/thd-will/crmtkr-v2.git
cd crmtkr-v2
```

2. Install dependencies
```bash
composer install
npm install
```

3. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

4. Database setup
```bash
php artisan migrate
php artisan db:seed
```

5. Build assets
```bash
npm run build
```

6. Start development server
```bash
php artisan serve
```

## 🎯 Usage

### Admin Panel
เข้าใช้งานที่: `http://localhost:8000/admin`

Login credentials (หลังจาก seed):
- Email: admin@example.com
- Password: password

### Public URLs
- Customer verification: `/ticket/{ticket_number}`
- Staff verification: `/ticket/{ticket_number}/staff`

## 📁 Project Structure

```
app/
├── Filament/
│   ├── Resources/
│   │   ├── PolicyTickets/     # ระบบจัดการตั๋วประกัน
│   │   ├── Customers/         # ระบบจัดการลูกค้า
│   │   ├── Payments/          # ระบบการเงิน
│   │   └── ...
│   └── Widgets/               # Dashboard widgets
├── Http/Controllers/
├── Models/
└── ...
```

## 🎨 Key Features Details

### Policy Ticket Sections
1. **📄 ข้อมูลคำขอประกันภัย** - ข้อมูลพื้นฐานและรายละเอียดกรมธรรม์
2. **🏢 ข้อมูลจากทิพยประกันภัย** - ข้อมูลที่เจ้าหน้าที่ทิพยกรอก
3. **⚙️ ข้อมูลระบบ** - การตั้งค่าระบบและ URL
4. **💰 ข้อมูลการชำระเงิน** - สถานะและรายละเอียดการชำระเงิน
5. **📋 การจัดการงาน** - ความสำคัญ วันครบกำหนด และการติดตาม

### File Management
- รองรับไฟล์ประเภท: PDF, DOC, DOCX, ZIP
- แยกโฟลเดอร์: policy-requests/ สำหรับลูกค้า, staff-files/ สำหรับเจ้าหน้าที่
- ขนาดสูงสุด: 300MB สำหรับไฟล์ลูกค้า, 10MB สำหรับไฟล์เจ้าหน้าที่
- ฟีเจอร์: ดาวน์โหลด, ดูไฟล์, ลบไฟล์

## 📈 Future Enhancements

- [ ] API สำหรับ Mobile App
- [ ] ระบบแจ้งเตือนแบบ Real-time
- [ ] ระบบรายงานที่ซับซ้อนมากขึ้น
- [ ] Integration กับระบบภายนอก
- [ ] Multi-tenant support

## 🤝 Contributing

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 Support

สำหรับการสนับสนุนหรือคำถาม กรุณาติดต่อทีมพัฒนา

---

**Built with ❤️ for Tipaya Insurance**

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
