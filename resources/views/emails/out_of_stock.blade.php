<!DOCTYPE html>
<html>

    <head>
        <meta charset="utf-8" />
        <title>Product Out of Stock</title>
    </head>

    <body>
        <h2>Product Out of Stock Notification</h2>
        <p>Hello {{ $recipient->name ?? 'Admin' }},</p>
        <p>The following product is currently out of stock:</p>
        <ul>
            <li><strong>Product:</strong> {{ $product->name ?? 'N/A' }}</li>
            <li><strong>Product ID:</strong> {{ $product->id ?? '' }}</li>
            <li><strong>Warehouse:</strong> {{ $warehouse->name ?? 'N/A' }}</li>
            <li><strong>Current Quantity:</strong> {{ $quantity }}</li>
        </ul>
        <p>Please restock as soon as possible.</p>
        <p>Thanks,<br />{{ config('app.name') }}</p>
    </body>

</html>
