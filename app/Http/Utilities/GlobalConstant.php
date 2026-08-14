<?php
namespace App\Http\Utilities;

use DB;

class GlobalConstant {

    function __construct() {
        if( !isset($_SESSION) ) { 
            session_start(); 
        }
    }

    // Return Technician Job Status
    public static function getTechJobStatus()
    {
        $techJobStatus = [
            'new' => 'New',
            'wip' => 'WIP',
            'complete' => 'Complete',
            'cancel' => 'Cancel',
            'pull_out' => 'Pull Out',
        ];

        return $techJobStatus;
    }

    // Return Technician Job Status
    public static function getQCJobStatus()
    {
        $jobQCStatus = [
            'new' => 'New',
            'wip' => 'WIP',
            'fail' => 'Fail',
            'pass' => 'Pass',
        ];

        return $jobQCStatus;
    }

    // Return eWarranty Status
    public static function getEWarrantyStatus()
    {
        $eWarrantyStatus = [
            '1' => 'Registered', // Successfully register customer's device.
            '2' => 'Not Register', // Did not register customer's device.
            '3' => 'Device Not Found', // Doesn't match the IMEI & Model Code.
        ];
        
        return $eWarrantyStatus;
    }

    // Return BOM Status
    public static function getBOMStatus()
    {
        $bomStatus = [
            '1' => 'Active',
            '2' => 'Inactive'
        ];
        return $bomStatus;
    }

    // Return BOM Status
    public static function getBOMCategory()
    {
        $bomCategory = [
            'Body' => 'Body',
            'Accessories' => 'Accessories'
        ];
        return $bomCategory;
    }

    // Return Device Warranty Status
    public static function getWarrantyStatus()
    {
        $warrantyStatus = [
            '1' => 'In',
            '2' => 'Out',
            '3' => 'Void',
        ];
        return $warrantyStatus;
    }

    // Return Job Type
    public static function getJobType()
    {
        $jobType = [
            '1' => 'Body',
            '2' => 'Accessories'
        ];
        return $jobType;
    }

    // Return Job Status ID based on Job Status type
    public static function getJobStatusID()
    {
        $jobStatus = [
            'new' => 1,
            'complete' => [29, 30],
            'pull_out' => 31,
            'close' => 32, // Item returned to cust, close Job.
        ];

        return $jobStatus;
    }

    // Return Job Process - For job logging purposes.
    public static function getJobProcess()
    {
        $jobProcess = [
            'create_job'            => 1,
            'edit_job'              => 2,
            'change_job_status'     => 3,
            'assign_job'            => 4,
            'create_job_technical'  => 5,
            'create_job_qc'         => 6,
            'route_job'             => 7,
            'accept_job_route'      => 8,
            'close_job'             => 9,
            'accept_tech_job'       => 10,
            'complete_tech_job'       => 11,
            'pull_out_tech_job'       => 12,
            'accept_job_qc'       => 13,
            'complete_job_qc'       => 14,
            'change_job_level'       => 15,
        ];
        return $jobProcess;
    }

    // Return Job Process - For job logging purposes.
    public static function getJobProcessByID()
    {
        $jobProcess = [
            1 => 'create_job',
            2 => 'edit_job',
            3 => 'change_job_status',
            4 => 'assign_job',
            5 => 'create_job_technical',
            6 => 'create_job_qc',
            7 => 'route_job',
            8 => 'accept_job_route',
            9 => 'close_job',
            10 => 'accept_tech_job',
            11 => 'complete_tech_job',
            12 => 'pull_out_tech_job',
            13 => 'accept_job_qc',
            14 => 'complete_job_qc',
            15 => 'change_job_level',
        ];
        return $jobProcess;
    }

    // Return Job Routing Status.
    public static function getJobRouteStatus()
    {
        $jobRouteStatus = [
            '1' => 'Transferred',
            '2' => 'Pending',
            '3' => 'Accepted',
            '3' => 'Cancelled',
        ];
        return $jobRouteStatus;
    }

