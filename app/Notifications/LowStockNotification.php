<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    protected $inventory;

    public function __construct($inventory)
    {
        $this->inventory = $inventory;
    }

    public function via($notifiable)
    {
        return ['database']; // Menyimpan notifikasi di database
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Stok ' . $this->inventory->sparePart->name . ' menipis!',
            'spare_part_id' => $this->inventory->spare_part_id,
            'current_stock' => $this->inventory->stock,
            'min_stock' => $this->inventory->min_stock,
        ];
    }
}