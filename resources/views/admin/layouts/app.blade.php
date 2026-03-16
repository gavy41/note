<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', '管理后台') · 碎片灵感抽屉</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <style>
    :root {
      --sidebar-w: 240px;
      --accent: #8b7355;
      --bg: #f5f2ed;
    }
    body { background: #f0ede8; font-family: 'PingFang SC', 'Microsoft YaHei', sans-serif; }

    /* Sidebar */
    .sidebar {
      position: fixed; top: 0; left: 0; bottom: 0;
      width: var(--sidebar-w);
      background: #2d2926;
      display: flex; flex-direction: column;
      z-index: 100;
    }
    .sidebar-brand {
      padding: 24px 20px 16px;
      border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .sidebar-brand h5 { color: #f5f2ed; font-weight: 600; margin: 0; font-size: 15px; letter-spacing: 1px; }
    .sidebar-brand small { color: #b0a99f; font-size: 12px; }

    .sidebar-nav { flex: 1; padding: 12px 0; overflow-y: auto; }
    .sidebar-nav .nav-label {
      font-size: 11px; color: #7a7068; letter-spacing: 2px;
      padding: 16px 20px 6px; text-transform: uppercase;
    }
    .sidebar-nav .nav-link {
      display: flex; align-items: center; gap: 10px;
      padding: 10px 20px; color: #b0a99f;
      font-size: 14px; border-radius: 0; transition: all .15s;
      border-left: 3px solid transparent;
    }
    .sidebar-nav .nav-link:hover { color: #f5f2ed; background: rgba(255,255,255,0.05); }
    .sidebar-nav .nav-link.active { color: #f5f2ed; background: rgba(139,115,85,0.2); border-left-color: var(--accent); }
    .sidebar-nav .nav-link i { font-size: 16px; width: 20px; }

    .sidebar-footer {
      padding: 16px 20px;
      border-top: 1px solid rgba(255,255,255,0.08);
      font-size: 13px; color: #7a7068;
    }

    /* Main */
    .main-wrap { margin-left: var(--sidebar-w); min-height: 100vh; }
    .topbar {
      background: #fff; border-bottom: 1px solid #e8e3dc;
      padding: 0 28px; height: 56px;
      display: flex; align-items: center; justify-content: space-between;
      position: sticky; top: 0; z-index: 50;
    }
    .topbar .page-title { font-size: 16px; font-weight: 600; color: #2d2926; }
    .topbar .admin-info { font-size: 13px; color: #7a7068; }

    .content-area { padding: 28px; }

    /* Cards */
    .stat-card {
      background: #fff; border-radius: 12px;
      padding: 24px; border: 1px solid #e8e3dc;
      transition: box-shadow .2s;
    }
    .stat-card:hover { box-shadow: 0 4px 20px rgba(45,41,38,0.08); }
    .stat-card .stat-icon {
      width: 48px; height: 48px; border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 22px; margin-bottom: 16px;
    }
    .stat-card .stat-num { font-size: 32px; font-weight: 700; color: #2d2926; line-height: 1; }
    .stat-card .stat-label { font-size: 13px; color: #7a7068; margin-top: 6px; }

    .table-card { background: #fff; border-radius: 12px; border: 1px solid #e8e3dc; overflow: hidden; }
    .table-card .table-header {
      padding: 16px 20px; border-bottom: 1px solid #e8e3dc;
      display: flex; align-items: center; justify-content: space-between;
    }
    .table-card .table-header h6 { margin: 0; font-weight: 600; color: #2d2926; }
    .table-card .table { margin: 0; }
    .table-card .table th { font-size: 12px; color: #7a7068; font-weight: 500; letter-spacing: .5px; border-bottom-width: 1px; }
    .table-card .table td { font-size: 13px; vertical-align: middle; }

    .badge-type {
      font-size: 11px; padding: 3px 8px; border-radius: 20px; font-weight: 500;
    }
    .badge-excerpt     { background: #e8e2d8; color: #6b5c45; }
    .badge-inspiration { background: #dde8e2; color: #3d6b55; }
    .badge-quote       { background: #e2dde8; color: #5a4d6b; }

    .color-dot-sm {
      width: 14px; height: 14px; border-radius: 50%;
      display: inline-block; border: 1px solid rgba(0,0,0,0.08);
      vertical-align: middle; margin-right: 6px;
    }
  </style>
  @stack('styles')
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
  <div class="sidebar-brand">
    <h5>碎片灵感抽屉</h5>
    <small>管理后台</small>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-label">概览</div>
    <a href="{{ route('admin.dashboard') }}"
       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <i class="bi bi-grid-1x2"></i> 数据概览
    </a>
    <div class="nav-label">内容管理</div>
    <a href="{{ route('admin.cards.index') }}"
       class="nav-link {{ request()->routeIs('admin.cards.*') ? 'active' : '' }}">
      <i class="bi bi-card-text"></i> 碎片管理
    </a>
    <a href="{{ route('admin.users.index') }}"
       class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
      <i class="bi bi-people"></i> 用户管理
    </a>
  </nav>
  <div class="sidebar-footer">
    <form action="{{ route('admin.logout') }}" method="POST">
      @csrf
      <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
        <i class="bi bi-box-arrow-left"></i> 退出登录
      </button>
    </form>
  </div>
</aside>

<!-- Main -->
<div class="main-wrap">
  <div class="topbar">
    <span class="page-title">@yield('page-title', '概览')</span>
    <span class="admin-info">
      <i class="bi bi-person-circle me-1"></i>{{ session('admin_name', '管理员') }}
    </span>
  </div>
  <div class="content-area">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @yield('content')
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