    // Return Customer ID Type
    public static function getCustomerIDType()
    {
        $indentityType = ['1' => 'SSS', '2' => 'NBI', '3' => 'DRIVERS LICENSE', '4' => 'COMPANY ID', '5' => 'PAG-IBIG', '6' => 'COMELEC', '7' => 'PASSPORT', '8' => 'POLICE CLEARANCE', '9' => 'STUDENT ID', '10' => 'OTHERS'];
        return $indentityType;
    }

    // Return Job's Case Category
    public static function getCaseCategory()
    {
        $caseCategory = [
            'walk_in' => 'Walk In',
            'dealer' => 'Dealer',
        ];
        return $caseCategory;
    }

    // Return Warehouse Status
    public static function getWarehouseStatus()
    {
        $warehouseStatus = [
            1 => 'Active',
            2 => 'Inactive',
        ];

        return $warehouseStatus;
    }

    // Return Shipping Status
    public static function getLogisticStatus()
    {
        // WIP: May need to change the status to pending, accept & reject.
        // Need to analyse whether to categorize between HQ & branch.
        $logisticStatus = [
            1 => 'Sent to HQ',
            2 => 'Accepted by HQ',
            3 => 'Rejected by HQ',
            4 => 'Sent to Branch',
            5 => 'Accepted by Branch',
            6 => 'Rejected by Branch',
            7 => 'Missing',
        ];

        return $logisticStatus;
    }

    // Return Individual Device Logistic Status
    public static function getJobLogisticStatus()
    {
        // TODO: May need to change the status to pending, accept & reject.
        // Need to analyse whether to categorize between HQ & branch.
        $logisticStatus = [
            1 => 'Sent to HQ',
            2 => 'Accepted by HQ',
            3 => 'Cancelled by HQ',
            4 => 'Sent to Branch',
            5 => 'Accepted by Branch',
            6 => 'Cancelled by Branch',
        ];

        return $logisticStatus;
    }

    // Return Device Decoding Status
    public static function getDeviceDecodingStatus()
    {       
        $decodingStatus = [
            1 => 'Transfer to HQ',
            2 => 'Accept by Decoder',
            3 => 'Reject by Decoder',
            4 => 'Device Not Found by Decoder',
            5 => 'Transfer to Branch',
            6 => 'Accept by Admin',
            7 => 'Device Not Found by Admin',
        ];

        return $decodingStatus;
    }

    // Return Job Storage Status
    public static function getJobStorageStatus()
    {       
        $jobStorageStatus = [
            0 => 'Device Taken from Storage',
            1 => 'Device Being Stored'
        ];

        return $jobStorageStatus;
    }

    // Return Ticket Type
    public static function getTicketType()
    {       
        $ticketType = [
            1 => 'Incoming',
            2 => 'Outgoing'
        ];

        return $ticketType;
    }

    // Return Report Type
    public static function getReportType()
    {       
        $reportType = [
            'master_report' => 'Master Report',
            'device_receive_report' => 'Device Receive Report',
            'device_release_report' => 'Device Release Report',
            //2 => 'Outgoing'
        ];

        return $reportType;
    }

    // Return Month List
    public static function getMonth()
    {       
        $month = [ 
            1 => 'JAN', 
            2 => 'FEB', 
            3 => 'MAR', 
            4 => 'APR', 
            5 => 'MAY', 
            6 => 'JUN', 
            7 => 'JUL', 
            8 => 'AUG', 
            9 => 'SEP', 
            10 => 'OCT', 
            11 => 'NOV', 
            12 => 'DEC'
        ];

        return $month;
    }

    // Return Year List
    public static function getYear()
    {       
        $year = [ 
            '2018' => '2018',
            '2019' => '2019'
        ];

        return $year;
    }

    // Return Special Case Status
    public static function getSpecialCaseStatus()
    {       
        $specialCaseStatus = [ 
            1 => 'New',
            2 => 'Approve',
            3 => 'Deny'
        ];

        return $specialCaseStatus;
    }

    // Return Special Case Status
    public static function getTechnicalRemarks()
    {  
        if( !isset($_SESSION['tech_remarks']) || empty($_SESSION['tech_remarks']) ){
            $_SESSION['tech_remarks'] = DB::table('technical_remarks')->where('flag', true)->lists('name', 'id');
        }

        return $_SESSION['tech_remarks'];
    }

}
?>