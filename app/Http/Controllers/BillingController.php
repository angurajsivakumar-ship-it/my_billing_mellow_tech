<?php

namespace App\Http\Controllers;

use App\Events\InvoiceGenerated;
use App\Models\Customer;
use App\Models\InventoryLog;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Services\InvoiceService;
use App\Traits\CommonFunctions;
use App\Traits\ResponseTraits;
use Database\Seeders\ProductSeeder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Concerns\TValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\ValidationException;


class BillingController extends Controller
{
    use ResponseTraits, CommonFunctions;

    /**
     * @param Request $request
     * @return Factory
     */
    public function create(Request $request)
    {
        return view('pages.billing.create-billing');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $validationData = $this->validateBillingStore($request);
        if (!$validationData['success']) {
            return $this->handleValidationErrorResponse(
                $validationData['errors'], config('common.errors.validation')
            );
        }

        $data = $validationData['data'];
        try{
            $customer = $this->findTheCustomer($data);
            $created = InvoiceService::createInvoice($data, $customer);
            $invoice = $this->invoiceDetails($created->id);
            event(new InvoiceGenerated($invoice));
            $urlForInvoicePdf = route('web.invoice.generate', $invoice->id);
            $invoice->url_to_navigate = $urlForInvoicePdf;
            return $this->successResponse($invoice, config('common.success.simple_success'));
        } catch (\Exception $e) {
          return $this->errorResponse($e, config('common.errors.went_wrong'));
        }
    }
}
