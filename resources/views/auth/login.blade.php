<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Login Sistem Absensi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/png" href="{{ asset('image/logo_ma.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <style>
        /* Menggunakan Bootstrap Icons untuk ikon mata */
        @import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css");

        body {
            /* Pastikan path gambar benar: /image/ma.jpg */
            background: url('/image/ma.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            /* Filter blur dan overlay lebih profesional */
            background-color: rgba(47, 147, 17, 0.616);
            backdrop-filter: blur(5px);
            z-index: 0;
        }
        .login-container {
            max-width: 420px; /* Ditingkatkan sedikit */
            width: 90%;
            padding: 35px;
            background: rgba(255, 255, 255, 0.693); /* Lebih solid */
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
            position: relative;
            z-index: 1;
            margin: auto; /* Memastikan centering vertikal dan horizontal */
        }
        .logo {
            width: 100px;
            height: 100px;
            /* Pastikan path logo benar: /image/logo_ma.png */
            background: url('/image/logo_ma.png') no-repeat center center;
            background-size: contain;
            margin: 0 auto 15px;
        }
        .toggle-password {
            cursor: pointer;
            user-select: none;
            color: #777;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            padding: 5px;
        }
        .welcome-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: #343a40;
            text-align: center;
            margin-bottom: 2rem;
        }
        .form-label {
            font-weight: 600;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo"></div>
        <h3 class="text-center fw-bold text-success mb-2">SISTEM PRESENSI</h3>
        <div class="welcome-text">  MA NURUL IMAN</div>

        @if(session('success'))
            <div class="alert alert-success p-2 small">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger p-2 small">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.process') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" id="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus placeholder="Masukkan email Anda" />
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Kata Sandi</label>
                <div class="input-group">
                    <input type="password" id="password" name="password" class="form-control" required placeholder="Masukkan kata sandi" />
                    <span class="input-group-text toggle-password" onclick="togglePassword()">
                         <i class="bi bi-eye-fill" id="pwd-icon"></i>
                    </span>
                </div>
            </div>
            <button type="submit" class="btn btn-success w-100 fw-bold py-2">SIGNIN</button>
        </form>

        {{-- Menghapus bagian Register dan Login Google --}}

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const pwdInput = document.getElementById('password');
            const icon = document.getElementById('pwd-icon');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                icon.classList.remove('bi-eye-fill');
                icon.classList.add('bi-eye-slash-fill');
            } else {
                pwdInput.type = 'password';
                icon.classList.remove('bi-eye-slash-fill');
                icon.classList.add('bi-eye-fill');
            }
        }
    </script>
</body>
</html>
