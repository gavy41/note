<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>登录 · 碎片灵感抽屉管理后台</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      background: #f0ede8;
      min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      font-family: 'PingFang SC', 'Microsoft YaHei', sans-serif;
    }
    .login-card {
      width: 400px;
      background: #fff;
      border-radius: 16px;
      padding: 48px 40px;
      box-shadow: 0 8px 40px rgba(45,41,38,0.10);
    }
    .login-logo { font-size: 28px; font-weight: 700; color: #2d2926; letter-spacing: 2px; margin-bottom: 6px; }
    .login-sub  { font-size: 13px; color: #b0a99f; margin-bottom: 36px; }
    .form-label { font-size: 13px; color: #7a7068; font-weight: 500; }
    .form-control {
      border-radius: 10px; border-color: #e8e3dc;
      padding: 10px 14px; font-size: 14px;
    }
    .form-control:focus { border-color: #8b7355; box-shadow: 0 0 0 3px rgba(139,115,85,0.12); }
    .btn-login {
      background: #2d2926; color: #f5f2ed;
      border: none; border-radius: 10px;
      padding: 12px; font-size: 15px; font-weight: 500;
      width: 100%; transition: opacity .15s;
    }
    .btn-login:hover { opacity: .85; color: #f5f2ed; }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-logo">碎片灵感抽屉</div>
    <div class="login-sub">管理后台 · 请登录</div>

    @if(session('error'))
      <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:13px; border-radius:8px;">
        {{ session('error') }}
      </div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}">
      @csrf
      <div class="mb-3">
        <label class="form-label">邮箱</label>
        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}" placeholder="admin@example.com" autofocus>
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div class="mb-4">
        <label class="form-label">密码</label>
        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
               placeholder="••••••••">
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <button type="submit" class="btn-login">登 录</button>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
