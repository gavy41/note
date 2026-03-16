<?php
// app/Console/Commands/CreateAdmin.php
// 用法: php artisan admin:create

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    protected $signature   = 'admin:create';
    protected $description = '创建管理后台账号';

    public function handle()
    {
        $name     = $this->ask('管理员名称');
        $email    = $this->ask('邮箱');
        $password = $this->secret('密码');

        if (Admin::where('email', $email)->exists()) {
            $this->error("邮箱 {$email} 已存在");
            return 1;
        }

        Admin::create([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
        ]);

        $this->info("管理员 [{$name}] 创建成功，请访问 /admin/login 登录");
        return 0;
    }
}
