<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Produksi & Inventory</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
  <style>
    body {
      background: #f5f7fb;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
    }

    .login-container {
      width: 100%;
      max-width: 420px;
      padding: 20px;
    }

    .login-card {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
      overflow: hidden;
      border: 1px solid #eef2f6;
    }

    .login-header {
      padding: 22px 20px;
      text-align: center;
      color: #222;
      border-bottom: 1px solid #f1f5f9;
    }

    .login-header img {
      max-width: 120px;
      margin-bottom: 8px;
    }

    .login-header h1 {
      font-size: 20px;
      font-weight: 700;
      margin: 0;
    }

    .login-header p {
      margin: 4px 0 0;
      font-size: 13px;
      color: #555;
    }

    .login-body {
      padding: 24px 20px;
    }

    .form-group { margin-bottom: 16px; }
    .form-group label { display:block; margin-bottom:8px; font-weight:600; color:#333; font-size:13px; }
    .form-group input { width:100%; padding:10px 12px; border:1px solid #e6edf3; border-radius:8px; }
    .form-group input:focus { outline:none; box-shadow:0 0 0 4px rgba(14,165,233,0.06); border-color:#0ea5e9; }

    .form-check { margin:12px 0; }
    .form-check-input { width:18px; height:18px; margin-top:3px; }
    .form-check-label { margin-left:8px; color:#666; }

    .btn-login { width:100%; padding:10px; background:#0d6efd; color:#fff; border:none; border-radius:8px; font-weight:600; }
    .btn-login:hover { opacity:0.95; }

    .login-footer { text-align:center; padding:14px 20px 20px; }
    .login-footer p { margin:0; color:#666; font-size:13px; }
    .login-footer a { color:#0d6efd; font-weight:600; }

    .alert { margin-bottom:12px; padding:10px 12px; border-radius:8px; font-size:13px; }
    .alert-success { background:#e6ffed; color:#126644; border:1px solid #c6f6d5; }
    .alert-danger { background:#fff1f2; color:#7a1f24; border:1px solid #fecaca; }

    .icon-input { position:relative; }
    .icon-input i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#999; font-size:16px; }
    .icon-input input { padding-left:40px; }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="login-card">
      <!-- Header -->
      <div class="login-header">
        <img src="{{ asset('assets/images/logos/appLogo.png') }}" alt="Logo">
        <h1>Produksi & Inventory</h1>
        <p>Sistem Manajemen Produksi</p>
      </div>

      <!-- Body -->
      <div class="login-body">
        <!-- Success Message -->
        @if(session('success'))
        <div class="alert alert-success">
          <i class="ti ti-check-circle me-2"></i>{{ session('success') }}
        </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
          @csrf

          <!-- Username Input -->
          <div class="form-group">
            <label for="username">Username</label>
            <div class="icon-input">
              <i class="ti ti-user"></i>
              <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror"
                placeholder="Masukkan username" value="{{ old('username') }}" required autofocus>
            </div>
            @error('username')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <!-- Password Input -->
          <div class="form-group">
            <label for="password">Password</label>
            <div class="icon-input">
              <i class="ti ti-lock"></i>
              <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="Masukkan password" required>
            </div>
            @error('password')
            <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>

          <!-- Remember Me -->
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember" name="remember">
            <label class="form-check-label" for="remember">
              Ingat saya
            </label>
          </div>

          <!-- Submit Button -->
          <button type="submit" class="btn-login">
            <i class="ti ti-login me-2"></i> Masuk
          </button>
        </form>
      </div>
    </div>
  </div>

  <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>