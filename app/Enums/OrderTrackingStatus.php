<?php

namespace App\Enums;

enum OrderTrackingStatus: string
{
    case ORDER_CREATED = 'Order Created';
    case PENDING_APPROVAL = 'Pending Approval';
    case AWAITING_PICKUP = 'Awaiting Pickup';
    case IN_TRANSIT_TO_HUB = 'In Transit to Hub';
    case ARRIVED_AT_HUB = 'Arrived at Hub';
    case OUT_FOR_DELIVERY = 'Out for Delivery';
    case DELIVERED = 'Delivered';
    case FAILED_ATTEMPT = 'Failed Delivery Attempt';
    case RETURNED = 'Returned to Sender';

    /**
     * Get all values as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
