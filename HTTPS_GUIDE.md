# HTTPS Configuration Guide

## 🔒 การตั้งค่า HTTPS สำหรับ TKR CRM System

### 📋 Overview
ระบบรองรับการใช้งานทั้ง HTTP (development) และ HTTPS (production) โดยสามารถควบคุมผ่าน environment variables

### 🛠 Environment Variables

#### Development (.env)
```env
FORCE_HTTPS=false
SESSION_SECURE_COOKIE=false
APP_URL=http://127.0.0.1:8000
```

#### Production (.env)
```env
FORCE_HTTPS=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
APP_URL=https://yourdomain.com
```

### 🎯 การทำงาน

1. **Development Mode**: 
   - ใช้ HTTP ปกติ
   - Session cookies ไม่บังคับ HTTPS
   - ยืดหยุ่นในการทดสอบ

2. **Production Mode**:
   - บังคับใช้ HTTPS อัตโนมัติ
   - Session cookies ใช้ secure flag
   - ปลอดภัยสูงสุด

### 🔧 การปรับใช้

#### Local Development
```bash
# ไม่ต้องเปลี่ยนอะไร - ใช้ HTTP ปกติ
php artisan serve
```

#### Production Deployment
```bash
# 1. อัปเดต .env
FORCE_HTTPS=true
APP_URL=https://yourdomain.com

# 2. Clear cache
php artisan config:cache
php artisan route:cache
```

### 🚀 Features

- ✅ **Auto-detection**: ตรวจจับ production environment อัตโนมัติ
- ✅ **Flexible**: เปิด/ปิดได้ผ่าน environment variable
- ✅ **Secure**: ใช้ Laravel best practices
- ✅ **Compatible**: รองรับทั้ง HTTP และ HTTPS

### 🔍 การทดสอบ

```php
// ตรวจสอบการตั้งค่า
php artisan tinker --execute="
echo 'FORCE_HTTPS: ' . (env('FORCE_HTTPS') ? 'true' : 'false') . PHP_EOL;
echo 'URL: ' . url('/admin') . PHP_EOL;
"
```

### ⚠️ ข้อควรระวัง

1. **SSL Certificate**: ต้องมี SSL certificate ที่ถูกต้องใน production
2. **Reverse Proxy**: หาก host ผ่าน reverse proxy ต้องตั้งค่า trusted proxies
3. **Mixed Content**: ตรวจสอบให้แน่ใจว่าทุก resource ใช้ HTTPS

### 📞 Support

หากมีปัญหาการตั้งค่า HTTPS:
1. ตรวจสอบ SSL certificate
2. ตรวจสอบ .env configuration  
3. ตรวจสอบ web server configuration (Nginx/Apache)

---
*Updated: August 20, 2025*
