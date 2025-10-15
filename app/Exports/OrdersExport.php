<?php

namespace App\Exports;

use App\Models\EstoreOrder;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OrdersExport implements FromView
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function view(): View
    {
        return view('user.estore-orders.export', [
            'orders' => $this->orders
        ]);
    }
}
