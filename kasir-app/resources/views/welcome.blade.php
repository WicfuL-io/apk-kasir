<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ config('app.name') }}</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

body{
font-family:Poppins;
background:linear-gradient(135deg,#4f46e5,#7c3aed);
min-height:100vh;
color:white;
}

.navbar{
background:transparent;
}

.hero{
height:90vh;
display:flex;
align-items:center;
}

.btn-login{
background:#10b981;
border:none;
}

.btn-login:hover{
background:#059669;
}

.btn-outline-light:hover{
color:black;
}

</style>

</head>

<body>

<nav class="navbar navbar-expand-lg">

<div class="container">

<a class="navbar-brand text-white fw-bold">
{{ config('app.name') }}
</a>

<div class="ms-auto">

@if (Route::has('login'))

@auth

<a href="{{ url('/dashboard') }}" class="btn btn-light">

Dashboard

</a>

@else

<a href="{{ route('login') }}" class="btn btn-outline-light me-2">

Login

</a>

@if (Route::has('register'))

<a href="{{ route('register') }}" class="btn btn-login">

Register

</a>

@endif

@endauth

@endif

</div>

</div>

</nav>

<div class="container hero">

<div class="row w-100 align-items-center">

<div class="col-lg-6">

<h1 class="display-4 fw-bold">

{{ config('app.name') }}

</h1>

<p class="lead mt-3">

Dengan {{ config('app.name') }}, semua kebutuhan toko Anda jadi lebih mudah. Kelola penjualan dengan cepat, pantau stok barang tanpa ribet, dan lihat laporan bisnis kapan saja dalam satu sistem yang praktis dan terintegrasi. Solusi tepat untuk membantu bisnis Anda berjalan lebih efisien dan berkembang lebih pesat.

</p>

<div class="mt-4">

<a href="{{ route('login') }}" class="btn btn-login btn-lg me-2">

Mulai Sekarang

</a>

<a href="#" class="btn btn-outline-light btn-lg">

Pelajari

</a>

</div>

</div>

<div class="col-lg-6 text-center">

<img src="https://cdn-icons-png.flaticon.com/512/3075/3075977.png"
width="300">

</div>

</div>

</div>

<footer class="text-center pb-4">

<p>

© {{ date('Y') }} {{ config('app.name') }} • All rights reserved.

</p>

</footer>

</body>

</html>