@extends('admin.layouts.app')
@section('title', '用户管理')
@section('page-title', '用户管理')

@section('content')

<div class="table-card">
  <div class="table-header">
    <h6>用户列表</h6>
    <span class="text-muted" style="font-size:13px;">共 {{ $users->total() }} 名用户</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th style="width:60px">ID</th>
          <th>微信昵称</th>
          <th>OpenID（后8位）</th>
          <th style="width:80px; text-align:center;">碎片数</th>
          <th style="width:150px">最后登录</th>
          <th style="width:150px">注册时间</th>
          <th style="width:100px">操作</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
          <tr>
            <td class="text-muted">{{ $user->id }}</td>
            <td>
              @if($user->avatar)
                <img src="{{ $user->avatar }}" class="rounded-circle me-2"
                     width="28" height="28" style="object-fit:cover;">
              @endif
              {{ $user->nickname ?? '—' }}
            </td>
            <td style="font-size:12px; color:#7a7068; font-family:monospace;">
              …{{ substr($user->openid, -8) }}
            </td>
            <td style="text-align:center;">
              <span class="badge bg-light text-dark border">{{ $user->cards_count }}</span>
            </td>
            <td style="font-size:12px; color:#7a7068;">
              {{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : '—' }}
            </td>
            <td style="font-size:12px; color:#7a7068;">
              {{ $user->created_at->format('Y-m-d H:i') }}
            </td>
            <td>
              <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                    onsubmit="return confirm('删除用户将同时删除其所有碎片，确认操作？')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">删除</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-5">暂无用户数据</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($users->hasPages())
    <div class="px-4 py-3 border-top">
      {{ $users->links() }}
    </div>
  @endif
</div>

@endsection
