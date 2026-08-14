<?php

namespace App\Console\Commands;

use App\Job;
use Excel;
use Illuminate\Console\Command;
use App\Http\Utilities\GlobalConstant;
use Carbon\Carbon;


class MasterReportV2 extends Command
{
     protected $signature = 'report:master-v2';

        protected $description = 'Master Report V2';
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
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '4096M');
        // $this->info(ini_get('memory_limit'));
        $rows = [];

        $jobTypes = GlobalConstant::getJobType();
        $warrantyStatuses = GlobalConstant::getWarrantyStatus();

        Job::with([
            'creator',
            'company',
            'level',
            'status',
            'logs',
            'complaints',
            'accessories',
            'device.customer',
            'device.inventory.model',

            'technicals.technician',
            'technicals.remarks',
            'technicals.qualityControl.qualityController',

            'logistic.logistic.routeTo',
            'logistic.acceptedBy',
            'logistic.encodeJobs.creator',
        ])
        ->where('created_at', '>=', '2024-01-01')
        ->where('created_at', '<', Carbon::now('Asia/Manila'))
        ->orderBy('id', 'asc')
        ->chunk(1000, function ($jobs) use (&$rows, $jobTypes, $warrantyStatuses) {

            foreach ($jobs as $job) {

                /*
                |--------------------------------------------------------------------------
                | Latest technical record
                |--------------------------------------------------------------------------
                */
                $lastTechnical = $job->technicals->isEmpty()
                    ? null
                    : $job->technicals->last();

                $technician = '';
                $technicianCompletionDate = '';
                $techRemark = '';
                $qa = '';

                if ($lastTechnical) {

                    if ($lastTechnical->technician) {
                        $technician = $lastTechnical->technician->name;
                    }

                    if ($lastTechnical->completion_date) {
                        $technicianCompletionDate = date(
                            'm/d/Y',
                            strtotime($lastTechnical->completion_date)
                        );
                    }

                    if ($lastTechnical->remarks && !$lastTechnical->remarks->isEmpty()) {
                        $techRemarks = [];

                        foreach ($lastTechnical->remarks as $remark) {
                            $techRemarks[] = $remark->name;
                        }

                        $techRemark = implode(', ', $techRemarks);
                    }

                    /*
                    | Old report also includes the manual remark field.
                    */
                    if ($lastTechnical->remark) {
                        $techRemark = $techRemark
                            ? $techRemark . ', ' . $lastTechnical->remark
                            : $lastTechnical->remark;
                    }

                    if (
                        $lastTechnical->qualityControl &&
                        $lastTechnical->qualityControl->qualityController
                    ) {
                        $qa = $lastTechnical
                            ->qualityControl
                            ->qualityController
                            ->name;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Complaints
                |--------------------------------------------------------------------------
                */
                $caseComplain = '';

                if (!$job->complaints->isEmpty()) {
                    $complaints = [];

                    foreach ($job->complaints as $complaint) {
                        $complaints[] = $complaint->name;
                    }

                    $caseComplain = implode(', ', $complaints);
                }

                /*
                |--------------------------------------------------------------------------
                | Accessories
                |--------------------------------------------------------------------------
                */
                $accessoryName = '';

                if (!$job->accessories->isEmpty()) {
                    $accessory = $job->accessories->last();
                    $accessoryName = $accessory ? $accessory->name : '';
                }

                /*
                |--------------------------------------------------------------------------
                | Shipped to client / close job remark
                |--------------------------------------------------------------------------
                */
                $shippedToClient = '';
                $closeJobRemark = '';

                if ($job->job_status_id == 32) {
                    $shippedToClient = $job->updated_at
                        ? $job->updated_at->format('m/d/Y')
                        : '';

                    $lastLog = $job->logs->isEmpty()
                        ? null
                        : $job->logs->last();

                    if ($lastLog && $lastLog->description) {
                        $remarkDescription = $lastLog->description;
                        $position = strpos($remarkDescription, 'remarks - ');

                        $closeJobRemark = $position === false
                            ? $remarkDescription
                            : substr($remarkDescription, $position + 10);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Logistics
                |--------------------------------------------------------------------------
                */
                $encoder = '';
                $caseTo = '';
                $hqAcceptDate = '';
                $rduAccept = '';
                $shipmentDate = '';
                $waybill = '';

                if (!$job->logistic->isEmpty()) {

                    $firstLogistic = $job->logistic->first();
                    $lastLogistic = $job->logistic->last();

                    /*
                    | Encoder + HQ Accept Date
                    */
                    if (
                        $firstLogistic &&
                        $firstLogistic->encodeJobs &&
                        !$firstLogistic->encodeJobs->isEmpty()
                    ) {
                        $firstEncodeJob = $firstLogistic->encodeJobs->first();

                        if ($firstEncodeJob) {

                            if ($firstEncodeJob->creator) {
                                $encoder = $firstEncodeJob->creator->name;
                            }

                            if (
                                $firstEncodeJob->status != 1 &&
                                $firstEncodeJob->updated_at
                            ) {
                                $hqAcceptDate = $firstEncodeJob
                                    ->updated_at
                                    ->format('m/d/Y');
                            }
                        }
                    }

                    /*
                    | Case To
                    */
                    if (
                        $firstLogistic &&
                        $firstLogistic->logistic &&
                        $firstLogistic->logistic->routeTo
                    ) {
                        $caseTo = $firstLogistic
                            ->logistic
                            ->routeTo
                            ->company_name;
                    }

                    /*
                    | RDU Accept / Shipment / Waybill
                    */
                    if ($lastLogistic) {

                        if ($lastLogistic->acceptedBy) {
                            $rduAccept = $lastLogistic->acceptedBy->name;
                        }

                        if ($lastLogistic->logistic) {
                            $shipmentDate = $lastLogistic->logistic->created_at
                                ? $lastLogistic->logistic->created_at->format('m/d/Y')
                                : '';

                            $waybill = $lastLogistic->logistic->waybill_number;
                        }
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Final Excel row — same header order as old Master Report
                |--------------------------------------------------------------------------
                */
                $rows[] = [
                    'Job ID' => sprintf('JO%08d', $job->id),

                    'Creator' => $job->creator ? $job->creator->name : '',

                    'Shipped To Client' => $shippedToClient,

                    'Close Job Remark' => $closeJobRemark,

                    'Company Name' => $job->company ? $job->company->company_name : '',

                    'Case Level' => $job->level ? $job->level->name : '',

                    'Case Route' => $job->case_category ? $job->case_category : '',

                    'Case Category' => $job->case_category ? $job->case_category : '',

                    'Created Date' => $job->created_at
                        ? $job->created_at->format('m/d/Y')
                        : '',

                    'Model Name' => (
                        $job->device &&
                        $job->device->inventory &&
                        $job->device->inventory->model
                    ) ? $job->device->inventory->model->name : '',

                    'Model Color' => (
                        $job->device &&
                        $job->device->inventory
                    ) ? $job->device->inventory->color : '',

                    'Status' => $job->status ? $job->status->name : '',

                    'Job Type' => isset($jobTypes[$job->job_type])
                        ? $jobTypes[$job->job_type]
                        : '',

                    'IMEI' => $job->imei ? $job->imei : '',

                    'Warranty Stat' => isset($warrantyStatuses[$job->warranty])
                        ? $warrantyStatuses[$job->warranty]
                        : '',

                    'Tech Remark' => $techRemark,

                    'Technician Completion Date' => $technicianCompletionDate,

                    'Case Complain' => $caseComplain,

                    'Case From' => $job->company ? $job->company->company_name : '',

                    'Technician' => $technician,

                    'CS Remark' => $job->note ? $job->note : '',

                    'Customer ID' => (
                        $job->device &&
                        $job->device->customer
                    ) ? $job->device->customer->id_number : '',

                    'Customer Name' => (
                        $job->device &&
                        $job->device->customer
                    ) ? $job->device->customer->name : '',

                    'Customer No' => (
                        $job->device &&
                        $job->device->customer
                    ) ? $job->device->customer->mobile_number : '',

                    'Accessory Name' => $accessoryName,

                    'Item POP' => $job->device ? $job->device->pop_date : '',

                    'POP No' => $job->device ? $job->device->pop_ref : '',

                    'Customer Address' => (
                        $job->device &&
                        $job->device->customer
                    ) ? $job->device->customer->address : '',

                    'QA' => $qa,

                    'Encoder' => $encoder,

                    'CaseTo' => $caseTo,

                    'HQ Accept Date' => $hqAcceptDate,

                    'RDU Accept' => $rduAccept,

                    'Shipment Date' => $shipmentDate,

                    'Waybill' => $waybill,
                ];
            }

            $this->info('Processed rows: ' . count($rows));
        });
        $filename = 'master-report-' . date('Ymd-His');

        Excel::create($filename, function ($excel) use ($rows) {
            $excel->sheet('Master Report', function ($sheet) use ($rows) {
                $sheet->fromArray($rows);
            });
        })->store('xlsx', public_path('reports/master_report'));

        $this->info('Excel generated. Rows: ' . count($rows));


    }
}

