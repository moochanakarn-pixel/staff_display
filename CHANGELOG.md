# Changelog — Staff Display

## [3.1.0] — 2026-05-19

### แก้ไขบัค
- **Login error ครั้งแรก** — แก้ปัญหา login ครั้งแรกขึ้น error แต่ครั้งที่สองด้วยรหัสเดิมกลับเข้าได้
  - `api_checker.php` — เพิ่ม `session_write_close()` ทันทีหลัง `auth_check.php` ปล่อย PHP session file lock ก่อน DB query ป้องกันการบล็อกจาก concurrent request
  - `staff_display.php` — เพิ่ม `window._isAuthed` flag ตั้งค่าใน `setStaff()` (true) / `showLogin()` (false)
  - `refreshBtn` และ `visibilitychange` guard ด้วย `_isAuthed` — ป้องกัน `loadAll()` ถูกเรียกขณะ login overlay ยังแสดงอยู่

---

## [3.0.0] — 2026-05-18

### เพิ่มใหม่ / ปรับปรุง
- `guide.html` — ออกแบบใหม่ทั้งหน้าให้ใช้ design system เดียวกับ `staff_display.php`
  - CSS variables, background gradient, topbar gradient ตรงกัน 100%
  - ใช้ class จริงจากแอป: `table-card`, `s-yellow`, `s-red`, `s-done`, `s-empty`, `tc-badge`, `tc-open-time`, `order-row`, `r-done`, `r-active`, `r-voided`, `btn-zone`, `sp-section`, `sp-toggle-on/off`
  - Mock login card, zone bar, settings panel ตรงกับ UI จริง
  - ปุ่ม "← กลับ" ใน topbar ลิ้งกลับ `staff_display.php`

---


## [2.9.0] — 2026-05-18

### เพิ่มใหม่
- `guide.html` — คู่มือการใช้งานแบบ standalone web page ครอบคลุม: login, topbar, การ์ดโต๊ะ, สีสถานะ, modal, zone filter, หน้าตั้งค่า

---

## [2.8.0] — 2026-05-18

### แก้ไขบัค
- `manifest.json` — เพิ่ม `"scope": "./"` ป้องกัน PWA เปิด 2 แถบเมื่อ URL เปลี่ยน (redirect หรือ query string)

---

## [2.7.0] — 2026-05-18

### แก้ไขบัค
- Guest mode — chip แสดง "เข้าสู่ระบบ" แทนที่จะซ่อน กดได้ทุกเมื่อเพื่อ login เป็นพนักงานจริง

---

## [2.6.0] — 2026-05-18

### เพิ่มใหม่
- โต๊ะว่างแสดง `TableName` จริงแทน `TableID` — `listTablesInZone` คืน `TableName` และ JS build `Map<TableID,TableName>` แทน `Set`
- `listZones` — เพิ่ม `WHERE Deleted=0` ไม่แสดงโซนที่ถูกลบ
- Settings panel — เพิ่ม section "🔐 การเข้าใช้งาน" toggle "บังคับใส่รหัสพนักงาน" (`kds_require_login`) ปิดแล้วข้าม login ได้เลย

---

## [2.5.0] — 2026-05-17

### แก้ไขบัค
- `config.php` default `db_port` จาก 3307 → 3306 (standard MySQL port)
- Logout button — ออก fullscreen ก่อนแสดง login overlay (browser บล็อก `confirm()` ใน fullscreen)
- `.staff-chip` — เพิ่ม `max-width:140px` + `text-overflow:ellipsis` ป้องกันชื่อยาวดัน topbar พัง

---

## [2.4.0] — 2026-05-17

### เพิ่มใหม่
- แสดงเวลาเปิดโต๊ะ (`⏱ HH:MM`) บนการ์ดใต้ชื่อโต๊ะ — ใช้ `SubmitOrderDateTime` ที่เร็วที่สุดของแต่ละโต๊ะ ไม่ต้องเพิ่ม column ใน DB

---

## [2.3.0] — 2026-05-17

### เพิ่มใหม่
- หน้า Login overlay — กรอก StaffCode เพื่อเข้าใช้งาน
  - POST `lookup_staff` → query ตาราง `staffs` WHERE StaffCode = ? AND Deleted = 0
  - เก็บ `{staff_id, staff_name}` ใน `localStorage['staff_display']` — reload ไม่ต้อง login ซ้ำ
  - แสดงชื่อพนักงานเป็น chip ใน topbar — กดเพื่อ logout
  - Polling เริ่มเฉพาะเมื่อ login สำเร็จเท่านั้น

