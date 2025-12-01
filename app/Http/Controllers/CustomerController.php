<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Traits\ResponseTraits;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    use ResponseTraits;

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request){
        $search = trim($request->get('query')) ?? "";

        if(empty($search)){
            return $this->errorResponse(null, config('common.errors.search_string_not_found'));
        }

        $customer = Customer::query()
            ->select('id', 'name', 'email', 'mobile_no', 'created_at')
            ->when(!empty($search), function($q) use($search){
                $q->where(function($subQuery) use($search) {
                    $subQuery->where('name', $search)
                        ->orWhere('email', $search)
                        ->orWhere('mobile_no', $search);
                });
            })
            ->first();

        if(!$customer){
            return $this->errorResponse(null, config('common.errors.not_found'));
        }

        return $this->successResponse($customer, config('common.success.simple_success'));
    }
}
