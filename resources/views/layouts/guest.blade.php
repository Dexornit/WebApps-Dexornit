<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo-square.png') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-cream: #FFF5E6;
            --color-white: #FFFFFF;
            --color-black: #1A1A2E;
            --color-coral: #F96854;
            --color-coral-dark: #E85A48;
            --color-pastel-yellow: #FFD93D;
            --color-pastel-purple: #D4A5FF;
            --border-width: 3px;
            --border-color: var(--color-black);
            --shadow-brutal: 5px 5px 0px var(--color-black);
            --shadow-brutal-lg: 8px 8px 0px var(--color-black);
            --font-heading: 'Space Grotesk', sans-serif;
            --font-body: 'Inter', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-body);
            background: var(--color-cream);
            color: var(--color-black);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 480px;
        }

        .auth-logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .auth-logo img {
            height: 48px;
            width: auto;
            margin: 0 auto;
        }

        .auth-card {
            background: var(--color-white);
            border: var(--border-width) solid var(--border-color);
            border-radius: 16px;
            padding: 40px;
            box-shadow: var(--shadow-brutal-lg);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .auth-title {
            font-family: var(--font-heading);
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .auth-subtitle {
            color: #666;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-family: var(--font-heading);
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: var(--border-width) solid var(--border-color);
            border-radius: 10px;
            font-size: 0.95rem;
            font-family: var(--font-body);
            transition: all 0.2s ease;
            background: var(--color-white);
        }

        .form-input:focus {
            outline: none;
            box-shadow: 4px 4px 0px var(--color-coral);
            border-color: var(--color-coral);
        }

        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .form-checkbox input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            border: 2px solid var(--border-color);
        }

        .form-checkbox label {
            font-size: 0.9rem;
            cursor: pointer;
            user-select: none;
        }

        .form-error {
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 6px;
        }

        .form-actions {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: var(--font-heading);
            font-weight: 600;
            font-size: 1rem;
            padding: 14px 28px;
            border: var(--border-width) solid var(--border-color);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            width: 100%;
        }

        .btn-primary {
            background: var(--color-coral);
            color: var(--color-white);
            box-shadow: var(--shadow-brutal);
        }

        .btn-primary:hover {
            box-shadow: 3px 3px 0px var(--color-black);
            transform: translate(2px, 2px);
            background: var(--color-coral-dark);
        }

        .btn-secondary {
            background: var(--color-white);
            color: var(--color-black);
            box-shadow: var(--shadow-brutal);
        }

        .btn-secondary:hover {
            box-shadow: 3px 3px 0px var(--color-black);
            transform: translate(2px, 2px);
            background: var(--color-cream);
        }

        .auth-link {
            text-align: center;
            font-size: 0.9rem;
            color: #666;
        }

        .auth-link a {
            color: var(--color-coral);
            text-decoration: none;
            font-weight: 600;
        }

        .auth-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 14px 18px;
            border: var(--border-width) solid var(--border-color);
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 3px 3px 0px var(--color-black);
        }

        .alert-success {
            background: var(--color-pastel-green);
        }

        .alert-error {
            background: #FFB5B5;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 28px 24px;
            }

            .auth-title {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-logo">
            <a href="/">
                <img src="{{ asset('assets/images/logo-horizontal.png') }}" alt="Dexornit Store">
            </a>
        </div>

        <div class="auth-card">
            {{ $slot }}
        </div>

        <div class="auth-link" style="margin-top: 20px;">
            <a href="{{ route('home') }}">← Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
