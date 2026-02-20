<?php

return [
    'success' => [
        'created' => 'สร้างข้อมูลสำเร็จ',
        'updated' => 'อัปเดตข้อมูลสำเร็จ',
        'deleted' => 'ลบข้อมูลสำเร็จ',
        'saved' => 'บันทึกข้อมูลสำเร็จ',
    ],
    'error' => [
        'not_found' => 'ไม่พบข้อมูล',
        'unauthorized' => 'ไม่มีสิทธิ์เข้าถึง',
        'insufficient_stock' => 'สินค้าในคลังไม่เพียงพอ',
        'insufficient_available' => 'จำนวนที่ว่างไม่เพียงพอ',
        'serial_required' => 'กรุณาระบุหมายเลข Serial ให้ครบตามจำนวน',
        'serial_not_available' => 'หมายเลข Serial :serial ไม่พร้อมให้เช่า',
        'serial_duplicate' => 'หมายเลข Serial ซ้ำกัน',
    ],
    'pos' => [
        'customer_required' => 'กรุณาระบุลูกค้าก่อนทำรายการเช่า',
        'checkout_success' => 'ชำระเงินสำเร็จ',
        'cart_empty' => 'ไม่มีสินค้าในตะกร้า',
        'item_added' => 'เพิ่มสินค้าแล้ว',
        'item_removed' => 'นำสินค้าออกแล้ว',
        'box_mode_unavailable' => 'สินค้านี้ไม่รองรับการขายยกกล่อง',
    ],
];
