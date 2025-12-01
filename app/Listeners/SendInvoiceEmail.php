<?php

namespace App\Listeners;

use App\Events\InvoiceGenerated;
use App\Mail\InvoiceGeneratedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendInvoiceEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param InvoiceGenerated $event
     */
    public function handle(InvoiceGenerated $event): void
    {
        $invoice = $event->invoice;

        Mail::to($invoice->customer->email)
            ->send(new InvoiceGeneratedMail($invoice));
    }
}
