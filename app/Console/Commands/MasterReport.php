<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Carbon\Carbon;
use Excel;
use File;
use DB;

use App\Http\Utilities\GlobalConstant;

use App\Report;
use App\Job;

class MasterReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:master';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Master Report Which Consists of Details of All Jobs Being Created';

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
        ini_set('max_execution_time', 6000); // 60 minutes of report generating execution time
        //ini_set('memory_limit', '2048M'); // 2GB Memory allocation for report generating
        ini_set('memory_limit', '4096M'); // 4GB Memory allocation for report generating
        
        $today = Carbon::now('Asia/Manila');

        $report_type = "master_report";
        $report_dir = public_path('reports/master_report/');
        $report_file_name = "master-report-" . $today->format('Ymd');
        $report_name = "Master Report at " . $today->toFormattedDateString();
        $report_loc = $report_dir . $report_file_name;
        $report_status = true;

        // Check whether dir exist
        if ( !is_dir($report_dir) ) {
            File::makeDirectory($report_dir, $mode = 0771, true, true);
        }

        if( !file_exists($report_loc) ) {
            $creator_id = 1;
            $report_id = Report::logReport($report_name, $report_type, $report_dir, $report_file_name, $creator_id);

            Job::with('company', 'logs', 'complaints', 'level', 'device', 'device.inventory', 'device.customer', 'status', 'technicals', 'logistic', 'logistic.logistic', 'logistic.acceptedBy', 'logistic.logistic.routeTo', 'logistic.encodeJobs', 'technicals.technician', 'technicals.qualityControl.qualityController', 'accessories')
                            ->where('created_at', '<', $today)
                            ->chunk(1000, function($raw_jobs) {
                                foreach ($raw_jobs as $key => $raw_job)
                                {
                                    if ( $raw_job->job_status_id == 32 ) {
                                        $jobs[$key]['ShippedToClient'] = $raw_job->updated_at->format('m/d/Y');
                                        $remark_desc = $raw_job->logs->last()->description;
                                        $pos = strpos($remark_desc,'remarks - ');
                                        if( $pos == false) {
                                            $jobs[$key]['CloseJobRemark'] = $remark_desc;
                                        } else {
                                            $jobs[$key]['CloseJobRemark'] = substr($remark_desc ,$pos+10, strlen($remark_desc));
                                        }
                                        
                                    } else {
                                        $jobs[$key]['ShippedToClient'] = null;
                                        $jobs[$key]['CloseJobRemark'] = null;
                                    }

                                    $jobs[$key]['TechRemark'] = null;
                                    $jobs[$key]['TechnicianCompletionDate'] = null;
                                    if( !$raw_job->technicals->isEmpty() ) {
                                        if ( $raw_job->technicals->last()->completion_date ) {
                                            $jobs[$key]['TechnicianCompletionDate'] = date('m/d/Y', strtotime($raw_job->technicals->last()->completion_date));
                                            //$jobs[$key]['TechnicianCompletionDate'] = $raw_job->technicals->last()->completion_date->format('m/d/Y');
                                        } else {
                                            $jobs[$key]['TechnicianCompletionDate'] = $raw_job->technicals->last()->completion_date;
                                        }
                                        

                                        if ( $raw_job->technicals->last()->remarks ) {
                                            foreach ($raw_job->technicals->last()->remarks as $remark) {
                                                if ($remark == $raw_job->technicals->last()->remarks->last()) {
                                                    $jobs[$key]['TechRemark'] .= $remark->name;
                                                } else {
                                                    $jobs[$key]['TechRemark'] .= $remark->name . ', ';
                                                }
                                            }
                                        }
                                        
                                        if ($raw_job->technicals->last()->remark) {
                                            if (!empty($jobs[$key]['TechRemark'])) {
                                                $jobs[$key]['TechRemark'] .= ', ' . $raw_job->technicals->last()->remark;
                                            } else {
                                                $jobs[$key]['TechRemark'] .= $raw_job->technicals->last()->remark;
                                            }
                                        }
                                    }

                                    $jobs[$key]['CaseComplain'] = null;
                                    if ( !$raw_job->complaints->isEmpty() ) {
                                        foreach ($raw_job->complaints as $complaint) {
                                            if ($complaint == $raw_job->complaints->last()) {
                                                $jobs[$key]['CaseComplain'] .= $complaint->name;
                                            } else {
                                                $jobs[$key]['CaseComplain'] .= $complaint->name . ', ';
                                            }
                                        }
                                    }     

                                    $jobs[$key]['Technician'] = !$raw_job->technicals->isEmpty() ? $raw_job->technicals->last()->technician->name : null; 
                                    $jobs[$key]['AccessoryName'] = !$raw_job->accessories->isEmpty() ? $raw_job->accessories->last()->name : null; 
                                   
                                    $jobs[$key]['QA'] = null;
                                    if ( $raw_job->technicals->isEmpty() == false ) {
                                        $jobs[$key]['QA'] = isset($raw_job->technicals->last()->qualityControl) == true ? $raw_job->technicals->last()->qualityControl->qualityController->name : null;
                                    }

                                    if( $raw_job->logistic->isEmpty() ) {
                                        $jobs[$key]['Encoder'] = null;
                                        $jobs[$key]['CaseTo'] = null;
                                        $jobs[$key]['HQAcceptDate'] = null;
                                        $jobs[$key]['RDUAccept'] = null;
                                        $jobs[$key]['ShipmentDate'] = null;
                                        $jobs[$key]['Waybill'] = null;
                                    } else {
                                        $jobs[$key]['Encoder'] = $raw_job->logistic->first()->encodeJobs->isEmpty() != true ? $raw_job->logistic->first()->encodeJobs->first()->creator->name : null;
                                        $jobs[$key]['CaseTo'] = $raw_job->logistic->first()->logistic->routeTo->company_name;
                                        if ( $raw_job->logistic->first()->encodeJobs->isEmpty() != true && $raw_job->logistic->first()->encodeJobs->first()->status != 1 ) {
                                            $jobs[$key]['HQAcceptDate'] = $raw_job->logistic->first()->encodeJobs->first()->updated_at->format('m/d/Y');
                                        } else {
                                            $jobs[$key]['HQAcceptDate'] = null;
                                        }
                                        $jobs[$key]['RDUAccept'] = $raw_job->logistic->last()->acceptedBy->name;
                                        $jobs[$key]['ShipmentDate'] = $raw_job->logistic->last()->logistic->created_at->format('m/d/Y');
                                        $jobs[$key]['Waybill'] = $raw_job->logistic->last()->logistic->waybill_number;
                                    }

                                    $jobs[$key]['JobID'] = sprintf('JO%08d', $raw_job->id);
                                    $jobs[$key]['Creator'] = $raw_job->creator->name;
                                    $jobs[$key]['CompanyName'] = $raw_job->company->company_name;
                                    $jobs[$key]['CaseLevel'] = $raw_job->level->name;
                                    $jobs[$key]['CaseRoute'] = $raw_job->case_category;
                                    $jobs[$key]['CaseCategory'] = $raw_job->case_category;
                                    $jobs[$key]['CreatedDate'] =   $raw_job->created_at->format('m/d/Y');
                                    $jobs[$key]['ModelName'] =  $raw_job->device->inventory->model->name;
                                    $jobs[$key]['ModelColor'] = $raw_job->device->inventory->color;
                                    $jobs[$key]['Status'] =  $raw_job->status->name;
                                    $jobs[$key]['JobType'] = GlobalConstant::getJobType()[$raw_job->job_type];
                                    $jobs[$key]['IMEI'] = $raw_job->imei;
                                    $jobs[$key]['WarrantyStat'] = GlobalConstant::getWarrantyStatus()[$raw_job->warranty];
                                    $jobs[$key]['CaseFrom'] = $raw_job->company->company_name;
                                    $jobs[$key]['CSRemark']  = $raw_job->note;
                                    $jobs[$key]['CustomerID']  = $raw_job->device->customer->id_number;
                                    $jobs[$key]['CustomerName'] = $raw_job->device->customer->name;
                                    $jobs[$key]['CustomerNo'] = $raw_job->device->customer->mobile_number;
                                    $jobs[$key]['ItemPOP'] = $raw_job->device->pop_date; 
                                    $jobs[$key]['POPNo'] = $raw_job->device->pop_ref;
                                    $jobs[$key]['CustomerAddress'] = $raw_job->device->customer->address;
                                }
                                
                                DB::table('master_reports')->insert($jobs);
                                unset($jobs);
                            });

            // Generate Master Report
            //$raw_jobs = DB::table('master_reports')->skip(0)->take(25000)->get();
            $raw_jobs = DB::table('master_reports')->get();

            foreach ($raw_jobs as $key => $job) {
                $jobs[$key]['Job ID'] = $job->JobID;
                $jobs[$key]['Creator'] = $job->Creator;
                $jobs[$key]['Shipped To Client'] = $job->ShippedToClient;
                $jobs[$key]['Close Job Remark'] = $job->CloseJobRemark;
                $jobs[$key]['Company Name'] = $job->CompanyName;
                $jobs[$key]['Case Level'] = $job->CaseLevel;
                $jobs[$key]['Case Route'] = $job->CaseRoute;
                $jobs[$key]['Case Category'] = $job->CaseCategory;
                $jobs[$key]['Created Date'] =    $job->CreatedDate;
                $jobs[$key]['Model Name'] = $job->ModelName;
                $jobs[$key]['Model Color'] = $job->ModelColor;
                $jobs[$key]['Status'] =  $job->Status;
                $jobs[$key]['Job Type'] = $job->JobType;
                $jobs[$key]['IMEI'] = $job->IMEI;
                $jobs[$key]['Warranty Stat'] = $job->WarrantyStat;
                $jobs[$key]['Tech Remark'] = $job->TechRemark;
                $jobs[$key]['Technician Completion Date'] = $job->TechnicianCompletionDate;
                $jobs[$key]['Case Complain'] = $job->CaseComplain;
                $jobs[$key]['Case From'] = $job->CaseFrom;
                $jobs[$key]['Technician']  = $job->Technician;
                $jobs[$key]['CS Remark']  = $job->CSRemark;
                $jobs[$key]['Customer ID']  = $job->CustomerID;
                $jobs[$key]['Customer Name'] = $job->CustomerName;
                $jobs[$key]['Customer No'] = $job->CustomerNo;
                $jobs[$key]['Accessory Name']   = $job->AccessoryName;
                $jobs[$key]['Item POP']  = $job->ItemPOP;
                $jobs[$key]['POP No'] = $job->POPNo;
                $jobs[$key]['Customer Address'] = $job->CustomerAddress;
                $jobs[$key]['QA'] = $job->QA;
                $jobs[$key]['Encoder'] = $job->Encoder;
                $jobs[$key]['CaseTo'] = $job->CaseTo;
                $jobs[$key]['HQ Accept Date'] = $job->HQAcceptDate;
                $jobs[$key]['RDU Accept'] = $job->RDUAccept;
                $jobs[$key]['Shipment Date'] = $job->ShipmentDate;
                $jobs[$key]['Waybill'] = $job->Waybill;
            }

            Excel::create($report_file_name, function($excel) use($report_name, $jobs) {
                            $excel->sheet($report_name, function($sheet) use($jobs) {
                                $sheet->fromArray($jobs);
                            });
                        })->store('xlsx', $report_dir);

            Report::updateReportStatus($report_id, $report_status, $creator_id);
            
            // Data Cleanup
            unset($raw_jobs);
            unset($jobs);
            DB::table('master_reports')->truncate();
        }
    }
}
