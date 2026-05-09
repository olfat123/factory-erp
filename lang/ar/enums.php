<?php

return [
    'machine_status' => [
        'available' => 'متاح',
        'running' => 'يعمل',
        'maintenance' => 'تحت الصيانة',
        'out_of_service' => 'خارج الخدمة',
    ],
    'movement_type' => [
        'purchase_receive' => 'استلام مشتريات',
        'production_consume' => 'استهلاك إنتاج',
        'production_output' => 'مخرجات الإنتاج',
        'adjustment_increase' => 'تعديل زيادة',
        'adjustment_decrease' => 'تعديل نقصان',
        'return' => 'مرتجع',
    ],
    'po_status' => [
        'draft' => 'مسودة',
        'approved' => 'معتمد',
        'partially_received' => 'مستلم جزئياً',
        'fully_received' => 'مستلم بالكامل',
        'closed' => 'مغلق',
    ],
    'production_status' => [
        'draft' => 'مسودة',
        'approved' => 'معتمد',
        'in_production' => 'قيد الإنتاج',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغى',
    ],
];
