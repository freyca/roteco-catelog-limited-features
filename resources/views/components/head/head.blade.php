<head>
    {{-- <title>{{ $seotags->getMetaTitle() }}</title> --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <meta name="description" content="{{ $seotags->getMetaDescription() }}"> --}}

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{--
    @foreach ($seotags->getAdditionalHeaders() as $header_name => $header_value)
        <meta name="{{ $header_name }}" content="{{ $header_value }}">
    @endforeach
     --}}

    <link rel="icon" href="https://roteco.es/wp-content/uploads/2018/04/cropped-Favicon-castroteco-192x192.png"
        sizes="192x192">
    <link rel="icon" href="https://roteco.es/wp-content/uploads/2018/04/cropped-Favicon-castroteco-32x32.png"
        sizes="32x32">
    <link rel="apple-touch-icon"
        href="https://roteco.es/wp-content/uploads/2018/04/cropped-Favicon-castroteco-180x180.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="stylesheet" href="{{ asset('css/filament/filament/app.css?v=4.2.1.0') }}">

    @vite('resources/css/app.css')
    @filamentStyles
    @livewireStyles
</head>
