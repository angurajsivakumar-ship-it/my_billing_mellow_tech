<meta charset="utf-8" />
<meta name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

<title>{{ isset($title) ? $title : 'My Billing' }}</title>

<meta name="description" content="" />
<meta name="csrf_token" content="{{ csrf_token() }}">

<meta name="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<meta name="_token" content="{{ csrf_token() }}" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="expires" content="-1" />
<meta name="baseUrl" content="{{ url('/') }}" />

<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.png') }}" />

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
    rel="stylesheet" />

{{--<script src="https://cdn.tailwindcss.com"></script>--}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@vite('resources/css/app.css')
