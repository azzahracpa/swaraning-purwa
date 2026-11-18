<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="description" content="@yield('deskripsi', 'Swaraning Purwa, media informasi dan edukasi digital Museum Mpu Purwa Kota Malang.')">
<meta name="theme-color" content="#3B2927">

{{-- Judul tab: dari @section('title') di halaman, atau $judulHalaman dari layout komponen --}}
<title>@hasSection('title')@yield('title') · @elseif (! empty($judulHalaman)){{ $judulHalaman }} · @endif{{ config('app.name', 'Swaraning Purwa') }}</title>

<link rel="icon" type="image/svg+xml" href="{{ asset('images/ui/tanda.svg') }}">

@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('head')
