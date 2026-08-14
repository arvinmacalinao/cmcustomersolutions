<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Excel;
use File;
use DB;

use App\Report;

class MonthlyDeviceMovementReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:monthlydevicemovement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Monthly Device Movement Report Which Track Job Movement Between Branches';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$yesterday = Carbon::now('Asia/Manila')->subDay();
        /*$report_month = Carbon::now('Asia/Manila')->month;*/

        $begin_month = Carbon::now('Asia/Manila')->subDay()->startOfMonth();
        $end_month = Carbon::now('Asia/Manila')->subDay()->endOfMonth();

        $report_type = "monthly_device_receive_release_report";
        //$report_dir = public_path('reports\monthly_device_receive_release\\'); // local path
        $report_dir = public_path('reports/monthly_device_receive_release_report/'); // server path
        $report_file_name = "monthly-device-receive-release-report-" . $begin_month->format('Ym');
        $report_name = "Monthly Device Receive Release Report for " . $end_month->format('F Y');
        $excel_tab_name = "Report at " . $end_month->format('F Y');
        // $excel_tab_name = "Report at " . $end_month->toFormattedDateString();
        $report_loc = $report_dir . $report_file_name;
        $report_status = true;
        //dd($report_name);

        // Check whether dir exist
        if ( !is_dir($report_dir) ) {
            File::makeDirectory($report_dir, $mode = 0771, true, true);
        }

        if( !file_exists($report_loc) ) {
            $creator_id = 1;
            /*SELECT company_from.company_name as 'company_from', company_to.company_name as 'company_to', COUNT(logistics.id) as 'total'
            FROM cdu.logistics
            LEFT JOIN cdu.job_logistic ON logistics.id = job_logistic.logistic_id
            LEFT JOIN cdu.companies company_from ON company_from.id = logistics.company_from 
            LEFT JOIN cdu.companies company_to  ON company_to.id = logistics.company_to 
            GROUP BY logistics.id;*/
            
            $raw_logistics = DB::table('logistics')
                                ->select([
                                    /*'logistics.id',*/
                                    DB::raw('company_from.company_name AS company_from'),
                                    DB::raw('company_to.company_name AS company_to'),
                                    DB::raw('COUNT(logistics.id) AS total')
                                ])
                                ->leftJoin('job_logistic', 'logistics.id', '=', 'job_logistic.logistic_id')
                                ->leftJoin(DB::raw('companies company_from'), 'logistics.company_from', '=', 'company_from.id')
                                ->leftJoin(DB::raw('companies company_to'), 'logistics.company_to', '=', 'company_to.id')
                                ->groupBy('logistics.id')
                                ->whereDate('logistics.created_at', '>=', $begin_month->toDateString())
                                ->whereDate('logistics.created_at', '<=', $end_month->toDateString())
                                ->get();
            
            if (count($raw_logistics) == 0) {
                return;
            }

            /*DB::enableQueryLog();
            dd(DB::getQueryLog());*/
            
            $report_id = Report::logReport($report_name, $report_type, $report_dir, $report_file_name, $creator_id);
            
            foreach ($raw_logistics as $key => $raw_logistic) {
                /*isset($logistics[$raw_logistic->company_from]['received']) ?: $logistics[$raw_logistic->company_from]['received'] = 0;
                isset($logistics[$raw_logistic->company_from]['released']) ? $logistics[$raw_logistic->company_from]['released'] + $raw_logistic->total : $logistics[$raw_logistic->company_from]['released'] = $raw_logistic->total;
                isset($logistics[$raw_logistic->company_to]['received']) ? $logistics[$raw_logistic->company_to]['received'] + $raw_logistic->total : $logistics[$raw_logistic->company_to]['received'] = $raw_logistic->total;
                isset($logistics[$raw_logistic->company_to]['released']) ?: $logistics[$raw_logistic->company_to]['released'] = 0;*/
                isset($logistics[$raw_logistic->company_from]['released']) ?: $logistics[$raw_logistic->company_from]['released'] = 0;
                isset($logistics[$raw_logistic->company_from]['received']) ?: $logistics[$raw_logistic->company_from]['received'] = 0;
                isset($logistics[$raw_logistic->company_to]['received']) ?: $logistics[$raw_logistic->company_to]['received'] = 0;
                isset($logistics[$raw_logistic->company_to]['released']) ?: $logistics[$raw_logistic->company_to]['released'] = 0;

                $logistics[$raw_logistic->company_from]['released']++;
                $logistics[$raw_logistic->company_to]['received']++;
            }     

            $i = 0;
            foreach ($logistics as $key => $logistic) {
                $final_logistics[$i]['Branch'] = $key;
                $final_logistics[$i]['Monthly Received'] = $logistic['received'];
                //$final_logistics[$i]['Monthly Received'] = (string) $final_logistics[$i]['Monthly Received'];
                $final_logistics[$i]['Monthly Released'] = $logistic['released'];
                //$final_logistics[$i]['Monthly Released'] = (string) $final_logistics[$i]['Monthly Released'];
                $final_logistics[$i]['Monthly Transaction'] = $logistic['received'] + $logistic['released'];
                $i++;
            }
        }

        //dd($final_logistics);

        Excel::create($report_file_name, function($excel) use($excel_tab_name, $final_logistics) {
                            $excel->sheet($excel_tab_name, function($sheet) use($final_logistics) {
                                $sheet->fromArray($final_logistics);
                            });
                        })->store('xlsx', $report_dir);

        Report::updateReportStatus($report_id, $report_status, $creator_id);
    }
}
