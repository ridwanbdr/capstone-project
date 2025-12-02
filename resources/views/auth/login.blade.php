<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Sinar Collection</title>
  <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
  <style>
    body {
      background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
      min-height: 100vh;
    }
    .login-container {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
    }
    .login-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 20px 60px rgba(33, 150, 243, 0.2);
      width: 100%;
      max-width: 450px;
      padding: 50px 40px;
    }
    .login-header {
      text-align: center;
      margin-bottom: 40px;
    }
    .brand-title {
      font-size: 32px;
      font-weight: 700;
      color: #2196f3;
      margin-bottom: 8px;
      letter-spacing: -0.5px;
    }
    .brand-subtitle {
      font-size: 14px;
      color: #1976d2;
      font-weight: 500;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-label {
      font-weight: 600;
      color: #333;
      margin-bottom: 8px;
      font-size: 14px;
      display: block;
    }
    .form-control {
      border: 2px solid #e3f2fd;
      border-radius: 8px;
      padding: 12px 15px;
      font-size: 14px;
      transition: all 0.3s ease;
      background-color: #f5f5f5;
    }
    .form-control:focus {
      border-color: #2196f3;
      box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
      outline: none;
      background-color: #fff;
    }
    .remember-section {
      display: flex;
      align-items: center;
      margin-bottom: 25px;
      margin-top: 10px;
    }
    .form-check-input {
      width: 18px;
      height: 18px;
      margin-right: 8px;
      cursor: pointer;
      border: 2px solid #2196f3;
      border-radius: 4px;
      background-color: white;
    }
    .form-check-input:checked {
      background-color: #2196f3;
      border-color: #2196f3;
    }
    .form-check-label {
      cursor: pointer;
      color: #666;
      font-size: 14px;
      margin: 0;
    }
    .btn-login {
      background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
      border: none;
      color: white;
      padding: 12px;
      border-radius: 8px;
      font-weight: 600;
      font-size: 15px;
      width: 100%;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: block;
      text-align: center;
      margin-bottom: 20px;
    }
    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(33, 150, 243, 0.4);
      color: white;
      text-decoration: none;
    }
    .signup-section {
      text-align: center;
      padding-top: 20px;
      border-top: 1px solid #e0e0e0;
    }
    .signup-text {
      color: #666;
      font-size: 14px;
    }
    .signup-link {
      color: #2196f3;
      text-decoration: none;
      font-weight: 600;
      margin-left: 5px;
      transition: all 0.3s ease;
    }
    .signup-link:hover {
      color: #1976d2;
      text-decoration: underline;
    }
    .error-alert {
      background-color: #ffebee;
      border: 1px solid #ffcdd2;
      color: #c62828;
      padding: 12px 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 13px;
    }
    .error-message {
      color: #c62828;
      font-size: 12px;
      margin-top: 5px;
      display: block;
    }
    .input-error {
      border-color: #c62828 !important;
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <h1 class="brand-title">Sinar Collection</h1>
        <p class="brand-subtitle">Sistem Manajemen Inventori</p>
      </div>

      @if ($errors->any())
        <div class="error-alert">
          <strong>Login Gagal!</strong>
          @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
          @endforeach
        </div>
      @endif

      <form method="POST" action="{{ route('login.post') }}">
        @csrf
        
        <div class="form-group">
          <label for="username" class="form-label">Username</label>
          <input type="text" class="form-control @error('username') input-error @enderror" 
                 id="username" name="username" value="{{ old('username') }}" required autofocus>
          @error('username')
            <span class="error-message">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control @error('password') input-error @enderror" 
                 id="password" name="password" required>
          @error('password')
            <span class="error-message">{{ $message }}</span>
          @enderror
        </div>

        <div class="remember-section">
          <input type="checkbox" class="form-check-input" id="remember" name="remember" value="on">
          <label for="remember" class="form-check-label">Ingat Saya</label>
        </div>

        <button type="submit" class="btn-login">Masuk</button>

        <div class="signup-section">
          <span class="signup-text">Belum punya akun?<a href="{{ route('register') }}" class="signup-link">Daftar di sini</a></span>
        </div>
      </form>
    </div>
  </div>

  <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>