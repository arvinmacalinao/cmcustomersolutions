<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use DB;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\Inspire::class,
        Commands\MasterReport::class,
        Commands\MasterReportV2::class,
        Commands\DailyDeviceMovementReport::class,
        Commands\MonthlyDeviceMovementReport::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('inspire')->hourly();
        
        $schedule->call(function () {
            // Update customers' device warranty status
            $today = Carbon::today('Asia/Manila');
            $param = [
                'warranty_status' => 2,
                'updated_by' => 1,
                'updated_at' => $today
            ];

            //DB::enableQueryLog();
            DB::table('device_registrations')->where('warranty_status', 1)->where('warranty_date', '<', $today)->update($param);
            //dd(DB::getQueryLog());
        })->dailyAt('02:10');

        //$schedule->command('report:master')->dailyAt('00:01');
        $schedule->command('report:master-v2')->dailyAt('00:14');
        $schedule->command('report:dailydevicemovement')->dailyAt('03:00');
        $schedule->command('report:monthlydevicemovement')->monthly();
    }
}
