<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_cards' => Card::count(),
            'today_cards' => Card::whereDate('created_at', today())->count(),
            'type_counts' => Card::select('type', DB::raw('count(*) as cnt'))
                                 ->groupBy('type')
                                 ->pluck('cnt', 'type'),
        ];

        // 近 14 天每日新增碎片
        $daily = Card::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as cnt')
            )
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $dates = [];
        $counts = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $dates[]  = now()->subDays($i)->format('m/d');
            $counts[] = $daily->get($d)->cnt ?? 0;
        }

        return view('admin.dashboard', compact('stats', 'dates', 'counts'));
    }
}
