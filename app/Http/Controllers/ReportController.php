<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Auth;
use Carbon\Carbon;
use Excel;
use File;
use DB;
use DateTime;

use App\Http\Utilities\GlobalConstant;

use App\Job;
use App\Logistic;
use App\Report;
use App\Company;

class ReportController extends Controller
{
    public function listMasterReport(Request $request) 
    {
        $limit = $request->input('limit') ? : 50;
        $type = $request->input('type') ? : 'master_report';

        // $this->authorize($type);
        
        $title = ucwords(str_replace('_', ' ', $type));
        
        $reports = Report::where(function ($query) use($type) {
                                    $query->where('type',  $type);
                                })
                                ->orderBy('id', 'desc')
                                ->paginate($limit);

        return view('reports.index', compact('title', 'reports'));
    }

    
    /**
     * Generate list of jobs created.
     *
     * @return \Illuminate\Http\Response
     */
    public function genMasterReport()
    {
        $this->authorize('master_report');

        //set_time_limit(0);
        ini_set('max_execution_time', 600); // 10 minutes of report generating execution time
        ini_set('memory_limit', '700M'); // Memory allocation for report generating
        //ini_set('upload_max_filesize', '8M'); // Memory allocation for report generating

        $today = Carbon::now('Asia/Manila');
phpinfo();
        $report_type = "master_report";
        $report_dir = public_path('reports/master_report/'); // server path
        $report_file_name = "master-report-" . $today->format('Ymd');
        $report_name = "Master Report at " . $today->toFormattedDateString();
        $report_loc = $report_dir . $report_file_name;
        $report_status = true;
        //dd($report_dir . $report_name);

        // Check whether dir exist
        /*if ( !is_dir($report_dir) ) {
            File::makeDirectory($report_dir, $mode = 0771, true, true);
        }*/

        //if( !file_exists($report_loc) ) {
            $creator_id = 1;
            $report_id = Report::logReport($report_name, $report_type, $report_dir, $report_file_name, $creator_id);

            /*$raw_jobs = Job::with('company', 'logs', 'level', 'device', 'device.inventory', 'device.customer', 'status', 'technicals', 'logistic', 'logistic.logistic', 'logistic.acceptedBy', 'logistic.logistic.routeTo', 'logistic.encodeJobs', 'technicals.technician', 'technicals.qualityControl.qualityController', 'accessories')
                            ->where('created_at', '<=', $today)
                            //->whereIn('id', [1, 17])
                            //->whereBetween('id', [1, 2500])
                            ->get();*/
            //DB::transaction(function () {
                $raw_jobs = Job::with('company', 'logs', 'complaints', 'level', 'device', 'device.inventory', 'device.customer', 'status', 'technicals', 'logistic', 'logistic.logistic', 'logistic.acceptedBy', 'logistic.logistic.routeTo', 'logistic.encodeJobs', 'technicals.technician', 'technicals.qualityControl.qualityController', 'accessories')
                                ->where('created_at', '<=', $today)
                                ->whereBetween('id', [1, 5500])
                                //->whereBetween('id', [5501, 10000])
                                //->whereBetween('id', [10001, 16000])
                                //->where('imei', '357625053222496')
                                //->where('id', 4811)
                                ->get();

                foreach ($raw_jobs as $key => $raw_job) {
                    //dd($raw_job->logs);
                    $jobs[$key]['Job ID'] = sprintf('JO%08d', $raw_job->id);
                    $jobs[$key]['Creator'] = $raw_job->creator->name;

                    if( !$raw_job->logs->isEmpty() ) {
                        if ( $raw_job->logs->last()->job_status_id == 32 ) {
                            $jobs[$key]['Shipped to Client'] = $raw_job->logs->last()->created_at->format('m/d/Y');
                            $remark_desc = $raw_job->logs->last()->description;
                            $pos = strpos($remark_desc,'remarks - ');
                            if( $pos == false) {
                                $jobs[$key]['Close Job Remark'] = $remark_desc;
                            } else {
                                $jobs[$key]['Close Job Remark'] = substr($remark_desc ,$pos+10, strlen($remark_desc));
                            }
                            
                        } else {
                            $jobs[$key]['Shipped to Client'] = null;
                            $jobs[$key]['Close Job Remark'] = null;
                        }
                    } else {
                        $jobs[$key]['Shipped to Client'] = null;
                        $jobs[$key]['Close Job Remark'] = null;
                    }

                    $jobs[$key]['Company Name'] = $raw_job->company->company_name;
                    $jobs[$key]['Case Level'] = $raw_job->level->name;
                    $jobs[$key]['Case Route'] = $raw_job->case_category;
                    $jobs[$key]['Case Category'] = $raw_job->case_category;
                    $jobs[$key]['Created Date'] = $raw_job->created_at->format('m/d/Y');
                    $jobs[$key]['Model Name'] = $raw_job->device->inventory->model->name;
                    $jobs[$key]['Model Color'] = $raw_job->device->inventory->color;
                    $jobs[$key]['Status'] = $raw_job->status->name;
                    $jobs[$key]['Job Type'] = GlobalConstant::getJobType()[$raw_job->job_type];
                    $jobs[$key]['IMEI'] = $raw_job->imei;
                    $jobs[$key]['Warranty Stat'] = GlobalConstant::getWarrantyStatus()[$raw_job->warranty];
                    //$jobs[$key]['Warranty Stat'] = GlobalConstant::getWarrantyStatus()[$raw_job->device->warranty_status];

                    $jobs[$key]['Tech Remark'] = null;
                    if( !$raw_job->technicals->isEmpty() ) {
                        //dd($raw_job->technicals->last());
                        if ( $raw_job->technicals->last()->remarks ) {
                            foreach ($raw_job->technicals->last()->remarks as $remark) {
                                if ($remark == $raw_job->technicals->last()->remarks->last()) {
                                    $jobs[$key]['Tech Remark'] .= $remark->name;
                                } else {
                                    $jobs[$key]['Tech Remark'] .= $remark->name . ', ';
                                }
                            }
                        }
                        
                        if ($raw_job->technicals->last()->remark) {
                            if ($raw_job->technicals->last()->remarks) {
                                $jobs[$key]['Tech Remark'] .= ', ' . $raw_job->technicals->last()->remark;
                            } else {
                                $jobs[$key]['Tech Remark'] .= $raw_job->technicals->last()->remark;
                            }
                        }
                    }

                    //$jobs[$key]['Encoder'] = $raw_job->logistic->isEmpty() != true ? $raw_job->logistic->last()->acceptedBy->name : null;

                    $jobs[$key]['Case Complain'] = null;
                    if ( !$raw_job->complaints->isEmpty() ) {
                        foreach ($raw_job->complaints as $complaint) {
                            if ($complaint == $raw_job->complaints->last()) {
                                $jobs[$key]['Case Complain'] .= $complaint->name;
                            } else {
                                $jobs[$key]['Case Complain'] .= $complaint->name . ', ';
                            }
                        }
                    }

                    $jobs[$key]['Case From'] = $raw_job->company->company_name;
                    //$jobs[$key]['Case To'] = $raw_job->logistic->isEmpty() != true ? $raw_job->logistic->last()->logistic->routeTo->company_name : null;
                    //$jobs[$key]['On Hold Remark'] = $raw_job->logistic[0]->name;
                    //$jobs[$key]['OnHold Type'] = $raw_job->logistic[0]->name;
                    $jobs[$key]['Technician'] = $raw_job->technicals->isEmpty() != true ? $raw_job->technicals->last()->technician->name : null;
                    $jobs[$key]['CS Remark'] = $raw_job->note;
                    $jobs[$key]['Customer ID'] = $raw_job->device->customer->id_number;
                    $jobs[$key]['Customer Name'] = $raw_job->device->customer->name;
                    $jobs[$key]['Customer No'] = $raw_job->device->customer->mobile_number;

                    // TODO: Check whether accessory still exist?
                    $jobs[$key]['Accessory Name'] = $raw_job->accessories->isEmpty() != true ? $raw_job->accessories->last()->name : null; 
                    $jobs[$key]['Item POP'] = $raw_job->device->pop_date;
                    $jobs[$key]['POP No'] = $raw_job->device->pop_ref;
                    $jobs[$key]['Customer Address'] = $raw_job->device->customer->address;
                    if ( $raw_job->technicals->isEmpty() == false ) {
                        $jobs[$key]['QA'] = isset($raw_job->technicals->last()->qualityControl) == true ? $raw_job->technicals->last()->qualityControl->qualityController->name : null;
                    } else {
                        $jobs[$key]['QA'] = null;
                    }

                    if( $raw_job->logistic->isEmpty() ) {
                        $jobs[$key]['Encoder'] = null;
                        $jobs[$key]['Case To'] = null;
                        $jobs[$key]['HQ Accept Date'] = null;
                        $jobs[$key]['RDU Accept'] = null;
                        $jobs[$key]['Shipment Date'] = null;
                        $jobs[$key]['Waybill'] = null;
                    } else {
                        $jobs[$key]['Encoder'] = $raw_job->logistic->first()->encodeJobs->isEmpty() != true ? $raw_job->logistic->first()->encodeJobs->first()->creator->name : null;
                        $jobs[$key]['Case To'] = $raw_job->logistic->first()->logistic->routeTo->company_name;
                        if ( $raw_job->logistic->first()->encodeJobs->isEmpty() != true && $raw_job->logistic->first()->encodeJobs->first()->status != 1 ) {
                            $jobs[$key]['HQ Accept Date'] = $raw_job->logistic->first()->encodeJobs->first()->updated_at->format('m/d/Y');
                        } else {
                            $jobs[$key]['HQ Accept Date'] = null;
                        }
                        $jobs[$key]['RDU Accept'] = $raw_job->logistic->last()->acceptedBy->name;
                        $jobs[$key]['Shipment Date'] = $raw_job->logistic->last()->logistic->created_at->format('m/d/Y');
                        $jobs[$key]['Waybill'] = $raw_job->logistic->last()->logistic->waybill_number;
                    }
                }
            //});
            //dd($jobs);

            // Generate Master Report
            Excel::create($report_file_name, function($excel) use($report_name, $jobs) {
                            $excel->sheet($report_name, function($sheet) use($jobs) {
                                $sheet->fromArray($jobs);
                            });
                        })->store('xlsx', $report_dir);
            Report::updateReportStatus($report_id, $report_status, $creator_id);

            unset($jobs);

            /*$this->updateReportStatus($report_id, $report_status);*/
        //}

        //dd($jobs);

        // Allow user to download Master Report directly after creation
        /*Excel::create($report_name, function($excel) use($jobs) {
                        $excel->sheet('Master Report', function($sheet) use($jobs) {
                            $sheet->fromArray($jobs);
                        });
                    })->download('xlsx');*/
    }


