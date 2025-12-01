<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }
        th {
            background: #f0f0f0;
        }
        .right {
            text-align: right;
        }
    </style>
</head>
<body>

<h3>INVOICE</h3>
<p>
    Invoice No: {{ $invoice->invoice_no }}<br>
    Date: {{ $invoice->created_at->format('d-m-Y') }}<br>
    Customer: {{ $invoice->customer->name ?? '' }}
</p>

<table>
    <thead>
    <tr>
        <th>Product</th>
        <th class="right">Price</th>
        <th class="right">Qty</th>
        <th class="right">Tax</th>
        <th class="right">Total</th>
    </tr>
    </thead>

    <tbody>
    @foreach($invoice->items as $item)
        <tr>
            <td>{{ $item->product->name }}</td>
            <td class="right">₹{{ number_format($item->price_per_unit,2) }}</td>
            <td class="right">{{ $item->quantity }}</td>
            <td class="right">₹{{ number_format($item->tax_amount,2) }}</td>
            <td class="right">₹{{ number_format($item->total_amount,2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table>
    <tbody>
    <tr>
        <td>Total (Without Tax)</td>
        <td class="right">₹{{ number_format($invoice->total_amount - $invoice->total_tax,2) }}</td>
    </tr>
    <tr>
        <td>Total Tax</td>
        <td class="right">₹{{ number_format($invoice->total_tax,2) }}</td>
    </tr>
    <tr>
        <td><strong>Grand Total</strong></td>
        <td class="right"><strong>₹{{ number_format($invoice->total_amount,2) }}</strong></td>
    </tr>
    <tr>
        <td>Paid</td>
        <td class="right">₹{{ number_format($invoice->amount_paid,2) }}</td>
    </tr>
    <tr>
        <td>Balance Returned</td>
        <td class="right">₹{{ number_format($invoice->balance_returned,2) }}</td>
    </tr>
    </tbody>
</table>

@if($invoice->denominationTransactions->count())
    <h4>Balance Denomination</h4>
    <table>
        <thead>
        <tr>
            <th>Note</th>
            <th>Count</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoice->denominationTransactions as $d)
            <tr>
                <td>₹{{ $d->denomination->value }}</td>
                <td>{{ $d->count_used }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif

</body>
</html>
