<?php

namespace App\Traits;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait CommonFunctions
{
    /**
     * @param $request
     * @return array
     * @throws ValidationException
     */
    public function validateBillingStore($request){
        $validator = Validator::make($request->all(), [
            'customer_email'        => 'required|email',
            'customer_name'         => 'required|string|max:255',
            'cash_paid'             => 'required|numeric|min:0',
            'products'              => 'required|array|min:1',
            'products.*.id'         => 'required|exists:products,id',
            'products.*.quantity'   => 'required|integer|min:1',
        ], [
            'customer_email.required'       => 'Customer email is required',
            'customer_email.email'          => 'Please provide a valid email address',
            'customer_name.required'        => 'Customer name is required',
            'customer_name.string'          => 'Customer name must be a valid text',
            'customer_name.max'             => 'Customer name cannot exceed 255 characters',
            'cash_paid.required'            => 'Cash paid amount is required',
            'cash_paid.numeric'             => 'Cash paid must be a valid number',
            'cash_paid.min'                 => 'Cash paid cannot be negative',
            'products.required'             => 'At least one product must be selected',
            'products.array'                => 'Products must be in array format',
            'products.min'                  => 'At least one product is required',
            'products.*.id.required'        => 'Product ID is required for each product',
            'products.*.id.exists'          => 'One or more selected products do not exist',
            'products.*.quantity.required'  => 'Quantity is required for each product',
            'products.*.quantity.integer'   => 'Quantity must be a whole number',
            'products.*.quantity.min'       => 'Quantity must be at least 1 for each product',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()
            ];
        }

        return [
            'success' => true,
            'data' => $validator->validated()
        ];
    }

    /**
     * @param $data
     * @return mixed
     */
    public function findTheCustomer($data){
        return $customer = Customer::firstOrCreate(
                                    ['email' => $data['customer_email']],
                                    ['name' => $data['customer_name']]
                                );
    }



    /**
     * @param $amount
     * @param $taxSlab
     * @return float|int
     */
    public function calculateTax($amount, $taxSlab){
        return (($amount * $taxSlab) / 100);
    }

    /**
     * @param $invoiceId
     * @return TValue|Collection|null
     */
    private function invoiceDetails($invoiceId){
        return Invoice::with([
            'customer',
            'items.product',
            'denominationTransactions.denomination'
        ])->find($invoiceId);
    }
}
