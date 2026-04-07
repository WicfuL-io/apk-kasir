<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Register Kasir</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

body{
font-family:Poppins;
height:100vh;
background:linear-gradient(135deg,#4f46e5,#7c3aed);
display:flex;
align-items:center;
justify-content:center;
}

.register-card{
background:rgba(255,255,255,0.1);
backdrop-filter:blur(20px);
border-radius:15px;
padding:40px;
width:100%;
max-width:450px;
color:white;
box-shadow:0 10px 30px rgba(0,0,0,0.3);
}

.form-control{
background:rgba(255,255,255,0.2);
border:none;
color:white;
}

.form-control::placeholder{
color:#ddd;
}

.form-control:focus{
background:rgba(255,255,255,0.3);
box-shadow:none;
color:white;
}

.btn-register{
background:#10b981;
border:none;
font-weight:500;
}

.btn-register:hover{
background:#059669;
}

.title{
font-size:28px;
font-weight:600;
}

.subtitle{
font-size:14px;
color:#ddd;
}

.error-text{
color:#ff6b6b;
font-size:12px;
margin-top:5px;
}

</style>

</head>

<body>

<div class="register-card">

<div class="text-center mb-4">
<div class="title">Kasir App</div>
<div class="subtitle">Buat akun baru</div>
</div>

{{-- GLOBAL ERROR --}}
@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li style="font-size:12px;">{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('register') }}">
@csrf

<div class="mb-3">
<input type="text"
name="name"
class="form-control"
placeholder="Nama"
value="{{ old('name') }}"
required>

@error('name')
<div class="error-text">{{ $message }}</div>
@enderror
</div>

<div class="mb-3">
<input type="email"
name="email"
class="form-control"
placeholder="Email"
value="{{ old('email') }}"
required>

@error('email')
<div class="error-text">{{ $message }}</div>
@enderror
</div>

<div class="mb-3">
<input type="password"
name="password"
class="form-control"
placeholder="Password"
required>

@error('password')
<div class="error-text">{{ $message }}</div>
@enderror
</div>

<div class="mb-3">
<input type="password"
name="password_confirmation"
class="form-control"
placeholder="Konfirmasi Password"
required>
</div>

<div class="d-grid mb-3">
<button class="btn btn-register">
Register
</button>
</div>

<div class="text-center">
<a href="{{ route('login') }}" style="color:white;text-decoration:none">
Sudah punya akun? Login
</a>
</div>

</form>

</div>

</body>
</html>