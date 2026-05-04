<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'SentosaQuiz') : config('app.name', 'SentosaQuiz') }}
</title>

<link rel="icon" href="{{ asset('images/logo.webp') }}" type="image/webp">
<link rel="apple-touch-icon" href="{{ asset('images/logo.webp') }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])

{{-- Force light mode: override any system dark preference --}}
<script>document.documentElement.classList.remove('dark');</script>
