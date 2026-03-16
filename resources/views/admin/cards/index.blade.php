@extends('admin.layouts.app')
@section('title', '碎片管理')
@section('page-title', '碎片管理')

@section('content')

<!-- 搜索/筛选栏 -->
<form method="GET" class="row g-2 mb-4">
  <div class="col-md-5">
    <input type="text" name="keyword" class="form-control"
           placeholder="搜索内容、来源或作者…"
           value="{{ request('keyword') }}">
  </div>
  <div class="col-md-3">
    <select name="type" class="form-select">
      <option value="">全部类型</option>
      <option value="excerpt"     {{ request('type')=='excerpt'     ? 'selected' : '' }}>书摘</option>
      <option value="inspiration" {{ request('type')=='inspiration' ? 'selected' : '' }}>灵感</option>
      <option value="quote"       {{ request('type')=='quote'       ? 'selected' : '' }}>妙语</option>
    </select>
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-dark">搜索</button>
    <a href="{{ route('admin.cards.index') }}" class="btn btn-outline-secondary ms-1">重置</a>
  </div>
</form>

<!-- 碎片列表 -->
<div class="table-card">
  <div class="table-header">
    <h6>碎片列表</h6>
    <span class="text-muted" style="font-size:13px;">共 {{ $cards->total() }} 条</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th style="width:60px">ID</th>
          <th style="width:70px">类型</th>
          <th>内容</th>
          <th style="width:140px">来源 / 作者</th>
          <th style="width:90px">色调</th>
          <th style="width:120px">用户</th>
          <th style="width:130px">录入时间</th>
          <th style="width:80px">操作</th>
        </tr>
      </thead>
      <tbody>
        @forelse($cards as $card)
          <tr>
            <td class="text-muted">{{ $card->id }}</td>
            <td>
              @php
                $typeMap = ['excerpt'=>['书摘','badge-excerpt'], 'inspiration'=>['灵感','badge-inspiration'], 'quote'=>['妙语','badge-quote']];
                [$label, $cls] = $typeMap[$card->type] ?? ['未知','bg-secondary'];
              @endphp
              <span class="badge-type {{ $cls }}">{{ $label }}</span>
            </td>
            <td>
              <div style="max-width:320px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"
                   title="{{ $card->content }}">
                {{ $card->content }}
              </div>
            </td>
            <td class="text-muted" style="font-size:12px;">
              @if($card->source) <div>{{ $card->source }}</div> @endif
              @if($card->author) <div>{{ $card->author }}</div> @endif
            </td>
            <td>
              <span class="color-dot-sm" style="background:{{ $card->color }}"></span>
              <span style="font-size:11px; color:#7a7068;">{{ $card->color }}</span>
            </td>
            <td style="font-size:12px; color:#7a7068;">
              {{ optional($card->user)->nickname ?? '—' }}<br>
              <span style="font-size:11px;">{{ substr(optional($card->user)->openid ?? '', -8) }}</span>
            </td>
            <td style="font-size:12px; color:#7a7068;">
              {{ $card->created_at->format('Y-m-d H:i') }}
            </td>
            <td>
              <form method="POST" action="{{ route('admin.cards.destroy', $card->id) }}"
                    onsubmit="return confirm('确认删除这条碎片？')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">删除</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-5">暂无碎片数据</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($cards->hasPages())
    <div class="px-4 py-3 border-top">
      {{ $cards->links() }}
    </div>
  @endif
</div>

@endsection
