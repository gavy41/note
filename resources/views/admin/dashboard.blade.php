@extends('admin.layouts.app')
@section('title', '数据概览')
@section('page-title', '数据概览')

@push('styles')
<style>
  .chart-wrap { background:#fff; border-radius:12px; border:1px solid #e8e3dc; padding:20px 24px; }
  .chart-title { font-size:14px; font-weight:600; color:#2d2926; margin-bottom:16px; }
</style>
@endpush

@section('content')

<!-- 统计卡片 -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#e8e2d8;">📚</div>
      <div class="stat-num">{{ number_format($stats['total_cards']) }}</div>
      <div class="stat-label">碎片总数</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#dde8e2;">👤</div>
      <div class="stat-num">{{ number_format($stats['total_users']) }}</div>
      <div class="stat-label">注册用户</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#dde2e8;">✨</div>
      <div class="stat-num">{{ $stats['today_cards'] }}</div>
      <div class="stat-label">今日新增碎片</div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon" style="background:#e2dde8;">📖</div>
      <div class="stat-num">{{ $stats['type_counts']['excerpt'] ?? 0 }}</div>
      <div class="stat-label">书摘数量</div>
    </div>
  </div>
</div>

<div class="row g-3">
  <!-- 折线图：近14天新增 -->
  <div class="col-xl-8">
    <div class="chart-wrap">
      <div class="chart-title">近 14 天新增碎片</div>
      <canvas id="dailyChart" height="80"></canvas>
    </div>
  </div>

  <!-- 饼图：碎片类型分布 -->
  <div class="col-xl-4">
    <div class="chart-wrap">
      <div class="chart-title">碎片类型分布</div>
      <canvas id="typeChart" height="160"></canvas>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const dates  = @json($dates);
const counts = @json($counts);
const typeCounts = @json($stats['type_counts']);

// 折线图
new Chart(document.getElementById('dailyChart'), {
  type: 'line',
  data: {
    labels: dates,
    datasets: [{
      label: '新增碎片',
      data: counts,
      borderColor: '#8b7355',
      backgroundColor: 'rgba(139,115,85,0.08)',
      tension: 0.4,
      fill: true,
      pointRadius: 3,
      pointBackgroundColor: '#8b7355',
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { precision: 0 } }
    }
  }
});

// 饼图
new Chart(document.getElementById('typeChart'), {
  type: 'doughnut',
  data: {
    labels: ['书摘', '灵感', '妙语'],
    datasets: [{
      data: [
        typeCounts.excerpt     || 0,
        typeCounts.inspiration || 0,
        typeCounts.quote       || 0,
      ],
      backgroundColor: ['#e8e2d8', '#dde8e2', '#e2dde8'],
      borderColor:     ['#c8b99a', '#8ab5a5', '#9b90b8'],
      borderWidth: 1,
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'bottom', labels: { font: { size: 12 } } }
    }
  }
});
</script>
@endpush
