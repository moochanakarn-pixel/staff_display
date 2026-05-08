# Changelog — Staff Display

## [1.9.0] — 2026-05-08

### เพิ่มใหม่
- โต๊ะว่าง (empty card) แสดงชื่อโต๊ะจริง (TableName) แทนที่จะเป็น TableID
  - `list_tables_in_zone` คืน `TableName` เพิ่มเติมจาก `TableID`
  - `state.zoneTables` เปลี่ยนจาก `Set<id>` เป็น `Map<id→name>`
  - empty card loop ใช้ชื่อโต๊ะจาก Map แทน raw ID

---

## [1.8.0] — 2026-05-08

### เพิ่มใหม่ / แก้ไข
- `renderGrid` — เรียงโต๊ะด้วย natural sort (`localeCompare {numeric:true}`) แทน lexicographic เดิม: "1","2","10","11" และ "B1","B2","B10" เรียงถูกต้อง
- sort รันทุกครั้งไม่ว่า `state.zoneTables` จะ null หรือไม่ (เดิม sort ข้ามไปถ้าไม่มี zone data)

---

## [1.7.0] — 2026-05-08

### แก้ไขบัค
- `PS_RESOLVED = 4` — เพิ่ม ProcessStatus=4 (Resolved) เป็นสถานะ "เสร็จแล้ว" ใน JS แก้ปัญหา item ที่ถูก resolve ใน POS แสดงเป็น "กำลังทำ" แทน
- `buildRow` — ยกเลิก logic พิเศษสำหรับ `is_moved` ที่ทำให้รายการที่ย้ายโต๊ะแสดงเป็นสีเทา "🔄 ย้ายโต๊ะ" — ตอนนี้แสดงสถานะปกติ (กำลังทำ/เสร็จแล้ว)
- Modal summary counters — ลบ `!r.is_moved` filter ออก และเพิ่ม `PS_RESOLVED` ในการนับ nDone/nActive

---

## [1.6.0] — 2026-05-08

### แก้ไขบัค
- `nonKds(row, set)` — รวม logic ตรวจ non-KDS เป็นฟังก์ชันเดียว แทนที่จะมี `isNonKds` และ `isAutoD` แยกกัน 2 ที่
- ลบเงื่อนไข `pid > 0` ออกจาก non-KDS check — item ที่ `PrinterID = 0` (เช่น set menu parent) ก็ถูก auto-done ด้วยเมื่อไม่อยู่ใน allowed printer set
- Title bar `(X) Staff Display` — แก้ให้นับ pending ผ่าน `isNonKds` filter เหมือน grid card (เดิมตัวเลขสูงกว่าจริง)
- `openModal` — เพิ่ม `AbortController` (`_modalController`) ยกเลิก fetch เก่าทันทีเมื่อกดโต๊ะใหม่ก่อน response กลับมา ป้องกัน modal แสดงข้อมูลผิดโต๊ะ
- `fmtTime()` — fallback datetime ผ่าน `esc()` ป้องกัน raw string แสดงใน HTML

---

## [1.5.0] — 2026-05-08

### เพิ่มใหม่ / แก้ไข
- `list_table_orders` ส่ง `allowed_printer_ids` กลับมาใน response โดยตรง — modal ใช้ค่าจาก call เดียวกันแทนการพึ่ง `state.allowedPrinters` จาก `list_active` ที่อาจยังไม่ถูก populate
- `buildRow(row, printerSet)` รับ printerSet เป็น parameter — การ render modal ไม่ขึ้นกับ global state

### แก้ไขบัค
- Items จาก printer station อื่น แสดงเป็น "กำลังทำ" ใน modal แม้ grid card จะถูกแล้ว — เพราะ `buildRow` เดิมใช้ `state.allowedPrinters` ซึ่งอาจ null ขณะ modal render

---

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
