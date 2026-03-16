<?php
// app/Http/Controllers/Admin/CardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index(Request $request)
    {
        $query = Card::with('user')->latest();

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }
        if ($keyword = $request->query('keyword')) {
            $query->where(function ($q) use ($keyword) {
                $q->where('content', 'like', "%{$keyword}%")
                  ->orWhere('source', 'like', "%{$keyword}%")
                  ->orWhere('author', 'like', "%{$keyword}%");
            });
        }

        $cards = $query->paginate(20)->withQueryString();

        return view('admin.cards.index', compact('cards'));
    }

    public function destroy($id)
    {
        Card::findOrFail($id)->delete();
        return back()->with('success', '碎片已删除');
    }
}
