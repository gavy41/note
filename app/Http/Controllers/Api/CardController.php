<?php
// app/Http/Controllers/Api/CardController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    /**
     * GET /api/cards
     * 查询参数: ?date=2026-03-13（按日期筛选）
     *           ?type=excerpt|inspiration|quote（按类型筛选）
     */
    public function index(Request $request)
    {
        $query = Card::where('user_id', Auth::id())
                     ->orderBy('created_at', 'desc');

        // 按日期筛选
        if ($date = $request->query('date')) {
            $query->whereDate('created_at', $date);
        }

        // 按类型筛选
        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        return response()->json(['data' => $query->get()]);
    }

    /**
     * GET /api/cards/date-set
     * 返回该用户有碎片的所有日期（用于日历标记）
     */
    public function dateSet()
    {
        $dates = Card::where('user_id', Auth::id())
            ->selectRaw('DATE(created_at) as date')
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('date');

        return response()->json(['data' => $dates]);
    }

    /**
     * GET /api/cards/{id}
     */
    public function show($id)
    {
        $card = Card::where('user_id', Auth::id())->findOrFail($id);
        return response()->json(['data' => $card]);
    }

    /**
     * POST /api/cards
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'    => 'required|in:excerpt,inspiration,quote',
            'content' => 'required|string|max:2000',
            'source'  => 'nullable|string|max:255',
            'author'  => 'nullable|string|max:128',
            'color'   => 'nullable|string|max:32',
        ]);

        $card = Card::create([
            'user_id' => Auth::id(),
            'type'    => $validated['type'],
            'content' => $validated['content'],
            'source'  => $validated['source'] ?? null,
            'author'  => $validated['author'] ?? null,
            'color'   => $validated['color'] ?? '#e8e2d8',
        ]);

        return response()->json(['data' => $card], 201);
    }

    /**
     * PUT /api/cards/{id}
     */
    public function update(Request $request, $id)
    {
        $card = Card::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'content' => 'sometimes|string|max:2000',
            'source'  => 'nullable|string|max:255',
            'author'  => 'nullable|string|max:128',
            'color'   => 'nullable|string|max:32',
        ]);

        $card->update($validated);

        return response()->json(['data' => $card]);
    }

    /**
     * DELETE /api/cards/{id}
     */
    public function destroy($id)
    {
        $card = Card::where('user_id', Auth::id())->findOrFail($id);
        $card->delete();

        return response()->json(['data' => null], 200);
    }
}