    /**
     * Generate Daily Device Receive / Release Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function genDailyDeviceReceiveReleaseReport()
    {
        // Branch, Daily Receive, Daily Release, Daily Transaction
        $this->authorize('daily_device_receive_release_report');

        $today = Carbon::now('Asia/Manila');

        $report_type = "daily_device_receive_release_report";
        //$report_dir = public_path('reports\daily_device_receive_release\\'); // local path
        $report_dir = public_path('reports/daily_device_receive_release_report/'); // server path
        $report_file_name = "daily-device-receive-release-report-" . $today->format('Ymd');
        $report_name = "Daily Device Receive Release Report at " . $today->toFormattedDateString();
        $excel_tab_name = "Report at " . $today->toFormattedDateString();
        $report_loc = $report_dir . $report_file_name;
        $report_status = true;
        //dd($report_dir . $report_name);

        // Check whether dir exist
        if ( !is_dir($report_dir) ) {
            File::makeDirectory($report_dir, $mode = 0771, true, true);
        }

        if( !file_exists($report_loc) ) {
            
            /*SELECT company_from.company_name as 'company_from', company_to.company_name as 'company_to', COUNT(logistics.id) as 'total'
            FROM cdu.logistics
            LEFT JOIN cdu.job_logistic ON logistics.id = job_logistic.logistic_id
            LEFT JOIN cdu.companies company_from ON company_from.id = logistics.company_from 
            LEFT JOIN cdu.companies company_to  ON company_to.id = logistics.company_to 
            GROUP BY logistics.id;*/
            $raw_logistics = DB::table('logistics')
                                ->select([
                                    DB::raw('company_from.company_name AS company_from'),
                                    DB::raw('company_to.company_name AS company_to'),
                                    DB::raw('COUNT(logistics.id) AS total')
                                ])
                                ->leftJoin('job_logistic', 'logistics.id', '=', 'job_logistic.logistic_id')
                                ->leftJoin(DB::raw('companies company_from'), 'logistics.company_from', '=', 'company_from.id')
                                ->leftJoin(DB::raw('companies company_to'), 'logistics.company_to', '=', 'company_to.id')
                                ->groupBy('logistics.id')
                                ->whereDate('logistics.created_at', '=', $today->toDateString())
                                ->get();

            if (count($raw_logistics) == 0) {
                return;
            }
            //dd($raw_logistics);

            /*DB::enableQueryLog();
            dd(DB::getQueryLog());*/

            $report_id = $this->logReport($report_name, $report_type, $report_dir, $report_file_name);
            
            foreach ($raw_logistics as $key => $raw_logistic) {
                isset($logistics[$raw_logistic->company_from]['received']) ?: $logistics[$raw_logistic->company_from]['received'] = 0;
                isset($logistics[$raw_logistic->company_from]['released']) ? $logistics[$raw_logistic->company_from]['released'] + $raw_logistic->total : $logistics[$raw_logistic->company_from]['released'] = $raw_logistic->total;
                isset($logistics[$raw_logistic->company_to]['received']) ? $logistics[$raw_logistic->company_to]['received'] + $raw_logistic->total : $logistics[$raw_logistic->company_to]['received'] = $raw_logistic->total;
                isset($logistics[$raw_logistic->company_to]['released']) ?: $logistics[$raw_logistic->company_to]['released'] = 0;
            }            

