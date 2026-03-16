<?php
// app/Http/Controllers/Admin/UserController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('cards')
                     ->orderByDesc('last_login_at')
                     ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function destroy($id)
    {
        // 删除用户时级联删除其所有碎片（由 FK cascade 保证）
        User::findOrFail($id)->delete();
        return back()->with('success', '用户及其碎片已删除');
    }
}
