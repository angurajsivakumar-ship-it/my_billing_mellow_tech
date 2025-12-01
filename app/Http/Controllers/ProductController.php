<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\ResponseTraits;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ResponseTraits;

    public function index(Request $request){
        $search = trim($request->get('query')) ?? "";
        $products = Product::query()->select('id', 'name', 'product_code', 'price', 'tax_percentage', 'available_stock')
            ->when(!empty($search), function($q) use($search){
                $q->where(function($subQuery) use($search) {
                    $subQuery->where('product_code', $search)
                        ->orWhere('name', $search)
                        ->orWhere('product_code', 'LIKE', "%{$search}%")
                        ->orWhere('name', 'LIKE', "%{$search}%");
                });
            })->orderByDesc('id')->limit(10)->get();

        if(!$products){
            return $this->errorResponse(null, config('common.errors.not_found'));
        }

        return $this->successResponse($products, config('common.success.simple_success'));
    }
}
