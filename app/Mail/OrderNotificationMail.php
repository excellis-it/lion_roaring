<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $user;
    public $warehouseCarts;

    public function __construct($order, $user, $warehouseCarts = [])
    {
        $this->order = $order;
        $this->user = $user;
        $this->warehouseCarts = $warehouseCarts;
    }

    public function build()
    {
        return $this->subject('New Order Notification')
            ->view('user.emails.order_notification'); // create this Blade view
    }
}
