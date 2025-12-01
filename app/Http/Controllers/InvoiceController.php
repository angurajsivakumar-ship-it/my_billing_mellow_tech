<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Traits\CommonFunctions;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{

   use CommonFunctions;

    /**
     * @param Request $request
     * @return Factory
     */
   public function index(Request $request){
       $q = trim($request->get('q'));

       $invoices = Invoice::with('customer')
           ->when($q, function ($query) use ($q) {
               $query->where('invoice_no', 'like', "%{$q}%")
                   ->orWhereHas('customer', function ($c) use ($q) {
                       $c->where('name', 'like', "%{$q}%")
                           ->orWhere('email', 'like', "%{$q}%");
                   });
           })
           ->orderByDesc('id')
           ->paginate(config('common.pagination.invoice_limit'))
           ->withQueryString();

       return view('pages.invoices.list', compact('invoices', 'q'));
   }

    /**
     * @param $invoiceId
     * @return Response
     */
    public function generateInvoice($invoiceId){
        $invoice = $this->invoiceDetails($invoiceId);
        $pdf = Pdf::loadView('pages.pdf-templates.invoice.invoice_v1', [
            'invoice' => $invoice,
        ]);

        return $pdf->download('invoice_'.$invoice->invoice_no.'.pdf');
    }
}
