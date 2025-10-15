<?php

namespace App\Mail;

use App\Models\EstoreOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $templateBody;

    /**
     * Create a new message instance.
     */
    public function __construct(EstoreOrder $order, string $templateBody)
    {
        $this->order = $order;
        $this->templateBody = $templateBody;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->subject('Order Update') // You can also pass subject from template
            ->view('user.emails.order_status_updated')
            ->with([
                'body' => $this->templateBody,
            ]);
    }
}
