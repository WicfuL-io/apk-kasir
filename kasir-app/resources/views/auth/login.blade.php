<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Login Kasir</title>

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

.login-card{
background:rgba(255,255,255,0.1);
backdrop-filter:blur(20px);
border-radius:15px;
padding:40px;
width:100%;
max-width:420px;
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

.login-btn{
background:#10b981;
border:none;
font-weight:500;
}

.login-btn:hover{
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

</style>

</head>


<body>

<div class="login-card">

<div class="text-center mb-4">

<div class="title">Kasir App</div>
<div class="subtitle">Login untuk melanjutkan</div>

</div>

@if(session('status'))
<div class="alert alert-success">
{{ session('status') }}
</div>
@endif

<form method="POST" action="{{ route('login') }}">

@csrf

<div class="mb-3">

<input type="email"
name="email"
class="form-control"
placeholder="Email"
required autofocus>

</div>

<div class="mb-3">

<input type="password"
name="password"
class="form-control"
placeholder="Password"
required>

</div>

<div class="form-check mb-3">

<input class="form-check-input" type="checkbox" name="remember" id="remember">

<label class="form-check-label" for="remember">
Remember me
</label>

</div>

<div class="d-grid mb-3">

<button class="btn login-btn">

Login

</button>

</div>

<div class="text-center">

<a href="{{ route('register') }}" style="color:white;text-decoration:none">

Belum punya akun? Register

</a>

</div>

</form>

</div>

</body>

</html>