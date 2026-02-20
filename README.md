# ระบบจัดการสนามยิงปืน (Gun Range Management System)

ระบบจัดการสนามยิงปืนสร้างด้วย Laravel 11 + AdminLTE 4 + Bootstrap 5

## คุณสมบัติหลัก

- **จุดขาย (POS)**: ขายปลีก / ขายยกกล่อง / เช่า แบบ AJAX ไม่รีโหลดหน้า
- **สแกนบาร์โค้ด**: รองรับ USB Barcode Scanner (keyboard wedge) ปุ่มลัด F3
- **บัตรประชาชน**: รองรับเครื่องอ่านบัตรประชาชน (keyboard wedge) ปุ่มลัด F2
- **Serial Number ปืน**: จัดการหมายเลขซีเรียลสำหรับปืนเช่า พร้อมประวัติ
- **ระบบสมาชิก**: จัดการสมาชิกลูกค้า
- **แดชบอร์ด**: กราฟยอดขาย 7 วัน / รายประเภทรายเดือน (Chart.js)
- **จัดการ Role/Permission**: ผู้ดูแลระบบ / พนักงานขาย / เจ้าหน้าที่คลัง
- **รายงาน**: สรุปยอดขาย / สต็อกสินค้า

## การติดตั้ง

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

## บัญชีทดสอบ

| บทบาท | อีเมล | รหัสผ่าน |
|-------|-------|----------|
| ผู้ดูแลระบบ | admin@gunrange.local | password |
| พนักงานขาย | cashier@gunrange.local | password |
| เจ้าหน้าที่คลัง | inventory@gunrange.local | password |

## โครงสร้าง Role

| Role | สิทธิ์ |
|------|--------|
| ผู้ดูแลระบบ (admin) | ทุกสิทธิ์ |
| พนักงานขาย (cashier) | POS, ลูกค้า, สมาชิก, การเช่า, รายงาน |
| เจ้าหน้าที่คลัง (inventory) | สินค้า, Serial Number, รายงานสต็อก |

## กฎธุรกิจ

### การขาย
- **ต่อชิ้น (unit)**: `line_total = qty × price_per_unit`, ลด `stock_qty -= qty`
- **ยกกล่อง (box)**: `line_total = qty × price_per_box`, ลด `stock_qty -= qty × units_per_box`
- หากสินค้าไม่มี `price_per_box` หรือ `units_per_box` ปุ่มยกกล่องจะถูก disable

### การเช่า
- `available_qty -= qty` (stock_qty ไม่ลด)
- สินค้าที่มี `requires_serial = true` ต้องระบุ Serial Number ให้ครบตามจำนวน

### Serial Number
- สถานะ: `available` → `rented` → `available` (เมื่อคืน)
- `available_qty` sync จากจำนวน serial ที่ `status = available`

## การรันทดสอบ

```bash
php artisan test
```

## เทคโนโลยี

- Laravel 11
- Spatie Laravel Permission
- AdminLTE 4 + Bootstrap 5
- Chart.js 4
- SQLite (ทดสอบ) / MySQL (production)
