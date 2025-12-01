<h2>Invoice {{ $invoice->invoice_no }}</h2>
<p>Hello {{ $invoice->customer->name }},</p>
<p>Thank you for your purchase.</p>
<p>
    Total Amount: <strong>{{ number_format($invoice->total_amount, 2) }}</strong>
</p>
<p>Regards,<br>My Billings</p>
