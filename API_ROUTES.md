# TKR CRM System - API Routes Documentation

## Overview
This document contains all available routes in the TKR CRM system including web routes, admin panel routes, public ticket routes, and API endpoints.

Total Routes: **48**

---

## 🌐 Web Routes

| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `/` | Homepage redirect to admin login |
| `GET` | `admin` | Admin dashboard |
| `GET` | `admin/login` | Admin login page |
| `POST` | `admin/logout` | Admin logout action |

---

## 🔧 Admin Panel Routes (Filament)

### Activity Logs
| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `admin/activity-logs` | Activity logs listing |

### Credit Management
| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `admin/credit-dashboard` | Credit dashboard |
| `GET` | `admin/credit-transactions` | Credit transactions listing |
| `GET` | `admin/credit-transactions/create` | Create new credit transaction |
| `GET` | `admin/credit-transactions/{record}` | View credit transaction |
| `GET` | `admin/credit-transactions/{record}/edit` | Edit credit transaction |

### Customer Management
| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `admin/customers` | Customers listing |
| `GET` | `admin/customers/create` | Create new customer |
| `GET` | `admin/customers/{record}` | View customer details |
| `GET` | `admin/customers/{record}/edit` | Edit customer |

### Financial Reports
| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `admin/financial-report` | Financial reports page |

### Payment Management
| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `admin/payments` | Payments listing |
| `GET` | `admin/payments/create` | Create new payment |
| `GET` | `admin/payments/{record}` | View payment details |
| `GET` | `admin/payments/{record}/edit` | Edit payment |
| `GET` | `admin/pending-payments` | Pending payments page |

### Policy Ticket Management
| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `admin/policy-tickets` | Policy tickets listing |
| `GET` | `admin/policy-tickets/create` | Create new policy ticket |
| `GET` | `admin/policy-tickets/{record}` | View policy ticket |
| `GET` | `admin/policy-tickets/{record}/edit` | Edit policy ticket |

### User Management
| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `admin/users` | Users listing |
| `GET` | `admin/users/create` | Create new user |
| `GET` | `admin/users/{record}` | View user details |
| `GET` | `admin/users/{record}/edit` | Edit user |

---

## 🎫 Public Ticket Routes

| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `ticket` | Public ticket access form |
| `POST` | `ticket/access` | Submit ticket access request |
| `GET` | `ticket/check/{ticket_number}` | Check ticket by number |
| `POST` | `ticket/verify/{ticket_number}` | Verify and show ticket |
| `GET` | `ticket/staff/{ticket_number}` | Staff form for ticket |
| `GET` | `ticket/staff-verify/{ticket_number}` | Staff verify redirect |
| `POST` | `ticket/staff-verify/{ticket_number}` | Staff verify action |
| `GET` | `ticket/staff-verify/{ticket_number}/{access_code}` | Staff verify with access code |
| `POST` | `ticket/staff-update/{ticket_number}` | Update staff info |

---

## 🔌 API Routes

| Method | URI | Description |
|--------|-----|-------------|
| `POST` | `ticket/api/check-status` | Check ticket status via API |

---

## 🔧 System Routes

### File Management
| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `curator/{path}` | Media file access |
| `GET` | `storage/{path}` | Storage file access |

### Import/Export
| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `filament/exports/{export}/download` | Download export file |
| `GET` | `filament/imports/{import}/failed-rows/download` | Download failed import rows |

### Livewire
| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `livewire/livewire.js` | Livewire JavaScript |
| `GET` | `livewire/livewire.min.js.map` | Livewire source map |
| `GET` | `livewire/preview-file/{filename}` | File preview |
| `POST` | `livewire/update` | Livewire component update |
| `POST` | `livewire/upload-file` | File upload |

### Health Check
| Method | URI | Description |
|--------|-----|-------------|
| `GET` | `up` | Health check endpoint |

---

## 📝 Notes

- All admin routes require authentication
- Public ticket routes are accessible without authentication
- The system uses Filament for admin panel management
- Livewire is used for dynamic frontend interactions
- File uploads are handled through secure storage system

## 🔐 Authentication

- Admin panel uses session-based authentication
- Public ticket routes use ticket number and verification codes
- All sensitive operations require proper authorization

---

*Generated on: August 17, 2025*
*TKR CRM System v1.0*
