<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="vapid-public-key" content="{{ config('services.webpush.public_key') }}" />
<link rel="manifest" href="{{ asset('manifest.webmanifest') }}" />
<meta name="theme-color" content="#18181b" />

<title>
    @if(!empty($title))
        {{ $title }} | {{ config('app.name') }}
    @else
        {{ config('app.name') }}
    @endif
</title>

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<x-livewire-alert::scripts />
