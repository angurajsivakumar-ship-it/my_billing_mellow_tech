@extends('layout.app')

@section('content')
    <div class="max-w-7xl mx-auto p-6">

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Invoices</h2>

            <form method="GET" action="{{ route('web.invoice.list') }}" class="flex gap-2">
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search invoice / customer"
                    class="border rounded px-3 py-2 w-64"
                >

                <button
                    type="submit"
                    class="bg-blue-600 text-white px-4 rounded hover:bg-blue-700">
                    Search
                </button>

                <a
                    href="{{ route('web.invoice.list') }}"
                    class="bg-gray-200 text-gray-700 px-4 rounded hover:bg-gray-300 flex items-center justify-center"
                >
                    Reset
                </a>
            </form>
        </div>

        <div class="bg-white shadow rounded overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                <tr>
                    <th class="p-3 text-left">Invoice No</th>
                    <th class="p-3 text-left">Customer</th>
                    <th class="p-3 text-right">Total</th>
                    <th class="p-3 text-right">Paid</th>
                    <th class="p-3 text-right">Balance</th>
                    <th class="p-3 text-center">Date</th>
                    <th class="p-3 text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($invoices as $invoice)
                    <tr class="border-t">
                        <td class="p-3 font-semibold">
                            {{ $invoice->invoice_no }}
                        </td>

                        <td class="p-3">
                            {{ $invoice->customer->name ?? '—' }}<br>
                            <span class="text-gray-500 text-xs">
                                {{ $invoice->customer->email ?? '' }}
                            </span>
                        </td>

                        <td class="p-3 text-right">
                            ₹ {{ number_format($invoice->total_amount, 2) }}
                        </td>

                        <td class="p-3 text-right">
                            ₹ {{ number_format($invoice->amount_paid, 2) }}
                        </td>

                        <td class="p-3 text-right font-semibold">
                            ₹ {{ number_format($invoice->balance_returned, 2) }}
                        </td>

                        <td class="p-3 text-center">
                            {{ $invoice->created_at->format('d M Y') }}
                        </td>

                        <td class="p-3 text-center">
                            <a href="{{ route('web.invoice.generate', $invoice->id) }}"
                               target="_blank"
                               class="text-blue-600 hover:underline">
                                PDF
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-gray-500">
                            No invoices found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $invoices->links() }}
        </div>

    </div>
@endsection
