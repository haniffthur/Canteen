<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem Kantin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #fefce8, #f0fdf4);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-card {
            background: #ffffff;
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            max-width: 420px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .login-title {
            font-size: 26px;
            font-weight: bold;
            color: #2f3e46;
        }

        .login-subtitle {
            font-size: 14px;
            color: #6c757d;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #ced4da;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #16a34a;
            box-shadow: 0 0 0 0.2rem rgba(22, 163, 74, 0.2);
        }

        .btn-login {
            background-color: #16a34a;
            color: white;
            font-weight: 600;
            padding: 12px;
            border-radius: 12px;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-login:hover {
            background-color: #15803d;
        }

        .food-emoji {
            font-size: 36px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="food-emoji">🍽️</div>
            <h2 class="login-title">Sistem Kantin</h2>
            <p class="login-subtitle">Masukkan kredensial administrator untuk masuk ke panel</p>
        </div>
        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" name="email" id="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autofocus>
                @error('email')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-login">🍱 Masuk</button>
            </div>
        </form>
    </div>
</body>
</html>
