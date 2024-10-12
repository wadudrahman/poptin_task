<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta19
* @link https://tabler.io
* Copyright 2018-2023 The Tabler Authors
* Copyright 2018-2023 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, maximum-scale=1, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>{{ env('APP_NAME') }} - @yield('title')</title>
    <!-- CSS files -->
    <link href="{{ asset('css/tabler.css') }}" rel="stylesheet"/>
    <style>
        @import url('https://rsms.me/inter/inter.css');

        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>
    <!-- Additional Stylesheets -->
    @stack('stylesheets')
</head>
<body class="d-flex flex-column">

<div class="page">
    <!-- Header Section -->
    @include('layouts.header')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        @yield('content')
        <!-- Footer Section -->
        @include('layouts.footer')
    </div>
</div>

<!-- Libs JS -->
<!-- Tabler Core -->
<script src="{{ asset('js/tabler.js') }}" defer></script>
<!-- Additional Scripts -->
@stack('scripts')

</body>
</html>
