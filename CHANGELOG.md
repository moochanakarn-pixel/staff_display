# Changelog — Staff Display

## [1.2.0] — 2026-05-02

### เพิ่มใหม่
- รองรับการแสดงผลหลาย Zone ผ่าน URL parameter `?cid=X`
  - `staff_display.php?cid=2` → Zone A
  - `staff_display.php?cid=3` → Zone B
  - ทุก Zone ใช้ฐานข้อมูลและโค้ดชุดเดียวกัน ไม่ต้องแยก folder
- เพิ่มฟังก์ชัน `getEffectiveComputerId()` ใน `api_checker.php`
  - อ่านค่า `?cid` จาก URL ก่อน แล้ว fallback ไปใช้ `CURRENT_COMPUTER_ID` จาก `settings.local.php`
- JS endpoints ใน `staff_display.php` และ `staff_display2.php` ส่งต่อค่า `PAGE_CID` ให้ API request ถูก scope กับ zone ที่ถูกต้อง

---

## [1.1.0] — 2026-05-02

### เพิ่มใหม่
- `logo.svg` — ไอคอน clipboard + checkmark + ดินสอ ตามโทนสีของแอป
- `manifest.json` — PWA Manifest รองรับการ "Add to Home Screen" / ติดตั้งเป็นแอป
- เพิ่ม favicon, manifest meta tag, `apple-touch-icon`, `theme-color` และโลโก้ใน topbar ทั้งใน `staff_display.php` และ `staff_display2.php`

---

## [1.0.0] — 2026-05-01

### เปิดตัวครั้งแรก
- `staff_display.php` — หน้าแสดงผลพนักงาน (ฟอร์มหลัก)
- `staff_display2.php` — หน้าแสดงผลพนักงาน (เลย์เอาต์ทางเลือก)
- `api_checker.php` — Backend ตรวจสอบ API / ดึงข้อมูล
- `auth_check.php` — ตรวจสอบสิทธิ์การเข้าถึง
- `config.php` — ตั้งค่าระบบหลัก
- `settings.local.php` — ตั้งค่าเฉพาะเครื่อง (ไม่ commit)
- `web.config` — การตั้งค่า IIS
