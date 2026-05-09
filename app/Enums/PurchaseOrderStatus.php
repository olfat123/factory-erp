<?php

namespace App\Enums;

enum PurchaseOrderStatus: string
{
    case Draft = 'draft';
    case Approved = 'approved';
    case PartiallyReceived = 'partially_received';
    case FullyReceived = 'fully_received';
    case Closed = 'closed';

    public function label(): string
    {
        return match($this) {
            self::Draft => __('enums.po_status.draft'),
            self::Approved => __('enums.po_status.approved'),
            self::PartiallyReceived => __('enums.po_status.partially_received'),
            self::FullyReceived => __('enums.po_status.fully_received'),
            self::Closed => __('enums.po_status.closed'),
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Draft => 'gray',
            self::Approved => 'info',
            self::PartiallyReceived => 'warning',
            self::FullyReceived => 'success',
            self::Closed => 'danger',
        };
    }
}
