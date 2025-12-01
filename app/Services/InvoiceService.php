<?php

namespace App\Services;

use App\Models\Denomination;
use App\Models\DenominationTransaction;
use App\Models\InventoryLog;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    /**
     * @param array $data
     * @param $customer
     * @return mixed
     * @throws Exception
     * Create invoice with items & stock handling
     */
    public static function createInvoice(array $data, $customer)
    {
        try {
            return DB::transaction(function () use ($data, $customer) {

                $totalWithoutTax = 0;
                $totalTax = 0;
                $totalAmount = 0;



                $invoice = Invoice::create([
                    'invoice_no'       => generate_invoice_number(
                        config('common.prefixes.invoice_prefix')
                    ),
                    'customer_id'      => $customer->id,
                    'total_amount'     => 0,
                    'total_tax'        => 0,
                    'amount_paid'      => $data['cash_paid'],
                    'balance_returned' => 0,
                ]);

                foreach ($data['products'] as $product) {
                    $productInfo = Product::lockForUpdate()->findOrFail($product['id']);
                    if ($productInfo->available_stock < $product['quantity']) {
                        throw new Exception(
                            "Insufficient stock for {$productInfo->name}"
                        );
                    }

                    $qty        = (int) $product['quantity'];
                    $unitPrice = (float) $productInfo->price;

                    $priceWithoutTax = $unitPrice * $qty;
                    $taxAmount = self::calculateTax(
                        $priceWithoutTax,
                        (float) $productInfo->tax_percentage
                    );

                    $rowTotal = $priceWithoutTax + $taxAmount;

                    $totalWithoutTax += $priceWithoutTax;
                    $totalTax        += $taxAmount;
                    $totalAmount     += $rowTotal;
                    InvoiceItem::create([
                        'invoice_id'     => $invoice->id,
                        'product_id'     => $productInfo->id,
                        'quantity'       => $qty,
                        'price_per_unit' => $unitPrice,
                        'tax_amount'     => $taxAmount,
                        'total_amount'   => $rowTotal,
                    ]);

                    $productInfo->decrement('available_stock', $qty);
                    InventoryLog::create([
                        'product_id'      => $productInfo->id,
                        'change_type'     => 'out',
                        'quantity'        => $qty,
                        'reference_model' => Invoice::class,
                        'reference_id'    => $invoice->id,
                    ]);
                }

                $roundedTotal = round($totalAmount);
                $balance = $data['cash_paid'] - $roundedTotal;

                if ($balance < 0) {
                    throw new Exception('Cash paid must be greater than or equal to bill amount');
                }

                $invoice->update([
                    'total_amount'     => $roundedTotal,
                    'total_tax'        => $totalTax,
                    'balance_returned' => $balance,
                ]);

                if ($balance > 0) {
                    self::handleDenominations($balance, $invoice->id);
                }

                return $invoice;
            });

        } catch (Exception $e) {
            Log::error('Invoice creation failed', [
                'error' => $e->getMessage(),
                'data'  => $data
            ]);
            throw $e;
        }
    }

    /**
     * @param $amount
     * @param $percentage
     * @return false|float
     */
    private static function calculateTax($amount, $percentage)
    {
        return round(((float) $amount * (float) $percentage) / 100, 2);
    }

    private static function handleDenominations(float $balance, int $invoiceId)
    {
        // Get denominations highest → lowest
        $denominations = Denomination::orderBy('value', 'desc')
            ->lockForUpdate()
            ->get();

        foreach ($denominations as $denomination) {

            if ($balance <= 0) break;

            $noteValue = $denomination->value;

            // Max notes we can use
            $requiredCount = intdiv((int)$balance, $noteValue);

            if ($requiredCount <= 0) continue;

            // Use only available notes
            $useCount = min($requiredCount, $denomination->available_count);

            if ($useCount <= 0) continue;

            // Store transaction
            DenominationTransaction::create([
                'invoice_id'      => $invoiceId,
                'denomination_id' => $denomination->id,
                'count_used'      => $useCount,
            ]);

            // Reduce available count
            $denomination->decrement('available_count', $useCount);

            // Reduce balance
            $balance -= ($useCount * $noteValue);
        }

        if ($balance > 0) {
            throw new \Exception('Not enough denomination notes available');
        }
    }
}
