<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\CreateAdmin::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // 定时刷新微信 access_token（可选，缓存会自动处理）
        // $schedule->command('wechat:refresh-token')->hourly();
    }

    protected function bootstrappers()
    {
        return array_merge(
            [\Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class],
            parent::bootstrappers(),
        );
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
