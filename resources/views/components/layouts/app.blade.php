@props([
    'title' => null,
    'description' => 'Bolsa de trabajo y vinculación empresarial de la Universidad Politécnica de San Luis Potosí.',
])

<!DOCTYPE html>
<html lang="es" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $description }}">
    <meta name="theme-color" content="#0b3c7a">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('upslp.institucion.sistema') }} · {{ config('upslp.institucion.siglas') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml">
    <link rel="sitemap" type="application/xml" href="{{ route('sitemap') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col bg-slate-50 font-sans text-slate-800 antialiased">
    <a href="#contenido" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow">
        Saltar al contenido
    </a>

    <x-navbar />

    <main id="contenido" class="flex-1">
        <x-flash />

        {{ $slot }}
    </main>

    <x-footer />
</body>
</html>
