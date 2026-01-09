<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\WarehouseProduct;
use App\Models\User;

class OutOfStockNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $warehouseProduct;
    public $recipient;

    /**
     * Create a new message instance.
     *
     * @param WarehouseProduct $warehouseProduct
     * @param User|null $recipient
     */
    public function __construct(WarehouseProduct $warehouseProduct, User $recipient = null)
    {
        $this->warehouseProduct = $warehouseProduct;
        $this->recipient = $recipient;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $product = $this->warehouseProduct->product;
        $warehouse = $this->warehouseProduct->warehouse;

        $subject = 'Product out of stock: ' . ($product->name ?? ('Product #' . ($this->warehouseProduct->product_id ?? '')));

        return $this->subject($subject)
            ->view('emails.out_of_stock')
            ->with([
                'product' => $product,
                'warehouse' => $warehouse,
                'quantity' => $this->warehouseProduct->quantity,
                'recipient' => $this->recipient,
            ]);
    }
}