### ล้างโค้ด
- ลบ `staff_display2.php` และอ้างอิงทั้งหมดออกจาก repo (ไฟล์ทดลองที่ไม่ได้ใช้งาน)
- แก้ `manifest.json` `start_url` จาก `staff_display2.php` → `staff_display.php`
- แก้ `web.config` ลบ IIS rewrite rule ที่ต้องการ URL Rewrite Module ออก

---

## [2.2.0] — 2026-05-13

### แก้ไขบัค
- `zone-bar` ternary — ทั้ง 2 branch ให้ผล `' hidden'` เหมือนกัน → zone bar ซ่อนตลอดใน KDS mode แก้เป็น `''` เมื่อไม่ใช่ serve mode
- `$_pageCid` เปลี่ยนจาก `$_REQUEST['cid']` เป็น `$_GET['cid']` ให้ตรงกับ `PAGE_CID` ฝั่ง JS ป้องกัน cid ผิด scope เมื่อ request มาจาก POST
- `finish_staff_id` validation — เปลี่ยนเงื่อนไขจาก `<= 0` เป็น `< 0` เพราะ 0 เป็นค่า default ที่ถูกต้อง (ยังไม่ตั้งค่า)
- `strtotime($finishedAt)` — เพิ่ม guard `!empty()` ก่อน call ป้องกัน false เข้า `date()` เมื่อ finishedAt ว่างเปล่า

---

## [2.1.0] — 2026-05-13

### เพิ่มใหม่
- หน้าตั้งค่า — เปิดด้วยการแตะ logo 3 ครั้งใน 1.5 วินาที
  - **⏱️ เวลา & รีเฟรช**: เตือนสีเหลือง/แดง (นาที), รีเฟรชทุก 15/30/60 วินาที
  - **🔔 การแจ้งเตือน**: เปิด/ปิดเสียง
  - **🖥️ จอแสดงผล**: ชื่อจอ, DB Host, Database Name, Computer ID (port/user/pass ซ่อน)
  - **🍽️ Serve Mode**: แสดงเฉพาะโต๊ะพร้อมเสิร์ฟ, ซ่อนปุ่มสลับหน้าเสิร์ฟ
- ค่า server-side บันทึกผ่าน `save_system_settings` API → `settings.local.php`
- ค่า client-side (refresh, serve options) บันทึกใน localStorage

---

## [2.0.0] — 2026-05-12

### เพิ่มใหม่
- `?mode=serve` — รวม Serve Display เข้า `staff_display.php` ในหน้าเดียว
  - `staff_display.php` → KDS mode (เดิม)
  - `staff_display.php?mode=serve` → Serve mode สำหรับพนักงานเสิร์ฟ
  - topbar เปลี่ยนสีเป็น teal, body class `serve-mode` override card colours
  - card สีเขียว+pulse = พร้อมเสิร์ฟทั้งหมด, สีส้ม = ยังมีรายการทำอยู่
  - เรียงโต๊ะพร้อมเสิร์ฟขึ้นก่อน ตามด้วย natural sort
  - modal แสดง status ต่อรายการ: กำลังทำ / พร้อมเสิร์ฟ / เสิร์ฟแล้ว
  - title bar `(N) Serve Display` นับโต๊ะที่พร้อมเสิร์ฟทั้งหมด
- ลบ `serve_display.php` ออก (รวมเข้า staff_display แล้ว)

---

## [1.9.0] — 2026-05-12

### เพิ่มใหม่
- `serve_display.php` — หน้า read-only สำหรับพนักงานเสิร์ฟ
  - แสดงโต๊ะที่มีอาหารพร้อมเสิร์ฟ (ProcessStatus=1/4, ServingDateTime IS NULL)
  - สีเขียว+pulse = พร้อมเสิร์ฟทั้งหมด, สีส้ม = ยังมีรายการทำอยู่
  - เรียงโต๊ะพร้อมเสิร์ฟขึ้นก่อน ตามด้วย natural sort ชื่อโต๊ะ
  - กด card เปิด modal ดูรายละเอียดแต่ละรายการ (กำลังทำ / พร้อมเสิร์ฟ / เสิร์ฟแล้ว)
  - ไม่มีปุ่มกดทำอะไร — read-only ทั้งหมด
- `api_checker.php` — เพิ่ม 2 action ใหม่
  - `list_serve_view` — คืนรายการโต๊ะที่ยังมีของรอเสิร์ฟ (group by table)
  - `list_serve_table_orders` — คืนรายการอาหารในโต๊ะ รวม ServingDateTime

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
- `api_checker.php` — Backend ตรวจสอบ API / ดึงข้อมูล
- `auth_check.php` — ตรวจสอบสิทธิ์การเข้าถึง
- `config.php` — ตั้งค่าระบบหลัก
- `settings.local.php` — ตั้งค่าเฉพาะเครื่อง (ไม่ commit)
- `web.config` — การตั้งค่า IIS
