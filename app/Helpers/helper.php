<?php

use App\Models\Invoice;

/**
 * @param $prefix
 * @return string
 * Generate invoice number: IN25300001
 * Format: IN + Year(2digits) + Day(3digits) + Sequential(4digits)
 */
if (!function_exists('generate_invoice_number')) {

    function generate_invoice_number($prefix = 'IN')
    {
        return DB::transaction(function () use ($prefix) {
            $year = now()->format('y');
            $dayOfYear = str_pad(now()->dayOfYear, 3, '0', STR_PAD_LEFT);
            $base = $prefix . $year . $dayOfYear;
            $lastInvoice = Invoice::where('invoice_no', 'like', $base . '%')
                ->lockForUpdate()
                ->orderBy('invoice_no', 'desc')
                ->first();
            $lastSequence = $lastInvoice
                ? (int) substr($lastInvoice->invoice_no, -4)
                : 0;
            $newSequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
            return $base . $newSequence;
        });
    }
}


