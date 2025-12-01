@extends('layout.app')

@section('content')
    <div class="max-w-6xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">New Bill</h2>
            <p class="text-gray-600">Enter customer details and add products</p>
        </div>

        <!-- Customer Information Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Customer Information</h3>

            <form id="customerForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Customer Email -->
                    <div>
                        <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Customer Email <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="customer_email"
                            name="customer_email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter customer email"
                            required
                        >
                    </div>

                    <!-- Customer Name -->
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Customer Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="customer_name"
                            name="customer_name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Enter customer name"
                            required
                        >
                    </div>
                </div>
            </form>
        </div>

        <!-- Bill Section Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Bill Section</h3>
                <button
                    id="addProductBtn"
                    type="button"
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center transition duration-200"
                >
                    <i class="fas fa-plus mr-2"></i>
                    Add New Product
                </button>
            </div>

            <!-- Products Table -->
            <div class="overflow-x-auto">
                <table class="w-full" id="productsTable">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tax</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="productsTableBody">
                    <!-- Products will be added here dynamically -->
                    <tr id="noProductsRow">
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-shopping-cart text-4xl mb-2 block"></i>
                            No products added yet
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="text-sm text-gray-700">
                            <div class="flex justify-between"><span>Total Price without Tax :</span><span>₹ <strong id="total_without_tax">0.00</strong></span></div>
                            <div class="flex justify-between"><span>Total Tax Payable :</span><span>₹ <strong id="total_tax">0.00</strong></span></div>
                            <div class="flex justify-between"><span>Net Price of Purchased Items :</span><span>₹ <strong id="net_total">0.00</strong></span></div>
                            <div class="flex justify-between"><span>Rounded Net Price :</span><span>₹ <strong id="rounded_total">0.00</strong></span></div>
                            <div class="flex justify-between"><span>Balance Payable to the Customer :</span><span>₹ <strong id="balance">—</strong></span></div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-4">
                        <h4 class="font-semibold mb-2">Balance Denomination :</h4>
                        <div id="denomination" class="text-gray-700"></div>
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <div class="bg-gray-100 rounded px-6 py-3 text-lg font-semibold">
                        Customer Payable: ₹ <span id="grandTotal">0.00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Product Modal -->
        <div id="addProductModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-xl font-semibold mb-4">Add Product</h3>

                <form id="addProductForm">
                    <div class="space-y-4">
                        {{--<div>
                            <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">Product ID</label>
                            <select
                                id="product_id"
                                name="product_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                                <option value="">Select a product</option>
                                <!-- Products will be populated dynamically -->
                            </select>
                        </div>--}}

                        <div>
                            <label class="block text-sm font-medium mb-1">Product Code</label>
                            <input
                                type="text"
                                id="product_code"
                                class="w-full border px-3 py-2 rounded"
                                placeholder="Search product code or name"
                                autocomplete="off"
                            >
                            <ul id="product_suggestions"
                                class="border bg-white mt-1 max-h-40 overflow-y-auto hidden rounded shadow">
                            </ul>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Quantity</label>
                            <input
                                type="number"
                                id="quantity"
                                min="1"
                                value="1"
                                class="w-full border px-3 py-2 rounded"
                            >
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <button
                            type="button"
                            id="cancelAddProduct"
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 transition duration-200"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200"
                        >
                            Add Product
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Cash Payment Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Payment Information</h3>

            <div>
                <label for="cash_paid" class="block text-sm font-medium text-gray-700 mb-2">
                    Cash Paid by Customer <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    id="cash_paid"
                    name="cash_paid"
                    step="0.01"
                    min="0"
                    class="w-full md:w-1/2 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="0.00"
                    required
                >
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end items-center bg-white rounded-lg shadow-md p-6">
            {{--<button
                id="cancelBtn"
                class="bg-red-500 hover:bg-red-600 text-white px-8 py-3 rounded-lg font-semibold transition duration-200 flex items-center"
            >
                <i class="fas fa-times mr-2"></i>
                Cancel
            </button>--}}

            <button id="generateBillBtn" class="bg-blue-500 hover:bg-blue-600
        text-white px-8 py-3 rounded-lg font-semibold transition duration-200 flex items-center">
                <i class="fas fa-receipt mr-2"></i>
                Generate Bill
            </button>
        </div>
    </div>
@endsection

@section('after-scripts-end')
    <script src="{{ asset('assets/js/create-bill.js') }}"></script>
@endsection
