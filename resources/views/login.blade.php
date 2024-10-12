<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta21
* @link https://tabler.io
* Copyright 2018-2024 The Tabler Authors
* Copyright 2018-2024 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>{{ env('APP_NAME') }}</title>
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
</head>
<body class=" d-flex flex-column">
<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="row row-cards">
            <div class="col-12">
                @include('partials.alert')
            </div>
        </div>
        <div class="card card-md">
            <div class="card-body">
                <h2 class="h2 text-center mb-4">Login to your account</h2>
                <form action="{{ route('login') }}" method="post" autocomplete="off" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label required">Email address</label>
                        <input type="email" class="form-control" name="email" placeholder="your@email.com"
                               autocomplete="off" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label required">Password</label>
                        <div class="input-group input-group-flat">
                            <input type="password" class="form-control" name="password" placeholder="Your password"
                                   autocomplete="off" required>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn btn-primary w-100">Sign in</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="text-center text-secondary mt-3">
            Don't have account yet? <a href="{{ route('showRegister') }}" tabindex="-1">Sign up</a>
        </div>
    </div>
</div>
<!-- Libs JS -->
<!-- Tabler Core -->
<script src="{{ asset('js/tabler.js') }}" defer></script>
</body>
</html>
