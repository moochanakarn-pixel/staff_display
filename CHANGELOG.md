# Changelog — Staff Display

## [1.4.0] — 2026-05-08

### เพิ่มใหม่
- Usage log รายวัน (`logs/usage-YYYY-MM-DD.log`) เก็บอัตโนมัติ 7 วัน
  - `PAGE_LOAD` — พนักงานเปิดหน้า staff_display (บันทึก cid + IP)
  - `TABLE_OPEN` — กดเข้าดูโต๊ะ (บันทึก table_id + cid + IP)
  - `ERROR` — exception ที่เกิดใน api_checker (บันทึก action + error message)
- `logs/web.config` — บล็อก IIS ไม่ให้เข้าถึงไฟล์ .log โดยตรง
- `.gitignore` — ไม่ commit log file และ settings.local.php

---

## [1.3.0] — 2026-05-08

### แก้ไข
- `web.config`: เพิ่ม `existingResponse="PassThrough"` — ป้องกัน IIS แทนที่ JSON error ของ PHP ด้วย HTML 500 ของตัวเอง
- `api_checker.php` (`fetchTableOrders`): เปลี่ยน fallback filter จาก `SubmitOrderDateTime >= NOW() - 24h` เป็น `OrderDate = CURDATE()` — สอดคล้องกับ `fetchActiveRows` และรองรับระบบ POS ที่ไม่ populate TransactionID

---

## [1.2.0] — 2026-05-02

### เพิ่มใหม่
- รองรับการแสดงผลหลาย Zone ผ่าน URL parameter `?cid=X`
  - `staff_display.php?cid=2` → Zone A
  - `staff_display.php?cid=3` → Zone B
  - ทุก Zone ใช้ฐานข้อมูลและโค้ดชุดเดียวกัน ไม่ต้องแยก folder
- เพิ่มฟังก์ชัน `getEffectiveComputerId()` ใน `api_checker.php`
  - อ่านค่า `?cid` จาก URL ก่อน แล้ว fallback ไปใช้ `CURRENT_COMPUTER_ID` จาก `settings.local.php`
- JS endpoints ใน `staff_display.php` ส่งต่อค่า `PAGE_CID` ให้ API request ถูก scope กับ zone ที่ถูกต้อง

---

## [1.1.0] — 2026-05-02

### เพิ่มใหม่
- `logo.svg` — ไอคอน clipboard + checkmark + ดินสอ ตามโทนสีของแอป
- `manifest.json` — PWA Manifest รองรับการ "Add to Home Screen" / ติดตั้งเป็นแอป
- เพิ่ม favicon, manifest meta tag, `apple-touch-icon`, `theme-color` และโลโก้ใน topbar ของ `staff_display.php`

---

## [1.0.0] — 2026-05-01

### เปิดตัวครั้งแรก
- `staff_display.php` — หน้าแสดงผลพนักงาน (ไฟล์หลัก)
- `staff_display2.php` — ไฟล์ทดลองไอเดีย (ไม่ใช้งาน Production)
- `api_checker.php` — Backend ตรวจสอบ API / ดึงข้อมูล
- `auth_check.php` — ตรวจสอบสิทธิ์การเข้าถึง
- `config.php` — ตั้งค่าระบบหลัก
- `settings.local.php` — ตั้งค่าเฉพาะเครื่อง (ไม่ commit)
- `web.config` — การตั้งค่า IIS