            $i = 0;
            foreach ($logistics as $key => $logistic) {
                $final_logistics[$i]['Branch'] = $key;
                $final_logistics[$i]['Daily Received'] = $logistic['received'];
                //$final_logistics[$i]['Daily Received'] = (string) $final_logistics[$i]['Daily Received'];
                $final_logistics[$i]['Daily Released'] = $logistic['released'];
                //$final_logistics[$i]['Daily Released'] = (string) $final_logistics[$i]['Daily Released'];
                $final_logistics[$i]['Daily Transaction'] = $logistic['received'] + $logistic['released'];
                $i++;
            }
        }

        //dd($final_logistics);

        Excel::create($report_file_name, function($excel) use($excel_tab_name, $final_logistics) {
                            $excel->sheet($excel_tab_name, function($sheet) use($final_logistics) {
                                $sheet->fromArray($final_logistics);
                            });
                        })->store('xlsx', $report_dir);

        $this->updateReportStatus($report_id, $report_status);
    }


    /**
     * Generate Monthly Device Receive / Release Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function genMonthlyDeviceReceiveReleaseReport()
    {
        // Branch, Monthly Receive, Monthly Release, Monthly Transaction
        $this->authorize('monthly_device_receive_release_report');

        //$yesterday = Carbon::now('Asia/Manila')->subDay();
        $begin_month = Carbon::now('Asia/Manila')->subDay()->startOfMonth();
        $end_month = Carbon::now('Asia/Manila')->subDay()->endOfMonth();

        $report_type = "monthly_device_receive_release_report";
        //$report_dir = public_path('reports\monthly_device_receive_release\\'); // local path
        $report_dir = public_path('reports/monthly_device_receive_release_report/'); // server path
        $report_file_name = "monthly-device-receive-release-report-" . $begin_month->format('Ym');
        $report_name = "Monthly Device Receive Release Report at " . $end_month->toFormattedDateString();
        $excel_tab_name = "Report at " . $end_month->toFormattedDateString();
        $report_loc = $report_dir . $report_file_name;
        $report_status = true;
        //dd($report_name);

        // Check whether dir exist
        if ( !is_dir($report_dir) ) {
            File::makeDirectory($report_dir, $mode = 0771, true, true);
        }

        if( !file_exists($report_loc) ) {
            $creator_id = 1;
            //$report_id = $this->logReport($report_name, $report_type, $report_dir, $report_file_name);
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
            //dd($raw_logistics);

            /*DB::enableQueryLog();
            dd(DB::getQueryLog());*/
            
            foreach ($raw_logistics as $key => $raw_logistic) {
                isset($logistics[$raw_logistic->company_from]['released']) ?: $logistics[$raw_logistic->company_from]['released'] = 0;
                isset($logistics[$raw_logistic->company_from]['received']) ?: $logistics[$raw_logistic->company_from]['received'] = 0;
                isset($logistics[$raw_logistic->company_to]['received']) ?: $logistics[$raw_logistic->company_to]['received'] = 0;
                isset($logistics[$raw_logistic->company_to]['released']) ?: $logistics[$raw_logistic->company_to]['released'] = 0;

                $logistics[$raw_logistic->company_from]['released']++;
                $logistics[$raw_logistic->company_to]['received']++;
            }  

            //dd($logistics);          

            $i = 0;
            foreach ($logistics as $key => $logistic) {
                $final_logistics[$i]['Branch'] = $key;
                $final_logistics[$i]['Monthly Received'] = $logistic['received'];
                $final_logistics[$i]['Monthly Released'] = $logistic['released'];
                $final_logistics[$i]['Monthly Transaction'] = $logistic['received'] + $logistic['released'];
                $i++;
            }
        }

        Excel::create($report_file_name, function($excel) use($excel_tab_name, $final_logistics) {
                            $excel->sheet($excel_tab_name, function($sheet) use($final_logistics) {
                                $sheet->fromArray($final_logistics);
                            });
                        })->download('xlsx');
    }


    /**
     * Retrieve Report based on Report ID provided.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getReport($id)
    {
        $report = Report::find($id);
        $report_loc = $report->dir . $report->file_name . '.xlsx';

        if ( $report->status == false || !file_exists($report_loc) ) {
            flash(trans('cdu.report_not_found'), 'danger');
            return redirect()->back();
        }

        return response()->download($report_loc);
    }


    /**
     * Log report generated.
     *
     * @param  string  $name
     * @param  string  $type
     * @param  string  $dir
     * @param  string  $file_name
     * @return $report_id
     */
    private function logReport($name, $type, $dir, $file_name)
    {
        $report_exist = Report::where('file_name', $file_name)->where('dir', $dir)->get();

        if( $report_exist->isEmpty() ) {
            $report = Report::create([
                                    'name' => $name, 
                                    'type' => $type, 
                                    'dir' => $dir, 
                                    'file_name' => $file_name, 
                                    'created_by' => Auth::id(),
                                    ]);

            return $report->id;
        } else {
            return $report_exist->first()->id;
        }
    }


    /**
     * Update generated report status.
     *
     * @param  int  $id
     * @param  bool  $status
     * @return $report_id
     */
    private function updateReportStatus($id, $status)
    {
        Report::find($id)->update([
                                    'status' => $status,
                                    'updated_by' => Auth::id()
                                ]);
    }


    /**
     * Get Branch's Job's device warranty report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getBranchJobWarrantyReport(Request $request)
    {
        $report_type = "branch_job_device_warranty_report";
        
        $this->authorize($report_type);

        $companies = Company::where('flag', true)->lists('company_name', 'id');

        if ( $request->input('download_btn') ){
            // Generate & Download Report
            $today = Carbon::now('Asia/Manila');

            //$report_dir = public_path('reports\master_report\\'); // local path
            $year = $request->input('year') ? : 2018;
            $company_id = $request->input('company_id');

            $report_file_name = "branch-job-device-warranty-report-" . $today->format('Ymd');
            $report_name = "Report at " . $today->toFormattedDateString();
            $company_name = Company::where('id', $company_id)->value('company_name');

            /*SELECT YEAR(created_at) AS job_year, MONTH(created_at) AS job_month, COUNT(created_at) AS total, warranty, count(warranty)
            FROM cdu.jobs
            WHERE company_id = 3 AND YEAR(created_at) = 2018
            GROUP BY YEAR(created_at), MONTH(created_at), warranty
            ORDER BY created_at;*/

            $raw_info = DB::table('jobs')
                                ->select([
                                    DB::raw('MONTH(created_at) AS month'),
                                    'warranty',
                                    DB::raw('COUNT(created_at) AS total')
                                ])
                                ->where('company_id', $company_id)
                                ->whereYear('created_at', '=', $year)
                                ->groupBy(DB::raw('MONTH(created_at)'))
                                ->groupBy('warranty')
                                ->orderBy('created_at')
                                ->get();

            // dd($raw_info);
                                
            if ( empty($raw_info) ) {
                flash(trans('cdu.report_no_data', ['reportType' => str_replace('_', ' ', $report_type)]), 'warning');

                return view('reports.branchIndex', compact('report_type', 'companies'));
            }

            //dd($raw_info[0]->month);

            foreach ($raw_info as $key => $job_info) {
                //dd($job_info->month);
                $date_obj   = Carbon::createFromFormat('!m', $job_info->month);
                $month_name = $date_obj->format('M'); // ie: March
                
                $devices[$job_info->month]['MONTH'] = $month_name;
                isset($devices[$job_info->month]['IN']) ?: $devices[$job_info->month]['IN'] = 0;
                isset($devices[$job_info->month]['OUT']) ?: $devices[$job_info->month]['OUT'] = 0;
                isset($devices[$job_info->month]['VOID']) ?: $devices[$job_info->month]['VOID'] = 0;

                if( $job_info->warranty == 1 ) {
                    $devices[$job_info->month]['IN'] = (int)$job_info->total;
                } elseif ( $job_info->warranty == 2 ) {
                    $devices[$job_info->month]['OUT'] = (int)$job_info->total;
                } else {
                    $devices[$job_info->month]['VOID'] = (int)$job_info->total;
                }             
            }

            foreach ($devices as $key => $value) {
                isset($devices[$key]['IN']) ?: $devices[$key]['IN'] = 0;
                isset($devices[$key]['OUT']) ?: $devices[$key]['OUT'] = 0;
                isset($devices[$key]['VOID']) ?: $devices[$key]['VOID'] = 0;

                $devices[$key]['TOTAL'] = $devices[$key]['IN'] + $devices[$key]['OUT'] + $devices[$key]['VOID'];
                $devices[$key]['IN_PCT'] = $devices[$key]['IN'] / $devices[$key]['TOTAL'] * 100;
                $devices[$key]['OUT_PCT'] = $devices[$key]['OUT'] / $devices[$key]['TOTAL'] * 100;
                $devices[$key]['VOID_PCT'] = $devices[$key]['VOID'] / $devices[$key]['TOTAL'] * 100;
                $devices[$key]['AVG'] = (float)number_format($devices[$key]['TOTAL'] / 30, 2);
            }

            Excel::create($report_file_name, function($excel) use($report_name, $company_name, $devices) {
                            $excel->sheet($report_name, function($sheet) use($company_name, $devices) {
                                
                                $sheet->mergeCells('A1:J1');
                                $sheet->cell('A1', function($cell) use($company_name) {
                                    // manipulate the cell
                                    $cell->setValue($company_name);
                                });
                                $sheet->cells('A1:J1', function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->mergeCells('B2:D2');
                                $sheet->cell('B2', function($cell) {
                                    // manipulate the cell
                                    $cell->setValue('WARRANTY STATUS (QTY)');
                                });
                                $sheet->cells('B2:D2', function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->mergeCells('F2:H2');
                                $sheet->cell('F2', function($cell) {
                                    // manipulate the cell
                                    $cell->setValue('PERCENTAGE');
                                });
                                $sheet->cells('F2:H2', function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->setMergeColumn(array(
                                    'columns' => array('E', 'I'),
                                    'rows' => array(
                                        array(2,3),
                                    )
                                ));

                                $sheet->cell('E2', function($cell) {
                                    // manipulate the cell
                                    $cell->setValue('TOTAL');
                                });
                                $sheet->cells('E2:E3', function($cells) {
                                    $cells->setAlignment('center');
                                    $cells->setValignment('center');
                                });

                                $sheet->cell('I2', function($cell) {
                                    // manipulate the cell
                                    $cell->setValue('AVERAGE PER DAY');
                                });
                                $sheet->cells('I2:I3', function($cells) {
                                    $cells->setAlignment('center');
                                    $cells->setValignment('center');
                                });

                                $sheet->row(3, array(
                                                 'MONTH', 'IN', 'OUT', 'VOID', '', 'IN', 'OUT', 'VOID' 
                                            ));

                                
                                $sheet->fromArray($devices, null, 'A4', true, false);

                                $sheet->setWidth(array(
                                                    'A' => 10,
                                                    'B' => 8,
                                                    'C' => 8,
                                                    'D' => 8,
                                                    'I' => 20
                                                ));
                            });
                        })->export('xlsx');
        }

        return view('reports.branchIndex', compact('report_type', 'companies'));
    }


    /**
     * Get Branch Total Job Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function getBranchTotalJobReport(Request $request)
    {
        $report_type = "branch_total_job_report";
        
        $this->authorize($report_type);

        $companies = Company::where('flag', true)->lists('company_name', 'id');

        if ( $request->input('download_btn') ){
            // Generate & Download Report
            $today = Carbon::now('Asia/Manila');

            //$report_dir = public_path('reports\master_report\\'); // local path
            $year = $request->input('year') ? : 2018;
            $company_id = $request->input('company_id') ? : 3;

            $report_file_name = "branch-total-job-report-" . $today->format('Ymd');
            $report_name = "Report at " . $today->toFormattedDateString();
            $company_name = Company::find($company_id)->value('company_name');

            /*SELECT YEAR(created_at) AS job_year, MONTH(created_at) AS job_month, COUNT(id) AS total
            FROM cdu.jobs
            WHERE company_id = 3
            GROUP BY YEAR(created_at), MONTH(created_at)
            ORDER BY created_at;*/

            $raw_data = DB::table('jobs')
                                ->select([
                                    DB::raw('YEAR(created_at) AS year'),
                                    DB::raw('MONTH(created_at) AS month'),
                                    DB::raw('COUNT(id) AS total')
                                ])
                                ->where('company_id', $company_id)
                                ->groupBy(DB::raw('YEAR(created_at)'))
                                ->groupBy(DB::raw('MONTH(created_at)'))
                                ->orderBy('created_at')
                                ->get();

            if ( empty($raw_data) ) {
                flash(trans('cdu.report_no_data', ['reportType' => str_replace('_', ' ', $report_type)]), 'warning');

                return view('reports.branchIndex', compact('report_type', 'companies'));
            }

            foreach ($raw_data as $key => $job_info) {
                $days = cal_days_in_month(CAL_GREGORIAN, $job_info->month, $job_info->year);
                $total[$job_info->year][$job_info->month]['monthly'] = $job_info->total;
                $total[$job_info->year][$job_info->month]['daily'] = number_format($job_info->total / $days, 2);
            }

            //dd($total);

            Excel::create($report_file_name, function($excel) use($report_name, $company_name, $total) {
                            $excel->sheet($report_name, function($sheet) use($company_name, $total) {

                                $sheet->mergeCells('A1:G1');
                                $sheet->cell('A1', function($cell) use($company_name) {
                                    // manipulate the cell
                                    $cell->setValue($company_name);
                                });
                                $sheet->cells('A1:G1', function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->mergeCells('A2:G2');
                                $sheet->cell('A2', function($cell) use($company_name) {
                                    // manipulate the cell
                                    $cell->setValue("YEARLY DATA COMPARISON");
                                });
                                $sheet->cells('A2:G2', function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->setMergeColumn(array(
                                    'columns' => array('A'),
                                    'rows' => array(
                                        array(3,4),
                                    )
                                ));
                                $sheet->cell('A3', function($cell) {
                                    // manipulate the cell
                                    $cell->setValue('MONTH');
                                });
                                $sheet->cells('A3:A4', function($cells) {
                                    $cells->setAlignment('center');
                                    $cells->setValignment('center');
                                });

                                $curr_column = 'B';
                                foreach ($total as $year => $year_info) {
                                    $start_col = $curr_column;
                                    $start_cell = $curr_column . '3';
                                    $merge_cell = $curr_column . '3:' . ++$curr_column . '3';

                                    $sheet->mergeCells($merge_cell);
                                    $sheet->cell($start_cell, function($cell) use($year){
                                        // manipulate the cell
                                        $cell->setValue($year);
                                    });
                                    $sheet->cells($merge_cell, function($cells) {
                                        $cells->setAlignment('center');
                                    });

                                    $first_bottom_cell = $start_col . '4';
                                    $second_bottom_cell = $curr_column . '4';
                                    $sheet->cell($first_bottom_cell, function($cell) {
                                        // manipulate the cell
                                        $cell->setValue('MONTHLY');
                                    });
                                    $sheet->cell($second_bottom_cell, function($cell) {
                                        // manipulate the cell
                                        $cell->setValue('DAILY AVG');
                                    });

                                    // Loop to display total data B5 & C5
                                    $data_start_row = 4;
                                    foreach ($year_info as $key => $data) {
                                        $set_row = $data_start_row + $key;
                                        $data_month_cell = $start_col . $set_row;
                                        $data_daily_cell = $curr_column . $set_row;
                                        $sheet->cell($data_month_cell, function($cell) use($data){
                                            // manipulate the cell
                                            $cell->setValue($data['monthly']);
                                        });
                                        $sheet->cell($data_daily_cell, function($cell) use($data){
                                            // manipulate the cell
                                            $cell->setValue($data['daily']);
                                        });
                                    }

                                    $curr_column++;
                                }

                                $months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
                                $starting_row = 5;
                                foreach ($months as $key => $month) {
                                    $set_cell = 'A' . $starting_row;
                                    $sheet->cell($set_cell, function($cell) use($month){
                                        // manipulate the cell
                                        $cell->setValue($month);
                                    });
                                    ++$starting_row;
                                }

                                $sheet->setWidth(array(
                                                    'A' => 10,
                                                    'B' => 10,
                                                    'C' => 10
                                                ));

                            });
                        })->export('xlsx');
        }
        
        return view('reports.branchIndex', compact('report_type', 'companies'));
    }

    /**
     * Get Total Level 3 Warranty Type Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTotalLevelThreeWarrantyReport(Request $request)
    {
        $report_type = "total_level_three_warranty_type_report";
        
        $this->authorize($report_type);

        if ( $request->input('download_btn') ){
            // Generate & Download Report
            $today = Carbon::now('Asia/Manila');

            //$report_dir = public_path('reports\master_report\\'); // local path
            $year = $request->input('year') ? : 2018;

            $report_file_name = "total-level-three-warranty-type-report-" . $today->format('Ymd');
            $report_name = "Report at " . $today->toFormattedDateString();

            /*SELECT companies.company_name, MONTH(jobs.created_at) as month, jobs.warranty, count(jobs.warranty)
            FROM cdu.jobs, cdu.companies
            WHERE jobs.company_id = companies.id AND jobs.job_level_id = 3 AND YEAR(jobs.created_at) = 2018
            GROUP BY companies.company_name, MONTH(jobs.created_at), jobs.warranty
            ORDER BY jobs.created_at;*/

            $raw_data = DB::table('jobs')
                                ->join('companies', 'jobs.company_id', '=', 'companies.id')
                                ->select([
                                    'companies.company_name',
                                    DB::raw('MONTH(jobs.created_at) AS month'),
                                    'jobs.warranty',
                                    DB::raw('COUNT(jobs.warranty) AS total')
                                ])
                                ->whereYear('jobs.created_at', '=', $year)
                                ->where('jobs.job_level_id', 3)
                                ->groupBy('companies.company_name')
                                ->groupBy(DB::raw('MONTH(jobs.created_at)'))
                                ->groupBy('jobs.warranty')
                                ->orderBy('companies.company_name')
                                ->orderBy('jobs.created_at')
                                ->get();

            if (count($raw_data) == 0) {
                flash(trans('cdu.report_no_data', ['reportType' => str_replace('_', ' ', $report_type)]), 'danger');
                return back();
            }

            //dd($raw_data);

            foreach ($raw_data as $key => $data) {
                if ( $key == 0 ) {
                    $warranty[$data->company_name][$data->month]['in'] = 0;
                    $warranty[$data->company_name][$data->month]['out'] = 0;
                    $warranty[$data->company_name][$data->month]['void'] = 0;
                    $warranty[$data->company_name][$data->month]['total'] = 0;
                    $warranty[$data->company_name]['total'] = 0;
                } else {
                    if ( array_key_exists($data->company_name, $warranty) ) {
                        if ( !array_key_exists($data->month, $warranty[$data->company_name]) ) {
                            $warranty[$data->company_name][$data->month]['in'] = 0;
                            $warranty[$data->company_name][$data->month]['out'] = 0;
                            $warranty[$data->company_name][$data->month]['void'] = 0;
                            $warranty[$data->company_name][$data->month]['total'] = 0;
                        }
                    } else {
                        $warranty[$data->company_name][$data->month]['in'] = 0;
                        $warranty[$data->company_name][$data->month]['out'] = 0;
                        $warranty[$data->company_name][$data->month]['void'] = 0;
                        $warranty[$data->company_name][$data->month]['total'] = 0;
                        $warranty[$data->company_name]['total'] = 0;
                    }
                }

                if ( $data->warranty == 1 ) {
                    $warranty[$data->company_name][$data->month]['in'] = $data->total;
                } elseif ( $data->warranty == 2 ) {
                    $warranty[$data->company_name][$data->month]['out'] = $data->total;
                } else {
                    $warranty[$data->company_name][$data->month]['void'] = $data->total;
                }

                $warranty[$data->company_name][$data->month]['total'] += $data->total;
                $warranty[$data->company_name]['total'] += $data->total;
            }

            //dd($warranty);

            Excel::create($report_file_name, function($excel) use($report_name, $warranty) {
                            $excel->sheet($report_name, function($sheet) use($warranty) {
                                // Report Title
                                $sheet->setMergeColumn(array(
                                    'columns' => array('A', 'AX'),
                                    'rows' => array(
                                        array(1,2),
                                    )
                                ));

                                $sheet->cell('A1', function($cell) {
                                    // manipulate the cell
                                    $cell->setValue('BRANCH');
                                });
                                $sheet->cells('A1:A2', function($cells) {
                                    $cells->setAlignment('center');
                                    $cells->setValignment('center');
                                });

                                $sheet->cell('AX1', function($cell) {
                                    // manipulate the cell
                                    $cell->setValue('TOTAL');
                                });
                                $sheet->cells('AX1:AX2', function($cells) {
                                    $cells->setAlignment('center');
                                    $cells->setValignment('center');
                                });

                                // Character 'Z' = 90 = ord('Z')
                                $months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
                                //
                                $warranty_type = ['IN', 'OUT', 'VOID', 'TOTAL'];
                                $starting_col = 'B';
                                $testing = 'Z';
                                //dd(ord($testing));
                                foreach ($months as $key => $month) {
                                    $month_cell = $starting_col . '1';

                                    // Set month column
                                    // Calculate merge cell for month
                                    if( $starting_col == 'Z') {
                                        $month_end_col = 'AC';
                                    } else {
                                        // To crack the excel column from A-Z to AA - AZ
                                        if($key >= 7) {
                                            $month_end_col = 'A' . chr(ord(mb_substr($starting_col, 1)) + 3);
                                            //dd($month_end_col);
                                        } else {
                                            $month_end_col = chr(ord($starting_col) + 3);
                                        }
                                    }
                                    
                                    $month_merge_cell = $month_cell . ':' . $month_end_col . '1';

                                    //dd($month_cell);
                                    $sheet->mergeCells($month_merge_cell);
                                    $sheet->cell($month_cell, function($cell) use($month){
                                        // manipulate the cell
                                        $cell->setValue($month);
                                    });
                                    $sheet->cells($month_merge_cell , function($cells) {
                                        $cells->setAlignment('center');
                                    });

                                    // Set warranty type column
                                    foreach ($warranty_type as $key => $warranty_title) {
                                        $warranty_type_cell = $starting_col . '2';
                                        $sheet->cell($warranty_type_cell, function($cell) use($warranty_title){
                                            // manipulate the cell
                                            $cell->setValue($warranty_title);
                                        });
                                        $sheet->cells($warranty_type_cell, function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        if( $starting_col == 'Z') {
                                            $starting_col = 'AA';
                                        } else {
                                            ++$starting_col;
                                        }
                                    }
                                }

                                // Report Content
                                //dd($warranty);
                                $info_company_col = 'A';
                                $info_total_col = 'AX';
                                $info_warranty_start_col = 'B';
                                $info_start_row = 3;
                                foreach ($warranty as $company => $info) {
                                    // Populate Company Column
                                    $company_cell = $info_company_col . $info_start_row;
                                    $sheet->cell($company_cell, function($cell) use($company) {
                                        $cell->setValue($company);
                                    });

                                    foreach ($info as $key => $data) {
                                        if ($key == 'total') {
                                            // Populate Total Column
                                            $total_cell = $info_total_col . $info_start_row;
                                            $sheet->cell($total_cell, function($cell) use($data) {
                                                $cell->setValue($data);
                                            });
                                        } else {
                                            // Populate Warranty Column
                                            if( $key < 7 ) {
                                                // Manipulate warranty column for month Jan - Jun
                                                $column_multiplier = (($key - 1) * 4 == 0) ? 1 : (($key - 1) * 4) + 1;
                                                $info_warranty_start_col = chr(ord($starting_col) + $column_multiplier);
                                                $info_warranty_in_cell = $info_warranty_start_col . $info_start_row;
                                                $info_warranty_out_cell = ++$info_warranty_start_col . $info_start_row;
                                                $info_warranty_void_cell = ++$info_warranty_start_col . $info_start_row;
                                                $info_warranty_total_cell = ++$info_warranty_start_col . $info_start_row;

                                                $sheet->cell($info_warranty_in_cell, function($cell) use($data) {
                                                    $cell->setValue($data['in']);
                                                });
                                                $sheet->cell($info_warranty_out_cell, function($cell) use($data) {
                                                    $cell->setValue($data['out']);
                                                });
                                                $sheet->cell($info_warranty_void_cell, function($cell) use($data) {
                                                    $cell->setValue($data['void']);
                                                });
                                                $sheet->cell($info_warranty_total_cell, function($cell) use($data) {
                                                    $cell->setValue($data['total']);
                                                });
                                            } elseif ( $key == 7  ) {
                                                // Manipulate warranty column for month July
                                                $sheet->cell('Z'.$info_start_row, function($cell) use($data) {
                                                    $cell->setValue($data['in']);
                                                });
                                                $sheet->cell('AA'.$info_start_row, function($cell) use($data) {
                                                    $cell->setValue($data['out']);
                                                });
                                                $sheet->cell('AB'.$info_start_row, function($cell) use($data) {
                                                    $cell->setValue($data['void']);
                                                });
                                                $sheet->cell('AC'.$info_start_row, function($cell) use($data) {
                                                    $cell->setValue($data['total']);
                                                });
                                            } else {
                                                // Manipulate warranty column for month Aug - Dec
                                                if($info_warranty_start_col < 'Z') {
                                                    $info_warranty_start_col = 'AD'; // Start column for month of Aug
                                                    $column_multiplier = (($key - 7) * 4) + 1;
                                                    $info_warranty_start_col = 'A' . chr(ord(mb_substr($info_warranty_start_col, 1)) + $column_multiplier);
                                                    $info_warranty_in_cell = $info_warranty_start_col . $info_start_row;
                                                    $info_warranty_out_cell = ++$info_warranty_start_col . $info_start_row;
                                                    $info_warranty_void_cell = ++$info_warranty_start_col . $info_start_row;
                                                    $info_warranty_total_cell = ++$info_warranty_start_col . $info_start_row;

                                                    $sheet->cell($info_warranty_in_cell, function($cell) use($data) {
                                                        $cell->setValue($data['in']);
                                                    });
                                                    $sheet->cell($info_warranty_out_cell, function($cell) use($data) {
                                                        $cell->setValue($data['out']);
                                                    });
                                                    $sheet->cell($info_warranty_void_cell, function($cell) use($data) {
                                                        $cell->setValue($data['void']);
                                                    });
                                                    $sheet->cell($info_warranty_total_cell, function($cell) use($data) {
                                                        $cell->setValue($data['total']);
                                                    });
                                                } else {
                                                    $sheet->cell(++$info_warranty_in_cell, function($cell) use($data) {
                                                        $cell->setValue($data['in']);
                                                    });
                                                    $sheet->cell(++$info_warranty_out_cell, function($cell) use($data) {
                                                        $cell->setValue($data['out']);
                                                    });
                                                    $sheet->cell(++$info_warranty_void_cell, function($cell) use($data) {
                                                        $cell->setValue($data['void']);
                                                    });
                                                    $sheet->cell(++$info_warranty_total_cell, function($cell) use($data) {
                                                        $cell->setValue($data['total']);
                                                    });
                                                }
                                            }
                                        }
                                    }

                                    ++$info_start_row;
                                }
                                //dd($data);

                                $sheet->setWidth(array(
                                                    'A' => 30,
                                                    'B' => 8,
                                                    'C' => 8,
                                                    'D' => 8,
                                                    'E' => 8,
                                                    'AX' => 10
                                                ));
                            });
                        })->export('xlsx');
        }

        return view('reports.criticalIndex', compact('report_type'));
    }

    /**
     * Get Total Level 3 Job Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTotalLevelThreeReport(Request $request)
    {
        $report_type = "total_level_three_report";
        
        $this->authorize($report_type);

        if ( $request->input('download_btn') ){
            // Generate & Download Report
            $today = Carbon::now('Asia/Manila');

            //$report_dir = public_path('reports\master_report\\'); // local path
            $year = $request->input('year') ? : 2018;

            $report_file_name = "total-level-three-report-" . $today->format('Ymd');
            $report_name = "Report at " . $today->toFormattedDateString();

            /*SELECT companies.company_name, MONTH(jobs.created_at) as job_month, COUNT(jobs.created_at) as total
            FROM cdu.jobs, cdu.companies
            WHERE jobs.company_id = companies.id AND jobs.job_level_id = 3 AND YEAR(jobs.created_at) = 2018
            GROUP BY companies.company_name, MONTH(jobs.created_at)
            ORDER BY companies.company_name, jobs.created_at;*/

            $data = DB::table('jobs')
                                ->join('companies', 'jobs.company_id', '=', 'companies.id')
                                ->select([
                                    'companies.company_name',
                                    DB::raw('MONTH(jobs.created_at) AS month'),
                                    DB::raw('COUNT(jobs.created_at) AS total')
                                ])
                                ->whereYear('jobs.created_at', '=', $year)
                                ->where('jobs.job_level_id', 3)
                                ->groupBy('companies.company_name')
                                ->groupBy(DB::raw('MONTH(jobs.created_at)'))
                                ->orderBy('companies.company_name')
                                ->orderBy('jobs.created_at')
                                ->get();

            if (count($data) == 0) {
                flash(trans('cdu.report_no_data', ['reportType' => str_replace('_', ' ', $report_type)]), 'danger');
                return back();
            }

            //dd($data);
            foreach ($data as $key => $info) {
                $total[$info->company_name][$info->month] = $info->total;
                if ( array_key_exists('total', $total[$info->company_name]) ) {
                    $total[$info->company_name]['total'] += $info->total;
                } else {
                     $total[$info->company_name]['total'] = $info->total;
                }
                
            }
            //dd($total);

            Excel::create($report_file_name, function($excel) use($report_name, $year, $total) {
                            $excel->sheet($report_name, function($sheet) use($year, $total) {

                                // Report Title
                                $sheet->setMergeColumn(array(
                                    'columns' => array('A', 'N'),
                                    'rows' => array(
                                        array(1,2),
                                    )
                                ));

                                $sheet->cell('A1', function($cell) {
                                    $cell->setValue('BRANCH');
                                });
                                $sheet->cells('A1:A2', function($cells) {
                                    $cells->setAlignment('center');
                                    $cells->setValignment('center');
                                });

                                $sheet->cell('N1', function($cell) {
                                    $cell->setValue('TOTAL');
                                });
                                $sheet->cells('N1:N2', function($cells) {
                                    $cells->setAlignment('center');
                                    $cells->setValignment('center');
                                });

                                $sheet->mergeCells('B1:M1');
                                $sheet->cell('B1', function($cell) use($year){
                                    $cell->setValue($year);
                                });
                                $sheet->cells('B1:M1' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
                                $starting_col = 'B';
                                $starting_row = '2';
                                foreach ($months as $key => $month) {
                                    $month_cell = $starting_col . $starting_row;

                                    // Set month column
                                    $sheet->cell($month_cell, function($cell) use($month){
                                        $cell->setValue($month);
                                    });
                                    $sheet->cells($month_cell , function($cells) {
                                        $cells->setAlignment('center');
                                    });

                                    ++$starting_col;
                                }

                                $data_starting_col = 'A';
                                $data_starting_row = 3;
                                foreach ($total as $company => $total_info) {
                                    $company_cell = 'A' . $data_starting_row;
                                    $sheet->cell($company_cell, function($cell) use($company){
                                        $cell->setValue($company);
                                    });

                                    foreach ($total_info as $month => $total) {
                                        if ( $month == 'total' ) {
                                            $total_cell = 'N' . $data_starting_row;
                                            $sheet->cell($total_cell, function($cell) use($total_info){
                                                $cell->setValue($total_info['total']);
                                            });
                                        } else {
                                            $total_cell = chr(ord($data_starting_col) + $month) . $data_starting_row;
                                            $sheet->cell($total_cell, function($cell) use($total){
                                                $cell->setValue($total);
                                            });
                                        }
                                        
                                    }

                                    ++$data_starting_row;
                                }

                                $sheet->setWidth(array(
                                                'A' => 30,
                                                'N' => 10
                                            ));
                            });
                        })->export('xlsx');
        }

        return view('reports.criticalIndex', compact('report_type'));
    }
    

    /**
     * Get Total Defects Based of Model Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTotalDefectModelReport(Request $request)
    {
        $report_type = "total_model_defect_report";
        
        $this->authorize($report_type);

        if ( $request->input('download_btn') ){
            // Generate & Download Report
            $today = Carbon::now('Asia/Manila');

            //$report_dir = public_path('reports\master_report\\'); // local path
            $year = $request->input('year') ? : 2018;
            $month = $request->input('month') ? : 1;

            $report_file_name = "total-defect-model-report-" . $today->format('Ymd');
            $report_name = "Report at " . $today->toFormattedDateString();

            /*SELECT device_models.code, COUNT(device_models.code) AS total
            FROM jobs, device_inventories, device_models
            WHERE jobs.imei = device_inventories.imei AND device_inventories.device_model_id = device_models.id
            GROUP BY device_models.id;*/

            $data = DB::table('jobs')
                                ->join('device_inventories', 'jobs.imei', '=', 'device_inventories.imei')
                                ->join('device_models', 'device_inventories.device_model_id', '=', 'device_models.id')
                                ->select([
                                    'device_models.code',
                                    DB::raw('COUNT(device_models.code) AS total')
                                ])
                                ->whereYear('jobs.created_at', '=', $year)
                                ->whereMonth('jobs.created_at', '=', $month)
                                ->groupBy('device_models.id')
                                ->orderBy('total', 'dsc')
                                ->orderBy('device_models.code')
                                ->get();

            if (count($data) == 0) {
                flash(trans('cdu.report_no_data', ['reportType' => str_replace('_', ' ', $report_type)]), 'danger');
                return back();
            }

            Excel::create($report_file_name, function($excel) use($report_name, $year, $month, $data) {
                            $excel->sheet($report_name, function($sheet) use($year, $month, $data) {

                                $date_obj = new DateTime();
                                $month_name = $date_obj->createFromFormat('!m', $month)->format('F');

                                // Report Title
                                $sheet->mergeCells('A1:C1');
                                $sheet->cell('A1', function($cell) use($month_name, $year){
                                    $cell->setValue('TOP DEFECTIVE MODEL FOR ' . $month_name . ' ' . $year);
                                });
                                $sheet->cells('A1:C1' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->cell('A2', function($cell) use($year){
                                    $cell->setValue('RANKING');
                                });
                                $sheet->cells('A2' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->cell('B2', function($cell) use($year){
                                    $cell->setValue('MODEL');
                                });
                                $sheet->cells('B2' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->cell('C2', function($cell) use($year){
                                    $cell->setValue('QUANTITY');
                                });
                                $sheet->cells('C2' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                // Total Model Defect List
                                $start_row = 'A';
                                $start_col = 3;

                                foreach ($data as $key => $info) {
                                    $sheet->cell('A' . $start_col, function($cell) use($key){
                                        $cell->setValue($key + 1);
                                    });
                                    $sheet->cells('A' . $start_col , function($cells) {
                                        $cells->setAlignment('center');
                                    });

                                    $sheet->cell('B' . $start_col, function($cell) use($info){
                                        $cell->setValue($info->code);
                                    });
                                    $sheet->cells('B' . $start_col , function($cells) {
                                        $cells->setAlignment('center');
                                    });

                                    $sheet->cell('C' . $start_col, function($cell) use($info){
                                        $cell->setValue($info->total);
                                    });
                                    $sheet->cells('C' . $start_col , function($cells) {
                                        $cells->setAlignment('center');
                                    });

                                    ++$start_col;
                                }

                                $sheet->setWidth(array(
                                                'B' => 40,
                                                'C' => 10
                                            ));
                            });
                        })->export('xlsx');
            //dd($total);
        }

        return view('reports.reportIndex', compact('report_type'));
    }

    /**
     * Get Detailed of Defects of Selected Model Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function getDetailsDefectModelReport(Request $request)
    {
        $report_type = "detailed_model_defect_report";
        
        $this->authorize($report_type);

        if ( $request->input('download_btn') ){
            // Generate & Download Report
            $today = Carbon::now('Asia/Manila');

            //$report_dir = public_path('reports\master_report\\'); // local path
            $year = $request->input('year') ? : 2018;
            $month = $request->input('month') ? : 1;

            $report_file_name = "total-detail-defect-model-report-" . $today->format('Ymd');
            $report_name = "Report at " . $today->toFormattedDateString();

            /*SELECT device_models.code, complaints.name, COUNT(complaints.name) AS total
            FROM jobs, complaint_job, complaints, device_inventories, device_models
            WHERE jobs.id = complaint_job.job_id AND complaint_job.complaint_id = complaints.id AND jobs.imei = device_inventories.imei AND device_inventories.device_model_id = device_models.id AND YEAR(jobs.created_at) = 2018 AND MONTH(jobs.created_at) = 10
            GROUP BY device_models.id, complaints.name
            ORDER BY device_models.code, total, complaints.name;*/

            $raw_data = DB::table('jobs')
                                ->join('complaint_job', 'jobs.id', '=', 'complaint_job.job_id')
                                ->join('complaints', 'complaint_job.complaint_id', '=', 'complaints.id')
                                ->join('device_inventories', 'jobs.imei', '=', 'device_inventories.imei')
                                ->join('device_models', 'device_inventories.device_model_id', '=', 'device_models.id')
                                ->select([
                                    'device_models.code',
                                    'complaints.name AS complaint',
                                    DB::raw('COUNT(complaints.name) AS total')
                                ])
                                ->whereYear('jobs.created_at', '=', $year)
                                ->whereMonth('jobs.created_at', '=', $month)
                                ->groupBy('device_models.id')
                                ->groupBy('complaints.name')
                                ->orderBy('device_models.code')
                                ->orderBy('total', 'dsc')
                                ->orderBy('complaints.name')
                                ->get();

                                //dd($raw_data);

            if (count($raw_data) == 0) {
                flash(trans('cdu.report_no_data', ['reportType' => str_replace('_', ' ', $report_type)]), 'danger');

                return back();
            }

            //dd($raw_data);

            foreach ($raw_data as $key => $info) {
                $devices[$info->code][$info->complaint] = $info->total;
                if ( array_key_exists('total', $devices[$info->code]) ) {
                    $devices[$info->code]['total'] += $info->total;
                } else {
                     $devices[$info->code]['total'] = $info->total;
                }
            }

            uasort($devices, array($this, 'sortByTotal'));
            $devices = array_slice($devices, 0, 10);
            //dd($devices);

            Excel::create($report_file_name, function($excel) use($report_name, $devices, $month, $year) {
                            $excel->sheet($report_name, function($sheet) use($devices, $month, $year) {

                                $date_obj = new DateTime();
                                $month_name = $date_obj->createFromFormat('!m', $month)->format('F');
                                $report_title = 'TOP 10 DEFECTIVE UNITS WITH THEIR TOP 10 CONCERNS FOR THE MONTH OF ' . $month_name . ' ' . $year;
                                
                                $sheet->mergeCells('B1:T1');
                                $sheet->cell('B1', function($cell) use($report_title){
                                    $cell->setValue($report_title);
                                });
                                $sheet->cells('B1:T1' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $start_col = 'B';
                                $start_dbl_col = 'AD';
                                $start_row = 3;
                                $ranking = 1;
                                foreach ($devices as $device_code => $device) {
                                    if ($ranking <= 6) {
                                        // Result Title Field
                                        $result_title_merge_cell = $start_col . $start_row . ':' . chr(ord($start_col) + 2) . $start_row;
                                        $result_title_cell = $start_col . $start_row;
                                        $sheet->mergeCells($result_title_merge_cell);
                                        $sheet->cell($result_title_cell, function($cell) use($ranking, $device_code){
                                            $cell->setValue('RANK ' . $ranking . ' (' . $device_code . ')');
                                        });
                                        $sheet->cells($result_title_merge_cell , function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        // Result Desc Title
                                        ++$start_row;
                                        $result_start_col = $start_col;
                                        $no_title_cell = $start_col . $start_row;
                                        $complain_title_cell = ++$start_col . $start_row;
                                        $quantity_title_cell = ++$start_col . $start_row;

                                        $sheet->cell($no_title_cell, function($cell) {
                                            $cell->setValue('NO');
                                        });
                                        $sheet->cells($no_title_cell , function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        $sheet->cell($complain_title_cell, function($cell) {
                                            $cell->setValue('COMPLAIN');
                                        });
                                        $sheet->cells($complain_title_cell, function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        $sheet->cell($quantity_title_cell, function($cell) {
                                            $cell->setValue('QUANTITY');
                                        });
                                        $sheet->cells($quantity_title_cell, function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        // Result Cell
                                        $qty_result_col = $start_col;
                                        $desc_result_col = chr(ord($start_col) - 1);
                                        $rank_result_col = chr(ord($start_col) - 2);
                                        $result_row = $start_row + 1;
                                        $result_rank = 1;
                                        $result_total = 0;
                                        foreach ($device as $desc => $total) {
                                            if ($desc != 'total') {
                                                $result_rank_cell = $rank_result_col . $result_row;
                                                $result_desc_cell = $desc_result_col . $result_row;
                                                $result_qty_cell = $qty_result_col . $result_row;

                                                $sheet->cell($result_rank_cell, function($cell) use($result_rank) {
                                                    $cell->setValue($result_rank);
                                                });
                                                $sheet->cells($result_rank_cell , function($cells) {
                                                    $cells->setAlignment('center');
                                                });

                                                $sheet->cell($result_desc_cell, function($cell) use($desc) {
                                                    $cell->setValue($desc);
                                                });
                                                $sheet->cells($result_desc_cell, function($cells) {
                                                    $cells->setAlignment('center');
                                                });

                                                $sheet->cell($result_qty_cell, function($cell) use($total) {
                                                    $cell->setValue($total);
                                                });
                                                $sheet->cells($result_qty_cell, function($cells) {
                                                    $cells->setAlignment('center');
                                                });

                                                //$result_total += $total;

                                                ++$result_rank;
                                                ++$result_row;
                                                //++$start_col;
                                            } else {
                                                $result_total = $total;
                                            }
                                        }

                                        // Result Total
                                        $final_total_title_merge_cell = $rank_result_col . $result_row . ':' . $desc_result_col . $result_row ;
                                        $final_total_title_cell = $rank_result_col . $result_row;
                                        $final_total_cell = $qty_result_col . $result_row;

                                        //dd($final_total_cell);

                                        $sheet->mergeCells($final_total_title_merge_cell);
                                        $sheet->cell($final_total_title_cell, function($cell) {
                                            $cell->setValue('TOTAL');
                                        });
                                        $sheet->cells($final_total_title_merge_cell , function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        $sheet->cell($final_total_cell, function($cell) use($result_total) {
                                            $cell->setValue($result_total);
                                        });
                                        $sheet->cells($final_total_cell , function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        $start_col = chr(ord($start_col) + 2);

                                    } elseif ($ranking == 7) {
                                        // Result Title Field
                                        $sheet->mergeCells('Z3:AB3');
                                        $sheet->cell('Z3', function($cell) use($ranking, $device_code){
                                            $cell->setValue('RANK ' . $ranking . ' (' . $device_code . ')');
                                        });
                                        $sheet->cells('Z3:AB3' , function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        // Result Desc Title
                                        ++$start_row;
                                        $sheet->cell('Z4', function($cell) {
                                            $cell->setValue('NO');
                                        });
                                        $sheet->cells('Z4' , function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        $sheet->cell('AA4', function($cell) {
                                            $cell->setValue('COMPLAIN');
                                        });
                                        $sheet->cells('AA4', function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        $sheet->cell('AB4', function($cell) {
                                            $cell->setValue('QUANTITY');
                                        });
                                        $sheet->cells('AB4', function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        // Result Cell
                                        $qty_result_col = 'AB';
                                        $desc_result_col = 'AA';
                                        $rank_result_col = 'Z';
                                        $result_row = 5;
                                        $result_rank = 1;
                                        $result_total = 0;
                                        foreach ($device as $desc => $total) {
                                            if ($desc != 'total') {
                                                $result_rank_cell = $rank_result_col . $result_row;
                                                $result_desc_cell = $desc_result_col . $result_row;
                                                $result_qty_cell = $qty_result_col . $result_row;

                                                $sheet->cell($result_rank_cell, function($cell) use($result_rank) {
                                                    $cell->setValue($result_rank);
                                                });
                                                $sheet->cells($result_rank_cell , function($cells) {
                                                    $cells->setAlignment('center');
                                                });

                                                $sheet->cell($result_desc_cell, function($cell) use($desc) {
                                                    $cell->setValue($desc);
                                                });
                                                $sheet->cells($result_desc_cell, function($cells) {
                                                    $cells->setAlignment('center');
                                                });

                                                $sheet->cell($result_qty_cell, function($cell) use($total) {
                                                    $cell->setValue($total);
                                                });
                                                $sheet->cells($result_qty_cell, function($cells) {
                                                    $cells->setAlignment('center');
                                                });

                                                ++$result_rank;
                                                ++$result_row;
                                            } else {
                                                $result_total = $total;
                                            }
                                        }

                                        // Result Total
                                        $final_total_title_merge_cell = $rank_result_col . $result_row . ':' . $desc_result_col . $result_row ;
                                        $final_total_title_cell = $rank_result_col . $result_row;
                                        $final_total_cell = $qty_result_col . $result_row;

                                        //dd($final_total_cell);

                                        $sheet->mergeCells($final_total_title_merge_cell);
                                        $sheet->cell($final_total_title_cell, function($cell) {
                                            $cell->setValue('TOTAL');
                                        });
                                        $sheet->cells($final_total_title_merge_cell , function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        $sheet->cell($final_total_cell, function($cell) use($result_total) {
                                            $cell->setValue($result_total);
                                        });
                                        $sheet->cells($final_total_cell , function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                    } else {
                                        // Result Title Field
                                        //dd('A'.chr(ord(mb_substr($start_dbl_col, 1)) + 2));
                                        $result_title_merge_cell = $start_dbl_col . $start_row . ':' . 'A' . chr(ord(mb_substr($start_dbl_col, 1)) + 2) . $start_row;
                                        $result_title_cell = $start_dbl_col . $start_row;
                                        $sheet->mergeCells($result_title_merge_cell);
                                        $sheet->cell($result_title_cell, function($cell) use($ranking, $device_code){
                                            $cell->setValue('RANK ' . $ranking . ' (' . $device_code . ')');
                                        });
                                        $sheet->cells($result_title_merge_cell , function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        // Result Desc Title
                                        ++$start_row;
                                        $result_start_col = $start_dbl_col;
                                        $no_title_cell = $start_dbl_col . $start_row;
                                        $complain_title_cell = ++$start_dbl_col . $start_row;
                                        $quantity_title_cell = ++$start_dbl_col . $start_row;

                                        $sheet->cell($no_title_cell, function($cell) {
                                            $cell->setValue('NO');
                                        });
                                        $sheet->cells($no_title_cell , function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        $sheet->cell($complain_title_cell, function($cell) {
                                            $cell->setValue('COMPLAIN');
                                        });
                                        $sheet->cells($complain_title_cell, function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        $sheet->cell($quantity_title_cell, function($cell) {
                                            $cell->setValue('QUANTITY');
                                        });
                                        $sheet->cells($quantity_title_cell, function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        // Result Cell
                                        $qty_result_col = $start_dbl_col;
                                        $desc_result_col = 'A'.chr(ord(mb_substr($start_dbl_col, 1)) - 1);
                                        $rank_result_col = 'A'.chr(ord(mb_substr($start_dbl_col, 1)) - 2);
                                        $result_row = $start_row + 1;
                                        $result_rank = 1;
                                        $result_total = 0;
                                        foreach ($device as $desc => $total) {
                                            if ($desc != 'total') {
                                                $result_rank_cell = $rank_result_col . $result_row;
                                                $result_desc_cell = $desc_result_col . $result_row;
                                                $result_qty_cell = $qty_result_col . $result_row;

                                                $sheet->cell($result_rank_cell, function($cell) use($result_rank) {
                                                    $cell->setValue($result_rank);
                                                });
                                                $sheet->cells($result_rank_cell , function($cells) {
                                                    $cells->setAlignment('center');
                                                });

                                                $sheet->cell($result_desc_cell, function($cell) use($desc) {
                                                    $cell->setValue($desc);
                                                });
                                                $sheet->cells($result_desc_cell, function($cells) {
                                                    $cells->setAlignment('center');
                                                });

                                                $sheet->cell($result_qty_cell, function($cell) use($total) {
                                                    $cell->setValue($total);
                                                });
                                                $sheet->cells($result_qty_cell, function($cells) {
                                                    $cells->setAlignment('center');
                                                });

                                                //$result_total += $total;

                                                ++$result_rank;
                                                ++$result_row;
                                            } else {
                                                $result_total = $total;
                                            }
                                        }

                                        // Result Total
                                        $final_total_title_merge_cell = $rank_result_col . $result_row . ':' . $desc_result_col . $result_row ;
                                        $final_total_title_cell = $rank_result_col . $result_row;
                                        $final_total_cell = $qty_result_col . $result_row;

                                        $sheet->mergeCells($final_total_title_merge_cell);
                                        $sheet->cell($final_total_title_cell, function($cell) {
                                            $cell->setValue('TOTAL');
                                        });
                                        $sheet->cells($final_total_title_merge_cell , function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        $sheet->cell($final_total_cell, function($cell) use($result_total) {
                                            $cell->setValue($result_total);
                                        });
                                        $sheet->cells($final_total_cell , function($cells) {
                                            $cells->setAlignment('center');
                                        });

                                        $start_dbl_col = 'A'.chr(ord(mb_substr($start_dbl_col, 1)) + 2);
                                    }

                                    /*if ($ranking != 7) {
                                        
                                    }*/
                                    --$start_row;
                                    ++$ranking;
                                }

                                $sheet->setWidth(array(
                                                'B' => 6,
                                                'C' => 20,
                                                'D' => 15,
                                                'G' => 20,
                                                'H' => 15,
                                                'K' => 20,
                                                'L' => 15,
                                            ));
                            });
                        })->export('xlsx');
        }
        
        return view('reports.reportIndex', compact('report_type'));
    }

    

    /**
     * Get Pending Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPendingReport(Request $request)
    {
        $report_type = "pending_report";

        $this->authorize($report_type);

        if ( $request->input('download_btn') ) {
            // Generate & Download Report
            $today = Carbon::now('Asia/Manila');

            //$report_dir = public_path('reports\master_report\\'); // local path
            $date = $request->input('date') ? : $today->toDateString();

            $report_file_name = "pending-report-" . $today->format('Ymd');
            $report_name = "Report at " . $today->toFormattedDateString();

            /*SELECT companies.company_name, COUNT(jobs.job_status_id) AS total
            FROM jobs, companies
            WHERE jobs.company_id = companies.id AND jobs.job_status_id < 31 AND jobs.created_at < '2018-11-10'
            GROUP BY companies.company_name
            ORDER BY companies.company_name;*/

            $pending_jobs = DB::table('jobs')
                                ->join('companies', 'jobs.company_id', '=', 'companies.id')
                                ->select([
                                    'companies.company_name',
                                    DB::raw('COUNT(jobs.job_status_id) AS total')
                                ])
                                ->whereNotIn('jobs.job_status_id', [29, 30, 31, 32])
                                ->whereDate('jobs.created_at', '<=', $date)
                                ->groupBy('companies.company_name')
                                ->orderBy('companies.company_name')
                                ->get();
                                //->tosql();

            if (count($pending_jobs) == 0) {
                flash(trans('cdu.report_no_data', ['reportType' => str_replace('_', ' ', $report_type)]), 'warning');
                return redirect()->route('report.pending');
            }

            Excel::create($report_file_name, function($excel) use($report_name, $pending_jobs, $date) {
                            $excel->sheet($report_name, function($sheet) use($pending_jobs, $date) {

                                $report_title = 'LIST OF PENDING JOBS AS OF ' . date_format(date_create($date), 'j M Y');
                                
                                $sheet->mergeCells('A1:B1');
                                $sheet->cell('A1', function($cell) use($report_title){
                                    $cell->setValue($report_title);
                                });
                                $sheet->cells('A1:B1' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->cell('A2', function($cell) use($report_title){
                                    $cell->setValue('BRANCH');
                                });
                                $sheet->cells('A2' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->cell('B2', function($cell) use($report_title){
                                    $cell->setValue('TOTAL');
                                });
                                $sheet->cells('B2' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $start_col = 3;
                                foreach ($pending_jobs as $key => $pending_job) {
                                    $branch_cell = 'A' . $start_col;
                                    $total_cell = 'B' . $start_col;

                                    $sheet->cell($branch_cell, function($cell) use($pending_job){
                                        $cell->setValue($pending_job->company_name);
                                    });

                                    $sheet->cell($total_cell, function($cell) use($pending_job){
                                        $cell->setValue($pending_job->total);
                                    });

                                    ++$start_col;
                                }

                                $sheet->setWidth(array(
                                                'A' => 40,
                                                'B' => 10,
                                            ));
                            });
                        })->export('xlsx');

            // dd($raw_data);
        }

        return view('reports.reportIndex', compact('report_type'));
    }

    /**
     * Get Detailed of Defects of Selected Model Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTicketReport(Request $request)
    {
        $report_type = "ticketing_report";

        $this->authorize($report_type);

        if ( $request->input('download_btn') ) {
            // Generate & Download Report
            $today = Carbon::now('Asia/Manila');

            //$report_dir = public_path('reports\master_report\\'); // local path
            $month = $request->input('month') ? : 1;
            $year = $request->input('year') ? : 2018;

            $report_file_name = "ticketing-report-" . $today->format('Ymd');
            $report_name = "Report at " . $today->toFormattedDateString();

            /*SELECT tickets.job_id, jobs.imei, device_models.name, tickets.type, COUNT(tickets.id) AS total_ticket
            FROM tickets, jobs, device_inventories, device_models
            WHERE tickets.job_id = jobs.id AND jobs.imei = device_inventories.imei AND device_inventories.device_model_id = device_models.id AND MONTH(jobs.created_at) = 10 AND YEAR(jobs.created_at) = 2018
            GROUP BY tickets.job_id, tickets.type
            ORDER BY tickets.job_id;*/

            $raw_data = DB::table('tickets')
                                ->join('jobs', 'tickets.job_id', '=', 'jobs.id')
                                ->join('device_inventories', 'jobs.imei', '=', 'device_inventories.imei')
                                ->join('device_models', 'device_inventories.device_model_id', '=', 'device_models.id')
                                ->select([
                                    'tickets.job_id',
                                    'jobs.imei',
                                    'device_models.name',
                                    'tickets.type',
                                    DB::raw('COUNT(tickets.id) AS total')
                                ])
                                ->WhereMonth('tickets.created_at', '=', $month)
                                ->whereYear('tickets.created_at', '=', $year)
                                ->groupBy('tickets.job_id')
                                ->groupBy('tickets.type')
                                ->orderBy('tickets.job_id')
                                ->get();

            if (count($raw_data) == 0) {
                flash(trans('cdu.report_no_data', ['reportType' => str_replace('_', ' ', $report_type)]), 'warning');
                return redirect()->route('report.ticket');
            }

            foreach ($raw_data as $key => $info) {
                $tickets[$info->job_id]['imei'] = $info->imei;
                $tickets[$info->job_id]['model_name'] = $info->name;
                if( $info->type == 1 ) {
                    $tickets[$info->job_id]['incoming'] = $info->total;
                } else {
                    $tickets[$info->job_id]['outgoing'] = $info->total;
                }

                if ( array_key_exists('total', $tickets[$info->job_id]) ) {
                    $tickets[$info->job_id]['total'] += $info->total;
                } else {
                     $tickets[$info->job_id]['total'] = $info->total;
                }
            }

            //dd($tickets);

            Excel::create($report_file_name, function($excel) use($report_name, $tickets, $month, $year) {
                            $excel->sheet($report_name, function($sheet) use($tickets, $month, $year) {
                                $date_obj = new DateTime();
                                $month_name = $date_obj->createFromFormat('!m', $month)->format('F');

                                $report_title = 'LIST OF TICKETS AS OF ' . $month_name . ' ' . $year;
                                
                                $sheet->mergeCells('A1:F1');
                                $sheet->cell('A1', function($cell) use($report_title){
                                    $cell->setValue($report_title);
                                });
                                $sheet->cells('A1:F1' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->cell('A2', function($cell) use($report_title){
                                    $cell->setValue('JOB ID');
                                });
                                $sheet->cells('A2' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->cell('B2', function($cell) use($report_title){
                                    $cell->setValue('DEVICE MODEL');
                                });
                                $sheet->cells('B2' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->cell('C2', function($cell) use($report_title){
                                    $cell->setValue('IMEI');
                                });
                                $sheet->cells('C2' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->cell('D2', function($cell) use($report_title){
                                    $cell->setValue('INCOMING');
                                });
                                $sheet->cells('D2' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->cell('E2', function($cell) use($report_title){
                                    $cell->setValue('OUTGOING');
                                });
                                $sheet->cells('E2' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->cell('F2', function($cell) use($report_title){
                                    $cell->setValue('TOTAL');
                                });
                                $sheet->cells('F2' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $start_col = 3;
                                foreach ($tickets as $job_id => $ticket) {
                                    $job_id_cell = 'A' . $start_col;
                                    $model_cell = 'B' . $start_col;
                                    $imei_cell = 'C' . $start_col;
                                    $incoming_cell = 'D' . $start_col;
                                    $outgoing_cell = 'E' . $start_col;
                                    $total_cell = 'F' . $start_col;

                                    $sheet->cell($job_id_cell, function($cell) use($job_id){
                                        $cell->setValue(sprintf('JO%08d', $job_id));
                                    });

                                    $sheet->cell($model_cell, function($cell) use($ticket){
                                        $cell->setValue($ticket['model_name']);
                                    });

                                    $sheet->cell($imei_cell, function($cell) use($ticket){
                                        $cell->setValue($ticket['imei']);
                                    });

                                    $sheet->cell($incoming_cell, function($cell) use($ticket){
                                        if ( array_key_exists('incoming', $ticket) ) {
                                            $cell->setValue($ticket['incoming']);
                                        } else {
                                            $cell->setValue('0');
                                        }
                                    });

                                    $sheet->cell($outgoing_cell, function($cell) use($ticket){
                                        if ( array_key_exists('outgoing', $ticket) ) {
                                            $cell->setValue($ticket['outgoing']);
                                        } else {
                                            $cell->setValue('0');
                                        }                                     
                                    });

                                    $sheet->cell($total_cell, function($cell) use($ticket){
                                        $cell->setValue($ticket['total']);
                                    });

                                    ++$start_col;
                                }

                                $sheet->setWidth(array(
                                                'A' => 40,
                                                'B' => 20,
                                                'C' => 20,
                                                'D' => 15,
                                                'E' => 15,
                                                'F' => 15,
                                            ));
                            });
                        })->export('xlsx');

            // dd($raw_data);
        }

        return view('reports.reportIndex', compact('report_type'));
    }

    /**
     * Get Detailed of Defects of Selected Model Report.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCSRPerformanceReport(Request $request)
    {
        $report_type = "csr_performance_report";

        $this->authorize($report_type);

        if ( $request->input('download_btn') ){
            // Generate & Download Report
            $today = Carbon::now('Asia/Manila');

            //$report_dir = public_path('reports\master_report\\'); // local path
            $year = $request->input('year') ? : 2018;
            $month = $request->input('month') ? : 1;

            $report_file_name = "csr-performance-report-" . $today->format('Ymd');
            $report_name = "Report at " . $today->toFormattedDateString();

            /*SELECT users.name, jobs.job_status_id, COUNT(jobs.job_status_id) AS total
            FROM jobs, users
            WHERE jobs.created_by = users.id AND MONTH(jobs.created_at) = 10 AND YEAR(jobs.created_at) = 2018
            GROUP BY users.name, jobs.job_status_id
            ORDER BY users.name;*/

            $raw_data = DB::table('jobs')
                                ->join('users', 'jobs.created_by', '=', 'users.id')
                                ->select([
                                    'users.name',
                                    'jobs.job_status_id',
                                    DB::raw('COUNT(jobs.job_status_id) AS total')
                                ])
                                ->whereMonth('jobs.created_at', '=', $month)
                                ->whereYear('jobs.created_at', '=', $year)
                                ->groupBy('users.name')
                                ->groupBy('jobs.job_status_id')
                                ->orderBy('users.name')
                                ->get();

            if (count($raw_data) == 0) {
                flash(trans('cdu.report_no_data', ['reportType' => str_replace('_', ' ', $report_type)]), 'warning');
                return redirect()->route('report.csr.performance');
            }

            $users = array();
            foreach ($raw_data as $key => $user) {
                if ( !array_key_exists($user->name, $users) ) {
                    $users[$user->name]['IN'] = 0;
                    $users[$user->name]['SHIPPED'] = 0;
                }

                if ( $user->job_status_id < 31 ) {
                    $users[$user->name]['IN'] += $user->total;
                } else {
                    $users[$user->name]['SHIPPED'] += $user->total;
                }
            }

            //dd($users);

            Excel::create($report_file_name, function($excel) use($report_name, $users, $month, $year) {
                            $excel->sheet($report_name, function($sheet) use($users, $month, $year) {

                                // Report Title
                                $date_obj = new DateTime();
                                $month_name = $date_obj->createFromFormat('!m', $month)->format('F');
                                $report_title = 'Customer Service Representative Performance for ' . $month_name . ' ' . $year;
                                
                                $sheet->mergeCells('A1:C1');
                                $sheet->cell('A1', function($cell) use($report_title){
                                    $cell->setValue($report_title);
                                });
                                $sheet->cells('A1:C1' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->cell('A2', function($cell) {
                                    $cell->setValue('Customer Service Representative');
                                });
                                $sheet->cells('A2' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->cell('B2', function($cell) {
                                    $cell->setValue('IN');
                                });
                                $sheet->cells('B2' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                $sheet->cell('C2', function($cell) {
                                    $cell->setValue('SHIPPED');
                                });
                                $sheet->cells('C2' , function($cells) {
                                    $cells->setAlignment('center');
                                });

                                // Report Data
                                $start_row = 3;
                                foreach ($users as $name => $job_status) {
                                    $cust_cell = 'A' . $start_row;
                                    $in_cell = 'B' . $start_row;
                                    $shipped_cell = 'C' . $start_row;

                                    $sheet->cell($cust_cell, function($cell) use($name) {
                                        $cell->setValue($name);
                                    });
                                    $sheet->cells($cust_cell , function($cells) {
                                        $cells->setAlignment('center');
                                    });

                                    $sheet->cell($in_cell, function($cell) use($job_status) {
                                        $cell->setValue($job_status['IN']);
                                    });
                                    $sheet->cells($in_cell , function($cells) {
                                        $cells->setAlignment('center');
                                    });

                                    $sheet->cell($shipped_cell, function($cell) use($job_status) {
                                        $cell->setValue($job_status['SHIPPED']);
                                    });
                                    $sheet->cells($shipped_cell , function($cells) {
                                        $cells->setAlignment('center');
                                    });

                                    ++$start_row;
                                }

                                $sheet->setWidth(array(
                                                'A' => 40,
                                                'B' => 10,
                                                'C' => 15,
                                            ));
                            });
                        })->export('xlsx');
        }

        return view('reports.reportIndex', compact('report_type'));
    }

    private static function sortByTotal($x, $y) {
        //return $x['total'] - $y['total'];
        if( $x['total'] == $y['total'] ) return 0;

        return $x['total'] < $y['total'] ? 1 : -1;
    }
}