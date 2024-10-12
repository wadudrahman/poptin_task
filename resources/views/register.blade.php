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
<body class="d-flex flex-column">
<div class="page page-center">
    <div class="container container-tight py-4">
        <div class="row row-cards">
            <div class="col-12">
                @include('partials.alert')
            </div>
        </div>
        <form class="card card-md" action="{{ route('register') }}" method="post" autocomplete="off" novalidate>
            @csrf
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Create new account</h2>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                           placeholder="Enter name">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label required">Email address</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                           placeholder="Enter email" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label required">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password"
                           placeholder="Password" autocomplete="off" required>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-footer">
                    <button type="submit" class="btn btn-primary w-100">Create Account</button>
                </div>
            </div>
        </form>
        <div class="text-center text-secondary mt-3">
            Already have account? <a href="{{ route('showLogin') }}" tabindex="-1">Sign in</a>
        </div>
    </div>
</div>
<!-- Libs JS -->
<!-- Tabler Core -->
<script src="{{ asset('js/tabler.js') }}" defer></script>
</body>
</html>
