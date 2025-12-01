<!DOCTYPE html>
<html lang="en">
<head>
    @yield('meta')
    @yield('before-styles-end')
        @include('global.partials.meta')
    @yield('after-styles-end')
    @vite(['resources/js/app.js'])
</head>
<body class="bg-gray-50">
<header class="bg-blue-600 text-white shadow-lg">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between">
        <h1 class="text-xl font-bold tracking-wider">
            <a href="{{ route('web.billing.create') }}" class="hover:text-blue-200 transition">
                MallowTech Billing
            </a>
        </h1>

        <nav>
            <a href="{{ route('web.invoice.list') }}" class="text-lg font-medium hover:text-blue-300 transition duration-150">
                Invoices
            </a>
        </nav>
    </div>
</header>
<main class="container mx-auto px-4 py-8">
    @yield('content')
</main>
<footer class="bg-gray-800 text-white mt-12">
    <div class="container mx-auto px-4 py-6 text-center">
        <p>&copy; {{ date('Y') ?? '2025' }} My Billing System. All rights reserved.</p>
    </div>
</footer>

@yield('before-scripts-end')
    @include('global.partials.scripts')
@yield('after-scripts-end')
</body>
</html>
