<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - ChatApp</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0f0f13;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .card {
            background: #1a1a24;
            border: 1px solid #2a2a38;
            border-radius: 16px;
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 380px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 2rem;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon svg { width: 18px; height: 18px; fill: white; }

        .logo-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
        }

        h1 {
            font-size: 1.4rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.3rem;
        }

        .subtitle {
            font-size: 0.875rem;
            color: #6b6b80;
            margin-bottom: 1.75rem;
        }

        .error-box {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
            font-size: 0.85rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.25rem;
        }

        .error-box p + p { margin-top: 0.25rem; }

        .field { margin-bottom: 1rem; }

        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: #9999b0;
            margin-bottom: 0.4rem;
        }

        input {
            width: 100%;
            background: #0f0f13;
            border: 1px solid #2a2a38;
            color: #e2e2f0;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            padding: 0.7rem 0.9rem;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.2s;
        }

        input:focus { border-color: #6366f1; }
        input::placeholder { color: #3d3d50; }

        .btn {
            width: 100%;
            background: #6366f1;
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 0.5rem;
            transition: background 0.2s;
        }

        .btn:hover { background: #5254cc; }

        .footer-text {
            text-align: center;
            font-size: 0.83rem;
            color: #6b6b80;
            margin-top: 1.25rem;
        }

        .footer-text a {
            color: #818cf8;
            text-decoration: none;
            font-weight: 500;
        }

        .footer-text a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 2H4a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/>
                </svg>
            </div>
            <span class="logo-text">ChatApp</span>
        </div>

        <h1>Buat akun</h1>
        <p class="subtitle">Bergabung sekarang, gratis!</p>

        @if($errors->any())
            <div class="error-box">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="/register">
            @csrf
            <div class="field">
                <label for="name">Nama</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Nama lengkap" required>
            </div>
            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="field">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn">Daftar</button>
        </form>

        <p class="footer-text">
            Sudah punya akun? <a href="/login">Masuk</a>
        </p>
    </div>
</body>
</html>
